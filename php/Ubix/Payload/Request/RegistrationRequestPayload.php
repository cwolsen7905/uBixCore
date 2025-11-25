<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\String\Email;
use Ubix\DataType\String\FirstName;
use Ubix\DataType\String\LastName;
use Ubix\DataType\String\Password;
use Ubix\DataType\String\DisplayName;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Payload object for user registration request parameters
 */
final class RegistrationRequestPayload extends Payload implements RequestPayload
{
    public DisplayName $displayName;

    public FirstName $firstName;

    public LastName $lastName;

    public Email $email;

    public Password $password;

    public Password $confirmPassword;

    /**
     * Constructor
     *
     * @param ?string $displayName        The display name
     * @param ?string $firstName       The first name
     * @param ?string $lastName        The last name
     * @param ?string $email           The email address
     * @param ?string $password        The password
     * @param ?string $confirmPassword The password confirmation
     */
    public function __construct(
        ?string $displayName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password,
        ?string $confirmPassword,
    ) {

        $this->validateAndMapField('displayName', 'displayName', $displayName);
        $this->validateAndMapField('firstName', 'firstName', $firstName);
        $this->validateAndMapField('lastName', 'lastName', $lastName);
        $this->validateAndMapField('email', 'email', $email);
        $this->validateAndMapField('password', 'password', $password);
        $this->validateAndMapField('confirmPassword', 'confirmPassword', $confirmPassword);
        parent::__construct();
    }
}
