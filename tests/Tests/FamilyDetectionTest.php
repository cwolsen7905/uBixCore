<?php

declare(strict_types=1);

namespace Ubix\Tests\Tests;

use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for the family detection in \Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase
 *
 * Proves that house rules are chosen by the family segment after the vendor (and
 * optional product) root, so host projects such as `Kitg\SowingMe\...` get the
 * same treatment as `Ubix\...`.
 */
final class FamilyDetectionTest extends UbixConcreteClassOrEnumTestCase
{
    /**
     * Framework, host, and host-with-product names all resolve to their family
     *
     * @return void
     */
    public function testFamilyIsFoundAfterVendorOrProductRoot(): void
    {
        $this->assertTrue($this->isInFamily('Ubix\\Controller\\AbstractController', 'Controller'));
        $this->assertTrue($this->isInFamily('Kitg\\Controller\\HealthController', 'Controller'));
        $this->assertTrue($this->isInFamily('Kitg\\SowingMe\\Controller\\Api\\CreatorController', 'Controller'));
        $this->assertTrue($this->isInFamily('Kitg\\Shared\\DataType\\Int\\PostId', 'DataType'));
        $this->assertTrue($this->isInFamily('Ubix\\Service\\Sql\\MysqlPdoSqlService', 'Service\\Sql'));
        $this->assertTrue($this->isInFamily('Kitg\\SowingMe\\Service\\Sql\\FooSqlService', 'Service\\Sql'));
        $this->assertTrue($this->isInFamily('Ubix\\Console\\Command\\Code\\ReviewCommand', 'Console\\Command'));
    }

    /**
     * The first family segment wins, and non-family segments never match
     *
     * @return void
     */
    public function testFirstFamilySegmentWins(): void
    {
        $this->assertTrue($this->isInFamily('Ubix\\Enum\\Exception\\ExceptionCode', 'Enum'));
        $this->assertFalse($this->isInFamily('Ubix\\Enum\\Exception\\ExceptionCode', 'Exception'));
        $this->assertFalse($this->isInFamily('Ubix\\Service\\Migration\\SchemaDiffService', 'Service\\Sql'));
        $this->assertFalse($this->isInFamily('Ubix\\DataTransferObject\\SqlRepository\\UserOptions', 'Repository'));
        $this->assertFalse($this->isInFamily('Controller', 'Controller')); // A vendor root alone is never a family
        $this->assertFalse($this->isInFamily('Kitg\\SowingMe\\Post', 'Model'));
    }
}
