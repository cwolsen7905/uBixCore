<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\String\Email;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Request payload for POST /auth/password-reset/request
 *
 * @see \Ubix\Tests\Payload\Request\PasswordResetRequestPayloadTest PHPUnit test case
 */
final class PasswordResetRequestPayload extends Payload implements RequestPayload
{
    public Email $email;

    /**
     * Constructor
     *
     * @param ?string $email The account email address
     */
    public function __construct(
        ?string $email,
    ) {
        $this->validateAndMapField('email', 'email', $email);

        parent::__construct();
    }
}
