<?php

declare(strict_types=1);

namespace Ubix\Enum\Broadcaster;

/**
 * Enumeration of broadcaster statuses
 *
 * @see \Ubix\Tests\Enum\Broadcaster\BroadcasterStatusTest PHPUnit test case
 */
enum BroadcasterStatus: int
{
    case ENABLED  = 1;
    case DISABLED = 0;
}
