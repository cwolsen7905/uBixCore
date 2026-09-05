<?php

declare(strict_types=1);

namespace Ubix\Payload;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;

/**
 * Interface for all Payload Response classes
 */
interface ResponsePayloadInterface
{
    /**
     * Get response payload
     * map incoming DTO into a response payload specified by static::class
     *
     * @param Dto $dto The DTO to map from
     *
     * @return ResponsePayload The mapped response payload
     */
    public static function getResponse(Dto $dto): self;

    /**
     * Get the response data as an array
     *
     * @return array<string, mixed>
     */
    public function getResponseData(): array;
}
