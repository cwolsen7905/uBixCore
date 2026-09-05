<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Vault;

use Ubix\Service\Vault\VaultCredentialResolverService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Vault\VaultCredentialResolverService
 *
 * @coversDefaultClass \Ubix\Service\Vault\VaultCredentialResolverService
 */
final class VaultCredentialResolverServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(VaultCredentialResolverService::class);
    }
}
