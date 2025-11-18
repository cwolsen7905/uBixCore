<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating MP Codes
 *
 * @see \Ubix\Tests\DataType\String\MpCodeTest PHPUnit test case
 */
class MpCode extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore-next-line
        #[Length(
            min:        4,
            max:        5,
            minMessage: 'MP Code must be at least {{ limit }} characters long',
            maxMessage: 'MP Code cannot be longer than {{ limit }} characters',
        )]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
