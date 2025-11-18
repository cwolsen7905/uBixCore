<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating integers
 *
 * @see \Ubix\Tests\DataType\Int\IntegerTest PHPUnit test case
 */
class Integer extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: -2147483648, max: 2147483647)]
        private int $input,
    ) {
        $this->validate();

        parent::__construct($input);
    }
}
