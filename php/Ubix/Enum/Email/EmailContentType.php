<?php

declare(strict_types=1);

namespace Ubix\Enum\Email;

/**
 * Enumeration of email content types
 *
 * @see \Ubix\Tests\Enum\Email\EmailContentTypeTest PHPUnit test case
 */
enum EmailContentType: string
{
    case HTML       = 'text/html';
    case PLAIN_TEXT = 'text/plain';
}
