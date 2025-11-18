<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Blob;

use Ubix\Service\Blob\S3BlobService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Blob\S3BlobService
 *
 * @coversDefaultClass \Ubix\Service\Blob\S3BlobService
 */
final class S3BlobServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(S3BlobService::class);
    }
}
