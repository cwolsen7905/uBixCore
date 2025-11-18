<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * Enumeration of SameSite values for a cookie
 *
 * @see \Ubix\Tests\Enum\ServiceTypeTest PHPUnit test case
 */
enum ServiceType: string
{
    case GIRLS = 'girls';
    case GUYS  = 'guys';
    case TRANS = 'trans';
    case ALL   = 'all';
}
