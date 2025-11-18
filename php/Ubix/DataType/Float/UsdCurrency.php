<?php

declare(strict_types=1);

namespace Ubix\DataType\Float;

use Symfony\Component\Validator\Constraints\Regex;
use Ubix\DataType\Float\AbstractFloatDataType as FloatDataType;

/**
 * Object for creating and validating USD currency amounts
 *
 * @see \Ubix\Tests\DataType\Float\UsdCurrencyTest PHPUnit test case
 */
class UsdCurrency extends FloatDataType
{
    /**
     * Constructor
     *
     * @param float $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Regex(
            pattern: '/^-?\d+(?:\.\d{1,2})?$/',
            message: 'Amount must be a positive or negative number with up to 2 decimal places (e.g. 12, 12.3, 12.34)',
        )]
        private float $input,
    ) {
        $this->validate();

        parent::__construct(value: floatval($input));
    }
}
