<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform user login attempt
 *
 * @see \Ubix\Tests\Model\PlatformUserLoginAttemptTest PHPUnit test case
 */
final class PlatformUserLoginAttempt extends Model
{
    public const LOGIN_TYPE_PROJECT_NEPTUNE = 'neptune';

    // NOTE: Fail codes 1 and 13 appear to have been deprecated and were not included in the legacy code (or present in the database) at the time this code was ported
    public const FAIL_CODE_1CLICK_DOMAIN_MISMATCH = 8;
    public const FAIL_CODE_2FA_FAILED             = 12;
    public const FAIL_CODE_INCORRECT_PASSWORD     = 5;
    public const FAIL_CODE_INVALID_DOMAIN         = 14;
    public const FAIL_CODE_INVALID_FORM_DATA      = 2; // This fail code is being carried over from the legacy system but will never be invoked due to Project Neptune being a typesafe environment
    public const FAIL_CODE_INVALID_STATUS         = 4;
    public const FAIL_CODE_IOVATION_INVALID       = 9; // NOT_IMPLEMENTED: In legacy this exists on line 1005 of /home/webservices/vs3.com/ws/users/login.php
    public const FAIL_CODE_IOVATION_LIMIT         = 10; // NOT_IMPLEMENTED: In legacy this exists on line 2833 of /home/_includes/lib_php1.0.1/core/Databases.cl
    public const FAIL_CODE_IOVATION_MISSING       = 11; // NOT_IMPLEMENTED: In legacy this exists on line 944 of /home/webservices/vs3.com/ws/users/login.php
    public const FAIL_CODE_IP_ADDRESS_BLOCK       = 7;
    public const FAIL_CODE_PENDING_CONFIRMATION   = 3;
    public const FAIL_CODE_SUCCESS                = 0;
    public const FAIL_CODE_USERNAME_BLOCK         = 6;

    /**
     * Constructor
     *
     * @param ?int               $id        The login attempt ID
     * @param ?string            $username  The username on the login attempt
     * @param ?string            $password  The password on the login attempt (* This is no longer recording plaintext passwords and is instead a general logging field)
     * @param ?string            $ipAddress The IP address on the login attempt
     * @param ?DateTimeInterface $datetime  The date/time of the login attempt
     * @param ?int               $failCode  The fail code of the login attempt
     * @param ?string            $domain    The domain of the login attempt
     * @param ?string            $sitekey   The sitekey of the login attempt
     * @param ?string            $loginType The login type of the login attempt
     * @param ?string            $requestId The request ID of the login attempt
     */
    public function __construct(
        private ?int $id = null,
        private ?string $username = null,
        private ?string $password = null,
        private ?string $ipAddress = null,
        private ?DateTimeInterface $datetime = null,
        private ?int $failCode = null,
        private ?string $domain = null,
        private ?string $sitekey = null,
        private ?string $loginType = null,
        private ?string $requestId = null,
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
     * Get the value of datetime
     *
     * @return ?DateTimeInterface The value of datetime
     */
    public function getDatetime(): ?DateTimeInterface
    {
        return $this->datetime;
    }

    /**
     * Set the value of datetime
     *
     * @param ?DateTimeInterface $datetime The value for datetime
     *
     * @return void
     */
    public function setDatetime(?DateTimeInterface $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * Get the value of failCode
     *
     * @return ?int The value of failCode
     */
    public function getFailCode(): ?int
    {
        return $this->failCode;
    }

    /**
     * Set the value of failCode
     *
     * @param ?int $failCode The value for failCode
     *
     * @return void
     */
    public function setFailCode(?int $failCode): void
    {
        $this->failCode = $failCode;
    }

    /**
     * Get the value of domain
     *
     * @return ?string The value of domain
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Set the value of domain
     *
     * @param ?string $domain The value for domain
     *
     * @return void
     */
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Get the value of sitekey
     *
     * @return ?string The value of sitekey
     */
    public function getSitekey(): ?string
    {
        return $this->sitekey;
    }

    /**
     * Set the value of sitekey
     *
     * @param ?string $sitekey The value for sitekey
     *
     * @return void
     */
    public function setSitekey(?string $sitekey): void
    {
        $this->sitekey = $sitekey;
    }

    /**
     * Get the value of loginType
     *
     * @return ?string The value of loginType
     */
    public function getLoginType(): ?string
    {
        return $this->loginType;
    }

    /**
     * Set the value of loginType
     *
     * @param ?string $loginType The value for loginType
     *
     * @return void
     */
    public function setLoginType(?string $loginType): void
    {
        $this->loginType = $loginType;
    }

    /**
     * Get the value of requestId
     *
     * @return ?string The value of requestId
     */
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * Set the value of requestId
     *
     * @param ?string $requestId The value for requestId
     *
     * @return void
     */
    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
    }
}
