<?php

declare(strict_types=1);

namespace Ubix\SessionHandler;

use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use SessionHandlerInterface as SessionHandler;

/**
 * Session handler that uses the VSM legacy keys and a PSR simple cache source
 *
 * @see \Ubix\Tests\SessionHandler\SimpleCacheLegacySessionHandlerTest PHPUnit test case
 */
final class SimpleCacheLegacySessionHandler implements SessionHandler
{
    private const TTL_DEFAULT = 14400; // 14400 = 60 * 60 * 4

    /**
     * Constructor
     *
     * @param Logger      $logger      Logger
     * @param SimpleCache $simpleCache PSR simple cache
     * @param int         $ttl         Simple cache time to live
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SimpleCache $simpleCache,
        private int $ttl = self::TTL_DEFAULT,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $sessionId): bool
    {
        return $this->simpleCache->delete($this->getKey($sessionId));
    }

    /**
     * {@inheritDoc}
     */
    public function gc(int $maxLifetime): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused but required by the interface
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function open(string $path, string $name): bool // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- These parameters are unused but required by the interface
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $sessionId): string
    {
        $get = $this->simpleCache->get($this->getKey($sessionId));
        return is_string($get) ? $get : ''; // We return an empty string rather than false as per the interface
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $sessionId, string $data): bool
    {
        return $this->simpleCache->set($this->getKey($sessionId), $data, $this->ttl);
    }

    /**
     * {@inheritDoc}
     */
    private function getKey(string $sessionId): string
    {
        return sprintf('sess_%s_%s', session_get_cookie_params()['domain'], $sessionId); // This assumes that session_set_cookie_params() was previously invoked and the domain is already correct, if you are having problems make sure session_set_save_handler() was invoked
    }
}
