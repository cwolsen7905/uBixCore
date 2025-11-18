<?php

declare(strict_types=1);

namespace Ubix\Enum\AgeVerification;

/**
 * Enumeration of age verification requirements
 *
 * @see \Ubix\Tests\Enum\AgeVerification\AgeVerificationRequirementTest PHPUnit test case
 */
enum AgeVerificationRequirement
{
    case REQUIRED;
    case NOT_REQUIRED;
    case BLOCKED;
}
