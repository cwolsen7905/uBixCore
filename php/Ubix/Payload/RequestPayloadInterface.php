<?php

declare(strict_types=1);

namespace Ubix\Payload;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ubix\Exception\DtoException;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Interface for all request payload classes
 */
interface RequestPayloadInterface
{
    /**
     * Get data from the request and deserialize it into a DTO then validate it
     *
     * @param Request $request The PSR request object containing the data
     *
     * @return RequestPayload The deserialized and validated DTO
     *
     * @throws DtoException If the payload cannot be deserialized
     * @throws Exception If the payload is invalid
     */
    public static function getRequest(Request $request): self;

    /**
     * Attempt to build a typed value and collect errors in the same shape used elsewhere.
     *
     * @param string $name The field name for error reporting
     * @param string $dest The public property to receive the value
     * @param mixed  $raw  The raw input value
     *
     * @return void
     */
    public function validateAndMapField(string $name, string $dest, mixed $raw): void;
}
