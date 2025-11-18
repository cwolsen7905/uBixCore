<?php

declare(strict_types=1);

namespace Ubix\SimpleCache;

use Psr\Log\LoggerInterface as Logger;
use Traversable;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\SimpleCacheInvalidArgumentException;

/**
 * Abstract service following the PSR-16 simple cache standard
 */
abstract class AbstractSimpleCache
{
    private const KEY_MAXIMUM_LENGTH = 250;

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Validate a key according to the PSR-16 standard
     *
     * @param string $key The key to validate
     *
     * @throws SimpleCacheInvalidArgumentException If the key is invalid
     *
     * @return void
     */
    protected function validateKey(string $key): void
    {
        if ($key === '') {
            throw new SimpleCacheInvalidArgumentException('Simple cache key must not be empty.', ExceptionCode::SIMPLE_CACHE_KEY_IS_EMPTY->value);
        }

        if (preg_match('/[{}\(\)\/\\\\@:]/', $key)) {
            throw new SimpleCacheInvalidArgumentException('Simple cache key contains a reserved character.', ExceptionCode::SIMPLE_CACHE_KEY_USES_RESERVED_CHARACTERS->value);
        }

        if (strlen($key) > self::KEY_MAXIMUM_LENGTH) {
            throw new SimpleCacheInvalidArgumentException('Simple cache key is too long.', ExceptionCode::SIMPLE_CACHE_KEY_IS_TOO_LONG->value);
        }
    }

    /**
     * Validate an iterable
     *
     * @param iterable<string>|iterable<string, mixed> $iterable The iterable to validate
     *
     * @throws SimpleCacheInvalidArgumentException If the iterable is invalid
     *
     * @return void
     */
    protected function validateIterable(iterable $iterable): void
    {
        foreach ($iterable as $key) {
            if (!is_string($key)) {
                throw new SimpleCacheInvalidArgumentException('Simple cache key must be a string.', ExceptionCode::SIMPLE_CACHE_KEY_WRONG_TYPE->value);
            }

            $this->validateKey($key);
        }
    }

    /**
     * Validate a key value list
     *
     * @param array<string, mixed>|Traversable<string, mixed> $keyValueList The iterable to validate
     *
     * @return void
     */
    protected function validateKeyValueList(iterable $keyValueList): void
    {
		// phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable -- $unused won't be used and that's fine
        foreach ($keyValueList as $key => $unused) {
            $this->validateKey($key);
        }
    }
}
