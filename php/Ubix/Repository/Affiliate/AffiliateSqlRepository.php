<?php

declare(strict_types=1);

namespace Ubix\Repository\Affiliate;

use BackedEnum;
use DateTime;
use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use UnitEnum;
use Ubix\DataTransferObject\SqlRepository\AffiliateOptions;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum;
use Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\DetailsId;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\Int\ReferrerId;
use Ubix\DataType\Int\SalespersonId;
use Ubix\DataType\Int\TinyInt;
use Ubix\DataType\Int\UnsignedInt;
use Ubix\DataType\Int\UnsignedMediumInt;
use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\DataType\Int\UnsignedTinyInt;
use Ubix\DataType\String\Char;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateHowDidYouFindUs;
use Ubix\Enum\Affiliate\AffiliateIsHouse;
use Ubix\Enum\Affiliate\AffiliateReferrerStatus;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Affiliate;
use Ubix\Model\PlatformUser;
use Ubix\Repository\Affiliate\AffiliateReaderInterface as AffiliateReader;
use Ubix\Repository\Affiliate\AffiliateWriterInterface as AffiliateWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class AffiliateSqlRepository
 *
 * Implements methods to read affiliate-related data from the database.
 *
 * @see \Ubix\Tests\Repository\Affiliate\AffiliateSqlRepositoryTest PHPUnit test case
 */
final class AffiliateSqlRepository implements AffiliateReader, AffiliateWriter
{
    private const VSCASH_AFFILIATES_TABLE_COLUMNS = [
        'accountNickname'             => 'account_nickname',
        'activationCode'              => 'activation_code',
        'alertMsg'                    => 'alert_msg',
        'apiStatus'                   => 'api_status',
        'b2bTracker'                  => 'b2b_tracker',
        'dateOnHoldSince'             => 'on_hold_since',
        'dateTimeAlertMsgDismissedAt' => 'alert_msg_dismissed_at',
        'dateTimeCreated'             => 'date_created',
        'dateTimeLastLogin'           => 'date_last_login',
        'dateTimeLastUpdated'         => 'last_updated',
        'defaultCode'                 => 'default_code',
        'detailsId'                   => 'details_id',
        'holdReasonId'                => 'hold_reason_id',
        'howDidYouFindUs'             => 'how_did_you_find_us',
        'isHouse'                     => 'is_house',
        'lastFirstMoneyOver150'       => 'last_first_money_over_150',
        'liveVideoAds'                => 'live_video_ads',
        'mergedTo'                    => 'merged_to',
        'onHold'                      => 'on_hold',
        'parentId'                    => 'parent_id',
        'password'                    => 'password',
        'referrerId'                  => 'referrer_id',
        'referrerStatus'              => 'referrer_status',
        'salespersonId'               => 'salesperson_id',
        'salt'                        => 'salt',
        'siteType'                    => 'site_type',
        'status'                      => 'status',
        'testString'                  => 'test_string',
        'user2faSecret'               => '2fa_secret',
        'username'                    => 'username',
        'watchList'                   => 'watch_list',
    ];

