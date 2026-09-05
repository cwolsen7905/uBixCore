<?php

declare(strict_types=1);

namespace Ubix\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface as ClientException;

/**
 * Exception for use in HTTP client (PSR-18) integrations
 *
 * @see \Ubix\Tests\Exception\HttpClientExceptionTest PHPUnit test case
 */
final class HttpClientException extends Exception implements ClientException
{
}
