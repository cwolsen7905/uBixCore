<?php

declare(strict_types=1);

namespace Ubix\Enum\Creator;

/**
 * The lifecycle status of a creator page
 *
 * @see \Ubix\Tests\Enum\Creator\CreatorStatusTest PHPUnit test case
 */
enum CreatorStatus: string
{
    case DRAFT     = 'draft';
    case ACTIVE    = 'active';
    case SUSPENDED = 'suspended';
}
