<?php

declare(strict_types=1);

namespace Ubix\DataType\DateTime;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ubix\DataType\DateTime\AbstractNullableDateTimeDataType as NullableDateTimeDataType;

/**
 * Object for creating and validating date-time strings
 *
 * @see \Ubix\Tests\DataType\DateTime\NullableUbixDateTimeTest PHPUnit test case
 */
class NullableUbixDateTime extends NullableDateTimeDataType
{
    /**
     * Constructor
     *
     * @param DateTimeInterface|string|null $input The input value to validate (Y-m-d H:i:s format)
     *
     * @throws Exception If the input value is not a valid date-time string
     */
    public function __construct(
        DateTimeInterface|string|null $input,
    ) {
        if ($input === null) {
            parent::__construct(null);
        } elseif (is_string($input) === true) {
            parent::__construct(new DateTimeImmutable($input));
        } elseif ($input instanceof DateTimeImmutable) {
            parent::__construct($input);
        } elseif ($input instanceof DateTime) {
            parent::__construct(DateTimeImmutable::createFromMutable($input));
        } else {
            throw new Exception('Invalid DateTime input value provided');
        }
    }
}
