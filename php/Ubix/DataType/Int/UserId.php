<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\Positive;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * Object for creating and validating User ID integers
 *
 * @see \Ubix\Tests\DataType\Int\UserIdTest PHPUnit test case
 */
class UserId extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The input value
     */
    public function __construct(
        #[Positive]
        private int $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
