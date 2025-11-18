<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Email;

use Ubix\Enum\Email\EmailContentType;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Email\EmailContentType
 *
 * @coversDefaultClass \Ubix\Enum\Email\EmailContentType
 */
final class EmailContentTypeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(EmailContentType::class);
    }
}
