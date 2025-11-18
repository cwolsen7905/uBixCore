<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a screen name
 *
 * @see \Ubix\Tests\Model\ScreenNameTest PHPUnit test case
 */
final class ScreenName extends Model
{
    public const STATUS_ACTIVE   = 0;
    public const STATUS_INACTIVE = 1;
    public const STATUS_HIDDEN   = 2;

    /**
     * Constructor
     *
     * @param ?int               $id                    The screen name's ID
     * @param ?string            $screenName            The screen name
     * @param ?string            $screenNameLower       The screen name in lowercase
     * @param ?string            $password              Placeholder for password
     * @param ?int               $optiusersId           The platform user connected to the screen name's ID
     * @param ?string            $reconciled            Placeholder for reconciled
     * @param ?string            $chatDefault           Placeholder for chatDefault
     * @param ?int               $setRewardsStatus      Placeholder for setRewardsStatus
     * @param ?int               $setRewardsLevel       Placeholder for setRewardsLevel
     * @param ?string            $showPerformersRewards Placeholder for showPerformersRewards
     * @param ?string            $showCustomersRewards  Placeholder for showCustomersRewards
     * @param ?DateTimeInterface $timestamp             Timestamp of the screen name creation
     * @param ?int               $anonymousStatus       Placeholder for anonymousStatus
     * @param ?int               $status                The screen name's status
     * @param ?string            $dmOnline              Placeholder for dmOnline
     */
    public function __construct(
        private ?int $id = null,
        private ?string $screenName = null,
        private ?string $screenNameLower = null,
        private ?string $password = null,
        private ?int $optiusersId = null,
        private ?string $reconciled = null,
        private ?string $chatDefault = null,
        private ?int $setRewardsStatus = null,
        private ?int $setRewardsLevel = null,
        private ?string $showPerformersRewards = null,
        private ?string $showCustomersRewards = null,
        private ?DateTimeInterface $timestamp = null,
        private ?int $anonymousStatus = null,
        private ?int $status = null,
        private ?string $dmOnline = null,
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
     * Get the value of screenName
     *
     * @return ?string The value of screenName
     */
    public function getScreenName(): ?string
    {
        return $this->screenName;
    }

    /**
     * Set the value of screenName
     *
     * @param ?string $screenName The value for screenName
     *
     * @return void
     */
    public function setScreenName(?string $screenName): void
    {
        $this->screenName = $screenName;
    }

    /**
     * Get the value of screenNameLower
     *
     * @return ?string The value of screenNameLower
     */
    public function getScreenNameLower(): ?string
    {
        return $this->screenNameLower;
    }

    /**
     * Set the value of screenNameLower
     *
     * @param ?string $screenNameLower The value for screenNameLower
     *
     * @return void
     */
    public function setScreenNameLower(?string $screenNameLower): void
    {
        $this->screenNameLower = $screenNameLower;
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
     * Get the value of optiusersId
     *
     * @return ?int The value of optiusersId
     */
    public function getOptiusersId(): ?int
    {
        return $this->optiusersId;
    }

    /**
     * Set the value of optiusersId
     *
     * @param ?int $optiusersId The value for optiusersId
     *
     * @return void
     */
    public function setOptiusersId(?int $optiusersId): void
    {
        $this->optiusersId = $optiusersId;
    }

    /**
     * Get the value of reconciled
     *
     * @return ?string The value of reconciled
     */
    public function getReconciled(): ?string
    {
        return $this->reconciled;
    }

    /**
     * Set the value of reconciled
     *
     * @param ?string $reconciled The value for reconciled
     *
     * @return void
     */
    public function setReconciled(?string $reconciled): void
    {
        $this->reconciled = $reconciled;
    }

    /**
     * Get the value of chatDefault
     *
     * @return ?string The value of chatDefault
     */
    public function getChatDefault(): ?string
    {
        return $this->chatDefault;
    }

    /**
     * Set the value of chatDefault
     *
     * @param ?string $chatDefault The value for chatDefault
     *
     * @return void
     */
    public function setChatDefault(?string $chatDefault): void
    {
        $this->chatDefault = $chatDefault;
    }

    /**
     * Get the value of setRewardsStatus
     *
     * @return ?int The value of setRewardsStatus
     */
    public function getSetRewardsStatus(): ?int
    {
        return $this->setRewardsStatus;
    }

    /**
     * Set the value of setRewardsStatus
     *
     * @param ?int $setRewardsStatus The value for setRewardsStatus
     *
     * @return void
     */
    public function setSetRewardsStatus(?int $setRewardsStatus): void
    {
        $this->setRewardsStatus = $setRewardsStatus;
    }

    /**
     * Get the value of setRewardsLevel
     *
     * @return ?int The value of setRewardsLevel
     */
    public function getSetRewardsLevel(): ?int
    {
        return $this->setRewardsLevel;
    }

    /**
     * Set the value of setRewardsLevel
     *
     * @param ?int $setRewardsLevel The value for setRewardsLevel
     *
     * @return void
     */
    public function setSetRewardsLevel(?int $setRewardsLevel): void
    {
        $this->setRewardsLevel = $setRewardsLevel;
    }

    /**
     * Get the value of showPerformersRewards
     *
     * @return ?string The value of showPerformersRewards
     */
    public function getShowPerformersRewards(): ?string
    {
        return $this->showPerformersRewards;
    }

    /**
     * Set the value of showPerformersRewards
     *
     * @param ?string $showPerformersRewards The value for showPerformersRewards
     *
     * @return void
     */
    public function setShowPerformersRewards(?string $showPerformersRewards): void
    {
        $this->showPerformersRewards = $showPerformersRewards;
    }

    /**
     * Get the value of showCustomersRewards
     *
     * @return ?string The value of showCustomersRewards
     */
    public function getShowCustomersRewards(): ?string
    {
        return $this->showCustomersRewards;
    }

    /**
     * Set the value of showCustomersRewards
     *
     * @param ?string $showCustomersRewards The value for showCustomersRewards
     *
     * @return void
     */
    public function setShowCustomersRewards(?string $showCustomersRewards): void
    {
        $this->showCustomersRewards = $showCustomersRewards;
    }

    /**
     * Get the value of timestamp
     *
     * @return ?DateTimeInterface The value of timestamp
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * Set the value of timestamp
     *
     * @param ?DateTimeInterface $timestamp The value for timestamp
     *
     * @return void
     */
    public function setTimestamp(?DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Get the value of anonymousStatus
     *
     * @return ?int The value of anonymousStatus
     */
    public function getAnonymousStatus(): ?int
    {
        return $this->anonymousStatus;
    }

    /**
     * Set the value of anonymousStatus
     *
     * @param ?int $anonymousStatus The value for anonymousStatus
     *
     * @return void
     */
    public function setAnonymousStatus(?int $anonymousStatus): void
    {
        $this->anonymousStatus = $anonymousStatus;
    }

    /**
     * Get the value of status
     *
     * @return ?int The value of status
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?int $status The value for status
     *
     * @return void
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of dmOnline
     *
     * @return ?string The value of dmOnline
     */
    public function getDmOnline(): ?string
    {
        return $this->dmOnline;
    }

    /**
     * Set the value of dmOnline
     *
     * @param ?string $dmOnline The value for dmOnline
     *
     * @return void
     */
    public function setDmOnline(?string $dmOnline): void
    {
        $this->dmOnline = $dmOnline;
    }
}
