<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Enum\Affiliate\AffiliateSiteTypeEnum;
use Ubix\DataType\Enum\Affiliate\AffiliateStatusEnum;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\DetailsId;
use Ubix\DataType\Int\ReferrerId;
use Ubix\DataType\Int\SalespersonId;
use Ubix\DataType\Int\TinyInt;
use Ubix\DataType\Int\UnsignedInt;
use Ubix\DataType\Int\UnsignedMediumInt;
use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\DataType\Int\UnsignedTinyInt;
use Ubix\DataType\String\Char;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateHowDidYouFindUs;
use Ubix\Enum\Affiliate\AffiliateIsHouse;
use Ubix\Enum\Affiliate\AffiliateReferrerStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of an Affiliate
 *
 * @see \Ubix\Tests\Model\AffiliateTest PHPUnit test case
 */
final class Affiliate extends Model
{
    /**
     * Constructor
     *
     * @param ?AffiliateId              $id                          The affiliate ID
     * @param ?AffiliateSiteTypeEnum    $siteType                    The site type
     * @param ?UnsignedMediumInt        $mergedTo                    The merged to
     * @param ?Varchar                  $accountNickname             The account nickname
     * @param ?Varchar                  $username                    The username
     * @param ?Varchar                  $password                    The password
     * @param ?Varchar                  $salt                        The salt
     * @param ?Varchar                  $user2faSecret               The 2fa secret
     * @param ?Varchar                  $defaultCode                 The default code
     * @param ?AffiliateReferrerStatus  $referrerStatus              The referrer status
     * @param ?ReferrerId               $referrerId                  The referrer ID
     * @param ?SalespersonId            $salespersonId               The salesperson ID
     * @param ?Text                     $alertMsg                    The alert message
     * @param ?DetailsId                $detailsId                   The details ID
     * @param ?Varchar                  $activationCode              The activation code
     * @param ?UbixDateTime              $dateTimeCreated             The date created
     * @param ?UbixDateTime              $dateTimeLastLogin           The date last login
     * @param ?UbixDateTime              $dateTimeLastUpdated         The last updated
     * @param ?UnsignedSmallInt         $b2bTracker                  The B2B tracker
     * @param ?UnsignedTinyInt          $watchList                   The watch list
     * @param ?UnsignedTinyInt          $apiStatus                   The API status
     * @param ?AffiliateStatusEnum      $status                      The status
     * @param ?UnsignedTinyInt          $lastFirstMoneyOver150       The last first money over 150
     * @param ?Char                     $onHold                      The on hold
     * @param ?UnsignedInt              $holdReasonId                The hold reason ID
     * @param ?AffiliateHowDidYouFindUs $howDidYouFindUs             The how did you find us
     * @param ?AffiliateIsHouse         $isHouse                     The is house
     * @param ?AffiliateId              $parentId                    The parent ID
     * @param ?UbixDateTime              $dateOnHoldSince             The on hold since
     * @param ?UbixDateTime              $dateTimeAlertMsgDismissedAt The alert message dismissed at
     * @param ?TinyInt                  $liveVideoAds                The live video ads
     */
    public function __construct(
        private ?AffiliateId $id = null,
        private ?AffiliateSiteTypeEnum $siteType = null,
        private ?UnsignedMediumInt $mergedTo = null,
        private ?Varchar $accountNickname = null,
        private ?Varchar $username = null,
        private ?Varchar $password = null,
        private ?Varchar $salt = null,
        private ?Varchar $user2faSecret = null,
        private ?Varchar $defaultCode = null,
        private ?AffiliateReferrerStatus $referrerStatus = null,
        private ?ReferrerId $referrerId = null,
        private ?SalespersonId $salespersonId = null,
        private ?Text $alertMsg = null,
        private ?DetailsId $detailsId = null,
        private ?Varchar $activationCode = null,
        private ?UbixDateTime $dateTimeCreated = null,
        private ?UbixDateTime $dateTimeLastLogin = null,
        private ?UbixDateTime $dateTimeLastUpdated = null,
        private ?UnsignedSmallInt $b2bTracker = null,
        private ?UnsignedTinyInt $watchList = null,
        private ?UnsignedTinyInt $apiStatus = null,
        private ?AffiliateStatusEnum $status = null,
        private ?UnsignedTinyInt $lastFirstMoneyOver150 = null,
        private ?Char $onHold = null,
        private ?UnsignedInt $holdReasonId = null,
        private ?AffiliateHowDidYouFindUs $howDidYouFindUs = null,
        private ?AffiliateIsHouse $isHouse = null,
        private ?AffiliateId $parentId = null,
        private ?UbixDateTime $dateOnHoldSince = null,
        private ?UbixDateTime $dateTimeAlertMsgDismissedAt = null,
        private ?TinyInt $liveVideoAds = null,
    ) {
        $this->markAllChanged();
    }

