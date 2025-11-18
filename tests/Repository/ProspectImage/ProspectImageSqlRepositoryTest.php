<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\ProspectImage;

use Ubix\Repository\ProspectImage\ProspectImageSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\ProspectImage\ProspectImageSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\ProspectImage\ProspectImageSqlRepository
 */
final class ProspectImageSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProspectImageSqlRepository::class);
    }
}
