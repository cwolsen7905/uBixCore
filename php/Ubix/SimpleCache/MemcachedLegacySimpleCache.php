<?php

declare(strict_types=1);

namespace Ubix\SimpleCache;

use DateInterval;
use DateTime;
use Memcached;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Traversable;

/**
 * Service to access Memcached data following the PSR-16 simple cache standard that is compatible with the legacy VSM sessions
 *
 * @see \Ubix\Tests\SimpleCache\MemcachedLegacySimpleCacheTest PHPUnit test case
 */
final class MemcachedLegacySimpleCache extends AbstractSimpleCache implements SimpleCache
{
    public const REATTEMPTS_ON_ERROR_DEFAULT = 3;

    private const PORT_DEFAULT = 11211;

    private const RESULT_CODE_ERRORS = [Memcached::RES_PROTOCOL_ERROR, Memcached::RES_SERVER_ERROR];

    private const TTL_DEFAULT = 3600; // 3600 = 60 * 60

    /**
     * An array of Memcached objects available as singletons stored with a key which is an MD5 hash
     *
     * @var array<string, Memcached> $singletons
     */
    private static array $singletons = [];

    /**
     * Constructor
     *
     * @param Logger   $logger            Logger
     * @param string[] $servers           An array of Memcached servers in the form 'host:port'
     * @param int      $reattemptsOnError The maximum number of reattempts to try on error
     */
    public function __construct(
        private Logger $logger,
        private array $servers,
        private int $reattemptsOnError = self::REATTEMPTS_ON_ERROR_DEFAULT,
    ) {
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
        $memcached = $this->getMemcached($key);

        //
        //  Call get on the Memcached object
        //
        $get = $memcached->get($key);

        //
        //  If the result code shows not found then return the default value
        //
        if ($memcached->getResultCode() === Memcached::RES_NOTFOUND) {
            return $default;
        }

        //
        //  Return the retrieved value
        //
        return $get;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time
     *
     * @param string                $key   The key of the item to store
     * @param mixed                 $value The value of the item to store, must be serializable
     * @param int|DateInterval|null $ttl   The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that. (optional)
     *
     * @return bool True on success and false on failure
     */
    public function set(string $key, mixed $value, int|DateInterval|null $ttl = null): bool
    {
        $memcached = $this->getMemcached($key);

        //
        //  Handle $ttl when it is passed as a DateInterval
        //
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime())->add($ttl)->getTimestamp() - (int)(new DateTime())->format('U');
        } elseif ($ttl === null) {
            $ttl = self::TTL_DEFAULT;
        } elseif ($ttl <= 0) { // If TTL is <= 0 then simply delete the key
            return $this->delete($key);
        }

        //
        //  Call set on the Memcached object (and retry on fail)
        //
        $attempts = 0;
        do {
            //
            //  Log any reattempts
            //
            if ($attempts > 1) {
                $this->logger->warning('Reattempting Memcached `set` in `' . self::class . '` on key `' . $key . '`');
            }

            //
            //  Attempt the set and increment the attempts count
            //
            $set = $memcached->set($key, $value, $ttl);
            $attempts++;
        } while ($attempts <= $this->reattemptsOnError && in_array($memcached->getResultCode(), self::RESULT_CODE_ERRORS, true));

