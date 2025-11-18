<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Prospect\ProspectStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a prospect
 *
 * @see \Ubix\Tests\Model\ProspectTest PHPUnit test case
 */
final class Prospect extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                     The prospect's ID
     * @param ?string            $name                   The prospect's name
     * @param ?string            $password               The prospect's password
     * @param ?string            $encPassword            The prospect's encrypted password
     * @param ?string            $salt                   The prospect's salt
     * @param ?string            $emailAddress           The prospect's email address
     * @param ?string            $phoneNumber            The prospect's phone number
     * @param ?int               $salespersonId          The prospect's salesperson ID
     * @param ?ProspectStatus    $status                 The prospect's status
     * @param ?Performer         $performer              The prospect's performer
     * @param ?int               $adminUserId            The prospect's admin user ID
     * @param ?AdminUser         $adminUser              The prospect's admin user
     * @param ?string            $companyName            The prospect's company name
     * @param ?string            $authKey                The prospect's auth key
     * @param ?string            $fax                    The prospect's fax number
     * @param ?string            $imAddress              The prospect's IM addres
     * @param ?string            $countryCode            The prospect's country code
     * @param ?string            $state                  The prospect's state code
     * @param ?string            $experience             The prospect's experience
     * @param ?string            $onF4f                  Whether or not the prospect is on F4F
     * @param ?string            $stageName              The prospect's stage name
     * @param ?string            $cameras                The prospect's cameras value (user agent? browser?)
     * @param ?string            $performers             The prospect's performers value
     * @param ?string            $ipAddress              The prospect's IP address
     * @param ?string            $tracker                The prospect's tracker
     * @param ?int               $campaignId             The prospect's campaign ID
     * @param ?string            $comments               The prospect's comments
     * @param ?string            $preferredContactMethod The prospect's preferred contact method
     * @param ?string            $contactMethodInfo      The prospect's contact method ino
     * @param ?string            $studioWebsite          The prospect's studio website
     * @param ?int               $refAffiliateId         The prospect's referring affiliate ID
     * @param ?int               $refModelId             The prospect's referring model ID
     * @param ?int               $refBroadcasterId       The prospect's referring broadcaster ID
     * @param ?int               $refUserId              The prospect's referring user ID
     * @param ?DateTimeInterface $birthdate              The prospect's birthday
     * @param ?string            $service                The prospect's service
     * @param ?int               $allowTextMessages      Whether or not the prospect allows text messages
     * @param ?DateTimeInterface $dateCreated            The date the prospect record was created
     * @param ?DateTimeInterface $dateLastUpdated        The date the prospect record was last update
     * @param ?string            $signupOrigin           The prospect's sign-up origin
     * @param ?string            $statusFinal            The prospect's status (only this time it's final)
     * @param ?string            $siteType               The prospect's site type
     * @param ?int               $emailConfirmed         The prospect's email address confirmation status
     * @param ?DateTimeInterface $dateEmailConfirmed     The date the prospect confirmed their email address
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?string $password = null,
        private ?string $encPassword = null,
        private ?string $salt = null,
        private ?string $emailAddress = null,
        private ?string $phoneNumber = null,
        private ?int $salespersonId = null,
        private ?ProspectStatus $status = null,
        private ?Performer $performer = null,
        private ?int $adminUserId = null,
        private ?AdminUser $adminUser = null,
        private ?string $companyName = null,
        private ?string $authKey = null,
        private ?string $fax = null,
        private ?string $imAddress = null,
        private ?string $countryCode = null,
        private ?string $state = null,
        private ?string $experience = null,
        private ?string $onF4f = null,
        private ?string $stageName = null,
        private ?string $cameras = null,
        private ?string $performers = null,
        private ?string $ipAddress = null,
        private ?string $tracker = null,
        private ?int $campaignId = null,
        private ?string $comments = null,
        private ?string $preferredContactMethod = null,
        private ?string $contactMethodInfo = null,
        private ?string $studioWebsite = null,
        private ?int $refAffiliateId = null,
        private ?int $refModelId = null,
        private ?int $refBroadcasterId = null,
        private ?int $refUserId = null,
        private ?DateTimeInterface $birthdate = null,
        private ?string $service = null,
        private ?int $allowTextMessages = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?DateTimeInterface $dateLastUpdated = null,
        private ?string $signupOrigin = null,
        private ?string $statusFinal = null,
        private ?string $siteType = null,
        private ?int $emailConfirmed = null,
        private ?DateTimeInterface $dateEmailConfirmed = null,
    ) {
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
     * Get the value of name
     *
     * @return ?string The value of name
     */
    public function getName(): ?string
    {
        return $this->name;
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
     * Get the value of password
     *
     * @return ?string The value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
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
     * Get the value of encPassword
     *
     * @return ?string The value of encPassword
     */
    public function getEncPassword(): ?string
    {
        return $this->encPassword;
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
     * Get the value of salt
     *
     * @return ?string The value of salt
     */
    public function getSalt(): ?string
    {
        return $this->salt;
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
     * Get the value of salespersonId
     *
     * @return ?int The value of salespersonId
     */
    public function getSalespersonId(): ?int
    {
        return $this->salespersonId;
    }

    /**
     * Set the value of salespersonId
     *
     * @param ?int $salespersonId The value for salespersonId
     *
     * @return void
     */
    public function setSalespersonId(?int $salespersonId): void
    {
        $this->salespersonId = $salespersonId;
    }

    /**
     * Get the value of status
     *
     * @return ?ProspectStatus The value of status
     */
    public function getStatus(): ?ProspectStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?ProspectStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?ProspectStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of adminUserId
     *
     * @return ?int The value of adminUserId
     */
    public function getAdminUserId(): ?int
    {
        return $this->adminUserId;
    }

    /**
     * Set the value of adminUserId
     *
     * @param ?int $adminUserId The value for adminUserId
     *
     * @return void
     */
    public function setAdminUserId(?int $adminUserId): void
    {
        $this->adminUserId = $adminUserId;
    }

    /**
     * Get the value of emailAddress
     *
     * @return ?string The value of emailAddress
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * Set the value of emailAddress
     *
     * @param ?string $emailAddress The value for emailAddress
     *
     * @return void
     */
    public function setEmailAddress(?string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
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
     * Get the value of performer
     *
     * @return ?Performer The value of performer
     */
    public function getPerformer(): ?Performer
    {
        return $this->performer;
    }

    /**
     * Set the value of performer
     *
     * @param ?Performer $performer The value for performer
     *
     * @return void
     */
    public function setPerformer(?Performer $performer): void
    {
        $this->performer = $performer;
    }

    /**
     * Get the value of adminUser
     *
     * @return ?AdminUser The value of adminUser
     */
    public function getAdminUser(): ?AdminUser
    {
        return $this->adminUser;
    }

    /**
     * Set the value of adminUser
     *
     * @param ?AdminUser $adminUser The value for adminUser
     *
     * @return void
     */
    public function setAdminUser(?AdminUser $adminUser): void
    {
        $this->adminUser = $adminUser;
    }

    /**
     * Get the value of companyName
     *
     * @return ?string The value of companyName
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * Set the value of companyName
     *
     * @param ?string $companyName The value for companyName
     *
     * @return void
     */
    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * Get the value of authKey
     *
     * @return ?string The value of authKey
     */
    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    /**
     * Set the value of authKey
     *
     * @param ?string $authKey The value for authKey
     *
     * @return void
     */
    public function setAuthKey(?string $authKey): void
    {
        $this->authKey = $authKey;
    }

    /**
     * Get the value of fax
     *
     * @return ?string The value of fax
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * Set the value of fax
     *
     * @param ?string $fax The value for fax
     *
     * @return void
     */
    public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * Get the value of imAddress
     *
     * @return ?string The value of imAddress
     */
    public function getImAddress(): ?string
    {
        return $this->imAddress;
    }

    /**
     * Set the value of imAddress
     *
     * @param ?string $imAddress The value for imAddress
     *
     * @return void
     */
    public function setImAddress(?string $imAddress): void
    {
        $this->imAddress = $imAddress;
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
     * Get the value of state
     *
     * @return ?string The value of state
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @param ?string $state The value for state
     *
     * @return void
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * Get the value of experience
     *
     * @return ?string The value of experience
     */
    public function getExperience(): ?string
    {
        return $this->experience;
    }

    /**
     * Set the value of experience
     *
     * @param ?string $experience The value for experience
     *
     * @return void
     */
    public function setExperience(?string $experience): void
    {
        $this->experience = $experience;
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
     * Get the value of stageName
     *
     * @return ?string The value of stageName
     */
    public function getStageName(): ?string
    {
        return $this->stageName;
    }

    /**
     * Set the value of stageName
     *
     * @param ?string $stageName The value for stageName
     *
     * @return void
     */
    public function setStageName(?string $stageName): void
    {
        $this->stageName = $stageName;
    }

    /**
     * Get the value of cameras
     *
     * @return ?string The value of cameras
     */
    public function getCameras(): ?string
    {
        return $this->cameras;
    }

    /**
     * Set the value of cameras
     *
     * @param ?string $cameras The value for cameras
     *
     * @return void
     */
    public function setCameras(?string $cameras): void
    {
        $this->cameras = $cameras;
    }

    /**
     * Get the value of performers
     *
     * @return ?string The value of performers
     */
    public function getPerformers(): ?string
    {
        return $this->performers;
    }

    /**
     * Set the value of performers
     *
     * @param ?string $performers The value for performers
     *
     * @return void
     */
    public function setPerformers(?string $performers): void
    {
        $this->performers = $performers;
    }

    /**
     * Get the value of ipAddress
     *
     * @return ?string The value of ipAddress
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * Set the value of ipAddress
     *
     * @param ?string $ipAddress The value for ipAddress
     *
     * @return void
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the value of tracker
     *
     * @return ?string The value of tracker
     */
    public function getTracker(): ?string
    {
        return $this->tracker;
    }

    /**
     * Set the value of tracker
     *
     * @param ?string $tracker The value for tracker
     *
     * @return void
     */
    public function setTracker(?string $tracker): void
    {
        $this->tracker = $tracker;
    }

    /**
     * Get the value of campaignId
     *
     * @return ?int The value of campaignId
     */
    public function getCampaignId(): ?int
    {
        return $this->campaignId;
    }

    /**
     * Set the value of campaignId
     *
     * @param ?int $campaignId The value for campaignId
     *
     * @return void
     */
    public function setCampaignId(?int $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Get the value of comments
     *
     * @return ?string The value of comments
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @param ?string $comments The value for comments
     *
     * @return void
     */
    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * Get the value of preferredContactMethod
     *
     * @return ?string The value of preferredContactMethod
     */
    public function getPreferredContactMethod(): ?string
    {
        return $this->preferredContactMethod;
    }

    /**
     * Set the value of preferredContactMethod
     *
     * @param ?string $preferredContactMethod The value for preferredContactMethod
     *
     * @return void
     */
    public function setPreferredContactMethod(?string $preferredContactMethod): void
    {
        $this->preferredContactMethod = $preferredContactMethod;
    }

    /**
     * Get the value of contactMethodInfo
     *
     * @return ?string The value of contactMethodInfo
     */
    public function getContactMethodInfo(): ?string
    {
        return $this->contactMethodInfo;
    }

    /**
     * Set the value of contactMethodInfo
     *
     * @param ?string $contactMethodInfo The value for contactMethodInfo
     *
     * @return void
     */
    public function setContactMethodInfo(?string $contactMethodInfo): void
    {
        $this->contactMethodInfo = $contactMethodInfo;
    }

    /**
     * Get the value of studioWebsite
     *
     * @return ?string The value of studioWebsite
     */
    public function getStudioWebsite(): ?string
    {
        return $this->studioWebsite;
    }

    /**
     * Set the value of studioWebsite
     *
     * @param ?string $studioWebsite The value for studioWebsite
     *
     * @return void
     */
    public function setStudioWebsite(?string $studioWebsite): void
    {
        $this->studioWebsite = $studioWebsite;
    }

    /**
     * Get the value of refAffiliateId
     *
     * @return ?int The value of refAffiliateId
     */
    public function getRefAffiliateId(): ?int
    {
        return $this->refAffiliateId;
    }

    /**
     * Set the value of refAffiliateId
     *
     * @param ?int $refAffiliateId The value for refAffiliateId
     *
     * @return void
     */
    public function setRefAffiliateId(?int $refAffiliateId): void
    {
        $this->refAffiliateId = $refAffiliateId;
    }

    /**
     * Get the value of refModelId
     *
     * @return ?int The value of refModelId
     */
    public function getRefModelId(): ?int
    {
        return $this->refModelId;
    }

    /**
     * Set the value of refModelId
     *
     * @param ?int $refModelId The value for refModelId
     *
     * @return void
     */
    public function setRefModelId(?int $refModelId): void
    {
        $this->refModelId = $refModelId;
    }

    /**
     * Get the value of refBroadcasterId
     *
     * @return ?int The value of refBroadcasterId
     */
    public function getRefBroadcasterId(): ?int
    {
        return $this->refBroadcasterId;
    }

    /**
     * Set the value of refBroadcasterId
     *
     * @param ?int $refBroadcasterId The value for refBroadcasterId
     *
     * @return void
     */
    public function setRefBroadcasterId(?int $refBroadcasterId): void
    {
        $this->refBroadcasterId = $refBroadcasterId;
    }

    /**
     * Get the value of refUserId
     *
     * @return ?int The value of refUserId
     */
    public function getRefUserId(): ?int
    {
        return $this->refUserId;
    }

    /**
     * Set the value of refUserId
     *
     * @param ?int $refUserId The value for refUserId
     *
     * @return void
     */
    public function setRefUserId(?int $refUserId): void
    {
        $this->refUserId = $refUserId;
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
     * Get the value of service
     *
     * @return ?string The value of service
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @param ?string $service The value for service
     *
     * @return void
     */
    public function setService(?string $service): void
    {
        $this->service = $service;
    }

    /**
     * Get the value of allowTextMessages
     *
     * @return ?int The value of allowTextMessages
     */
    public function getAllowTextMessages(): ?int
    {
        return $this->allowTextMessages;
    }

    /**
     * Set the value of allowTextMessages
     *
     * @param ?int $allowTextMessages The value for allowTextMessages
     *
     * @return void
     */
    public function setAllowTextMessages(?int $allowTextMessages): void
    {
        $this->allowTextMessages = $allowTextMessages;
    }

    /**
     * Get the value of dateCreated
     *
     * @return ?DateTimeInterface The value of dateCreated
     */
    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?DateTimeInterface $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get the value of dateLastUpdated
     *
     * @return ?DateTimeInterface The value of dateLastUpdated
     */
    public function getDateLastUpdated(): ?DateTimeInterface
    {
        return $this->dateLastUpdated;
    }

    /**
     * Set the value of dateLastUpdated
     *
     * @param ?DateTimeInterface $dateLastUpdated The value for dateLastUpdated
     *
     * @return void
     */
    public function setDateLastUpdated(?DateTimeInterface $dateLastUpdated): void
    {
        $this->dateLastUpdated = $dateLastUpdated;
    }

    /**
     * Get the value of signupOrigin
     *
     * @return ?string The value of signupOrigin
     */
    public function getSignupOrigin(): ?string
    {
        return $this->signupOrigin;
    }

    /**
     * Set the value of signupOrigin
     *
     * @param ?string $signupOrigin The value for signupOrigin
     *
     * @return void
     */
    public function setSignupOrigin(?string $signupOrigin): void
    {
        $this->signupOrigin = $signupOrigin;
    }

    /**
     * Get the value of statusFinal
     *
     * @return ?string The value of statusFinal
     */
    public function getStatusFinal(): ?string
    {
        return $this->statusFinal;
    }

    /**
     * Set the value of statusFinal
     *
     * @param ?string $statusFinal The value for statusFinal
     *
     * @return void
     */
    public function setStatusFinal(?string $statusFinal): void
    {
        $this->statusFinal = $statusFinal;
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
     * Get the value of emailConfirmed
     *
     * @return ?int The value of emailConfirmed
     */
    public function getEmailConfirmed(): ?int
    {
        return $this->emailConfirmed;
    }

    /**
     * Set the value of emailConfirmed
     *
     * @param ?int $emailConfirmed The value for emailConfirmed
     *
     * @return void
     */
    public function setEmailConfirmed(?int $emailConfirmed): void
    {
        $this->emailConfirmed = $emailConfirmed;
    }

    /**
     * Get the value of dateEmailConfirmed
     *
     * @return ?DateTimeInterface The value of dateEmailConfirmed
     */
    public function getDateEmailConfirmed(): ?DateTimeInterface
    {
        return $this->dateEmailConfirmed;
    }

    /**
     * Set the value of dateEmailConfirmed
     *
     * @param ?DateTimeInterface $dateEmailConfirmed The value for dateEmailConfirmed
     *
     * @return void
     */
    public function setDateEmailConfirmed(?DateTimeInterface $dateEmailConfirmed): void
    {
        $this->dateEmailConfirmed = $dateEmailConfirmed;
    }
}