    /**
     * AffiliateSqlRepository constructor.
     *
     * @param Logger      $logger      The logger interface
     * @param SqlService  $sqlService  The SQL service for database interactions
     * @param SimpleCache $simpleCache The caching service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
        private SimpleCache $simpleCache,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If there is an error during the save operation
     */
    public function save(Affiliate $affiliate): void
    {
        $sql = $affiliate->getId() !== null && $affiliate->getId()->value > 0 ? 'UPDATE VSCASH.Affiliates
				SET ' : 'INSERT INTO VSCASH.Affiliates
                SET ';

        /**
         * @var array<string, bool|float|int|string|null> $parameters
         */
        $parameters = [];

        foreach ($affiliate->getChangedProperties() as $property) {
            if ($affiliate->{'get' . ucfirst($property)}() === null) {
                continue;
            }

            if (array_key_exists($property, self::VSCASH_AFFILIATES_TABLE_COLUMNS)) {
                $sql .= self::VSCASH_AFFILIATES_TABLE_COLUMNS[$property] . ' = :' . $property . ', ';

                if (is_object($affiliate->{'get' . ucfirst($property)}())) {
                    if (property_exists($affiliate->{'get' . ucfirst($property)}(), 'value') === false) {
                        throw new Exception('Property ' . $property . ' does not exist on ' . get_class($affiliate->{'get' . ucfirst($property)}()));
                    }
                    $dataObjectValue = $affiliate->{'get' . ucfirst($property)}()->value;

                    if ($dataObjectValue instanceof DateTimeInterface) {
                        if (str_starts_with($property, 'dateTime')) {
                            $parameters[$property] = $dataObjectValue->format('Y-m-d H:i:s');
                        } elseif (str_starts_with($property, 'date')) {
                            $parameters[$property] = $dataObjectValue->format('Y-m-d');
                        } else {
                            // NOT_IMPLEMENTED: Throw exception here as this property is not following the naming convention.
                            $parameters[$property] = $dataObjectValue->format('Y-m-d H:i:s');
                        }
                    } elseif ($dataObjectValue instanceof BackedEnum) {
                        $parameters[$property] = $dataObjectValue->value;
                    } elseif ($dataObjectValue instanceof UnitEnum) {
                        $parameters[$property] = $dataObjectValue->name;
                    } else {
                        $parameters[$property] = $dataObjectValue;
                    }
                } else {
                    $parameters[$property] = $affiliate->{'get' . ucfirst($property)}();
                }
            }
        }

        $sql = rtrim($sql, ', '); // Remove trailing comma and space

        if ($affiliate->getId() !== null && $affiliate->getId()->value > 0) {
            $sql              = 'WHERE id = :id';
            $parameters['id'] = $affiliate->getId()->value;
        }

        // @phpstan-ignore-next-line
        $this->sqlService->query($sql, $parameters);

        if ($affiliate->getId() === null || $affiliate->getId()->value <= 0) {
            $affiliate->setId(new AffiliateId(intval($this->sqlService->lastInsertId())));
        }

        $affiliate->clearChanges();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById(AffiliateId $affiliateId): void
    {
        $sql        = 'DELETE FROM VSCASH.Affiliates WHERE id = :id';
        $parameters = ['id' => $affiliateId->value];
        $this->sqlService->query($sql, $parameters);
    }

   /**
    * {@inheritDoc}
    */
    public function getMediaBuyingCampaignAffiliates(): array
    {
        $memcacheKey = 'mediaBuyingCampaignAffiliates_' . (new DateTime())->format('Y-m-d');

        $mediaBuyingCampaignAffiliates = getenv('IN_DEV') === '0' ? false : $this->simpleCache->get($memcacheKey);

        if (!is_array($mediaBuyingCampaignAffiliates)) {
            $mediaBuyingCampaignAffiliates = [];

            $sql = "SELECT affiliate_id FROM VSCASH.Deals
					WHERE status='active' 
					AND channel_type LIKE 'MB_%'";

            $parameters = [];

            /**
             * @var array{affiliate_id: int} $row
             */
            foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
                $mediaBuyingCampaignAffiliates[] = $row['affiliate_id'];
            }

            $this->simpleCache->set($memcacheKey, $mediaBuyingCampaignAffiliates, 1800); // Cache for 30 minutes
        }

        /**
         * @var array<int> $mediaBuyingCampaignAffiliates
         */
        $mediaBuyingCampaignAffiliates = array_values(array_unique($mediaBuyingCampaignAffiliates));

        // NOT_IMPLEMENTED: Implement actual query logic
        $refAffiliates = $this->query(new AffiliateOptions());

        if (!empty($refAffiliates)) {
            $mediaBuyingCampaignAffiliates = [];

            foreach ($refAffiliates as $affiliate) {
                $mediaBuyingCampaignAffiliates[] = $affiliate->getId() !== null ? $affiliate->getId()->value : 0;
            }
        }

        return $mediaBuyingCampaignAffiliates;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the affiliate is not found
     */
    public function getAffiliateById(AffiliateId $affiliateId): Affiliate
    {
        //  Check existing Memcache key
        //  Mp Code is already a somewhat arbitrary value of 5+ chars, and due to key crc32 this should be enough to spread the keys across the memcache cluster, and I think using the value raw is more efficient than an md5 hash
        $memcacheKey = 'NEPTUNE_AFFILIATE_ID_' . $affiliateId->value;

        $memcacheExpire = 3600;    // 1 Hour as affiliates don't change often
        $affiliate      = getenv('IN_DEV') === '0' ? false : $this->simpleCache->get($memcacheKey);


        //  If no memcache data, do DB lookup
        if (empty($affiliate) || !($affiliate instanceof Affiliate)) {
            $parameters = [];

            $sql = 'SELECT * FROM VSCASH.Affiliates 
            WHERE id = :affiliateId';

            $parameters['affiliateId'] = $affiliateId->value;

            $row = $this->sqlService->getRow($sql, $parameters);

            if (empty($row)) {
                throw new Exception('Affiliate not found for id ' . $affiliateId->value, ExceptionCode::AFFILIATE_NOT_FOUND->value);
            }

            $affiliate = new Affiliate(
                id:                          new AffiliateId((int)$row['id']),
                siteType:                    new AffiliateSiteTypeEnum((string)$row['site_type']),
                mergedTo:                    !empty($row['merged_to']) ? new UnsignedMediumInt((int)$row['merged_to']) : null,
                accountNickname:             !empty($row['account_nickname']) ? new Varchar((string)$row['account_nickname']) : null,
                username:                    new Varchar((string)$row['username']),
                password:                    new Varchar((string)$row['password']),
                salt:                        new Varchar((string)$row['salt']),
                user2faSecret:               new Varchar((string)$row['2fa_secret']),
                defaultCode:                 new Varchar((string)$row['default_code']),
                referrerStatus:              AffiliateReferrerStatus::tryFrom((string)$row['referrer_status']),
                referrerId:                  new ReferrerId((int)$row['referrer_id']),
                salespersonId:               new SalespersonId((int)$row['salesperson_id']),
                alertMsg:                    new Text((string)$row['alert_msg']),
                detailsId:                   new DetailsId((int)$row['details_id']),
                activationCode:              new Varchar((string)$row['activation_code']),
                dateTimeCreated:             !empty($row['date_created']) ? new UbixDateTime((string)$row['date_created']) : null,
                dateTimeLastLogin:           !empty($row['date_last_login']) ? new UbixDateTime((string)$row['date_last_login']) : null,
                dateTimeLastUpdated:         !empty($row['last_updated']) ? new UbixDateTime((string)$row['last_updated']) : null,
                b2bTracker:                  $row['b2b_tracker'] !== null ? new UnsignedSmallInt((int)$row['b2b_tracker']) : null,
                watchList:                   $row['watch_list'] !== null ? new UnsignedTinyInt((int)$row['watch_list']) : null,
                apiStatus:                   $row['api_status'] !== null ? new UnsignedTinyInt((int)$row['api_status']) : null,
                status:                      new AffiliateStatusEnum((string)$row['status']),
                lastFirstMoneyOver150:       $row['last_first_money_over_150'] !== null ? new UnsignedTinyInt((int)$row['last_first_money_over_150']) : null,
                onHold:                      new Char((string)$row['on_hold']),
                holdReasonId:                $row['hold_reason_id'] !== null ? new UnsignedInt((int)$row['hold_reason_id']) : null,
                howDidYouFindUs:             AffiliateHowDidYouFindUs::tryFrom((string)$row['how_did_you_find_us']),
                isHouse:                     AffiliateIsHouse::tryFrom((string)$row['is_house']),
                parentId:                    $row['parent_id'] !== null ? new AffiliateId((int)$row['parent_id']) : null,
                dateOnHoldSince:             !empty($row['on_hold_since']) ? new UbixDateTime((string)$row['on_hold_since']) : null,
                dateTimeAlertMsgDismissedAt: $row['alert_msg_dismissed_at'] !== null ? new UbixDateTime((string)$row['alert_msg_dismissed_at']) : null,
                liveVideoAds:                $row['live_video_ads'] !== null ? new TinyInt((int)$row['live_video_ads']) : null,
            );

            //  30-day expiration because mp codes don't change
            $this->simpleCache->set($memcacheKey, $affiliate, $memcacheExpire);
        }

        $affiliate->clearChanges();

        return $affiliate;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the affiliate is not found
     */
    public function getAffiliateByMpCode(MpCode $mpCode): Affiliate
    {
        //  Check existing Memcache key
        //  Mp Code is already a somewhat arbitrary value of 5+ chars, and due to key crc32 this should be enough to spread the keys across the memcache cluster, and I think using the value raw is more efficient than an md5 hash
        $memcacheKey    = 'NEPTUNE_AFFILIATE_ID_' . $mpCode->value;
        $memcacheExpire = 3600;    // 1 Hour as mp_code doesn't change often
        $affiliate      = getenv('IN_DEV') === '0' ? false : $this->simpleCache->get($memcacheKey);

        //  If no memcache data, do DB lookup
        if (empty($affiliate) || !($affiliate instanceof Affiliate)) {
            $parameters = [];

            $sql = 'SELECT aff.* FROM VSCASH.Affiliates aff
            JOIN VSCASH.Affiliates_Codes ac ON aff.id = ac.affiliate_id
            WHERE ac.mp_code = :mp_code';

            $parameters['mp_code'] = $mpCode->value;

            $row = $this->sqlService->getRow($sql, $parameters);

            if (empty($row)) {
                throw new Exception('Affiliate not found for mp_code ' . $mpCode->value, ExceptionCode::AFFILIATE_NOT_FOUND->value);
            }

            $affiliate = new Affiliate(
                id:                          new AffiliateId((int)$row['id']),
                siteType:                    empty($row['site_type']) ? new AffiliateSiteTypeEnum('adult') : new AffiliateSiteTypeEnum((string)$row['site_type']), // TEMPORARY: Because the db is not strict it's not enforcing enums so I need to override with a default value
                mergedTo:                    !empty($row['merged_to']) ? new UnsignedMediumInt((int)$row['merged_to']) : null,
                accountNickname:             !empty($row['account_nickname']) ? new Varchar((string)$row['account_nickname']) : null,
                username:                    new Varchar((string)$row['username']),
                password:                    new Varchar((string)$row['password']),
                salt:                        new Varchar((string)$row['salt']),
                user2faSecret:               new Varchar((string)$row['2fa_secret']),
                defaultCode:                 new Varchar((string)$row['default_code']),
                referrerStatus:              AffiliateReferrerStatus::tryFrom((string)$row['referrer_status']),
                referrerId:                  new ReferrerId((int)$row['referrer_id']),
                salespersonId:               new SalespersonId((int)$row['salesperson_id']),
                alertMsg:                    new Text((string)$row['alert_msg']),
                detailsId:                   new DetailsId((int)$row['details_id']),
                activationCode:              new Varchar((string)$row['activation_code']),
                dateTimeCreated:             !empty($row['date_created']) ? new UbixDateTime((string)$row['date_created']) : null,
                dateTimeLastLogin:           !empty($row['date_last_login']) ? new UbixDateTime((string)$row['date_last_login']) : null,
                dateTimeLastUpdated:         !empty($row['last_updated']) ? new UbixDateTime((string)$row['last_updated']) : null,
                b2bTracker:                  $row['b2b_tracker'] !== null ? new UnsignedSmallInt((int)$row['b2b_tracker']) : null,
                watchList:                   $row['watch_list'] !== null ? new UnsignedTinyInt((int)$row['watch_list']) : null,
                apiStatus:                   $row['api_status'] !== null ? new UnsignedTinyInt((int)$row['api_status']) : null,
                status:                      new AffiliateStatusEnum((string)$row['status']),
                lastFirstMoneyOver150:       $row['last_first_money_over_150'] !== null ? new UnsignedTinyInt((int)$row['last_first_money_over_150']) : null,
                onHold:                      new Char((string)$row['on_hold']),
                holdReasonId:                $row['hold_reason_id'] !== null ? new UnsignedInt((int)$row['hold_reason_id']) : null,
                howDidYouFindUs:             AffiliateHowDidYouFindUs::tryFrom((string)$row['how_did_you_find_us']),
                isHouse:                     AffiliateIsHouse::tryFrom((string)$row['is_house']),
                parentId:                    $row['parent_id'] !== null ? new AffiliateId((int)$row['parent_id']) : null,
                dateOnHoldSince:             !empty($row['on_hold_since']) ? new UbixDateTime((string)$row['on_hold_since']) : null,
                dateTimeAlertMsgDismissedAt: $row['alert_msg_dismissed_at'] !== null ? new UbixDateTime((string)$row['alert_msg_dismissed_at']) : null,
                liveVideoAds:                $row['live_video_ads'] !== null ? new TinyInt((int)$row['live_video_ads']) : null,
            );

            //  30-day expiration because mp codes don't change
            $this->simpleCache->set($memcacheKey, $affiliate, $memcacheExpire);
        }

        $affiliate->clearChanges();

        return $affiliate;
    }

    /**
     * {@inheritDoc}
     */
    public function loadHouseAccounts(): array
    {
        $houseAccounts = [];

        //
        //  Load internal accounts
        //
        $sql = 'SELECT id as affiliate_id 
                FROM VSCASH.Affiliates
                WHERE is_house = "Y"';

        /**
         * @var array{
         *    affiliate_id: int
         *  } $row
         */
        foreach ($this->sqlService->getRows($sql, []) as $row) {
            $houseAccounts[] = $row['affiliate_id'];
        }

        return $houseAccounts;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserRegistrationMpCode(PlatformUserId $userId): MpCode
    {
        // NOT_IMPLEMENTED: Create RegistrationViewRepository
        $sql        = 'SELECT mp_code FROM flirt4free.Registration_Views WHERE confirm_user_id = :userId';
        $parameters = ['userId' => $userId->value];
        $res        = $this->sqlService->getColumn($sql, $parameters);

        //  Check 1Click
        if (empty($res)) {
            $sql = 'SELECT mp_code FROM flirt4free.Registration_Views_1click WHERE confirm_user_id = :userId';
            $res = $this->sqlService->getColumn($sql, $parameters);
        }

        return new MpCode((string)$res);
    }

    /**
     * {@inheritDoc}
     */
    public function updateUserMpCodeAndBountyPaid(PlatformUser $user): void
    {
        $sql = 'UPDATE ntl_db.optiusers 
                    SET mp_code = :mp_code, 
                        bounty_paid = :bounty_paid 
                    WHERE user_id = :user_id';

        $parameters = [
            'bounty_paid' => $user->getBountyPaid(),
            'mp_code'     => $user->getMpCode(),
            'user_id'     => $user->getUserId(),
        ];

        $this->sqlService->query($sql, $parameters);
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param AffiliateOptions $options DTO of options to generate the query
     *
     * @return array<Affiliate> An array of matching platform user(s)
     */
    private function query(AffiliateOptions $options): array
    {
        if ($options->id === 1) {
            return [new Affiliate(id: new AffiliateId(1))];
        }

        return [];
    }
}
