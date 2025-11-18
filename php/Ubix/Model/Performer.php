<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;
use Ubix\Model\AccountInterface as Account;

/**
 * Model of a performer
 *
 * @see \Ubix\Tests\Model\PerformerTest PHPUnit test case
 */
final class Performer extends Model implements Account
{
    public const QUOTA_DEFAULT = 300; // The default quota a performer is given to host their images and videos (in MB)

    private const BCRYPT_ROUNDS_PASSWORD = 10; // 10 is the PHP default

    /**
     * Constructor
     *
     * @param ?int               $id                                  Placeholder for id // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $name                                Placeholder for name // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $slug                                Placeholder for slug // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $siteType                            Placeholder for siteType // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $image                               Placeholder for image // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $service                             Placeholder for service // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $studioCode                          Placeholder for studioCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $active                              Placeholder for active // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $beginDate                           Placeholder for beginDate // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $harvestCode                         Placeholder for harvestCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $isTestAccount                       Placeholder for isTestAccount // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $studioLocationId                    Placeholder for studioLocationId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $firstname                           Placeholder for firstname // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $middlename                          Placeholder for middlename // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $lastname                            Placeholder for lastname // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $birthdate                           Placeholder for birthdate // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $lastUpdated                         Placeholder for lastUpdated // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $username                            Placeholder for username // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $password                            Placeholder for password // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $encPassword                         Placeholder for encPassword // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $salt                                Placeholder for salt // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $twoFaSecret                         Placeholder for twoFaSecret // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $email                               Placeholder for email // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $phoneNumber                         Placeholder for phoneNumber // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $mailAddress                         Placeholder for mailAddress // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $serverName                          Placeholder for serverName // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $categoryId                          Placeholder for categoryId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $categoryId2                         Placeholder for categoryId2 // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $categoryId3                         Placeholder for categoryId3 // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $prevCategoryId                      Placeholder for prevCategoryId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $numPerf                             Placeholder for numPerf // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $reviewType                          Placeholder for reviewType // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $custodianId                         Placeholder for custodianId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $rateId                              Placeholder for rateId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $roomName                            Placeholder for roomName // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $canFakeChat                         Placeholder for canFakeChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $canBanUsers                         Placeholder for canBanUsers // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $canScheduleShows                    Placeholder for canScheduleShows // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $vodAllowed                          Placeholder for vodAllowed // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $keepVodPrivate                      Placeholder for keepVodPrivate // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $applyVodRules                       Placeholder for applyVodRules // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $vodDefaultCpm                       Placeholder for vodDefaultCpm // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $vodImportStatus                     Placeholder for vodImportStatus // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $ratePerCredit                       Placeholder for ratePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $useNetRate                          Placeholder for useNetRate // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $wishlistId                          Placeholder for wishlistId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $affiliateId                         Placeholder for affiliateId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $pre2010Credits                      Placeholder for pre2010Credits // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $totalCredits                        Placeholder for totalCredits // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $totalHours                          Placeholder for totalHours // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $minPowerScore                       Placeholder for minPowerScore // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $lastOpened                          Placeholder for lastOpened // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateFirstBroadcast                  Placeholder for dateFirstBroadcast // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $interactive                         Placeholder for interactive // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $isNew                               Placeholder for isNew // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $isFetish                            Placeholder for isFetish // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $unlimitedPartyChat                  Placeholder for unlimitedPartyChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $unrestrictedPartyChat               Placeholder for unrestrictedPartyChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $unlimitedGroupChat                  Placeholder for unlimitedGroupChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $unlimitedFakeChat                   Placeholder for unlimitedFakeChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $fetishImageId                       Placeholder for fetishImageId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $countryCode                         Placeholder for countryCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $nationalIdType                      Placeholder for nationalIdType // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $nationalIdValue                     Placeholder for nationalIdValue // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $cookiePolicyAcceptanceDatetime      Placeholder for cookiePolicyAcceptanceDatetime // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cookiePolicyAcceptanceIp            Placeholder for cookiePolicyAcceptanceIp // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cookiePolicyAcceptanceVersion       Placeholder for cookiePolicyAcceptanceVersion // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $activationDate                      Placeholder for activationDate // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onF4f                               Placeholder for onF4f // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onXvc                               Placeholder for onXvc // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onXvt                               Placeholder for onXvt // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $blockGuestChat                      Placeholder for blockGuestChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $blockBasicChat                      Placeholder for blockBasicChat // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $powerscoreBoostExpiry               Placeholder for powerscoreBoostExpiry // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $widescreenImageId                   Placeholder for widescreenImageId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onSgg                               Placeholder for onSgg // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $displayFanclubRatePerCredit         Placeholder for displayFanclubRatePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $displayFanclubReferredRatePerCredit Placeholder for displayFanclubReferredRatePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $displayVodRatePerCredit             Placeholder for displayVodRatePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $displayVodReferredRatePerCredit     Placeholder for displayVodReferredRatePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onClub                              Placeholder for onClub // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $onAsia                              Placeholder for onAsia // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $isNewEnded                          Placeholder for isNewEnded // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $isTrans                             Placeholder for isTrans // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $totalCreditsScale                   Placeholder for totalCreditsScale // NOT_IMPLEMENTED: replace placeholder
     * @param ?float             $displayModelAccessRatePerCredit     Placeholder for displayModelAccessRatePerCredit // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $sfwProfileImageId                   Placeholder for sfwProfileImageId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $broadcasterId                       Placeholder for broadcasterId // NOT_IMPLEMENTED: replace placeholder
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?string $slug = null,
        private ?string $siteType = null,
        private ?string $image = null,
        private ?int $service = null,
        private ?string $studioCode = null,
        private ?string $active = null,
        private ?DateTimeInterface $beginDate = null,
        private ?int $harvestCode = null,
        private ?string $isTestAccount = null,
        private ?int $studioLocationId = null,
        private ?string $firstname = null,
        private ?string $middlename = null,
        private ?string $lastname = null,
        private ?DateTimeInterface $birthdate = null,
        private ?DateTimeInterface $lastUpdated = null,
        private ?string $username = null,
        private ?string $password = null,
        private ?string $encPassword = null,
        private ?string $salt = null,
        private ?string $twoFaSecret = null,
        private ?string $email = null,
        private ?string $phoneNumber = null,
        private ?string $mailAddress = null,
        private ?string $serverName = null,
        private ?int $categoryId = null,
        private ?int $categoryId2 = null,
        private ?int $categoryId3 = null,
        private ?int $prevCategoryId = null,
        private ?int $numPerf = null,
        private ?string $reviewType = null,
        private ?int $custodianId = null,
        private ?int $rateId = null,
        private ?string $roomName = null,
        private ?string $canFakeChat = null,
        private ?string $canBanUsers = null,
        private ?string $canScheduleShows = null,
        private ?string $vodAllowed = null,
        private ?string $keepVodPrivate = null,
        private ?string $applyVodRules = null,
        private ?int $vodDefaultCpm = null,
        private ?string $vodImportStatus = null,
        private ?float $ratePerCredit = null,
        private ?string $useNetRate = null,
        private ?int $wishlistId = null,
        private ?int $affiliateId = null,
        private ?int $pre2010Credits = null,
        private ?int $totalCredits = null,
        private ?int $totalHours = null,
        private ?int $minPowerScore = null,
        private ?DateTimeInterface $lastOpened = null,
        private ?DateTimeInterface $dateFirstBroadcast = null,
        private ?int $interactive = null,
        private ?int $isNew = null,
        private ?string $isFetish = null,
        private ?string $unlimitedPartyChat = null,
        private ?string $unrestrictedPartyChat = null,
        private ?string $unlimitedGroupChat = null,
        private ?string $unlimitedFakeChat = null,
        private ?int $fetishImageId = null,
        private ?string $countryCode = null,
        private ?string $nationalIdType = null,
        private ?string $nationalIdValue = null,
        private ?DateTimeInterface $cookiePolicyAcceptanceDatetime = null,
        private ?string $cookiePolicyAcceptanceIp = null,
        private ?string $cookiePolicyAcceptanceVersion = null,
        private ?DateTimeInterface $activationDate = null,
        private ?string $onF4f = null,
        private ?string $onXvc = null,
        private ?string $onXvt = null,
        private ?string $blockGuestChat = null,
        private ?string $blockBasicChat = null,
        private ?DateTimeInterface $powerscoreBoostExpiry = null,
        private ?int $widescreenImageId = null,
        private ?string $onSgg = null,
        private ?float $displayFanclubRatePerCredit = null,
        private ?float $displayFanclubReferredRatePerCredit = null,
        private ?float $displayVodRatePerCredit = null,
        private ?float $displayVodReferredRatePerCredit = null,
        private ?string $onClub = null,
        private ?string $onAsia = null,
        private ?DateTimeInterface $isNewEnded = null,
        private ?int $isTrans = null,
        private ?int $totalCreditsScale = null,
        private ?float $displayModelAccessRatePerCredit = null,
        private ?int $sfwProfileImageId = null,
        private ?int $broadcasterId = null,
    ) {
    }

    /**
     * Get the performer's fan club's permalink
     *
     * @return string The performer's fan club's permalink
     */
    public function getFanClubPermalink(): string
    {
        return '/' . $this->getSlug();
    }

