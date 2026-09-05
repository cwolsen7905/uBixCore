<?php

declare(strict_types=1);

namespace Ubix\Enum\Git;

/**
 * Enumeration of git file status values
 *
 * @see \Ubix\Tests\Enum\Git\GitFileStatusTest PHPUnit test case
 */
enum GitFileStatus: string
{
    case UNMODIFIED           = ' ';
    case MODIFIED             = 'M';
    case ADDED                = 'A';
    case DELETED              = 'D';
    case RENAMED              = 'R';
    case COPIED               = 'C';
    case UPDATED_BUT_UNMERGED = 'U';
    case UNTRACKED            = '?';
}