    /**
     * Get the value of affiliateId
     *
     * @return ?AffiliateId The value of affiliateId
     */
    public function getId(): ?AffiliateId
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?AffiliateId $id The value for id
     *
     * @return void
     */
    public function setId(?AffiliateId $id): void
    {
        $this->id = $id;
        $this->markChanged('id');
    }

    /**
     * Get the value of siteType
     *
     * @return ?AffiliateSiteTypeEnum The value of siteType
     */
    public function getSiteType(): ?AffiliateSiteTypeEnum
    {
        return $this->siteType;
    }

    /**
     * Set the value of siteType
     *
     * @param ?AffiliateSiteTypeEnum $siteType The value for siteType
     *
     * @return void
     */
    public function setSiteType(?AffiliateSiteTypeEnum $siteType): void
    {
        $this->siteType = $siteType;
        $this->markChanged('siteType');
    }

    /**
     * Get the value of mergedTo
     *
     * @return ?UnsignedMediumInt The value of mergedTo
     */
    public function getMergedTo(): ?UnsignedMediumInt
    {
        return $this->mergedTo;
    }

    /**
     * Set the value of mergedTo
     *
     * @param ?UnsignedMediumInt $mergedTo The value for mergedTo
     *
     * @return void
     */
    public function setMergedTo(?UnsignedMediumInt $mergedTo): void
    {
        $this->mergedTo = $mergedTo;
        $this->markChanged('mergedTo');
    }

    /**
     * Get the value of accountNickname
     *
     * @return ?Varchar The value of accountNickname
     */
    public function getAccountNickname(): ?Varchar
    {
        return $this->accountNickname;
    }

    /**
     * Set the value of accountNickname
     *
     * @param ?Varchar $accountNickname The value for accountNickname
     *
     * @return void
     */
    public function setAccountNickname(?Varchar $accountNickname): void
    {
        $this->accountNickname = $accountNickname;
        $this->markChanged('accountNickname');
    }

    /**
     * Get the value of username
     *
     * @return ?Varchar The value of username
     */
    public function getUsername(): ?Varchar
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @param ?Varchar $username The value for username
     *
     * @return void
     */
    public function setUsername(?Varchar $username): void
    {
        $this->username = $username;
        $this->markChanged('username');
    }

    /**
     * Get the value of password
     *
     * @return ?Varchar The value of password
     */
    public function getPassword(): ?Varchar
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param ?Varchar $password The value for password
     *
     * @return void
     */
    public function setPassword(?Varchar $password): void
    {
        $this->password = $password;
        $this->markChanged('password');
    }

    /**
     * Get the value of salt
     *
     * @return ?Varchar The value of salt
     */
    public function getSalt(): ?Varchar
    {
        return $this->salt;
    }

    /**
     * Set the value of salt
     *
     * @param ?Varchar $salt The value for salt
     *
     * @return void
     */
    public function setSalt(?Varchar $salt): void
    {
        $this->salt = $salt;
        $this->markChanged('salt');
    }

    /**
     * Get the value of user2faSecret
     *
     * @return ?Varchar The value of user2faSecret
     */
    public function getUser2faSecret(): ?Varchar
    {
        return $this->user2faSecret;
    }

    /**
     * Set the value of user2faSecret
     *
     * @param ?Varchar $user2faSecret The value for user2faSecret
     *
     * @return void
     */
    public function setUser2faSecret(?Varchar $user2faSecret): void
    {
        $this->user2faSecret = $user2faSecret;
        $this->markChanged('user2faSecret');
    }

    /**
     * Get the value of defaultCode
     *
     * @return ?Varchar The value of defaultCode
     */
    public function getDefaultCode(): ?Varchar
    {
        return $this->defaultCode;
    }

