<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating small integers
 *
 * @see \Ubix\Tests\DataType\Int\SmallIntTest PHPUnit test case
 */
class SmallInt extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: -32768, max: 32767)]
        private int $input,
    ) {
        $this->validate();

        parent::__construct($input);
    }
}
