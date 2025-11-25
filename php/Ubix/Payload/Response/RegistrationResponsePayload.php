<?php

declare(strict_types=1);

namespace Ubix\Payload\Response;

use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;

/**
 * Payload object for registration response
 */
final class RegistrationResponsePayload extends Payload implements ResponsePayload
{
    public string $status;

    public string $message;

    public ?int $userId;

    /**
     * Constructor
     *
     * @param string $status  The response status
     * @param string $message Response message
     * @param ?int   $userId  The created user ID (optional)
     */
    public function __construct(
        string $status,
        string $message,
        ?int $userId = null,
    ) {
        $this->status  = $status;
        $this->message = $message;
        $this->userId  = $userId;
    }
}
