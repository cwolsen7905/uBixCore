<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Email;

use Ubix\Service\Email\PhpMailFunctionEmailService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Email\PhpMailFunctionEmailService
 *
 * @coversDefaultClass \Ubix\Service\Email\PhpMailFunctionEmailService
 */
final class PhpMailFunctionEmailServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PhpMailFunctionEmailService::class);
    }
}
