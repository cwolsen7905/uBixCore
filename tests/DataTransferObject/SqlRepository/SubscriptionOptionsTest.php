<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\SqlRepository\SubscriptionOptions;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\SqlRepository\SubscriptionOptions
 *
 * @coversDefaultClass \Ubix\DataTransferObject\SqlRepository\SubscriptionOptions
 */
final class SubscriptionOptionsTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SubscriptionOptions::class);
    }
}
