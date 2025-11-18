<?php

declare(strict_types=1);

namespace Ubix\Enum\Prospect;

/**
 * Enumeration of prospect statuses
 *
 * @see \Ubix\Tests\Enum\Prospect\ProspectStatusTest PHPUnit test case
 */
enum ProspectStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 0;
}
