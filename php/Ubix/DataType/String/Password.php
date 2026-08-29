<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating Password strings
 *
 * @see \Ubix\Tests\DataType\String\PasswordTest PHPUnit test case
 */
class Password extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        #[Length(min: 8, max: 255)]
        #[NotBlank]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM)]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
