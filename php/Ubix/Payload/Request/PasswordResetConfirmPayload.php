<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\String\Password;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Request payload for POST /auth/password-reset/confirm
 *
 * @see \Ubix\Tests\Payload\Request\PasswordResetConfirmPayloadTest PHPUnit test case
 */
final class PasswordResetConfirmPayload extends Payload implements RequestPayload
{
    public string $token;

    public Password $password;

    public Password $confirmPassword;

    /**
     * Constructor
     *
     * @param ?string $token           The raw reset token from the emailed URL
     * @param ?string $password        The new password
     * @param ?string $confirmPassword The new password, repeated
     */
    public function __construct(
        ?string $token,
        ?string $password,
        ?string $confirmPassword,
    ) {
        $this->validateAndMapField('token', 'token', $token);
        $this->validateAndMapField('password', 'password', $password);
        $this->validateAndMapField('confirmPassword', 'confirmPassword', $confirmPassword);

        parent::__construct();
    }
}
