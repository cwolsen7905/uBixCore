<?php

declare(strict_types=1);

namespace Ubix\Enum\Message;

/**
 * Enumeration of folder ID's for messages
 *
 * @see \Ubix\Tests\Enum\Message\MessageFolderIdTest PHPUnit test case
 */
enum MessageFolderId: int
{
    case INBOX         = 1;
    case SEND_MESSAGES = 2;
    case ARCHIVE       = 3;
    case TRASH         = 4;
}
