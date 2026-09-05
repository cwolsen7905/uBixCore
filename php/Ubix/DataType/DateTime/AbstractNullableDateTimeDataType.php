<?php

declare(strict_types=1);

namespace Ubix\DataType\DateTime;

use DateTimeImmutable;
use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract datatype for use.
 */
abstract class AbstractNullableDateTimeDataType extends DataType
{
   /**
    * Constructor
    *
    * @param ?DateTimeImmutable $value The DateTime value for this data type
    */
    public function __construct(
        public readonly ?DateTimeImmutable $value,
    ) {
    }
}
