<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject;

use Ubix\DataTransferObject\S3BlobServiceParameters;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\S3BlobServiceParameters
 *
 * @coversDefaultClass \Ubix\DataTransferObject\S3BlobServiceParameters
 */
final class S3BlobServiceParametersTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(S3BlobServiceParameters::class);
    }
}