        return $set;
    }

    /**
     * Delete an item from the cache by its unique key
     *
     * @param string $key The unique cache key of the item to delete
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete(string $key): bool
    {
        $memcached = $this->getMemcached($key);

        //
        //  Call delete on the Memcached object
        //
        $delete = $memcached->delete($key);

        //
        //  If the key wasn't found still return true since the "key is gone" end state has still been achieved
        //
        if ($memcached->getResultCode() === Memcached::RES_NOTFOUND) {
            return true;
        }

        //
        //  Return the result of the delete call
        //
        return $delete;
    }

    /**
     * Wipes clean the entire cache's keys
     *
     * @return bool True on success and false on failure
     */
    public function clear(): bool
    {
        return $this->getMemcachedFromServersKey(0)->flush();
    }

    /**
     * Obtains multiple cache items by their unique keys
     *
     * @param iterable<string> $keys    A list of keys that can be obtained in a single operation
     * @param mixed            $default Default value to return for keys that do not exist
     *
     * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- IncorrectTypeHint wants `iterable<string>` as the return type hint which PHP doesn't support
    {
        //
        //  Validate the iterable
        //
        $this->validateIterable($keys);

        //
        //  Loop through all keys and get all values
        //
        $multiple = [];
        foreach ($keys as $key) {
            $multiple[$key] = $this->get($key, $default);
        }
        return $multiple;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL
     *
     * @param array<string, mixed>|Traversable<string, mixed> $values A list of key => value pairs for a multiple-set operation
     * @param int|DateInterval|null                           $ttl    The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that. (optional)
     *
     * @return bool True on success and false on failure
     */
    public function setMultiple(iterable $values, int|DateInterval|null $ttl = null): bool
    {
        //
        //  Validate the iterable
        //
        $this->validateKeyValueList($values);

        //
        //  Handle $ttl when it is passed as a DateInterval
        //
        if ($ttl instanceof DateInterval) {
            $ttl = (new DateTime())->add($ttl)->getTimestamp() - (int)(new DateTime())->format('U');
        } elseif ($ttl === null) {
            $ttl = self::TTL_DEFAULT;
        } elseif ($ttl <= 0) { // If TTL is <= 0 then simply delete the keys
            //
            //  Extract just the keys from the key value list
            //
            $keysToDelete = [];

			// phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable -- $unused won't be used and that's fine
            foreach ($values as $key => $unused) {
                $keysToDelete[] = $key;
            }

            return $this->deleteMultiple($keysToDelete);
        }

        //
        //  Loop through all keys and set all values
        //
        foreach ($values as $key => $value) {
            if ($this->set($key, $value, $ttl) === false) { // If the set failed then return false
                return false;
            }
        }

        return true; // All sets succeeded so return true
    }

    /**
     * Deletes multiple cache items in a single operation
     *
     * @param iterable<string> $keys A list of string-based keys to be deleted
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     */
    public function deleteMultiple(iterable $keys): bool // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- IncorrectTypeHint wants `iterable<string>` as the parameter type hint which PHP doesn't support
    {
        //
        //  Validate the iterable
        //
        $this->validateIterable($keys);

        //
        //  Loop through all keys and get all values
        //
        foreach ($keys as $key) {
            if ($this->delete($key) === false) { // If the key delete failed then return false
                return false;
            }
        }

        return true; // All deletes succeeded so return true
    }

    /**
     * Determines whether an item is present in the cache
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes and not to be used within your live applications operations for get/set, as this method is subject to a race condition where your has() will return true and immediately after, another script can remove it making the state of your app out of date
     *
     * @param string $key The cache item key
     *
     * @return bool Whether or not Memached has the key in its cache
     */
    public function has(string $key): bool
    {
        $memcached = $this->getMemcached($key);

        //
        //  Call get on the Memcached object
        //
        $memcached->get($key);

        //
        //  Return the result by looking at the result code
        //
        return $memcached->getResultCode() === Memcached::RES_SUCCESS;
    }

    /**
     * Get a Memcached object based on a key
     *
     * @param string $key The key that needs to be mapped to a server
     *
     * @return Memcached The Memcached object
     */
    private function getMemcached(string $key): Memcached
    {
        //
        //  Validate the key
        //
        $this->validateKey($key);

        //
        //  Determine which server to use based on the $key
        //
        $serversKey = abs(crc32($key)) % count($this->servers);

        //
        //  Get the singleton based on the servers key
        //
        return $this->getMemcachedFromServersKey($serversKey);
    }

    /**
     * Get a Memcached object based on the key of the servers property
     *
     * @param int $serversKey The $this->servers key
     *
     * @return Memcached The Memcached object
     */
    private function getMemcachedFromServersKey(int $serversKey): Memcached
    {
        //
        //  Determine the singleton key
        //
        $singletonKey = md5((string)$serversKey . serialize($this->servers));

        //
        //  Create the singleton if it doesn't already exist
        //
        if (!isset(self::$singletons[$singletonKey])) {
            self::$singletons[$singletonKey] = new Memcached();

            $reordered = array_merge(
                array_slice($this->servers, $serversKey), // The $serversKey entry to the end of the array of $this->servers
                array_slice($this->servers, 0, $serversKey), // The beginning of the array though to the $serversKey entry of $this->servers
            );
            foreach ($reordered as $server) { // $server will be in the form 'host:port'
                self::$singletons[$singletonKey]->addServer(
                    host: strpos($server, ':') !== false ? substr($server, 0, strpos($server, ':')) : $server,
                    port: strpos($server, ':') !== false ? (int)substr($server, strpos($server, ':') + 1) : self::PORT_DEFAULT,
                );
            }
        }

        //
        //  Return the singleton
        //
        return self::$singletons[$singletonKey];
    }
}