    /**
     * Set the value of defaultCode
     *
     * @param ?Varchar $defaultCode The value for defaultCode
     *
     * @return void
     */
    public function setDefaultCode(?Varchar $defaultCode): void
    {
        $this->defaultCode = $defaultCode;
        $this->markChanged('defaultCode');
    }

    /**
     * Get the value of referrerStatus
     *
     * @return ?AffiliateReferrerStatus The value of referrerStatus
     */
    public function getReferrerStatus(): ?AffiliateReferrerStatus
    {
        return $this->referrerStatus;
    }

    /**
     * Set the value of referrerStatus
     *
     * @param ?AffiliateReferrerStatus $referrerStatus The value for referrerStatus
     *
     * @return void
     */
    public function setReferrerStatus(?AffiliateReferrerStatus $referrerStatus): void
    {
        $this->referrerStatus = $referrerStatus;
        $this->markChanged('referrerStatus');
    }

    /**
     * Get the value of referrerId
     *
     * @return ?ReferrerId The value of referrerId
     */
    public function getReferrerId(): ?ReferrerId
    {
        return $this->referrerId;
    }

    /**
     * Set the value of referrerId
     *
     * @param ?ReferrerId $referrerId The value for referrerId
     *
     * @return void
     */
    public function setReferrerId(?ReferrerId $referrerId): void
    {
        $this->referrerId = $referrerId;
        $this->markChanged('referrerId');
    }

    /**
     * Get the value of salespersonId
     *
     * @return ?SalespersonId The value of salespersonId
     */
    public function getSalespersonId(): ?SalespersonId
    {
        return $this->salespersonId;
    }

    /**
     * Set the value of salespersonId
     *
     * @param ?SalespersonId $salespersonId The value for salespersonId
     *
     * @return void
     */
    public function setSalespersonId(?SalespersonId $salespersonId): void
    {
        $this->salespersonId = $salespersonId;
        $this->markChanged('salespersonId');
    }

    /**
     * Get the value of alertMsg
     *
     * @return ?Text The value of alertMsg
     */
    public function getAlertMsg(): ?Text
    {
        return $this->alertMsg;
    }

    /**
     * Set the value of alertMsg
     *
     * @param ?Text $alertMsg The value for alertMsg
     *
     * @return void
     */
    public function setAlertMsg(?Text $alertMsg): void
    {
        $this->alertMsg = $alertMsg;
        $this->markChanged('alertMsg');
    }

    /**
     * Get the value of detailsId
     *
     * @return ?DetailsId The value of detailsId
     */
    public function getDetailsId(): ?DetailsId
    {
        return $this->detailsId;
    }

    /**
     * Set the value of detailsId
     *
     * @param ?DetailsId $detailsId The value for detailsId
     *
     * @return void
     */
    public function setDetailsId(?DetailsId $detailsId): void
    {
        $this->detailsId = $detailsId;
        $this->markChanged('detailsId');
    }

    /**
     * Get the value of activationCode
     *
     * @return ?Varchar The value of activationCode
     */
    public function getActivationCode(): ?Varchar
    {
        return $this->activationCode;
    }

    /**
     * Set the value of activationCode
     *
     * @param ?Varchar $activationCode The value for activationCode
     *
     * @return void
     */
    public function setActivationCode(?Varchar $activationCode): void
    {
        $this->activationCode = $activationCode;
        $this->markChanged('activationCode');
    }

    /**
     * Get the value of dateTimeCreated
     *
     * @return ?UbixDateTime The value of dateTimeCreated
     */
    public function getDateTimeCreated(): ?UbixDateTime
    {
        return $this->dateTimeCreated;
    }

    /**
     * Set the value of dateTimeCreated
     *
     * @param ?UbixDateTime $dateTimeCreated The value for dateTimeCreated
     *
     * @return void
     */
    public function setDateTimeCreated(?UbixDateTime $dateTimeCreated): void
    {
        $this->dateTimeCreated = $dateTimeCreated;
        $this->markChanged('dateTimeCreated');
    }

    /**
     * Get the value of dateTimeLastLogin
     *
     * @return ?UbixDateTime The value of dateTimeLastLogin
     */
    public function getDateTimeLastLogin(): ?UbixDateTime
    {
        return $this->dateTimeLastLogin;
    }

