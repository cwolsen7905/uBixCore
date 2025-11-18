<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Git;

use Ubix\Enum\Git\GitFileStatus;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Git\GitFileStatus
 *
 * @coversDefaultClass \Ubix\Enum\Git\GitFileStatus
 */
final class GitFileStatusTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(GitFileStatus::class);
    }
}