    /**
     * Get the performer's avatar
     *
     * @return string The performer's avatar
     */
    public function getAvatar(): string
    {
        return 'https://www.flirt4free.com/images/models/samples/' . $this->getHarvestCode() . '.jpg';
    }

    /**
     * Get the value of id
     *
     * @return ?int The value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return ?string The value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the value of slug
     *
     * @return ?string The value of slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Get the value of image
     *
     * @return ?string The value of image
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Get the value of studioCode
     *
     * @return ?string The value of studioCode
     */
    public function getStudioCode(): ?string
    {
        return $this->studioCode;
    }

    /**
     * Get the value of harvestCode
     *
     * @return ?int The value of harvestCode
     */
    public function getHarvestCode(): ?int
    {
        return $this->harvestCode;
    }

    /**
     * Get the value of username
     *
     * @return ?string The value of username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Get the value of broadcasterId
     *
     * @return ?int The value of broadcasterId
     */
    public function getBroadcasterId(): ?int
    {
        return $this->broadcasterId;
    }

    /**
     * Set the value of onClub
     *
     * @param ?string $onClub The value for onClub
     *
     * @return void
     */
    public function setOnClub(?string $onClub): void
    {
        $this->onClub = $onClub;
    }

    /**
     * Set the value of onAsia
     *
     * @param ?string $onAsia The value for onAsia
     *
     * @return void
     */
    public function setOnAsia(?string $onAsia): void
    {
        $this->onAsia = $onAsia;
    }

    /**
     * Set the value of isNewEnded
     *
     * @param ?DateTimeInterface $isNewEnded The value for isNewEnded
     *
     * @return void
     */
    public function setIsNewEnded(?DateTimeInterface $isNewEnded): void
    {
        $this->isNewEnded = $isNewEnded;
    }

    /**
     * Set the value of isTrans
     *
     * @param ?int $isTrans The value for isTrans
     *
     * @return void
     */
    public function setIsTrans(?int $isTrans): void
    {
        $this->isTrans = $isTrans;
    }

    /**
     * Set the value of totalCreditsScale
     *
     * @param ?int $totalCreditsScale The value for totalCreditsScale
     *
     * @return void
     */
    public function setTotalCreditsScale(?int $totalCreditsScale): void
    {
        $this->totalCreditsScale = $totalCreditsScale;
    }

    /**
     * Set the value of displayModelAccessRatePerCredit
     *
     * @param ?float $displayModelAccessRatePerCredit The value for displayModelAccessRatePerCredit
     *
     * @return void
     */
    public function setDisplayModelAccessRatePerCredit(?float $displayModelAccessRatePerCredit): void
    {
        $this->displayModelAccessRatePerCredit = $displayModelAccessRatePerCredit;
    }

