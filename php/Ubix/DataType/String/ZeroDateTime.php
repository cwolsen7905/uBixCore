<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Regex;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating date-time strings
 *
 * @see \Ubix\Tests\DataType\String\DateTimeStringTest PHPUnit test case
 * @see \Ubix\Tests\DataType\String\ZeroDateTimeTest PHPUnit test case
 */
class ZeroDateTime extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value to validate (Y-m-d H:i:s format)
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Regex(
            pattern: '/^(0000-00-00( 00:00:00)?|\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?)$/',
            message: 'Date must be YYYY-MM-DD, YYYY-MM-DD HH:MM:SS, or zero date',
        )]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
