<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Sql;

use Ubix\Service\Sql\SqlitePdoSqlService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Sql\SqlitePdoSqlService
 *
 * @coversDefaultClass \Ubix\Service\Sql\SqlitePdoSqlService
 */
final class SqlitePdoSqlServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SqlitePdoSqlService::class);
    }
}
