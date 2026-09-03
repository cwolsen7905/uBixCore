<?php

declare(strict_types=1);

namespace Ubix\Tests\SlimHandler;

use Ubix\SlimHandler\SlimErrorHandler;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\SlimHandler\SlimErrorHandler
 *
 * @coversDefaultClass \Ubix\SlimHandler\SlimErrorHandler
 */
final class SlimErrorHandlerTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SlimErrorHandler::class);
    }
}
