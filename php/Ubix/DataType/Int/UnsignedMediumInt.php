<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating unsigned integers
 *
 * @see \Ubix\Tests\DataType\Int\UnsignedMediumIntTest PHPUnit test case
 */
class UnsignedMediumInt extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: 0, max: 16777215)]
        private int $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
