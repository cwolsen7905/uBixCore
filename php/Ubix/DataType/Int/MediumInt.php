<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating medium integers
 *
 * @see \Ubix\Tests\DataType\Int\MediumIntTest PHPUnit test case
 */
class MediumInt extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: -8388608, max: 8388607)]
        private int $input,
    ) {
        $this->validate();

        parent::__construct($input);
    }
}
