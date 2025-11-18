<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\AttributionLog;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\AttributionLogId;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Model\AttributionLog;
use Ubix\Repository\AttributionLog\AttributionLogSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\AttributionLog\AttributionLogSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\AttributionLog\AttributionLogSqlRepository
 */
final class AttributionLogSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private static ?AttributionLogSqlRepository $attributionLogReader = null;

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AttributionLogSqlRepository::class);
    }

    /**
     * Test getLogByPlatformUserId()
     *
     * @return void
     *
     * @covers ::getLogByPlatformUserId
     */
    public function testGetLogByPlatformUserId(): void
    {
        $result = $this->attributionLogReader()->getLogByPlatformUserId(new PlatformUserId(68974841));

        $compareLog = end($result);
        $this->assertInstanceOf(AttributionLog::class, $compareLog);

        $this->assertGreaterThan(0, count($result));

        $testLog = new AttributionLog(
            id:            new AttributionLogId(2),
            userId:        new PlatformUserId(68974841),
            method:        new Varchar(''),
            oldMpCode:     new MpCode('ek4ci'),
            newMpCode:     new MpCode('xxxx'),
            reason:        new Varchar('User Registered'),
            bountyPaid:    new Varchar('N'),
            dateTimeEvent: new UbixDateTime('2025-07-23 00:00:00'),
        );



        $testLog->setId($compareLog->getId());
        $testLog->setDateTimeEvent($compareLog->getDateTimeEvent());
        $testLog->clearChanges();

        $this->assertEquals($testLog, $compareLog); // phpcs:ignore Ubix.UbixCore.DisallowAssertEquals.Found
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE BILLING.Attribution_V2_Log');
    }

    /**
     * Setup before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->insertSeedData("INSERT INTO BILLING.Attribution_V2_Log SET id = 2, user_id = 68974841, method='', old_mp_code='ek4ci', new_mp_code='xxxx', reason='User Registered', bounty_paid='N', event_date='2025-07-23 00:00:00'");
    }

    /**
     * Get the AttributionLogSqlRepository instance
     *
     * @return AttributionLogSqlRepository
     */
    private function attributionLogReader(): AttributionLogSqlRepository
    {
        if (self::$attributionLogReader === null) {
            $attributionLogReader = $this->getContainer()->get(AttributionLogSqlRepository::class);
            $this->assertInstanceOf(AttributionLogSqlRepository::class, $attributionLogReader);
            self::$attributionLogReader = $attributionLogReader;
        }
        return self::$attributionLogReader;
    }
}
