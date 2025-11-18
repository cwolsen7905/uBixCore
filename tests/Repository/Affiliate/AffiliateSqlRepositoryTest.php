<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\Affiliate;

use Exception;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum;
use Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\DetailsId;
use Ubix\DataType\Int\ReferrerId;
use Ubix\DataType\Int\SalespersonId;
use Ubix\DataType\Int\TinyInt;
use Ubix\DataType\Int\UnsignedInt;
use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\DataType\Int\UnsignedTinyInt;
use Ubix\DataType\String\Char;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateHowDidYouFindUs;
use Ubix\Enum\Affiliate\AffiliateIsHouse;
use Ubix\Enum\Affiliate\AffiliateReferrerStatus;
use Ubix\Model\Affiliate;
use Ubix\Repository\Affiliate\AffiliateSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\Affiliate\AffiliateSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\Affiliate\AffiliateSqlRepository
 */
final class AffiliateSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private static ?AffiliateSqlRepository $affiliateReader = null;

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateSqlRepository::class);
    }

        /**
         * Test getLogByPlatformUserId()
         *
         * @return void
         *
         * @covers ::getLogByPlatformUserId
         */
    public function testGetAffiliateByMpCode(): void
    {
        $result = $this->affiliateReader()->getAffiliateByMpCode(new MpCode('0000'));

        $testAffiliate = new Affiliate(
            id:                          new AffiliateId(10000001),
            siteType:                    new AffiliateSiteTypeEnum('adult'),
            mergedTo:                    null,
            accountNickname:             new Varchar('VSM Default'),
            username:                    new Varchar('0000'),
            password:                    new Varchar('dad4ultra'),
            salt:                        new Varchar(''),
            user2faSecret:               new Varchar(''),
            defaultCode:                 new Varchar(''),
            referrerStatus:              AffiliateReferrerStatus::YES,
            referrerId:                  new ReferrerId(0),
            salespersonId:               new SalespersonId(999),
            alertMsg:                    new Text(''),
            dateTimeAlertMsgDismissedAt: null,
            detailsId:                   new DetailsId(234847),
            activationCode:              new Varchar(''),
            dateTimeCreated:             new UbixDateTime('2003-07-30 19:20:35'),
            dateTimeLastLogin:           new UbixDateTime('2021-12-07 17:52:19'),
            dateTimeLastUpdated:         new UbixDateTime('2022-06-23 12:13:09'),
            b2bTracker:                  new UnsignedSmallInt(0),
            watchList:                   new UnsignedTinyInt(0),
            apiStatus:                   new UnsignedTinyInt(1),
            status:                      new AffiliateStatusEnum('active'),
            lastFirstMoneyOver150:       new UnsignedTinyInt(1),
            onHold:                      new Char('N'),
            holdReasonId:                new UnsignedInt(0),
            howDidYouFindUs:             AffiliateHowDidYouFindUs::WORD_OF_MOUTH,
            isHouse:                     AffiliateIsHouse::YES,
            parentId:                    new AffiliateId(0),
            dateOnHoldSince:             null,
            liveVideoAds:                new TinyInt(0),
        );

        $testAffiliate->clearChanges();

        $this->assertEquals($testAffiliate, $result); // phpcs:ignore Ubix.UbixCore.DisallowAssertEquals.Found

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Affiliate not found for mp_code 0b0b');
        $this->affiliateReader()->getAffiliateByMpCode(new MpCode('0b0b'));
    }

    /**
     * Test save()
     *
     * @return void
     */
    public function testSave(): void
    {
        $testAffiliate = new Affiliate(
            siteType:                    new AffiliateSiteTypeEnum('adult'),
            mergedTo:                    null,
            accountNickname:             new Varchar('VSM Seed Affiliate ' . random_int(1000, 9999)),
            username:                    new Varchar('SEED' . random_int(1000, 9999)),
            password:                    new Varchar('NO_PASSWORD'),
            salt:                        new Varchar(''),
            user2faSecret:               new Varchar(''),
            defaultCode:                 new Varchar(''),
            referrerStatus:              AffiliateReferrerStatus::YES,
            referrerId:                  new ReferrerId(0),
            salespersonId:               new SalespersonId(999),
            alertMsg:                    new Text(''),
            dateTimeAlertMsgDismissedAt: null,
            detailsId:                   new DetailsId(234847),
            activationCode:              new Varchar(''),
            dateTimeCreated:             new UbixDateTime('2003-07-30 19:20:35'),
            dateTimeLastLogin:           new UbixDateTime('2021-12-07 17:52:19'),
            dateTimeLastUpdated:         new UbixDateTime('2022-06-23 12:13:09'),
            b2bTracker:                  new UnsignedSmallInt(0),
            watchList:                   new UnsignedTinyInt(0),
            apiStatus:                   new UnsignedTinyInt(1),
            status:                      new AffiliateStatusEnum('active'),
            lastFirstMoneyOver150:       new UnsignedTinyInt(1),
            onHold:                      new Char('N'),
            holdReasonId:                new UnsignedInt(0),
            howDidYouFindUs:             AffiliateHowDidYouFindUs::WORD_OF_MOUTH,
            isHouse:                     AffiliateIsHouse::YES,
            parentId:                    new AffiliateId(0),
            dateOnHoldSince:             null,
            liveVideoAds:                new TinyInt(0),
        );

        $this->affiliateReader()->save($testAffiliate);

        $this->assertNotNull($testAffiliate->getId());

        $this->assertGreaterThan(0, $testAffiliate->getId()->value);

        $compareAffiliate = $this->affiliateReader()->getAffiliateById($testAffiliate->getId());

        $this->assertEquals($testAffiliate, $compareAffiliate); // phpcs:ignore Ubix.UbixCore.DisallowAssertEquals.Found

        $this->assertNotNull($testAffiliate->getId());

        $this->affiliateReader()->deleteById($testAffiliate->getId());
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
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates VALUES (10000001,'adult',NULL,'VSM Default','0000','dad4ultra','','','','Y',0,999,'',234847,'','2003-07-30 19:20:35','2021-12-07 17:52:19','2022-06-23 12:13:09',0,0,1,'active',1,'N',0,'wordofmouth','Y',0,NULL,NULL,0)");
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates_Codes VALUES (10000001,'0000','','','','0000-00-00 00:00:00','',NULL)");
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE ntl_db.transact');
        $this->insertSeedData('TRUNCATE TABLE ntl_db.optiusers');
        $this->insertSeedData('TRUNCATE TABLE flirt4free.Registration_Views');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates_Codes');
    }

    /**
     * Get the AffiliateSqlRepository instance
     *
     * @return AffiliateSqlRepository
     */
    private function affiliateReader(): AffiliateSqlRepository
    {
        if (self::$affiliateReader === null) {
            $affiliateReader = $this->getContainer()->get(AffiliateSqlRepository::class);
            $this->assertInstanceOf(AffiliateSqlRepository::class, $affiliateReader);
            self::$affiliateReader = $affiliateReader;
        }
        return self::$affiliateReader;
    }
}
