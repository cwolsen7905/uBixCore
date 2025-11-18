<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Sql;

use Ubix\Service\Sql\MysqlPdoSqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Sql\MysqlPdoSqlService
 *
 * @coversDefaultClass \Ubix\Service\Sql\MysqlPdoSqlService
 */
final class MysqlPdoSqlServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MysqlPdoSqlService::class);
    }
}
