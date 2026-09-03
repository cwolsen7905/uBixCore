<?php

declare(strict_types=1);

namespace Ubix\DataType\Bool;

use Symfony\Component\Validator\Constraints\Type;
use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract nullable boolean datatype.
 */
abstract class AbstractNullableBoolDataType extends DataType
{
    /**
     * Constructor
     *
     * @param ?bool $value The boolean value for this data type or null
     */
    public function __construct(
        #[Type('bool')]
        public readonly ?bool $value,
    ) {
        $this->validate();
    }
}
