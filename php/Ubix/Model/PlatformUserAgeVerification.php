<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform user age verification
 *
 * @see \Ubix\Tests\Model\PlatformUserAgeVerificationTest PHPUnit test case
 */
final class PlatformUserAgeVerification extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                The verification's ID
     * @param ?int               $userId            The platform user's ID
     * @param ?string            $country           The country code
     * @param ?string            $state             The state code
     * @param ?DateTimeInterface $dateVerified      The date the verification occured
     * @param ?string            $method            The method of validation
     * @param ?string            $avsRegulation     The AVS regulation code
     * @param ?string            $verificationToken The verification token
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?string $country = null,
        private ?string $state = null,
        private ?DateTimeInterface $dateVerified = null,
        private ?string $method = null,
        private ?string $avsRegulation = null,
        private ?string $verificationToken = null,
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
     * Get the value of userId
     *
     * @return ?int The value of userId
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param ?int $userId The value for userId
     *
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get the value of country
     *
     * @return ?string The value of country
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @param ?string $country The value for country
     *
     * @return void
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
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
     * Get the value of dateVerified
     *
     * @return ?DateTimeInterface The value of dateVerified
     */
    public function getDateVerified(): ?DateTimeInterface
    {
        return $this->dateVerified;
    }

    /**
     * Set the value of dateVerified
     *
     * @param ?DateTimeInterface $dateVerified The value for dateVerified
     *
     * @return void
     */
    public function setDateVerified(?DateTimeInterface $dateVerified): void
    {
        $this->dateVerified = $dateVerified;
    }

    /**
     * Get the value of method
     *
     * @return ?string The value of method
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @param ?string $method The value for method
     *
     * @return void
     */
    public function setMethod(?string $method): void
    {
        $this->method = $method;
    }

    /**
     * Get the value of avsRegulation
     *
     * @return ?string The value of avsRegulation
     */
    public function getAvsRegulation(): ?string
    {
        return $this->avsRegulation;
    }

    /**
     * Set the value of avsRegulation
     *
     * @param ?string $avsRegulation The value for avsRegulation
     *
     * @return void
     */
    public function setAvsRegulation(?string $avsRegulation): void
    {
        $this->avsRegulation = $avsRegulation;
    }

    /**
     * Get the value of verificationToken
     *
     * @return ?string The value of verificationToken
     */
    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    /**
     * Set the value of verificationToken
     *
     * @param ?string $verificationToken The value for verificationToken
     *
     * @return void
     */
    public function setVerificationToken(?string $verificationToken): void
    {
        $this->verificationToken = $verificationToken;
    }
}