    /**
     * Set the value of sfwProfileImageId
     *
     * @param ?int $sfwProfileImageId The value for sfwProfileImageId
     *
     * @return void
     */
    public function setSfwProfileImageId(?int $sfwProfileImageId): void
    {
        $this->sfwProfileImageId = $sfwProfileImageId;
    }

    /**
     * Set the value of broadcasterId
     *
     * @param ?int $broadcasterId The value for broadcasterId
     *
     * @return void
     */
    public function setBroadcasterId(?int $broadcasterId): void
    {
        $this->broadcasterId = $broadcasterId;
    }

    /**
     * Get the value of siteType
     *
     * @return ?string The value of siteType
     */
    public function getSiteType(): ?string
    {
        return $this->siteType;
    }

    /**
     * Get the value of service
     *
     * @return ?int The value of service
     */
    public function getService(): ?int
    {
        return $this->service;
    }

    /**
     * Get the value of active
     *
     * @return ?string The value of active
     */
    public function getActive(): ?string
    {
        return $this->active;
    }

    /**
     * Get the value of beginDate
     *
     * @return ?DateTimeInterface The value of beginDate
     */
    public function getBeginDate(): ?DateTimeInterface
    {
        return $this->beginDate;
    }

    /**
     * Get the value of isTestAccount
     *
     * @return ?string The value of isTestAccount
     */
    public function getIsTestAccount(): ?string
    {
        return $this->isTestAccount;
    }

    /**
     * Get the value of studioLocationId
     *
     * @return ?int The value of studioLocationId
     */
    public function getStudioLocationId(): ?int
    {
        return $this->studioLocationId;
    }

    /**
     * Get the value of firstname
     *
     * @return ?string The value of firstname
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Get the value of middlename
     *
     * @return ?string The value of middlename
     */
    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    /**
     * Get the value of lastname
     *
     * @return ?string The value of lastname
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Get the value of birthdate
     *
     * @return ?DateTimeInterface The value of birthdate
     */
    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    /**
     * Get the value of lastUpdated
     *
     * @return ?DateTimeInterface The value of lastUpdated
     */
    public function getLastUpdated(): ?DateTimeInterface
    {
        return $this->lastUpdated;
    }

    /**
     * Get the value of password
     *
     * @return ?string The value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get the value of encPassword
     *
     * @return ?string The value of encPassword
     */
    public function getEncPassword(): ?string
    {
        return $this->encPassword;
    }

    /**
     * Get the value of salt
     *
     * @return ?string The value of salt
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * Get the value of twoFaSecret
     *
     * @return ?string The value of twoFaSecret
     */
    public function getTwoFaSecret(): ?string
    {
        return $this->twoFaSecret;
    }

    /**
     * Get the value of email
     *
     * @return ?string The value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get the value of phoneNumber
     *
     * @return ?string The value of phoneNumber
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * Get the value of mailAddress
     *
     * @return ?string The value of mailAddress
     */
    public function getMailAddress(): ?string
    {
        return $this->mailAddress;
    }

    /**
     * Get the value of serverName
     *
     * @return ?string The value of serverName
     */
    public function getServerName(): ?string
    {
        return $this->serverName;
    }

    /**
     * Get the value of categoryId
     *
     * @return ?int The value of categoryId
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    /**
     * Get the value of categoryId2
     *
     * @return ?int The value of categoryId2
     */
    public function getCategoryId2(): ?int
    {
        return $this->categoryId2;
    }

    /**
     * Get the value of categoryId3
     *
     * @return ?int The value of categoryId3
     */
    public function getCategoryId3(): ?int
    {
        return $this->categoryId3;
    }

    /**
     * Get the value of prevCategoryId
     *
     * @return ?int The value of prevCategoryId
     */
    public function getPrevCategoryId(): ?int
    {
        return $this->prevCategoryId;
    }

    /**
     * Get the value of numPerf
     *
     * @return ?int The value of numPerf
     */
    public function getNumPerf(): ?int
    {
        return $this->numPerf;
    }

    /**
     * Get the value of reviewType
     *
     * @return ?string The value of reviewType
     */
    public function getReviewType(): ?string
    {
        return $this->reviewType;
    }

    /**
     * Get the value of custodianId
     *
     * @return ?int The value of custodianId
     */
    public function getCustodianId(): ?int
    {
        return $this->custodianId;
    }

    /**
     * Get the value of rateId
     *
     * @return ?int The value of rateId
     */
    public function getRateId(): ?int
    {
        return $this->rateId;
    }

    /**
     * Get the value of roomName
     *
     * @return ?string The value of roomName
     */
    public function getRoomName(): ?string
    {
        return $this->roomName;
    }

    /**
     * Get the value of canFakeChat
     *
     * @return ?string The value of canFakeChat
     */
    public function getCanFakeChat(): ?string
    {
        return $this->canFakeChat;
    }

    /**
     * Get the value of canBanUsers
     *
     * @return ?string The value of canBanUsers
     */
    public function getCanBanUsers(): ?string
    {
        return $this->canBanUsers;
    }

    /**
     * Get the value of canScheduleShows
     *
     * @return ?string The value of canScheduleShows
     */
    public function getCanScheduleShows(): ?string
    {
        return $this->canScheduleShows;
    }

    /**
     * Get the value of vodAllowed
     *
     * @return ?string The value of vodAllowed
     */
    public function getVodAllowed(): ?string
    {
        return $this->vodAllowed;
    }

    /**
     * Get the value of keepVodPrivate
     *
     * @return ?string The value of keepVodPrivate
     */
    public function getKeepVodPrivate(): ?string
    {
        return $this->keepVodPrivate;
    }

    /**
     * Get the value of applyVodRules
     *
     * @return ?string The value of applyVodRules
     */
    public function getApplyVodRules(): ?string
    {
        return $this->applyVodRules;
    }

    /**
     * Get the value of vodDefaultCpm
     *
     * @return ?int The value of vodDefaultCpm
     */
    public function getVodDefaultCpm(): ?int
    {
        return $this->vodDefaultCpm;
    }

    /**
     * Get the value of vodImportStatus
     *
     * @return ?string The value of vodImportStatus
     */
    public function getVodImportStatus(): ?string
    {
        return $this->vodImportStatus;
    }

