<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\ProspectDocument;

use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\ProspectDocument\ProspectDocumentType
 *
 * @coversDefaultClass \Ubix\Enum\ProspectDocument\ProspectDocumentType
 */
final class ProspectDocumentTypeTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectDocumentType::class);
    }
}
