<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PasswordResetToken;

use Ubix\Repository\PasswordResetToken\PasswordResetTokenSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PasswordResetToken\PasswordResetTokenSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PasswordResetToken\PasswordResetTokenSqlRepository
 */
final class PasswordResetTokenSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PasswordResetTokenSqlRepository::class);
    }
}
