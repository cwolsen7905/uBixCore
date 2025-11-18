<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectApplication;

/**
 * Enumeration of prospect application identification method options
 *
 * @see \Ubix\Tests\Enum\ProspectApplication\ProspectApplicationIdentificationMethodTest PHPUnit test case
 */
enum ProspectApplicationIdentificationMethod: string
{
    case DRIVERS_LICENSE = 'Drivers License';
    case PASSPORT        = 'Passport';
    case STATE_ID        = 'State ID';
}
