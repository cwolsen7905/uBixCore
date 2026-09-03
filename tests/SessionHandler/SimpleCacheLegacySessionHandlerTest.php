<?php

declare(strict_types=1);

namespace Ubix\Tests\SessionHandler;

use Ubix\SessionHandler\SimpleCacheLegacySessionHandler;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\SessionHandler\SimpleCacheLegacySessionHandler
 *
 * @coversDefaultClass \Ubix\SessionHandler\SimpleCacheLegacySessionHandler
 */
final class SimpleCacheLegacySessionHandlerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SimpleCacheLegacySessionHandler::class);
    }
}
