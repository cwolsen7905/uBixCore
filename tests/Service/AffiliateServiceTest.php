<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Ubix\DataTransferObject\MediaBuyingDetails;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\String\MpCode;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Service\AffiliateService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\AffiliateService
 *
 * @coversDefaultClass \Ubix\Service\AffiliateService
 */
final class AffiliateServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private static ?AffiliateService $affiliateService = null;

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateService::class);
    }

    /**
     * Test getMediaBuyingDetails()
     *
     * @return void
     *
     * @covers ::getMediaBuyingDetails
     */
    public function testGetMediaBuyingDetails(): void
    {
        $result = $this->affiliateService()->getMediaBuyingDetails(new AffiliateId(10000001), new MpCode('0000'));
        $this->assertInstanceOf(MediaBuyingDetails::class, $result);
        $this->assertFalse($result->isMediaBuying);

        $result = $this->affiliateService()->getMediaBuyingDetails(new AffiliateId(10000002), new MpCode('a37eb'));
        $this->assertInstanceOf(MediaBuyingDetails::class, $result);
        $this->assertTrue($result->isMediaBuying);
        $this->assertSame('mb_deal_mp', $result->type);

        $result = $this->affiliateService()->getMediaBuyingDetails(new AffiliateId(10013749), new MpCode('0000'));
        $this->assertInstanceOf(MediaBuyingDetails::class, $result);
        $this->assertTrue($result->isMediaBuying);
        $this->assertSame('mb_campaign_aff', $result->type);

        $result = $this->affiliateService()->getMediaBuyingDetails(new AffiliateId(10073924), new MpCode('0000'));
        $this->assertInstanceOf(MediaBuyingDetails::class, $result);
        $this->assertTrue($result->isMediaBuying);
        $this->assertSame('mb_deals', $result->type);
    }

    /**
     * Test isHouseAccount()
     *
     * @return void
     *
     * @covers ::isHouseAccount
     */
    public function testIsHouseAccount(): void
    {
        $result = $this->affiliateService()->isHouseAccount(new AffiliateId(10000001));
        $this->assertTrue($result);

        $result = $this->affiliateService()->isHouseAccount(new AffiliateId(10));
        $this->assertFalse($result);
    }

    /**
     * Test etRateTypeByMpCode()
     *
     * @return void
     *
     * @covers ::getRateTypeByMpCode
     */
    public function testGetRateTypeByMpCode(): void
    {
        $result = $this->affiliateService()->getRateTypeByMpCode(new MpCode('0000'), AffiliateProductType::LIVE);

        $this->assertSame(AffiliateRateType::from('sliding'), $result);
    }

    /**
     * Sets up the test case.
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
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates VALUES (10073924,'adult',NULL,'Test Get Affiliate Plans','1111','F0F0F0','','','','Y',0,999,'',234847,'','2003-07-30 19:20:35','2021-12-07 17:52:19','2022-06-23 16:13:09',0,0,1,'active',1,'N',0,'wordofmouth','Y',0,NULL,NULL,0)");
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates_Codes VALUES (10000001,'0000','','','','0000-00-00 00:00:00','',NULL)");
        $this->insertSeedData("INSERT INTO VSCASH.Deals SET id = 100, name = 'Deal 100', affiliate_id = 10000000,channel_type='MB_DEAL',description='Seed Data - 100',contact_info=''");
        $this->insertSeedData("INSERT INTO VSCASH.Deals SET id = 101, name = 'Deal 101', affiliate_id = 10073924,channel_type='MB_DEAL',description='Seed Data - 101',contact_info='',status='inactive'");
        $this->insertSeedData("INSERT INTO VSCASH.Deal_MP_Codes SET mp_code='a37eb',deal_id=100");
        $this->insertSeedData("INSERT INTO VSCASH.Commission_Plans SET id = 1, name = 'Seed Plan 1', rate_type='sliding', product_type = 'live', description = 'Seed Description'");
        $this->insertSeedData("INSERT INTO VSCASH.Commission_Plans SET id = 2, name = 'Seed Plan 2', rate_type='sliding', product_type = 'live', description = 'Seed Description'");
        $this->insertSeedData("INSERT INTO VSCASH.Commission_Plans_Join SET affiliate_id = 10000001, plan_id = 2, date_start = DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 MINUTE), date_end = '0000-00-00 00:00:00'");
        $this->insertSeedData("INSERT INTO VSCASH.Commission_Plans_Join SET affiliate_id = 10073924, mp_code = 'woot', plan_id = 1, product_type = 'live', date_start = DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 MINUTE), date_end = '0000-00-00 00:00:00'");
        $this->insertSeedData("INSERT INTO VSCASH.Commission_Plans_Join SET affiliate_id = 10073924, mp_code = 'w00t', plan_id = 2, product_type = 'vip', date_start = DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 MINUTE), date_end = '0000-00-00 00:00:00'");
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
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Deals');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Deal_MP_Codes');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Commission_Plans');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Commission_Plans_Join');
    }

    /**
     * Get the AffiliateService instance
     *
     * @return AffiliateService
     */
    private function affiliateService(): AffiliateService
    {
        if (self::$affiliateService === null) {
            $affiliateService = $this->getContainer()->get(AffiliateService::class);
            $this->assertInstanceOf(AffiliateService::class, $affiliateService);
            self::$affiliateService = $affiliateService;
        }
        return self::$affiliateService;
    }
}