    /**
     * Get the value of ratePerCredit
     *
     * @return ?float The value of ratePerCredit
     */
    public function getRatePerCredit(): ?float
    {
        return $this->ratePerCredit;
    }

    /**
     * Get the value of useNetRate
     *
     * @return ?string The value of useNetRate
     */
    public function getUseNetRate(): ?string
    {
        return $this->useNetRate;
    }

    /**
     * Get the value of wishlistId
     *
     * @return ?int The value of wishlistId
     */
    public function getWishlistId(): ?int
    {
        return $this->wishlistId;
    }

    /**
     * Get the value of affiliateId
     *
     * @return ?int The value of affiliateId
     */
    public function getAffiliateId(): ?int
    {
        return $this->affiliateId;
    }

    /**
     * Get the value of pre2010Credits
     *
     * @return ?int The value of pre2010Credits
     */
    public function getPre2010Credits(): ?int
    {
        return $this->pre2010Credits;
    }

    /**
     * Get the value of totalCredits
     *
     * @return ?int The value of totalCredits
     */
    public function getTotalCredits(): ?int
    {
        return $this->totalCredits;
    }

    /**
     * Get the value of totalHours
     *
     * @return ?int The value of totalHours
     */
    public function getTotalHours(): ?int
    {
        return $this->totalHours;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id The value for id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of minPowerScore
     *
     * @return ?int The value of minPowerScore
     */
    public function getMinPowerScore(): ?int
    {
        return $this->minPowerScore;
    }

    /**
     * Get the value of lastOpened
     *
     * @return ?DateTimeInterface The value of lastOpened
     */
    public function getLastOpened(): ?DateTimeInterface
    {
        return $this->lastOpened;
    }

    /**
     * Get the value of dateFirstBroadcast
     *
     * @return ?DateTimeInterface The value of dateFirstBroadcast
     */
    public function getDateFirstBroadcast(): ?DateTimeInterface
    {
        return $this->dateFirstBroadcast;
    }

    /**
     * Get the value of interactive
     *
     * @return ?int The value of interactive
     */
    public function getInteractive(): ?int
    {
        return $this->interactive;
    }

    /**
     * Get the value of isNew
     *
     * @return ?int The value of isNew
     */
    public function getIsNew(): ?int
    {
        return $this->isNew;
    }

    /**
     * Get the value of isFetish
     *
     * @return ?string The value of isFetish
     */
    public function getIsFetish(): ?string
    {
        return $this->isFetish;
    }

    /**
     * Get the value of unlimitedPartyChat
     *
     * @return ?string The value of unlimitedPartyChat
     */
    public function getUnlimitedPartyChat(): ?string
    {
        return $this->unlimitedPartyChat;
    }

    /**
     * Get the value of unrestrictedPartyChat
     *
     * @return ?string The value of unrestrictedPartyChat
     */
    public function getUnrestrictedPartyChat(): ?string
    {
        return $this->unrestrictedPartyChat;
    }

    /**
     * Get the value of unlimitedGroupChat
     *
     * @return ?string The value of unlimitedGroupChat
     */
    public function getUnlimitedGroupChat(): ?string
    {
        return $this->unlimitedGroupChat;
    }

    /**
     * Get the value of unlimitedFakeChat
     *
     * @return ?string The value of unlimitedFakeChat
     */
    public function getUnlimitedFakeChat(): ?string
    {
        return $this->unlimitedFakeChat;
    }

    /**
     * Get the value of fetishImageId
     *
     * @return ?int The value of fetishImageId
     */
    public function getFetishImageId(): ?int
    {
        return $this->fetishImageId;
    }

    /**
     * Get the value of countryCode
     *
     * @return ?string The value of countryCode
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * Get the value of nationalIdType
     *
     * @return ?string The value of nationalIdType
     */
    public function getNationalIdType(): ?string
    {
        return $this->nationalIdType;
    }

    /**
     * Get the value of nationalIdValue
     *
     * @return ?string The value of nationalIdValue
     */
    public function getNationalIdValue(): ?string
    {
        return $this->nationalIdValue;
    }

    /**
     * Get the value of cookiePolicyAcceptanceDatetime
     *
     * @return ?DateTimeInterface The value of cookiePolicyAcceptanceDatetime
     */
    public function getCookiePolicyAcceptanceDatetime(): ?DateTimeInterface
    {
        return $this->cookiePolicyAcceptanceDatetime;
    }

    /**
     * Get the value of cookiePolicyAcceptanceIp
     *
     * @return ?string The value of cookiePolicyAcceptanceIp
     */
    public function getCookiePolicyAcceptanceIp(): ?string
    {
        return $this->cookiePolicyAcceptanceIp;
    }

    /**
     * Get the value of cookiePolicyAcceptanceVersion
     *
     * @return ?string The value of cookiePolicyAcceptanceVersion
     */
    public function getCookiePolicyAcceptanceVersion(): ?string
    {
        return $this->cookiePolicyAcceptanceVersion;
    }

    /**
     * Get the value of activationDate
     *
     * @return ?DateTimeInterface The value of activationDate
     */
    public function getActivationDate(): ?DateTimeInterface
    {
        return $this->activationDate;
    }

    /**
     * Get the value of onF4f
     *
     * @return ?string The value of onF4f
     */
    public function getOnF4f(): ?string
    {
        return $this->onF4f;
    }

    /**
     * Get the value of onXvc
     *
     * @return ?string The value of onXvc
     */
    public function getOnXvc(): ?string
    {
        return $this->onXvc;
    }

    /**
     * Get the value of onXvt
     *
     * @return ?string The value of onXvt
     */
    public function getOnXvt(): ?string
    {
        return $this->onXvt;
    }

    /**
     * Get the value of blockGuestChat
     *
     * @return ?string The value of blockGuestChat
     */
    public function getBlockGuestChat(): ?string
    {
        return $this->blockGuestChat;
    }

    /**
     * Get the value of blockBasicChat
     *
     * @return ?string The value of blockBasicChat
     */
    public function getBlockBasicChat(): ?string
    {
        return $this->blockBasicChat;
    }

    /**
     * Get the value of powerscoreBoostExpiry
     *
     * @return ?DateTimeInterface The value of powerscoreBoostExpiry
     */
    public function getPowerscoreBoostExpiry(): ?DateTimeInterface
    {
        return $this->powerscoreBoostExpiry;
    }

    /**
     * Get the value of widescreenImageId
     *
     * @return ?int The value of widescreenImageId
     */
    public function getWidescreenImageId(): ?int
    {
        return $this->widescreenImageId;
    }

    /**
     * Get the value of onSgg
     *
     * @return ?string The value of onSgg
     */
    public function getOnSgg(): ?string
    {
        return $this->onSgg;
    }

    /**
     * Get the value of displayFanclubRatePerCredit
     *
     * @return ?float The value of displayFanclubRatePerCredit
     */
    public function getDisplayFanclubRatePerCredit(): ?float
    {
        return $this->displayFanclubRatePerCredit;
    }

    /**
     * Get the value of displayFanclubReferredRatePerCredit
     *
     * @return ?float The value of displayFanclubReferredRatePerCredit
     */
    public function getDisplayFanclubReferredRatePerCredit(): ?float
    {
        return $this->displayFanclubReferredRatePerCredit;
    }

    /**
     * Get the value of displayVodRatePerCredit
     *
     * @return ?float The value of displayVodRatePerCredit
     */
    public function getDisplayVodRatePerCredit(): ?float
    {
        return $this->displayVodRatePerCredit;
    }

    /**
     * Get the value of displayVodReferredRatePerCredit
     *
     * @return ?float The value of displayVodReferredRatePerCredit
     */
    public function getDisplayVodReferredRatePerCredit(): ?float
    {
        return $this->displayVodReferredRatePerCredit;
    }

    /**
     * Get the value of onClub
     *
     * @return ?string The value of onClub
     */
    public function getOnClub(): ?string
    {
        return $this->onClub;
    }

    /**
     * Get the value of onAsia
     *
     * @return ?string The value of onAsia
     */
    public function getOnAsia(): ?string
    {
        return $this->onAsia;
    }

    /**
     * Get the value of isNewEnded
     *
     * @return ?DateTimeInterface The value of isNewEnded
     */
    public function getIsNewEnded(): ?DateTimeInterface
    {
        return $this->isNewEnded;
    }

    /**
     * Get the value of isTrans
     *
     * @return ?int The value of isTrans
     */
    public function getIsTrans(): ?int
    {
        return $this->isTrans;
    }

    /**
     * Get the value of totalCreditsScale
     *
     * @return ?int The value of totalCreditsScale
     */
    public function getTotalCreditsScale(): ?int
    {
        return $this->totalCreditsScale;
    }

    /**
     * Get the value of displayModelAccessRatePerCredit
     *
     * @return ?float The value of displayModelAccessRatePerCredit
     */
    public function getDisplayModelAccessRatePerCredit(): ?float
    {
        return $this->displayModelAccessRatePerCredit;
    }

    /**
     * Get the value of sfwProfileImageId
     *
     * @return ?int The value of sfwProfileImageId
     */
    public function getSfwProfileImageId(): ?int
    {
        return $this->sfwProfileImageId;
    }

    /**
     * Set the value of name
     *
     * @param ?string $name The value for name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set the value of slug
     *
     * @param ?string $slug The value for slug
     *
     * @return void
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Set the value of siteType
     *
     * @param ?string $siteType The value for siteType
     *
     * @return void
     */
    public function setSiteType(?string $siteType): void
    {
        $this->siteType = $siteType;
    }

    /**
     * Set the value of image
     *
     * @param ?string $image The value for image
     *
     * @return void
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * Set the value of service
     *
     * @param ?int $service The value for service
     *
     * @return void
     */
    public function setService(?int $service): void
    {
        $this->service = $service;
    }

    /**
     * Set the value of studioCode
     *
     * @param ?string $studioCode The value for studioCode
     *
     * @return void
     */
    public function setStudioCode(?string $studioCode): void
    {
        $this->studioCode = $studioCode;
    }

    /**
     * Set the value of active
     *
     * @param ?string $active The value for active
     *
     * @return void
     */
    public function setActive(?string $active): void
    {
        $this->active = $active;
    }

    /**
     * Set the value of beginDate
     *
     * @param ?DateTimeInterface $beginDate The value for beginDate
     *
     * @return void
     */
    public function setBeginDate(?DateTimeInterface $beginDate): void
    {
        $this->beginDate = $beginDate;
    }

    /**
     * Set the value of harvestCode
     *
     * @param ?int $harvestCode The value for harvestCode
     *
     * @return void
     */
    public function setHarvestCode(?int $harvestCode): void
    {
        $this->harvestCode = $harvestCode;
    }

    /**
     * Set the value of isTestAccount
     *
     * @param ?string $isTestAccount The value for isTestAccount
     *
     * @return void
     */
    public function setIsTestAccount(?string $isTestAccount): void
    {
        $this->isTestAccount = $isTestAccount;
    }

    /**
     * Set the value of studioLocationId
     *
     * @param ?int $studioLocationId The value for studioLocationId
     *
     * @return void
     */
    public function setStudioLocationId(?int $studioLocationId): void
    {
        $this->studioLocationId = $studioLocationId;
    }

    /**
     * Set the value of firstname
     *
     * @param ?string $firstname The value for firstname
     *
     * @return void
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * Set the value of middlename
     *
     * @param ?string $middlename The value for middlename
     *
     * @return void
     */
    public function setMiddlename(?string $middlename): void
    {
        $this->middlename = $middlename;
    }

    /**
     * Set the value of lastname
     *
     * @param ?string $lastname The value for lastname
     *
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * Set the value of birthdate
     *
     * @param ?DateTimeInterface $birthdate The value for birthdate
     *
     * @return void
     */
    public function setBirthdate(?DateTimeInterface $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * Set the value of lastUpdated
     *
     * @param ?DateTimeInterface $lastUpdated The value for lastUpdated
     *
     * @return void
     */
    public function setLastUpdated(?DateTimeInterface $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * Set the value of username
     *
     * @param ?string $username The value for username
     *
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * Set the value of password
     *
     * @param ?string $password The value for password
     *
     * @return void
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * Set the value of encPassword
     *
     * @param ?string $encPassword The value for encPassword
     *
     * @return void
     */
    public function setEncPassword(?string $encPassword): void
    {
        $this->encPassword = $encPassword;
    }

    /**
     * Set the value of salt
     *
     * @param ?string $salt The value for salt
     *
     * @return void
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * Set the value of twoFaSecret
     *
     * @param ?string $twoFaSecret The value for twoFaSecret
     *
     * @return void
     */
    public function setTwoFaSecret(?string $twoFaSecret): void
    {
        $this->twoFaSecret = $twoFaSecret;
    }

    /**
     * Set the value of email
     *
     * @param ?string $email The value for email
     *
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Set the value of phoneNumber
     *
     * @param ?string $phoneNumber The value for phoneNumber
     *
     * @return void
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Set the value of mailAddress
     *
     * @param ?string $mailAddress The value for mailAddress
     *
     * @return void
     */
    public function setMailAddress(?string $mailAddress): void
    {
        $this->mailAddress = $mailAddress;
    }

    /**
     * Set the value of serverName
     *
     * @param ?string $serverName The value for serverName
     *
     * @return void
     */
    public function setServerName(?string $serverName): void
    {
        $this->serverName = $serverName;
    }

    /**
     * Set the value of categoryId
     *
     * @param ?int $categoryId The value for categoryId
     *
     * @return void
     */
    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Set the value of onXvc
     *
     * @param ?string $onXvc The value for onXvc
     *
     * @return void
     */
    public function setOnXvc(?string $onXvc): void
    {
        $this->onXvc = $onXvc;
    }

    /**
     * Set the value of onXvt
     *
     * @param ?string $onXvt The value for onXvt
     *
     * @return void
     */
    public function setOnXvt(?string $onXvt): void
    {
        $this->onXvt = $onXvt;
    }

    /**
     * Set the value of blockGuestChat
     *
     * @param ?string $blockGuestChat The value for blockGuestChat
     *
     * @return void
     */
    public function setBlockGuestChat(?string $blockGuestChat): void
    {
        $this->blockGuestChat = $blockGuestChat;
    }

    /**
     * Set the value of blockBasicChat
     *
     * @param ?string $blockBasicChat The value for blockBasicChat
     *
     * @return void
     */
    public function setBlockBasicChat(?string $blockBasicChat): void
    {
        $this->blockBasicChat = $blockBasicChat;
    }

    /**
     * Set the value of powerscoreBoostExpiry
     *
     * @param ?DateTimeInterface $powerscoreBoostExpiry The value for powerscoreBoostExpiry
     *
     * @return void
     */
    public function setPowerscoreBoostExpiry(?DateTimeInterface $powerscoreBoostExpiry): void
    {
        $this->powerscoreBoostExpiry = $powerscoreBoostExpiry;
    }

    /**
     * Set the value of widescreenImageId
     *
     * @param ?int $widescreenImageId The value for widescreenImageId
     *
     * @return void
     */
    public function setWidescreenImageId(?int $widescreenImageId): void
    {
        $this->widescreenImageId = $widescreenImageId;
    }

    /**
     * Set the value of onSgg
     *
     * @param ?string $onSgg The value for onSgg
     *
     * @return void
     */
    public function setOnSgg(?string $onSgg): void
    {
        $this->onSgg = $onSgg;
    }

    /**
     * Set the value of displayFanclubRatePerCredit
     *
     * @param ?float $displayFanclubRatePerCredit The value for displayFanclubRatePerCredit
     *
     * @return void
     */
    public function setDisplayFanclubRatePerCredit(?float $displayFanclubRatePerCredit): void
    {
        $this->displayFanclubRatePerCredit = $displayFanclubRatePerCredit;
    }

    /**
     * Set the value of displayFanclubReferredRatePerCredit
     *
     * @param ?float $displayFanclubReferredRatePerCredit The value for displayFanclubReferredRatePerCredit
     *
     * @return void
     */
    public function setDisplayFanclubReferredRatePerCredit(?float $displayFanclubReferredRatePerCredit): void
    {
        $this->displayFanclubReferredRatePerCredit = $displayFanclubReferredRatePerCredit;
    }

    /**
     * Set the value of displayVodRatePerCredit
     *
     * @param ?float $displayVodRatePerCredit The value for displayVodRatePerCredit
     *
     * @return void
     */
    public function setDisplayVodRatePerCredit(?float $displayVodRatePerCredit): void
    {
        $this->displayVodRatePerCredit = $displayVodRatePerCredit;
    }

    /**
     * Set the value of displayVodReferredRatePerCredit
     *
     * @param ?float $displayVodReferredRatePerCredit The value for displayVodReferredRatePerCredit
     *
     * @return void
     */
    public function setDisplayVodReferredRatePerCredit(?float $displayVodReferredRatePerCredit): void
    {
        $this->displayVodReferredRatePerCredit = $displayVodReferredRatePerCredit;
    }

    /**
     * Set the value of unlimitedPartyChat
     *
     * @param ?string $unlimitedPartyChat The value for unlimitedPartyChat
     *
     * @return void
     */
    public function setUnlimitedPartyChat(?string $unlimitedPartyChat): void
    {
        $this->unlimitedPartyChat = $unlimitedPartyChat;
    }

    /**
     * Set the value of unrestrictedPartyChat
     *
     * @param ?string $unrestrictedPartyChat The value for unrestrictedPartyChat
     *
     * @return void
     */
    public function setUnrestrictedPartyChat(?string $unrestrictedPartyChat): void
    {
        $this->unrestrictedPartyChat = $unrestrictedPartyChat;
    }

    /**
     * Set the value of unlimitedGroupChat
     *
     * @param ?string $unlimitedGroupChat The value for unlimitedGroupChat
     *
     * @return void
     */
    public function setUnlimitedGroupChat(?string $unlimitedGroupChat): void
    {
        $this->unlimitedGroupChat = $unlimitedGroupChat;
    }

    /**
     * Set the value of unlimitedFakeChat
     *
     * @param ?string $unlimitedFakeChat The value for unlimitedFakeChat
     *
     * @return void
     */
    public function setUnlimitedFakeChat(?string $unlimitedFakeChat): void
    {
        $this->unlimitedFakeChat = $unlimitedFakeChat;
    }

    /**
     * Set the value of fetishImageId
     *
     * @param ?int $fetishImageId The value for fetishImageId
     *
     * @return void
     */
    public function setFetishImageId(?int $fetishImageId): void
    {
        $this->fetishImageId = $fetishImageId;
    }

    /**
     * Set the value of countryCode
     *
     * @param ?string $countryCode The value for countryCode
     *
     * @return void
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Set the value of nationalIdType
     *
     * @param ?string $nationalIdType The value for nationalIdType
     *
     * @return void
     */
    public function setNationalIdType(?string $nationalIdType): void
    {
        $this->nationalIdType = $nationalIdType;
    }

    /**
     * Set the value of nationalIdValue
     *
     * @param ?string $nationalIdValue The value for nationalIdValue
     *
     * @return void
     */
    public function setNationalIdValue(?string $nationalIdValue): void
    {
        $this->nationalIdValue = $nationalIdValue;
    }

    /**
     * Set the value of cookiePolicyAcceptanceDatetime
     *
     * @param ?DateTimeInterface $cookiePolicyAcceptanceDatetime The value for cookiePolicyAcceptanceDatetime
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceDatetime(?DateTimeInterface $cookiePolicyAcceptanceDatetime): void
    {
        $this->cookiePolicyAcceptanceDatetime = $cookiePolicyAcceptanceDatetime;
    }

    /**
     * Set the value of cookiePolicyAcceptanceIp
     *
     * @param ?string $cookiePolicyAcceptanceIp The value for cookiePolicyAcceptanceIp
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceIp(?string $cookiePolicyAcceptanceIp): void
    {
        $this->cookiePolicyAcceptanceIp = $cookiePolicyAcceptanceIp;
    }

    /**
     * Set the value of cookiePolicyAcceptanceVersion
     *
     * @param ?string $cookiePolicyAcceptanceVersion The value for cookiePolicyAcceptanceVersion
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceVersion(?string $cookiePolicyAcceptanceVersion): void
    {
        $this->cookiePolicyAcceptanceVersion = $cookiePolicyAcceptanceVersion;
    }

    /**
     * Set the value of activationDate
     *
     * @param ?DateTimeInterface $activationDate The value for activationDate
     *
     * @return void
     */
    public function setActivationDate(?DateTimeInterface $activationDate): void
    {
        $this->activationDate = $activationDate;
    }

    /**
     * Set the value of onF4f
     *
     * @param ?string $onF4f The value for onF4f
     *
     * @return void
     */
    public function setOnF4f(?string $onF4f): void
    {
        $this->onF4f = $onF4f;
    }

    /**
     * Set the value of categoryId2
     *
     * @param ?int $categoryId2 The value for categoryId2
     *
     * @return void
     */
    public function setCategoryId2(?int $categoryId2): void
    {
        $this->categoryId2 = $categoryId2;
    }

    /**
     * Set the value of categoryId3
     *
     * @param ?int $categoryId3 The value for categoryId3
     *
     * @return void
     */
    public function setCategoryId3(?int $categoryId3): void
    {
        $this->categoryId3 = $categoryId3;
    }

    /**
     * Set the value of prevCategoryId
     *
     * @param ?int $prevCategoryId The value for prevCategoryId
     *
     * @return void
     */
    public function setPrevCategoryId(?int $prevCategoryId): void
    {
        $this->prevCategoryId = $prevCategoryId;
    }

    /**
     * Set the value of numPerf
     *
     * @param ?int $numPerf The value for numPerf
     *
     * @return void
     */
    public function setNumPerf(?int $numPerf): void
    {
        $this->numPerf = $numPerf;
    }

    /**
     * Set the value of reviewType
     *
     * @param ?string $reviewType The value for reviewType
     *
     * @return void
     */
    public function setReviewType(?string $reviewType): void
    {
        $this->reviewType = $reviewType;
    }

    /**
     * Set the value of custodianId
     *
     * @param ?int $custodianId The value for custodianId
     *
     * @return void
     */
    public function setCustodianId(?int $custodianId): void
    {
        $this->custodianId = $custodianId;
    }

    /**
     * Set the value of rateId
     *
     * @param ?int $rateId The value for rateId
     *
     * @return void
     */
    public function setRateId(?int $rateId): void
    {
        $this->rateId = $rateId;
    }

    /**
     * Set the value of roomName
     *
     * @param ?string $roomName The value for roomName
     *
     * @return void
     */
    public function setRoomName(?string $roomName): void
    {
        $this->roomName = $roomName;
    }

    /**
     * Set the value of canFakeChat
     *
     * @param ?string $canFakeChat The value for canFakeChat
     *
     * @return void
     */
    public function setCanFakeChat(?string $canFakeChat): void
    {
        $this->canFakeChat = $canFakeChat;
    }

    /**
     * Set the value of canBanUsers
     *
     * @param ?string $canBanUsers The value for canBanUsers
     *
     * @return void
     */
    public function setCanBanUsers(?string $canBanUsers): void
    {
        $this->canBanUsers = $canBanUsers;
    }

    /**
     * Set the value of canScheduleShows
     *
     * @param ?string $canScheduleShows The value for canScheduleShows
     *
     * @return void
     */
    public function setCanScheduleShows(?string $canScheduleShows): void
    {
        $this->canScheduleShows = $canScheduleShows;
    }

    /**
     * Set the value of vodAllowed
     *
     * @param ?string $vodAllowed The value for vodAllowed
     *
     * @return void
     */
    public function setVodAllowed(?string $vodAllowed): void
    {
        $this->vodAllowed = $vodAllowed;
    }

    /**
     * Set the value of keepVodPrivate
     *
     * @param ?string $keepVodPrivate The value for keepVodPrivate
     *
     * @return void
     */
    public function setKeepVodPrivate(?string $keepVodPrivate): void
    {
        $this->keepVodPrivate = $keepVodPrivate;
    }

    /**
     * Set the value of applyVodRules
     *
     * @param ?string $applyVodRules The value for applyVodRules
     *
     * @return void
     */
    public function setApplyVodRules(?string $applyVodRules): void
    {
        $this->applyVodRules = $applyVodRules;
    }

    /**
     * Set the value of vodDefaultCpm
     *
     * @param ?int $vodDefaultCpm The value for vodDefaultCpm
     *
     * @return void
     */
    public function setVodDefaultCpm(?int $vodDefaultCpm): void
    {
        $this->vodDefaultCpm = $vodDefaultCpm;
    }

    /**
     * Set the value of vodImportStatus
     *
     * @param ?string $vodImportStatus The value for vodImportStatus
     *
     * @return void
     */
    public function setVodImportStatus(?string $vodImportStatus): void
    {
        $this->vodImportStatus = $vodImportStatus;
    }

    /**
     * Set the value of ratePerCredit
     *
     * @param ?float $ratePerCredit The value for ratePerCredit
     *
     * @return void
     */
    public function setRatePerCredit(?float $ratePerCredit): void
    {
        $this->ratePerCredit = $ratePerCredit;
    }

    /**
     * Set the value of useNetRate
     *
     * @param ?string $useNetRate The value for useNetRate
     *
     * @return void
     */
    public function setUseNetRate(?string $useNetRate): void
    {
        $this->useNetRate = $useNetRate;
    }

    /**
     * Set the value of wishlistId
     *
     * @param ?int $wishlistId The value for wishlistId
     *
     * @return void
     */
    public function setWishlistId(?int $wishlistId): void
    {
        $this->wishlistId = $wishlistId;
    }

    /**
     * Set the value of affiliateId
     *
     * @param ?int $affiliateId The value for affiliateId
     *
     * @return void
     */
    public function setAffiliateId(?int $affiliateId): void
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Set the value of pre2010Credits
     *
     * @param ?int $pre2010Credits The value for pre2010Credits
     *
     * @return void
     */
    public function setPre2010Credits(?int $pre2010Credits): void
    {
        $this->pre2010Credits = $pre2010Credits;
    }

    /**
     * Set the value of totalCredits
     *
     * @param ?int $totalCredits The value for totalCredits
     *
     * @return void
     */
    public function setTotalCredits(?int $totalCredits): void
    {
        $this->totalCredits = $totalCredits;
    }

    /**
     * Set the value of totalHours
     *
     * @param ?int $totalHours The value for totalHours
     *
     * @return void
     */
    public function setTotalHours(?int $totalHours): void
    {
        $this->totalHours = $totalHours;
    }

    /**
     * Set the value of minPowerScore
     *
     * @param ?int $minPowerScore The value for minPowerScore
     *
     * @return void
     */
    public function setMinPowerScore(?int $minPowerScore): void
    {
        $this->minPowerScore = $minPowerScore;
    }

    /**
     * Set the value of lastOpened
     *
     * @param ?DateTimeInterface $lastOpened The value for lastOpened
     *
     * @return void
     */
    public function setLastOpened(?DateTimeInterface $lastOpened): void
    {
        $this->lastOpened = $lastOpened;
    }

    /**
     * Set the value of dateFirstBroadcast
     *
     * @param ?DateTimeInterface $dateFirstBroadcast The value for dateFirstBroadcast
     *
     * @return void
     */
    public function setDateFirstBroadcast(?DateTimeInterface $dateFirstBroadcast): void
    {
        $this->dateFirstBroadcast = $dateFirstBroadcast;
    }

    /**
     * Set the value of interactive
     *
     * @param ?int $interactive The value for interactive
     *
     * @return void
     */
    public function setInteractive(?int $interactive): void
    {
        $this->interactive = $interactive;
    }

    /**
     * Set the value of isNew
     *
     * @param ?int $isNew The value for isNew
     *
     * @return void
     */
    public function setIsNew(?int $isNew): void
    {
        $this->isNew = $isNew;
    }

    /**
     * Set the value of isFetish
     *
     * @param ?string $isFetish The value for isFetish
     *
     * @return void
     */
    public function setIsFetish(?string $isFetish): void
    {
        $this->isFetish = $isFetish;
    }

    /**
     * {@inheritDoc}
     */
    public function login(string $password): bool
    {
        return $this->validatePassword($password);
    }

    /**
     * {@inheritDoc}
     */
    public function autoLogin(): bool
    {
        return true;
    }

    /**
     * Validate a password
     *
     * @param string $password The password to validate
     *
     * @return bool Whether or not the password is valid
     */
    private function validatePassword(string $password): bool
    {
        //
        //  Check for an exact match (password property)
        //
        if ($password === $this->password) {
            return true;
        }

        //
        //  Check for an exact match (encPassword property)
        //
        if ($password === $this->encPassword) {
            return true;
        }

        //
        //  Check for a match using an MD5 hash (password property)
        //
        if ($this->password !== null && $password === md5($this->password)) {
            return true;
        }

        //
        //  Check for a match using an MD5 hash (encPassword property)
        //
        if ($this->encPassword !== null && $password === md5($this->encPassword)) {
            return true;
        }

        //
        //  Check for a match using an SHA1 hash (password property)
        //
        if ($this->salt !== null && $this->password === sha1($this->salt . sha1($password))) { // Yes, we double hash with SHA1 - this is how the legacy system was designed
            return true;
        }

        //
        //  Check for a match using an SHA1 hash (encPassword property)
        //
        if ($this->salt !== null && $this->encPassword === sha1($this->salt . sha1($password))) { // Yes, we double hash with SHA1 - this is how the legacy system was designed
            return true;
        }

        //
        //  Check for a match using a bcrypt hash (password property)
        //
        if ($this->password !== null && $password === password_hash($this->password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_ROUNDS_PASSWORD])) {
            return true;
        }

        //
        //  Check for a match using a bcrypt hash (encPassword property)
        //
        return $this->encPassword !== null && $password === password_hash($this->encPassword, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_ROUNDS_PASSWORD]);
    }
}
