<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club
 *
 * @see \Ubix\Tests\Model\FanClubTest PHPUnit test case
 */
final class FanClub extends Model
{
    public const MONTHLY_PRICE_DEFAULT = 30; // In credits
    public const MONTHLY_PRICE_MAXIMUM = 9999; // In credits
    public const DAYS_FOR_GIFT_MAXIMUM = 90;

    private const STATUSES_CLOSED = [
        'fast_disabled',
        'slow_disabled',
    ];

    /**
     * Constructor
     *
     * @param ?int               $modelId               The ID of the performer connected to the fan club
     * @param ?Performer         $performer             The performer connected to the fan club
     * @param ?string            $status                Fan club status
     * @param ?DateTimeInterface $statusUpdatedAt       Timestamp of the last fan club status change
     * @param ?int               $monthlyPrice          Monthly price (in credits)
     * @param ?DateTimeInterface $monthlyPriceUpdatedAt Timestamp of the last monthly price change
     */
    public function __construct(
        private ?int $modelId = null,
        private ?Performer $performer = null,
        private ?string $status = null,
        private ?DateTimeInterface $statusUpdatedAt = null,
        private ?int $monthlyPrice = null,
        private ?DateTimeInterface $monthlyPriceUpdatedAt = null,
    ) {
    }

    /**
     * Determine if the fan club is closed
     *
     * @return bool Whether or not the fan club is closed
     */
    public function isClosed(): bool
    {
        return in_array($this->status, self::STATUSES_CLOSED, true);
    }

    /**
     * Get the value of modelId
     *
     * @return ?int The value of modelId
     */
    public function getModelId(): ?int
    {
        return $this->modelId;
    }

    /**
     * Set the value of modelId
     *
     * @param ?int $modelId The value for modelId
     *
     * @return void
     */
    public function setModelId(?int $modelId): void
    {
        $this->modelId = $modelId;
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
     * Get the value of status
     *
     * @return ?string The value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?string $status The value for status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of statusUpdatedAt
     *
     * @return ?DateTimeInterface The value of statusUpdatedAt
     */
    public function getStatusUpdatedAt(): ?DateTimeInterface
    {
        return $this->statusUpdatedAt;
    }

    /**
     * Set the value of statusUpdatedAt
     *
     * @param ?DateTimeInterface $statusUpdatedAt The value for statusUpdatedAt
     *
     * @return void
     */
    public function setStatusUpdatedAt(?DateTimeInterface $statusUpdatedAt): void
    {
        $this->statusUpdatedAt = $statusUpdatedAt;
    }

    /**
     * Get the value of monthlyPrice
     *
     * @return ?int The value of monthlyPrice
     */
    public function getMonthlyPrice(): ?int
    {
        return $this->monthlyPrice;
    }

    /**
     * Set the value of monthlyPrice
     *
     * @param ?int $monthlyPrice The value for monthlyPrice
     *
     * @return void
     */
    public function setMonthlyPrice(?int $monthlyPrice): void
    {
        $this->monthlyPrice = $monthlyPrice;
    }

    /**
     * Get the value of monthlyPriceUpdatedAt
     *
     * @return ?DateTimeInterface The value of monthlyPriceUpdatedAt
     */
    public function getMonthlyPriceUpdatedAt(): ?DateTimeInterface
    {
        return $this->monthlyPriceUpdatedAt;
    }

    /**
     * Set the value of monthlyPriceUpdatedAt
     *
     * @param ?DateTimeInterface $monthlyPriceUpdatedAt The value for monthlyPriceUpdatedAt
     *
     * @return void
     */
    public function setMonthlyPriceUpdatedAt(?DateTimeInterface $monthlyPriceUpdatedAt): void
    {
        $this->monthlyPriceUpdatedAt = $monthlyPriceUpdatedAt;
    }
}
