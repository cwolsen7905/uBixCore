<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating Varchar strings
 *
 * @see \Ubix\Tests\DataType\String\VarcharTest PHPUnit test case
 */
class Varchar extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Length(min: 0, max: 65535)]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
