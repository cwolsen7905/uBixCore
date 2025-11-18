<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectApplication;

/**
 * Enumeration of prospect application contact method options
 *
 * @see \Ubix\Tests\Enum\ProspectApplication\ProspectApplicationContactMethodTest PHPUnit test case
 */
enum ProspectApplicationContactMethod: string
{
    case EMAIL     = 'email';
    case PHONE     = 'phone';
    case SKYPE     = 'skype';
    case TEST      = 'text';
    case WHATS_APP = 'whatsapp';
}
