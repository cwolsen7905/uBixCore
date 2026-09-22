<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Identity;

use Ubix\Enum\Identity\IdentityProvider;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Identity\IdentityProvider
 *
 * @coversDefaultClass \Ubix\Enum\Identity\IdentityProvider
 */
final class IdentityProviderTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(IdentityProvider::class);
    }

    /**
     * Test that the stored values never change: hosts persist them beside the subject id
     *
     * @return void
     */
    public function testBackingValuesAreStable(): void
    {
        $values = [];
        foreach (IdentityProvider::cases() as $provider) {
            $values[] = $provider->value;
        }

        $this->assertSame(['apple', 'facebook', 'google'], $values);
    }
}