    /**
     * Set the value of dateTimeLastLogin
     *
     * @param ?UbixDateTime $dateTimeLastLogin The value for dateTimeLastLogin
     *
     * @return void
     */
    public function setDateTimeLastLogin(?UbixDateTime $dateTimeLastLogin): void
    {
        $this->dateTimeLastLogin = $dateTimeLastLogin;
        $this->markChanged('dateTimeLastLogin');
    }

    /**
     * Get the value of dateTimeLastUpdated
     *
     * @return ?UbixDateTime The value of dateTimeLastUpdated
     */
    public function getDateTimeLastUpdated(): ?UbixDateTime
    {
        return $this->dateTimeLastUpdated;
    }

    /**
     * Set the value of dateTimeLastUpdated
     *
     * @param ?UbixDateTime $dateTimeLastUpdated The value for dateTimeLastUpdated
     *
     * @return void
     */
    public function setDateTimeLastUpdated(?UbixDateTime $dateTimeLastUpdated): void
    {
        $this->dateTimeLastUpdated = $dateTimeLastUpdated;
        $this->markChanged('dateTimeLastUpdated');
    }

    /**
     * Get the value of b2bTracker
     *
     * @return ?UnsignedSmallInt The value of b2bTracker
     */
    public function getB2bTracker(): ?UnsignedSmallInt
    {
        return $this->b2bTracker;
    }

    /**
     * Set the value of b2bTracker
     *
     * @param ?UnsignedSmallInt $b2bTracker The value for b2bTracker
     *
     * @return void
     */
    public function setB2bTracker(?UnsignedSmallInt $b2bTracker): void
    {
        $this->b2bTracker = $b2bTracker;
        $this->markChanged('b2bTracker');
    }

    /**
     * Get the value of watchList
     *
     * @return ?UnsignedTinyInt The value of watchList
     */
    public function getWatchList(): ?UnsignedTinyInt
    {
        return $this->watchList;
    }

    /**
     * Set the value of watchList
     *
     * @param ?UnsignedTinyInt $watchList The value for watchList
     *
     * @return void
     */
    public function setWatchList(?UnsignedTinyInt $watchList): void
    {
        $this->watchList = $watchList;
        $this->markChanged('watchList');
    }

    /**
     * Get the value of apiStatus
     *
     * @return ?UnsignedTinyInt The value of apiStatus
     */
    public function getApiStatus(): ?UnsignedTinyInt
    {
        return $this->apiStatus;
    }

    /**
     * Set the value of apiStatus
     *
     * @param ?UnsignedTinyInt $apiStatus The value for apiStatus
     *
     * @return void
     */
    public function setApiStatus(?UnsignedTinyInt $apiStatus): void
    {
        $this->apiStatus = $apiStatus;
        $this->markChanged('apiStatus');
    }

    /**
     * Get the value of status
     *
     * @return ?AffiliateStatusEnum The value of status
     */
    public function getStatus(): ?AffiliateStatusEnum
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?AffiliateStatusEnum $status The value for status
     *
     * @return void
     */
    public function setStatus(?AffiliateStatusEnum $status): void
    {
        $this->status = $status;
        $this->markChanged('status');
    }

    /**
     * Get the value of lastFirstMoneyOver150
     *
     * @return ?UnsignedTinyInt The value of lastFirstMoneyOver150
     */
    public function getLastFirstMoneyOver150(): ?UnsignedTinyInt
    {
        return $this->lastFirstMoneyOver150;
    }

    /**
     * Set the value of lastFirstMoneyOver150
     *
     * @param ?UnsignedTinyInt $lastFirstMoneyOver150 The value for lastFirstMoneyOver150
     *
     * @return void
     */
    public function setLastFirstMoneyOver150(?UnsignedTinyInt $lastFirstMoneyOver150): void
    {
        $this->lastFirstMoneyOver150 = $lastFirstMoneyOver150;
        $this->markChanged('lastFirstMoneyOver150');
    }

    /**
     * Get the value of onHold
     *
     * @return ?Char The value of onHold
     */
    public function getOnHold(): ?Char
    {
        return $this->onHold;
    }

    /**
     * Set the value of onHold
     *
     * @param ?Char $onHold The value for onHold
     *
     * @return void
     */
    public function setOnHold(?Char $onHold): void
    {
        $this->onHold = $onHold;
        $this->markChanged('onHold');
    }

