<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform user two factor authentication (2fa) entry
 *
 * @see \Ubix\Tests\Model\PlatformUserTwoFactorAuthenticationTest PHPUnit test case
 */
final class PlatformUserTwoFactorAuthentication extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id          The 2fa's ID
     * @param ?int               $userId      The platform user's ID
     * @param ?string            $authMethod  The authentication method
     * @param ?string            $label       The 2fa's label
     * @param ?string            $authKey     The 2fa's auth key
     * @param ?string            $authValue   The 2fa's auth value
     * @param ?DateTimeInterface $dateCreated The 2fa's creation date
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?string $authMethod = null,
        private ?string $label = null,
        private ?string $authKey = null,
        private ?string $authValue = null,
        private ?DateTimeInterface $dateCreated = null,
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
     * Get the value of authMethod
     *
     * @return ?string The value of authMethod
     */
    public function getAuthMethod(): ?string
    {
        return $this->authMethod;
    }

    /**
     * Set the value of authMethod
     *
     * @param ?string $authMethod The value for authMethod
     *
     * @return void
     */
    public function setAuthMethod(?string $authMethod): void
    {
        $this->authMethod = $authMethod;
    }

    /**
     * Get the value of label
     *
     * @return ?string The value of label
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param ?string $label The value for label
     *
     * @return void
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
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
     * Get the value of authValue
     *
     * @return ?string The value of authValue
     */
    public function getAuthValue(): ?string
    {
        return $this->authValue;
    }

    /**
     * Set the value of authValue
     *
     * @param ?string $authValue The value for authValue
     *
     * @return void
     */
    public function setAuthValue(?string $authValue): void
    {
        $this->authValue = $authValue;
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
}
