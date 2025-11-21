<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating Password strings
 */
class Password extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        #[NotBlank]
        #[Length(min: 8, max: 255)]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM)]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
