<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Tier\TierBillingInterval;
use Ubix\Enum\Tier\TierStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * A creator subscription tier (position 0 = implicit free tier, never stored)
 *
 * @see \Ubix\Tests\Model\TierTest PHPUnit test case
 */
final class Tier extends Model
{
    /**
     * Constructor
     *
     * @param ?int                 $id              The value of id
     * @param ?int                 $creatorId       The value of creatorId
     * @param ?string              $name            The value of name
     * @param ?string              $description     The value of description
     * @param ?int                 $priceAmount     The value of priceAmount
     * @param ?string              $priceCurrency   The value of priceCurrency
     * @param ?TierBillingInterval $billingInterval The value of billingInterval
     * @param ?int                 $position        The value of position
     * @param ?TierStatus          $status          The value of status
     * @param ?DateTimeInterface   $createdAt       The value of createdAt
     * @param ?DateTimeInterface   $updatedAt       The value of updatedAt
     */
    public function __construct(
        private ?int $id = null,
        private ?int $creatorId = null,
        private ?string $name = null,
        private ?string $description = null,
        private ?int $priceAmount = null,
        private ?string $priceCurrency = null,
        private ?TierBillingInterval $billingInterval = null,
        private ?int $position = null,
        private ?TierStatus $status = null,
        private ?DateTimeInterface $createdAt = null,
        private ?DateTimeInterface $updatedAt = null,
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
     * @param ?int $id The value of id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of creatorId
     *
     * @return ?int The value of creatorId
     */
    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    /**
     * Set the value of creatorId
     *
     * @param ?int $creatorId The value of creatorId
     *
     * @return void
     */
    public function setCreatorId(?int $creatorId): void
    {
        $this->creatorId = $creatorId;
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
     * @param ?string $name The value of name
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
     * @param ?string $description The value of description
     *
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of priceAmount
     *
     * @return ?int The value of priceAmount
     */
    public function getPriceAmount(): ?int
    {
        return $this->priceAmount;
    }

    /**
     * Set the value of priceAmount
     *
     * @param ?int $priceAmount The value of priceAmount
     *
     * @return void
     */
    public function setPriceAmount(?int $priceAmount): void
    {
        $this->priceAmount = $priceAmount;
    }

    /**
     * Get the value of priceCurrency
     *
     * @return ?string The value of priceCurrency
     */
    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    /**
     * Set the value of priceCurrency
     *
     * @param ?string $priceCurrency The value of priceCurrency
     *
     * @return void
     */
    public function setPriceCurrency(?string $priceCurrency): void
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get the value of billingInterval
     *
     * @return ?TierBillingInterval The value of billingInterval
     */
    public function getBillingInterval(): ?TierBillingInterval
    {
        return $this->billingInterval;
    }

    /**
     * Set the value of billingInterval
     *
     * @param ?TierBillingInterval $billingInterval The value of billingInterval
     *
     * @return void
     */
    public function setBillingInterval(?TierBillingInterval $billingInterval): void
    {
        $this->billingInterval = $billingInterval;
    }

    /**
     * Get the value of position
     *
     * @return ?int The value of position
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @param ?int $position The value of position
     *
     * @return void
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * Get the value of status
     *
     * @return ?TierStatus The value of status
     */
    public function getStatus(): ?TierStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?TierStatus $status The value of status
     *
     * @return void
     */
    public function setStatus(?TierStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of createdAt
     *
     * @return ?DateTimeInterface The value of createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param ?DateTimeInterface $createdAt The value of createdAt
     *
     * @return void
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get the value of updatedAt
     *
     * @return ?DateTimeInterface The value of updatedAt
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param ?DateTimeInterface $updatedAt The value of updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
