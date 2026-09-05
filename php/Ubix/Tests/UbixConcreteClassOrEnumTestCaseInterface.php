<?php

declare(strict_types=1);

namespace Ubix\Tests;

/**
 * Interface for a PHPUnit TestCase for use on uBixCore concrete classes or enums
 */
interface UbixConcreteClassOrEnumTestCaseInterface
{
    /**
     * Test that the concrete class or enum follows uBix standards by (hopefully) calling the `testClassOrFollowingUbixStandards` method
     *
     * Example:
     * ```
     * final class ExampleTest extends \Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase implements \Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface
     * {
     *     public function testFollowingUbixStandards(): void
     *     {
     *         $this->testClassFollowingUbixStandards(Example::class);
     *     }
     * }
     * ```
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void;
}
