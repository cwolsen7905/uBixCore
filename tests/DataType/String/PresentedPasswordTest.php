<?php

declare(strict_types=1);

namespace Ubix\Tests\DataType\String;

use Exception;
use Ubix\DataType\String\PresentedPassword;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataType\String\PresentedPassword
 *
 * @coversDefaultClass \Ubix\DataType\String\PresentedPassword
 */
final class PresentedPasswordTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PresentedPassword::class);
    }

    /**
     * A weak or short password is accepted: it is checked against the hash, not judged
     *
     * @return void
     */
    public function testAWeakPasswordIsAccepted(): void
    {
        $this->assertSame('password123', (new PresentedPassword('password123'))->value);
        $this->assertSame('abc', (new PresentedPassword('abc'))->value);
    }

    /**
     * An empty password is refused
     *
     * @return void
     */
    public function testAnEmptyPasswordIsRefused(): void
    {
        $this->expectException(Exception::class);
        new PresentedPassword('');
    }

    /**
     * An absurdly long password is refused before it reaches a hash function
     *
     * @return void
     */
    public function testAnOverlongPasswordIsRefused(): void
    {
        $this->expectException(Exception::class);
        new PresentedPassword(str_repeat('x', 256));
    }
}
