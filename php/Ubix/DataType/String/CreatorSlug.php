<?php

declare(strict_types=1);

namespace Ubix\DataType\String;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Ubix\DataType\String\AbstractStringDataType as StringDataType;

/**
 * Object for creating and validating creator slugs (creator-profile SRS FR-201)
 *
 * @see \Ubix\Tests\DataType\String\CreatorSlugTest PHPUnit test case
 */
class CreatorSlug extends StringDataType
{
    /**
     * Constructor
     *
     * @param string $input The input value
     */
    public function __construct(
        // @phpstan-ignore property.onlyWritten (The promoted $input property carries the validation attributes read via reflection in validate(); the value itself is exposed through the parent DataType)
        #[Length(min: 1, max: 64)]
        #[NotBlank]
        #[Regex(
            pattern: '/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
            message: 'Slug must be lowercase letters, digits, and hyphens, and cannot start or end with a hyphen',
        )]
        private string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
