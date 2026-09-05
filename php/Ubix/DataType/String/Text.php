<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating Text strings
 *
 * @see \Ubix\Tests\DataType\String\TextTest PHPUnit test case
 */
class Text extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore property.onlyWritten (The promoted $input property carries the validation attributes read via reflection in validate(); the value itself is exposed through the parent DataType)
        #[Length(min: 0, max: 4294967295)]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
