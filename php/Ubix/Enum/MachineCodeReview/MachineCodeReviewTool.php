<?php

declare(strict_types=1);

namespace Ubix\Enum\MachineCodeReview;

/**
 * Enumeration of machine code review tools
 *
 * @see \Ubix\Tests\Enum\MachineCodeReview\MachineCodeReviewToolTest PHPUnit test case
 */
enum MachineCodeReviewTool
{
    case PHPCS;
    case PHPSTAN;
    case PHPUNIT;
}
