<?php

declare(strict_types=1);

namespace Ubix\Tests;

/**
 * Interface for a PHPUnit TestCase for use on VSM concrete classes or enums
 */
interface UbixConcreteClassOrEnumTestCaseInterface
{
    /**
     * Test that the VSM concrete class or enum follows VSM standards by (hopefully) calling the `testClassOrFollowingUbixStandards` method
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
