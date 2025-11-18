<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\AffiliateCodeId;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a AffiliateCode
 *
 * @see \Ubix\Tests\Model\AffiliateCodeTest PHPUnit test case
 */
final class AffiliateCode extends Model
{
    /**
     * Constructor
     *
     * @param ?AffiliateCodeId $id           The affiliate code ID
     * @param ?AffiliateId     $affiliateId  The affiliate ID
     * @param ?MpCode          $mpCode       The mp code
     * @param ?Varchar         $trackingCode The tracking code
     * @param ?UbixDateTime     $dateCreated  The date created
     * @param ?Varchar         $description  The description
     * @param ?Varchar         $channelType  The channel type
     */
    public function __construct(
        private ?AffiliateCodeId $id = null,
        private ?AffiliateId $affiliateId = null,
        private ?MpCode $mpCode = null,
        private ?Varchar $trackingCode = null,
        private ?UbixDateTime $dateCreated = null,
        private ?Varchar $description = null,
        private ?Varchar $channelType = null,
    ) {
    }

    /**
     * Get the value of trackingCode
     *
     * @return ?Varchar The value of trackingCode
     */
    public function getTrackingCode(): ?Varchar
    {
        return $this->trackingCode;
    }

    /**
     * Get the value of dateCreated
     *
     * @return ?UbixDateTime The value of dateCreated
     */
    public function getDateCreated(): ?UbixDateTime
    {
        return $this->dateCreated;
    }

    /**
     * Get the value of description
     *
     * @return ?Varchar The value of description
     */
    public function getDescription(): ?Varchar
    {
        return $this->description;
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
     * Set the value of trackingCode
     *
     * @param ?Varchar $trackingCode The value for trackingCode
     *
     * @return void
     */
    public function setTrackingCode(?Varchar $trackingCode): void
    {
        $this->trackingCode = $trackingCode;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?UbixDateTime $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?UbixDateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Set the value of description
     *
     * @param ?Varchar $description The value for description
     *
     * @return void
     */
    public function setDescription(?Varchar $description): void
    {
        $this->description = $description;
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

    /**
     * Get the value of mpCode
     *
     * @return ?MpCode The value of mpCode
     */
    public function getMpCode(): ?MpCode
    {
        return $this->mpCode;
    }

    /**
     * Set the value of mpCode
     *
     * @param ?MpCode $mpCode The value for mpCode
     *
     * @return void
     */
    public function setMpCode(?MpCode $mpCode): void
    {
        $this->mpCode = $mpCode;
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
     * Get the value of id
     *
     * @return ?AffiliateCodeId The value of id
     */
    public function getId(): ?AffiliateCodeId
    {
        return $this->id;
    }

    /**
     * Set the value of the ID
     *
     * @param ?AffiliateCodeId $id The value for the id
     *
     * @return void
     */
    public function setId(?AffiliateCodeId $id): void
    {
        $this->id = $id;
    }
}
