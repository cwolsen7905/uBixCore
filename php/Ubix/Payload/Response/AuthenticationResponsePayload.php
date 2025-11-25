<?php

declare(strict_types=1);

namespace Ubix\Payload\Response;

use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;

/**
 * Payload object for authentication response
 */
final class AuthenticationResponsePayload extends Payload implements ResponsePayload
{
    public int $id;

    public string $displayName;

    public string $message;

    /**
     * Constructor
     *
     * @param int    $id       The user ID
     * @param string $displayName The display name
     * @param string $message  Response message
     */
    public function __construct(
        int $id,
        string $displayName,
        string $message,
    ) {
        $this->id       = $id;
        $this->displayName = $displayName;
        $this->message  = $message;
    }
}
