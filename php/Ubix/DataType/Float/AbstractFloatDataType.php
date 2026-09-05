<?php

declare(strict_types=1);

namespace Ubix\DataType\Float;

use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract datatype for use.
 */
abstract class AbstractFloatDataType extends DataType
{
   /**
    * Constructor
    *
    * @param float $value The float value for this data type
    */
    public function __construct(
        public readonly float $value,
    ) {
    }
}
