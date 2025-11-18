<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Blob;

use Ubix\Service\Blob\FilestackBlobService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Blob\FilestackBlobService
 *
 * @coversDefaultClass \Ubix\Service\Blob\FilestackBlobService
 */
final class FilestackBlobServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FilestackBlobService::class);
    }
}
