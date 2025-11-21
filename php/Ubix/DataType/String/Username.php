<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating Username strings
 */
class Username extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        #[NotBlank]
        #[Length(min: 3, max: 32)]
        #[Regex(pattern: '/^\S+$/', message: 'Username cannot contain spaces.')]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
