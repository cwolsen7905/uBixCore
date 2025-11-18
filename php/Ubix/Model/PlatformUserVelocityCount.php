<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform user velocity count
 *
 * @see \Ubix\Tests\Model\PlatformUserVelocityCountTest PHPUnit test case
 */
final class PlatformUserVelocityCount extends Model
{
    /**
     * Constructor
     *
     * @param ?string $ipAddress      The IP address
     * @param ?int    $ipAddressCount The IP address velocity count
     * @param ?string $username       The username
     * @param ?int    $usernameCount  The username velocity count
     */
    public function __construct(
        private ?string $ipAddress = null,
        private ?int $ipAddressCount = null,
        private ?string $username = null,
        private ?int $usernameCount = null,
    ) {
    }

    /**
     * Get the value of ipAddress
     *
     * @return ?string The value of ipAddress
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * Set the value of ipAddress
     *
     * @param ?string $ipAddress The value for ipAddress
     *
     * @return void
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the value of ipAddressCount
     *
     * @return ?int The value of ipAddressCount
     */
    public function getIpAddressCount(): ?int
    {
        return $this->ipAddressCount;
    }

    /**
     * Set the value of ipAddressCount
     *
     * @param ?int $ipAddressCount The value for ipAddressCount
     *
     * @return void
     */
    public function setIpAddressCount(?int $ipAddressCount): void
    {
        $this->ipAddressCount = $ipAddressCount;
    }

    /**
     * Get the value of username
     *
     * @return ?string The value of username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @param ?string $username The value for username
     *
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * Get the value of usernameCount
     *
     * @return ?int The value of usernameCount
     */
    public function getUsernameCount(): ?int
    {
        return $this->usernameCount;
    }

    /**
     * Set the value of usernameCount
     *
     * @param ?int $usernameCount The value for usernameCount
     *
     * @return void
     */
    public function setUsernameCount(?int $usernameCount): void
    {
        $this->usernameCount = $usernameCount;
    }
}
