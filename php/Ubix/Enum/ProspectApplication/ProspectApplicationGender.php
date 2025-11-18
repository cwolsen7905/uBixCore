<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectApplication;

/**
 * Enumeration of prospect application gender options
 *
 * @see \Ubix\Tests\Enum\ProspectApplication\ProspectApplicationGenderTest PHPUnit test case
 */
enum ProspectApplicationGender: string
{
    case FEMALE = 'female';
    case MALE   = 'male';
    case TRANS  = 'trans';
}
