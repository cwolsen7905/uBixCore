<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\User\UserStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a user for authentication
 */
final class User extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                  User ID
     * @param ?string            $displayName         Display name
     * @param ?string            $passwordHash        Password hash
     * @param ?string            $email               Email address
     * @param ?string            $firstName           First name
     * @param ?string            $lastName            Last name
     * @param ?UserStatus        $status              User status
     * @param ?string            $roles               User roles
     * @param ?int               $failedLoginAttempts Failed login attempts count
     * @param ?DateTimeInterface $lastFailedLogin     Last failed login datetime
     * @param ?DateTimeInterface $lastLogin           Last login datetime
     * @param ?DateTimeInterface $createdAt           Created datetime
     * @param ?DateTimeInterface $updatedAt           Updated datetime
     */
    public function __construct(
        private ?int $id = null,
        private ?string $displayName = null,
        private ?string $passwordHash = null,
        private ?string $email = null,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?UserStatus $status = null,
        private ?string $roles = null,
        private ?int $failedLoginAttempts = null,
        private ?DateTimeInterface $lastFailedLogin = null,
        private ?DateTimeInterface $lastLogin = null,
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
     * @param ?int $id The value for id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of displayName
     *
     * @return ?string The value of displayName
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * Set the value of displayName
     *
     * @param ?string $displayName The value for displayName
     *
     * @return void
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * Get the value of passwordHash
     *
     * @return ?string The value of passwordHash
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Set the value of passwordHash
     *
     * @param ?string $passwordHash The value for passwordHash
     *
     * @return void
     */
    public function setPasswordHash(?string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Get the value of email
     *
     * @return ?string The value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param ?string $email The value for email
     *
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get the value of firstName
     *
     * @return ?string The value of firstName
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param ?string $firstName The value for firstName
     *
     * @return void
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * Get the value of lastName
     *
     * @return ?string The value of lastName
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param ?string $lastName The value for lastName
     *
     * @return void
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * Get the value of status
     *
     * @return ?UserStatus The value of status
     */
    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?UserStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?UserStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of roles
     *
     * @return ?string The value of roles
     */
    public function getRoles(): ?string
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @param ?string $roles The value for roles
     *
     * @return void
     */
    public function setRoles(?string $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Get the value of failedLoginAttempts
     *
     * @return ?int The value of failedLoginAttempts
     */
    public function getFailedLoginAttempts(): ?int
    {
        return $this->failedLoginAttempts;
    }

    /**
     * Set the value of failedLoginAttempts
     *
     * @param ?int $failedLoginAttempts The value for failedLoginAttempts
     *
     * @return void
     */
    public function setFailedLoginAttempts(?int $failedLoginAttempts): void
    {
        $this->failedLoginAttempts = $failedLoginAttempts;
    }

    /**
     * Get the value of lastFailedLogin
     *
     * @return ?DateTimeInterface The value of lastFailedLogin
     */
    public function getLastFailedLogin(): ?DateTimeInterface
    {
        return $this->lastFailedLogin;
    }

    /**
     * Set the value of lastFailedLogin
     *
     * @param ?DateTimeInterface $lastFailedLogin The value for lastFailedLogin
     *
     * @return void
     */
    public function setLastFailedLogin(?DateTimeInterface $lastFailedLogin): void
    {
        $this->lastFailedLogin = $lastFailedLogin;
    }

    /**
     * Get the value of lastLogin
     *
     * @return ?DateTimeInterface The value of lastLogin
     */
    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    /**
     * Set the value of lastLogin
     *
     * @param ?DateTimeInterface $lastLogin The value for lastLogin
     *
     * @return void
     */
    public function setLastLogin(?DateTimeInterface $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
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
     * @param ?DateTimeInterface $createdAt The value for createdAt
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
     * @param ?DateTimeInterface $updatedAt The value for updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
