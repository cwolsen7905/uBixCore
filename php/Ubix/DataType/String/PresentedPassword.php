<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * A password as presented at sign-in, to be checked against a stored hash
 *
 * Not `Password`. That type enforces length and strength, which is right where
 * a password is *set* (registration, reset, change) and wrong where one is
 * *presented*: a login payload typed with `Password` refuses a correct but
 * weak password before it is ever checked, locking its owner out, and its
 * error tells an attacker the policy. A password set before the policy, or one
 * the strength estimator later rates lower, must still sign in.
 *
 * Only the bounds a hash check needs: present, and not absurdly long.
 *
 * @see \Ubix\Tests\DataType\String\PresentedPasswordTest PHPUnit test case
 */
class PresentedPassword extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore property.onlyWritten (The promoted $input property carries the validation attributes read via reflection in validate(); the value itself is exposed through the parent DataType)
        #[Length(max: 255)]
        #[NotBlank]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
