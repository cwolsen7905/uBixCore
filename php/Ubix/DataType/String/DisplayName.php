<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating DisplayName strings
 *
 * @see \Ubix\Tests\DataType\String\DisplayNameTest PHPUnit test case
 */
class DisplayName extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore property.onlyWritten (The promoted $input property carries the validation attributes read via reflection in validate(); the value itself is exposed through the parent DataType)
        #[Length(min: 1, max: 128)]
        #[NotBlank]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
