<?php

declare(strict_types=1);

namespace Ubix\Enum\Cookie;

/**
 * Enumeration of SameSite values for a cookie
 *
 * @see \Ubix\Tests\Enum\Cookie\CookieSameSiteTest PHPUnit test case
 */
enum CookieSameSite: string
{
    case LAX    = 'Lax';
    case STRICT = 'Strict';
    case NONE   = 'None';
}
