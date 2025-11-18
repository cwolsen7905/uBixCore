<?php

declare(strict_types=1);

namespace Ubix\DataType\DateTime;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ubix\DataType\DateTime\AbstractDateTimeDataType as DateTimeDataType;

/**
 * Object for creating and validating date-time strings
 *
 * @see \Ubix\Tests\DataType\DateTime\DateTimeTest PHPUnit test case
 * @see \Ubix\Tests\DataType\DateTime\UbixDateTimeTest PHPUnit test case
 */
class UbixDateTime extends DateTimeDataType
{
    /**
     * Constructor
     *
     * @param DateTimeInterface|string $input The input value to validate (Y-m-d H:i:s format)
     *
     * @throws Exception If the input value is invalid
     */
    public function __construct(
        DateTimeInterface|string $input,
    ) {
        $this->validate();


        if (is_string($input) === true) {
            // NOT_IMPLEMENTED: Check for zero date and throw exception.
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
