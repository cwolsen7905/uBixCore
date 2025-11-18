<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\JsonError;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;

/**
 * Service to interact with JSON
 *
 * @see \Ubix\Tests\Service\JsonServiceTest PHPUnit test case
 */
final class JsonService
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
     * Decode JSON string
     *
     * @param string $json The JSON string being decoded
     *
     * @throws DtoException If the JSON decoding fails
     *
     * @return array<int|string, mixed> The JSON decoded
     */
    public function decode(string $json): array
    {
        //
        //  Decode the JSON
        //
        /**
         * @var array<int|string, mixed>|null $decoded
         */
        $decoded = json_decode($json, true); // phpcs:ignore Generic.PHP.ForbiddenFunctions -- This is the only place in our code we allow json_decode, otherwise developers must be using \Ubix\Service\JsonService::decode()

        //
        //  Throw an exception if the decoding failed, otherwise return the decoded JSON
        //
        if (json_last_error() !== JSON_ERROR_NONE || $decoded === null) {
            throw new DtoException(
                'There was an error decoding from JSON.',
                ExceptionCode::JSON_DECODE_FAILED->value,
                new JsonError(
                    code:    json_last_error(),
                    input:   $json,
                    message: json_last_error_msg(),
                    method:  'decode',
                ),
            );
        }

        return $decoded;
    }

    /**
     * Encode a value as JSON
     *
     * @param mixed $value The value being encoded (can be any type except a resource)
     *
     * @throws DtoException If the JSON encoding fails
     *
     * @return string The encoded JSON
     */
    public function encode(mixed $value): string
    {
        //
        //  Encode the value as JSON
        //
        /**
         * @var non-empty-string|false $encoded
         */
        $encoded = json_encode($value); // phpcs:ignore Generic.PHP.ForbiddenFunctions -- This is the only place in our code we allow json_encode, otherwise developers must be using \Ubix\Service\JsonService::encode()

        //
        //  Throw an exception if the encoding failed, otherwise return the JSON
        //
        if (json_last_error() !== JSON_ERROR_NONE || $encoded === false) {
            throw new DtoException(
                'There was an error encoding to JSON.',
                ExceptionCode::JSON_ENCODE_FAILED->value,
                new JsonError(
                    code:    json_last_error(),
                    input:   serialize($value),
                    message: json_last_error_msg(),
                    method:  'encode',
                ),
            );
        }

        return $encoded;
    }
}
