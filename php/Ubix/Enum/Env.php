<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * Enumeration of environment values
 *
 * @see \Ubix\Tests\Enum\EnvTest PHPUnit test case
 */
enum Env: string
{
    case DEV     = 'dev';
    case SANDBOX = 'sandbox';
    case PROD    = 'prod';
    case STAGING = 'staging';
}
