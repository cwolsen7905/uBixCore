<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Exception;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\Model\PlatformUser;
use Ubix\Model\Transaction;
use Ubix\Service\AttributionService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\AttributionService
 *
 * @coversDefaultClass \Ubix\Service\AttributionService
 */
final class AttributionServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private static ?AttributionService $attributionService = null;

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AttributionService::class);
    }

    /**
     * Test getEarliestAccountDetails()
     *
     * @return void
     *
     * @covers ::getEarliestAccountDetails
     */
    public function testGetEarliestAccountDetails(): void
    {
        // Test by e-mail
        $testUser = new PlatformUser(
            email:   'test1@example.com',
            userId:  100,
            sitekey: 'flirt4free',
        );

        $result = $this->attributionService()->getEarliestAccountDetails($testUser);

        assert($result->user instanceof PlatformUser);
        $this->assertSame(200, $result->user->getUserId());


        // Test by Fingerprint
        $testUser = new PlatformUser(
            email:   'fake@example.com',
            userId:  57511396,
            sitekey: 'flirt4free',
        );

        $result = $this->attributionService()->getEarliestAccountDetails($testUser);

        assert($result->user instanceof PlatformUser);
        $this->assertSame(59016886, $result->user->getUserId());

        // Test no match
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No Earliest Account Found');
        $this->attributionService()->getEarliestAccountDetails(new PlatformUser());
    }

    /**
     * Test isTransactionDateEligible()
     *
     * @return void
     *
     * @covers ::isTransactionDateEligible
     */
    public function testIsTransactionDateEligible(): void
    {
        $randomTestDate = '2024-9-29 16:55:14';

        $this->assertFalse($this->attributionService()->isTransactionDateEligible(new Transaction(datetime: (new UbixDateTime($randomTestDate))), new UbixDateTime($randomTestDate)));
        $this->assertFalse($this->attributionService()->isTransactionDateEligible(new Transaction(datetime: (new UbixDateTime('2024-6-29 16:55:14'))), new UbixDateTime($randomTestDate)));
        $this->assertTrue($this->attributionService()->isTransactionDateEligible(new Transaction(datetime: (new UbixDateTime('2024-3-29 16:55:14'))), new UbixDateTime($randomTestDate)));
    }

    /**
     * Set up the test case
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->insertSeedData("INSERT INTO ntl_db.transact set user_id=59016886,amount=10.00,datetime=CURRENT_TIMESTAMP,date_out=UNIX_TIMESTAMP(CURRENT_TIMESTAMP),comments=0,enc_cc_num='000',enc_cc_exp_mo=12,enc_cc_exp_yr=28,enc_DDA='',enc_card_suffix=''");
        $this->insertSeedData("INSERT INTO ntl_db.transact set user_id=200,amount=10.00,datetime=CURRENT_TIMESTAMP,date_out=UNIX_TIMESTAMP(CURRENT_TIMESTAMP),comments=0,enc_cc_num='000',enc_cc_exp_mo=12,enc_cc_exp_yr=28,enc_DDA='',enc_card_suffix=''");
        $this->insertSeedData("INSERT INTO ntl_db.optiusers SET username='test1',salt='salt1',email='test1@example.com',user_id=100,sitekey='flirt4free',timeleft_banked=0,enc_cc_num='0000',enc_cc_exp_mo=12,enc_cc_exp_yr=28,enc_card_suffix='',enc_DDA='',bounty_amount=5.00,date_last_login=CURRENT_TIMESTAMP,spending_group='NEW',spending_group_date=CURRENT_TIMESTAMP,service_counter=1,girls_percent=1,guys_percent=0,trans_percent=0,fetish_percent=0,fetish_counter=0");
        $this->insertSeedData("INSERT INTO ntl_db.optiusers SET username='test2',salt='salt2',email='test1@example.com',user_id=200,sitekey='flirt4free',timeleft_banked=0,enc_cc_num='0000',enc_cc_exp_mo=12,enc_cc_exp_yr=28,enc_card_suffix='',enc_DDA='',bounty_amount=5.00,date_last_login=CURRENT_TIMESTAMP,spending_group='NEW',spending_group_date=CURRENT_TIMESTAMP,service_counter=1,girls_percent=1,guys_percent=0,trans_percent=0,fetish_percent=0,fetish_counter=0");
        $this->insertSeedData("INSERT INTO ntl_db.optiusers SET username='test3',salt='salt1',email='fake@example.com',user_id=57511396,sitekey='flirt4free',timeleft_banked=0,enc_cc_num='0000',enc_cc_exp_mo=12,enc_cc_exp_yr=28,enc_card_suffix='',enc_DDA='',bounty_amount=5.00,date_last_login=CURRENT_TIMESTAMP,spending_group='NEW',spending_group_date=CURRENT_TIMESTAMP,service_counter=1,girls_percent=1,guys_percent=0,trans_percent=0,fetish_percent=0,fetish_counter=0");
        $this->insertSeedData("INSERT INTO ntl_db.optiusers VALUES('MrOlsen',NULL,'salt','','','','','','US','','','fake@example.com','','','','',0,0,NULL,'',0,0,'','Y',0.00,'N','',0.000,'0000','12','28','','','','','',0,0,59016886,'','',0,0,'','flirt4free','','0000-00-00 00:00:00','',1,'N',20.00,'0000-00-00','NEW_USER','2025-10-24',50,1.000,0.000,0.000,10.000,0.00,0.00,0.00,1,1,1,'0000-00-00',0.000,0,NULL,NULL,0,0,0)");
        $this->insertSeedData("INSERT INTO flirt4free.Registration_Views SET mp_code='0000',confirm_user_id=59016886,udf01='',udf02='',udf03='',udf04=''");
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates VALUES (10000001,'adult',NULL,'VSM Default','0000','dad4ultra','','','','Y',0,999,'',234847,'','2003-07-30 19:20:35','2021-12-07 17:52:19','2022-06-23 16:13:09',0,0,1,'active',1,'N',0,'wordofmouth','Y',0,NULL,NULL,0)");
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates_Codes VALUES (10000001,'0000','','','','0000-00-00 00:00:00','',NULL)");
        $this->insertSeedData("INSERT INTO ntl_db.transact SET id = 1001, mp_code = 'vkvk', user_id = 59016886, comments = 'Seed Transaction For 59016886', enc_cc_num = '0000', enc_DDA = '000', enc_cc_exp_mo = 12, enc_cc_exp_yr =28, enc_card_suffix = '0000'");
        $this->insertSeedData("INSERT INTO ntl_db.transact_ex SET transact_id = 1001, user_mp_code = 'wwww'");
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE ntl_db.optiusers');
        $this->insertSeedData('TRUNCATE TABLE flirt4free.Registration_Views');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates_Codes');
        $this->insertSeedData('TRUNCATE TABLE ntl_db.transact');
        $this->insertSeedData('TRUNCATE TABLE ntl_db.transact_ex');
    }

    /**
     * Get the AttributionService instance
     *
     * @return AttributionService
     */
    private function attributionService(): AttributionService
    {
        if (self::$attributionService === null) {
            $attributionService = $this->getContainer()->get(AttributionService::class);
            assert($attributionService instanceof AttributionService);
            self::$attributionService = $attributionService;
        }

        return self::$attributionService;
    }
}
