<?php

declare(strict_types=1);

namespace Ubix\DataType\Bool;

use Symfony\Component\Validator\Constraints\Type;
use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract boolean datatype.
 */
abstract class AbstractBoolDataType extends DataType
{
    /**
     * Constructor
     *
     * @param bool $value The boolean value for this data type
     */
    public function __construct(
        #[Type('bool')]
        public readonly bool $value,
    ) {
        $this->validate();
    }
}
