<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Ubix\DataType\Int\AbstractIntDataType as IntDataType;

/**
 * A money amount in minor units (cents) — never a float (FR-104)
 *
 * @see \Ubix\Tests\DataType\Int\MinorUnitAmountTest PHPUnit test case
 */
class MinorUnitAmount extends IntDataType
{
    /**
     * Constructor
     *
     * @param int $input The amount in minor units, zero or more
     */
    public function __construct(
        // @phpstan-ignore property.onlyWritten (The promoted $input property carries the validation attributes read via reflection in validate(); the value itself is exposed through the parent DataType)
        #[PositiveOrZero]
        private int $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
