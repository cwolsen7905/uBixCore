<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * Enumeration of status values
 *
 * @see \Ubix\Tests\Enum\StatusTest PHPUnit test case
 */
enum Status: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
}
