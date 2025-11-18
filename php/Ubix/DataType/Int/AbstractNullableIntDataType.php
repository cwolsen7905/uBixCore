<?php

declare(strict_types=1);

namespace Ubix\DataType\Int;

use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract datatype for use.
 */
abstract class AbstractNullableIntDataType extends DataType
{
   /**
    * Constructor
    *
    * @param ?int $value The int value for this data type
    */
    public function __construct(
        public readonly ?int $value,
    ) {
    }
}
