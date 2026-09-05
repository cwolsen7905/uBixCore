<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * Enumeration of Yes/No values
 *
 * @see \Ubix\Tests\Enum\YesNoTest PHPUnit test case
 */
enum YesNo: string
{
    case Y   = 'Y';
    case N   = 'N';
    case YES = 'yes';
    case NO  = 'no';
}
