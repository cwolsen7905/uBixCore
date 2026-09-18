<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Media;

use Ubix\DataTransferObject\Media\SignedUrl;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Media\SignedUrl
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Media\SignedUrl
 */
final class SignedUrlTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SignedUrl::class);
    }
}
