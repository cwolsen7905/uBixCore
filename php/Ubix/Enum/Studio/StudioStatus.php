<?php

declare(strict_types=1);

namespace Ubix\Enum\Studio;

/**
 * Enumeration of studio statuses
 *
 * @see \Ubix\Tests\Enum\Studio\StudioStatusTest PHPUnit test case
 */
enum StudioStatus: string
{
    case ENABLED        = 'Y';
    case DISABLED       = 'N';
    case SOMETHING_ELSE = 'D'; // TEMPORARY: ANDREW:: figure out what "D" stands for (`SELECT DISTINCT status FROM ntl_db.studios` = Y, N, D)
}
