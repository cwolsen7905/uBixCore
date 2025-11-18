<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a solo acquisition campaign
 *
 * @see \Ubix\Tests\Model\SoloAcquisitionCampaignTest PHPUnit test case
 */
final class SoloAcquisitionCampaign extends Model
{
    /**
     * Constructor
     *
     * @param ?int                           $id          The campaign's ID
     * @param ?string                        $name        The campaign's name
     * @param ?string                        $description The campaign's description
     * @param ?string                        $contactInfo The campaign's contact info
     * @param ?SoloAcquisitionCampaignStatus $status      The campaign's status
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?string $description = null,
        private ?string $contactInfo = null,
        private ?SoloAcquisitionCampaignStatus $status = null,
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
     * Get the value of description
     *
     * @return ?string The value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?string $description The value for description
     *
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of contactInfo
     *
     * @return ?string The value of contactInfo
     */
    public function getContactInfo(): ?string
    {
        return $this->contactInfo;
    }

    /**
     * Set the value of contactInfo
     *
     * @param ?string $contactInfo The value for contactInfo
     *
     * @return void
     */
    public function setContactInfo(?string $contactInfo): void
    {
        $this->contactInfo = $contactInfo;
    }

    /**
     * Get the value of status
     *
     * @return ?SoloAcquisitionCampaignStatus The value of status
     */
    public function getStatus(): ?SoloAcquisitionCampaignStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?SoloAcquisitionCampaignStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?SoloAcquisitionCampaignStatus $status): void
    {
        $this->status = $status;
    }
}
