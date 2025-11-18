<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\BrokerId;
use Ubix\DataType\Int\DealId;
use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateSiteType;
use Ubix\Enum\ServiceType;
use Ubix\Enum\Status;
use Ubix\Enum\YesNo;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a Deal
 *
 * @see \Ubix\Tests\Model\DealTest PHPUnit test case
 */
final class Deal extends Model
{
    /**
     * Constructor
     *
     * @param ?DealId            $id                 The Deal ID
     * @param ?Varchar           $name               The name
     * @param ?Text              $description        The description
     * @param ?Text              $contactInfo        The contact info
     * @param ?Varchar           $campaignType       The campaign type
     * @param ?AffiliateSiteType $siteType           The site type
     * @param ?ServiceType       $service            The service type
     * @param ?AffiliateId       $affiliateId        The affiliate ID
     * @param ?BrokerId          $brokerId           The broker ID
     * @param ?Varchar           $defaultAdType      The default ad type
     * @param ?UnsignedSmallInt  $currentDealmakerId The current dealmaker ID
     * @param ?YesNo             $importCosts        Whether to import costs
     * @param ?Status            $status             Whether the deal is active
     * @param ?Varchar           $channelType        The channel type
     */
    public function __construct(
        private ?DealId $id = null,
        private ?Varchar $name = null,
        private ?Text $description = null,
        private ?Text $contactInfo = null,
        private ?Varchar $campaignType = null,
        private ?AffiliateSiteType $siteType = null,
        private ?ServiceType $service = null,
        private ?AffiliateId $affiliateId = null,
        private ?BrokerId $brokerId = null,
        private ?Varchar $defaultAdType = null,
        private ?UnsignedSmallInt $currentDealmakerId = null,
        private ?YesNo $importCosts = null,
        private ?Status $status = null,
        private ?Varchar $channelType = null,
    ) {
    }

    /**
     * Get the value of id
     *
     * @return ?DealId The value of id
     */
    public function getId(): ?DealId
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?DealId $id The value for id
     *
     * @return void
     */
    public function setId(?DealId $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of name
     *
     * @return ?Varchar The value of name
     */
    public function getName(): ?Varchar
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param ?Varchar $name The value for name
     *
     * @return void
     */
    public function setName(?Varchar $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the value of description
     *
     * @return ?Text The value of description
     */
    public function getDescription(): ?Text
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?Text $description The value for description
     *
     * @return void
     */
    public function setDescription(?Text $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of contactInfo
     *
     * @return ?Text The value of contactInfo
     */
    public function getContactInfo(): ?Text
    {
        return $this->contactInfo;
    }

    /**
     * Set the value of contactInfo
     *
     * @param ?Text $contactInfo The value for contactInfo
     *
     * @return void
     */
    public function setContactInfo(?Text $contactInfo): void
    {
        $this->contactInfo = $contactInfo;
    }

    /**
     * Get the value of campaignType
     *
     * @return ?Varchar The value of campaignType
     */
    public function getCampaignType(): ?Varchar
    {
        return $this->campaignType;
    }

    /**
     * Set the value of campaignType
     *
     * @param ?Varchar $campaignType The value for campaignType
     *
     * @return void
     */
    public function setCampaignType(?Varchar $campaignType): void
    {
        $this->campaignType = $campaignType;
    }

    /**
     * Get the value of siteType
     *
     * @return ?AffiliateSiteType The value of siteType
     */
    public function getSiteType(): ?AffiliateSiteType
    {
        return $this->siteType;
    }

    /**
     * Set the value of siteType
     *
     * @param ?AffiliateSiteType $siteType The value for siteType
     *
     * @return void
     */
    public function setSiteType(?AffiliateSiteType $siteType): void
    {
        $this->siteType = $siteType;
    }

    /**
     * Get the value of service
     *
     * @return ?ServiceType The value of service
     */
    public function getService(): ?ServiceType
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @param ?ServiceType $service The value for service
     *
     * @return void
     */
    public function setService(?ServiceType $service): void
    {
        $this->service = $service;
    }

    /**
     * Get the value of affiliateId
     *
     * @return ?AffiliateId The value of affiliateId
     */
    public function getAffiliateId(): ?AffiliateId
    {
        return $this->affiliateId;
    }

    /**
     * Set the value of affiliateId
     *
     * @param ?AffiliateId $affiliateId The value for affiliateId
     *
     * @return void
     */
    public function setAffiliateId(?AffiliateId $affiliateId): void
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Get the value of brokerId
     *
     * @return ?BrokerId The value of brokerId
     */
    public function getBrokerId(): ?BrokerId
    {
        return $this->brokerId;
    }

    /**
     * Set the value of brokerId
     *
     * @param ?BrokerId $brokerId The value for brokerId
     *
     * @return void
     */
    public function setBrokerId(?BrokerId $brokerId): void
    {
        $this->brokerId = $brokerId;
    }

    /**
     * Get the value of defaultAdType
     *
     * @return ?Varchar The value of defaultAdType
     */
    public function getDefaultAdType(): ?Varchar
    {
        return $this->defaultAdType;
    }

    /**
     * Set the value of defaultAdType
     *
     * @param ?Varchar $defaultAdType The value for defaultAdType
     *
     * @return void
     */
    public function setDefaultAdType(?Varchar $defaultAdType): void
    {
        $this->defaultAdType = $defaultAdType;
    }

    /**
     * Get the value of currentDealmakerId
     *
     * @return ?UnsignedSmallInt The value of currentDealmakerId
     */
    public function getCurrentDealmakerId(): ?UnsignedSmallInt
    {
        return $this->currentDealmakerId;
    }

    /**
     * Set the value of currentDealmakerId
     *
     * @param ?UnsignedSmallInt $currentDealmakerId The value for currentDealmakerId
     *
     * @return void
     */
    public function setCurrentDealmakerId(?UnsignedSmallInt $currentDealmakerId): void
    {
        $this->currentDealmakerId = $currentDealmakerId;
    }

    /**
     * Get the value of importCosts
     *
     * @return ?YesNo The value of importCosts
     */
    public function getImportCosts(): ?YesNo
    {
        return $this->importCosts;
    }

    /**
     * Set the value of importCosts
     *
     * @param ?YesNo $importCosts The value for importCosts
     *
     * @return void
     */
    public function setImportCosts(?YesNo $importCosts): void
    {
        $this->importCosts = $importCosts;
    }

    /**
     * Get the value of status
     *
     * @return ?Status The value of status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?Status $status The value for status
     *
     * @return void
     */
    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of channelType
     *
     * @return ?Varchar The value of channelType
     */
    public function getChannelType(): ?Varchar
    {
        return $this->channelType;
    }

    /**
     * Set the value of channelType
     *
     * @param ?Varchar $channelType The value for channelType
     *
     * @return void
     */
    public function setChannelType(?Varchar $channelType): void
    {
        $this->channelType = $channelType;
    }
}
