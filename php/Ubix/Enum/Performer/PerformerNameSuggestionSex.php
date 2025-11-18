<?php

declare(strict_types=1);

namespace Ubix\Enum\Performer;

/**
 * Enumeration of performer name suggestion services
 *
 * @see \Ubix\Tests\Enum\Performer\PerformerNameSuggestionSexTest PHPUnit test case
 */
enum PerformerNameSuggestionSex: string
{
    case EITHER = 'E';
    case FEMALE = 'F';
    case MALE   = 'M';
}
