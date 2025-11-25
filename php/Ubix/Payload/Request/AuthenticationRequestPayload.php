<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\String\Email;
use Ubix\DataType\String\Password;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Payload object for authentication request parameters
 */
final class AuthenticationRequestPayload extends Payload implements RequestPayload
{
    public Email $email;

    public Password $password;

    public ?bool $debug;

    /**
     * Constructor
     *
     * @param ?string $email The email
     * @param ?string $password The password
     * @param ?bool   $debug    Optional debug flag
     */
    public function __construct(
        ?string $email,
        ?string $password,
        ?bool $debug = null,
    ) {
        $this->validateAndMapField('email', 'email', $email);
        $this->validateAndMapField('password', 'password', $password);
        $this->validateAndMapField('debug', 'debug', $debug);

        parent::__construct();
    }
}
