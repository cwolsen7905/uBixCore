<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Range;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating auto-increment integers
 *
 * @see \Ubix\Tests\DataType\Int\AutoIncrementTest PHPUnit test case
 */
class AutoIncrement extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Range(min: 1, max: 4294967295)]
        private int $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
