<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * One ordered benefit line on a tier (descriptive only at M1, FR-202)
 *
 * @see \Ubix\Tests\Model\TierBenefitTest PHPUnit test case
 */
final class TierBenefit extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id          The value of id
     * @param ?int               $tierId      The value of tierId
     * @param ?string            $description The value of description
     * @param ?int               $position    The value of position
     * @param ?DateTimeInterface $createdAt   The value of createdAt
     * @param ?DateTimeInterface $updatedAt   The value of updatedAt
     */
    public function __construct(
        private ?int $id = null,
        private ?int $tierId = null,
        private ?string $description = null,
        private ?int $position = null,
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
     * Get the value of tierId
     *
     * @return ?int The value of tierId
     */
    public function getTierId(): ?int
    {
        return $this->tierId;
    }

    /**
     * Set the value of tierId
     *
     * @param ?int $tierId The value of tierId
     *
     * @return void
     */
    public function setTierId(?int $tierId): void
    {
        $this->tierId = $tierId;
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
