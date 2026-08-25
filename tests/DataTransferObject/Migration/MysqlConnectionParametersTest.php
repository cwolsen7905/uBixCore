<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Migration;

use Ubix\DataTransferObject\Migration\MysqlConnectionParameters;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\DataTransferObject\Migration\MysqlConnectionParameters
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Migration\MysqlConnectionParameters
 * @coversDefaultClass \Ubis\DataTransferObject\Migration\MysqlConnectionParameters
 */
final class MysqlConnectionParametersTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MysqlConnectionParameters::class);
    }
}
