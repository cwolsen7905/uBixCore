<?php

declare(strict_types=1);

namespace Ubix\SimpleCache;

use APCUIterator;
use DateInterval;
use DateTimeImmutable;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Traversable;

/**
 * PSR-16 simple cache over APCu: shared memory inside one container
 *
 * APCu lives in the PHP-FPM master process, so every worker of one pool sees the
 * same entries (and they survive worker turnover), while nothing leaves the
 * container and nothing is written to disk. That makes it the right place for
 * data that must stay local — credentials resolved from uBix Vault, for instance —
 * where the memcached cache, shared over the network by every pod, is not.
 *
 * APCu is off for the CLI by default (`apc.enable_cli`), so check
 * {@see self::isAvailable()} before relying on it; the callers here treat an
 * unavailable cache as "no cache".
 *
 * Every key is stored under $prefix, so {@see self::clear()} clears only this
 * cache's entries, never another user of the same APCu segment.
 *
 * @see \Ubix\Tests\SimpleCache\ApcuSimpleCacheTest PHPUnit test case
 */
final class ApcuSimpleCache extends AbstractSimpleCache implements SimpleCache
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     * @param string $prefix Prefix put in front of every key (it scopes clear())
     */
    public function __construct(
        Logger $logger,
        private string $prefix = 'ubix.',
    ) {
        parent::__construct($logger);
    }

    /**
     * Whether APCu is loaded and enabled for this SAPI
     *
     * @return bool True when the cache can be used
     */
    public function isAvailable(): bool
    {
        return function_exists('apcu_enabled') && apcu_enabled();
    }

    /**
     * Fetches a value from the cache
     *
     * @param string $key     The unique key of this item in the cache
     * @param mixed  $default Default value to return if the key does not exist
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        $success = false;
        $value   = apcu_fetch($this->prefix . $key, $success);

        return $success ? $value : $default;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time
     *
     * A TTL of zero or less (or a DateInterval that is already past) deletes the key,
     * as PSR-16 requires; null stores it without expiry.
     *
     * @param string                $key   The key of the item to store
     * @param mixed                 $value The value of the item to store, must be serializable
     * @param int|DateInterval|null $ttl   The TTL value of this item
     *
     * @return bool True on success and false on failure
     */
    public function set(string $key, mixed $value, int|DateInterval|null $ttl = null): bool
    {
        $this->validateKey($key);

        $seconds = $this->ttlSeconds($ttl);

        if ($seconds !== null && $seconds <= 0) {
            return $this->delete($key);
        }

        return apcu_store($this->prefix . $key, $value, $seconds ?? 0);
    }

    /**
     * Delete an item from the cache by its unique key
     *
     * @param string $key The unique cache key of the item to delete
     *
     * @return bool True if the item was removed or was not there, false on error
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);

        return apcu_delete($this->prefix . $key) || !apcu_exists($this->prefix . $key);
    }

    /**
     * Wipes this cache's keys (those under its prefix), leaving other APCu users alone
     *
     * @return bool True on success and false on failure
     */
    public function clear(): bool
    {
        $ok = true;

        foreach (new APCUIterator('/^' . preg_quote($this->prefix, '/') . '/', APC_ITER_KEY) as $entry) {
            if (is_array($entry) && isset($entry['key']) && is_string($entry['key'])) {
                $ok = apcu_delete($entry['key']) && $ok;
            }
        }

        return $ok;
    }

    /**
     * Obtains multiple cache items by their unique keys
     *
     * @param iterable<string> $keys    A list of keys that can be obtained in a single operation
     * @param mixed            $default Default value to return for keys that do not exist
     *
     * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- IncorrectTypeHint wants `iterable<string>` as the return type hint which PHP doesn't support
    {
        $this->validateIterable($keys);

        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL
     *
     * @param array<string, mixed>|Traversable<string, mixed> $values A list of key => value pairs for a multiple-set operation
     * @param int|DateInterval|null                           $ttl    The TTL value of this item
     *
     * @return bool True on success and false on failure
     */
    public function setMultiple(iterable $values, int|DateInterval|null $ttl = null): bool
    {
        $this->validateKeyValueList($values);

        $ok = true;

        foreach ($values as $key => $value) {
            $ok = $this->set($key, $value, $ttl) && $ok;
        }

        return $ok;
    }

    /**
     * Deletes multiple cache items in a single operation
     *
     * @param iterable<string> $keys A list of string-based keys to be deleted
     *
     * @return bool True if the items were successfully removed. False if there was an error
     */
    public function deleteMultiple(iterable $keys): bool // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- IncorrectTypeHint wants `iterable<string>` as the parameter type hint which PHP doesn't support
    {
        $this->validateIterable($keys);

        $ok = true;

        foreach ($keys as $key) {
            $ok = $this->delete($key) && $ok;
        }

        return $ok;
    }

    /**
     * Determines whether an item is present in the cache
     *
     * @param string $key The cache item key
     *
     * @return bool True if the item is present, false otherwise
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);

        return apcu_exists($this->prefix . $key);
    }

    /**
     * A PSR-16 TTL in whole seconds (null means no expiry)
     *
     * @param int|DateInterval|null $ttl The TTL
     *
     * @return ?int Seconds, or null for no expiry
     */
    private function ttlSeconds(int|DateInterval|null $ttl): ?int
    {
        if ($ttl instanceof DateInterval) {
            $now = new DateTimeImmutable();

            return $now->add($ttl)->getTimestamp() - $now->getTimestamp();
        }

        return $ttl;
    }
}
