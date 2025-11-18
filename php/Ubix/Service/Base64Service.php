<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to interact with Base64
 *
 * @see \Ubix\Tests\Service\Base64ServiceTest PHPUnit test case
 */
final class Base64Service
{
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
     * Decode a Base64 encoded string
     *
     * @param string $base64EncodedString The Base64 encoded string
     *
     * @throws Exception If the Base64 decoding fails
     *
     * @return string The Base64 encoded string decoded
     */
    public function decode(string $base64EncodedString): string // TEMPORARY: ANDREW:: do I really need this? I copied it from JsonService but that one is using arrays/objects and this is onyl strings so maybe I am over-engineering?
    {
        //
        //  Decode the Base64 encoded string
        //
        $decoded = base64_decode($base64EncodedString, true); // phpcs:ignore Generic.PHP.ForbiddenFunctions -- This is the only place in our code we allow base64_decode, otherwise developers must be using \Ubix\Service\Base64Service::decode()

        //
        //  Throw an exception if the decoding failed, otherwise return the decoded string
        //
        if ($decoded === false) {
            throw new Exception('There was an error decoding from Base64.', ExceptionCode::BASE64_DECODE_FAILED->value);
        }

        return $decoded;
    }
}
