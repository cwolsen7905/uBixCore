<?php

declare(strict_types=1);

namespace Ubix\Enum\DuplicateProspect;

/**
 * Enumeration of duplicate prospect statuses
 *
 * @see \Ubix\Tests\Enum\DuplicateProspect\DuplicateProspectStatusTest PHPUnit test case
 */
enum DuplicateProspectStatus: string
{
    case CANCELLED = 'cancelled';
    case PENDING   = 'pending';
}
