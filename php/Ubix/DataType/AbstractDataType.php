<?php

declare(strict_types=1);

namespace Ubix\DataType;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

/**
 * Abstract datatype for use.
 */
abstract class AbstractDataType
{
    /**
     * Validate the current object
     *
     * @throws Exception    If validation fails
     *
     * @return void
     */
    protected function validate(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $errors = $validator->validate($this);

        if (count($errors) > 0 && $errors[0] instanceof ConstraintViolation) {
            throw new Exception((string) $errors[0]->getMessage());
        }
    }
}
