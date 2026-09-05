<?php

declare(strict_types=1);

namespace Ubix\Tests\Tests;

use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for the family detection in \Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase
 *
 * Proves that house rules are chosen by the family segment after the vendor (and
 * optional product) root, so host projects such as `Acme\Product\...` get the
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
        $this->assertTrue($this->isInFamily('Acme\\Controller\\HealthController', 'Controller'));
        $this->assertTrue($this->isInFamily('Acme\\Product\\Controller\\Api\\CreatorController', 'Controller'));
        $this->assertTrue($this->isInFamily('Acme\\Shared\\DataType\\Int\\PostId', 'DataType'));
        $this->assertTrue($this->isInFamily('Ubix\\Service\\Sql\\MysqlPdoSqlService', 'Service\\Sql'));
        $this->assertTrue($this->isInFamily('Acme\\Product\\Service\\Sql\\FooSqlService', 'Service\\Sql'));
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
        $this->assertFalse($this->isInFamily('Acme\\Product\\Post', 'Model'));
    }
}
