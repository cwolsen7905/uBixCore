<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Media;

use Ubix\DataTransferObject\Media\PresignedUpload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Media\PresignedUpload
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Media\PresignedUpload
 */
final class PresignedUploadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PresignedUpload::class);
    }
}
