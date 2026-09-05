<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract datatype for use.
 */
abstract class AbstractStringDataType extends DataType
{
    /**
     * Constructor
     *
     * @param string $value The string value for this data type
     */
    public function __construct(
        public readonly string $value,
    ) {
    }
}