    /**
     * Get the value of holdReasonId
     *
     * @return ?UnsignedInt The value of holdReasonId
     */
    public function getHoldReasonId(): ?UnsignedInt
    {
        return $this->holdReasonId;
    }

    /**
     * Set the value of holdReasonId
     *
     * @param ?UnsignedInt $holdReasonId The value for holdReasonId
     *
     * @return void
     */
    public function setHoldReasonId(?UnsignedInt $holdReasonId): void
    {
        $this->holdReasonId = $holdReasonId;
        $this->markChanged('holdReasonId');
    }

    /**
     * Get the value of howDidYouFindUs
     *
     * @return ?AffiliateHowDidYouFindUs The value of howDidYouFindUs
     */
    public function getHowDidYouFindUs(): ?AffiliateHowDidYouFindUs
    {
        return $this->howDidYouFindUs;
    }

    /**
     * Set the value of howDidYouFindUs
     *
     * @param ?AffiliateHowDidYouFindUs $howDidYouFindUs The value for howDidYouFindUs
     *
     * @return void
     */
    public function setHowDidYouFindUs(?AffiliateHowDidYouFindUs $howDidYouFindUs): void
    {
        $this->howDidYouFindUs = $howDidYouFindUs;
        $this->markChanged('howDidYouFindUs');
    }

    /**
     * Get the value of isHouse
     *
     * @return ?AffiliateIsHouse The value of isHouse
     */
    public function getIsHouse(): ?AffiliateIsHouse
    {
        return $this->isHouse;
    }

    /**
     * Set the value of isHouse
     *
     * @param ?AffiliateIsHouse $isHouse The value for isHouse
     *
     * @return void
     */
    public function setIsHouse(?AffiliateIsHouse $isHouse): void
    {
        $this->isHouse = $isHouse;
        $this->markChanged('isHouse');
    }

    /**
     * Get the value of parentId
     *
     * @return ?AffiliateId The value of parentId
     */
    public function getParentId(): ?AffiliateId
    {
        return $this->parentId;
    }

    /**
     * Set the value of parentId
     *
     * @param ?AffiliateId $parentId The value for parentId
     *
     * @return void
     */
    public function setParentId(?AffiliateId $parentId): void
    {
        $this->parentId = $parentId;
        $this->markChanged('parentId');
    }

    /**
     * Get the value of dateOnHoldSince
     *
     * @return ?UbixDateTime The value of dateOnHoldSince
     */
    public function getDateOnHoldSince(): ?UbixDateTime
    {
        return $this->dateOnHoldSince;
    }

    /**
     * Set the value of dateOnHoldSince
     *
     * @param ?UbixDateTime $dateOnHoldSince The value for dateOnHoldSince
     *
     * @return void
     */
    public function setDateOnHoldSince(?UbixDateTime $dateOnHoldSince): void
    {
        $this->dateOnHoldSince = $dateOnHoldSince;
        $this->markChanged('dateOnHoldSince');
    }

    /**
     * Get the value of dateTimeAlertMsgDismissedAt
     *
     * @return ?UbixDateTime The value of dateTimeAlertMsgDismissedAt
     */
    public function getDateTimeAlertMsgDismissedAt(): ?UbixDateTime
    {
        return $this->dateTimeAlertMsgDismissedAt;
    }

    /**
     * Set the value of dateTimeAlertMsgDismissedAt
     *
     * @param ?UbixDateTime $dateTimeAlertMsgDismissedAt The value for dateTimeAlertMsgDismissedAt
     *
     * @return void
     */
    public function setDateTimeAlertMsgDismissedAt(?UbixDateTime $dateTimeAlertMsgDismissedAt): void
    {
        $this->dateTimeAlertMsgDismissedAt = $dateTimeAlertMsgDismissedAt;
        $this->markChanged('dateTimeAlertMsgDismissedAt');
    }

    /**
     * Get the value of liveVideoAds
     *
     * @return ?TinyInt The value of liveVideoAds
     */
    public function getLiveVideoAds(): ?TinyInt
    {
        return $this->liveVideoAds;
    }

    /**
     * Set the value of liveVideoAds
     *
     * @param ?TinyInt $liveVideoAds The value for liveVideoAds
     *
     * @return void
     */
    public function setLiveVideoAds(?TinyInt $liveVideoAds): void
    {
        $this->liveVideoAds = $liveVideoAds;
        $this->markChanged('liveVideoAds');
    }
}
