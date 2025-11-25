<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating FirstName strings
 */
class FirstName extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        #[NotBlank]
        #[Length(min: 1, max: 100)]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
