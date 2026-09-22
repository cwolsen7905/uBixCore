<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Realtime;

use Ubix\DataTransferObject\Realtime\SubjectGrant;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Realtime\SubjectGrant
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Realtime\SubjectGrant
 */
final class SubjectGrantTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SubjectGrant::class);
    }
}
