<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating unsigned big integers
 *
 * @see \Ubix\Tests\DataType\Int\UnsignedIntTest PHPUnit test case
 * @see \Ubix\Tests\DataType\Int\UnsignedBigIntTest PHPUnit test case
 */
class UnsignedBigInt extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: 0, max: 18446744073709551615)]
        private int $input,
    ) {
        $this->validate();

        parent::__construct($input);
    }
}
