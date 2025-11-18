<?php

declare(strict_types=1);

namespace Ubix\Exception;

use Exception;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Exception for use in simple cache (PSR-16) integrations
 *
 * @see \Ubix\Tests\Exception\SimpleCacheInvalidArgumentExceptionTest PHPUnit test case
 */
final class SimpleCacheInvalidArgumentException extends Exception implements InvalidArgumentException
{
}
