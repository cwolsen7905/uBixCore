<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectApplication;

/**
 * Enumeration of prospect application section statuses
 *
 * @see \Ubix\Tests\Enum\ProspectApplication\ProspectApplicationSectionStatusTest PHPUnit test case
 */
enum ProspectApplicationSectionStatus
{
    case APPROVED;
    case PENDING;
    case REJECTED;
}
