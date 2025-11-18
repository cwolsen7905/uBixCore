<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating unsigned integers
 *
 * @see \Ubix\Tests\DataType\Int\UnsignedSmallIntTest PHPUnit test case
 */
class UnsignedSmallInt extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: 0, max: 65535)]
        private int $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
