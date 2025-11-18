<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;
use Ubix\Model\AccountInterface as Account;

/**
 * Model of a pending platform user
 *
 * @see \Ubix\Tests\Model\PendingPlatformUserTest PHPUnit test case
 */
final class PendingPlatformUser extends Model implements Account
{
    private const BCRYPT_ROUNDS_PASSWORD = 10; // 10 is the PHP default

    /**
     * Constructor
     *
     * @param ?int               $id           Placeholder for id // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $username     Placeholder for username // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $password     Placeholder for password // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $salt         Placeholder for salt // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $authKey      Placeholder for authKey // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $email        Placeholder for email // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $firstname    Placeholder for firstname // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $lastname     Placeholder for lastname // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $country      Placeholder for country // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $state        Placeholder for state // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $mpCode       Placeholder for mpCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $sitekey      Placeholder for sitekey // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $domain       Placeholder for domain // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $productCode  Placeholder for productCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $service      Placeholder for service // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $modelId      Placeholder for modelId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $ipAddress    Placeholder for ipAddress // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $sourceSiteId Placeholder for sourceSiteId // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateCreated  Placeholder for dateCreated // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $reminderSent Placeholder for reminderSent // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $updateCnt    Placeholder for updateCnt // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $iovationBb   Placeholder for iovationBb // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $language     Placeholder for language // NOT_IMPLEMENTED: replace placeholder
     */
    public function __construct(
        private ?int $id = null,
        private ?string $username = null,
        private ?string $password = null,
        private ?string $salt = null,
        private ?string $authKey = null,
        private ?string $email = null,
        private ?string $firstname = null,
        private ?string $lastname = null,
        private ?string $country = null,
        private ?string $state = null,
        private ?string $mpCode = null,
        private ?string $sitekey = null,
        private ?string $domain = null,
        private ?int $productCode = null,
        private ?string $service = null,
        private ?int $modelId = null,
        private ?string $ipAddress = null,
        private ?int $sourceSiteId = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?int $reminderSent = null,
        private ?int $updateCnt = null,
        private ?string $iovationBb = null,
        private ?string $language = null,
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
     * Get the value of salt
     *
     * @return ?string The value of salt
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * Set the value of salt
     *
     * @param ?string $salt The value for salt
     *
     * @return void
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
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
     * Get the value of firstname
     *
     * @return ?string The value of firstname
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @param ?string $firstname The value for firstname
     *
     * @return void
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * Get the value of lastname
     *
     * @return ?string The value of lastname
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @param ?string $lastname The value for lastname
     *
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
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
     * Get the value of mpCode
     *
     * @return ?string The value of mpCode
     */
    public function getMpCode(): ?string
    {
        return $this->mpCode;
    }

    /**
     * Set the value of mpCode
     *
     * @param ?string $mpCode The value for mpCode
     *
     * @return void
     */
    public function setMpCode(?string $mpCode): void
    {
        $this->mpCode = $mpCode;
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
     * Get the value of productCode
     *
     * @return ?int The value of productCode
     */
    public function getProductCode(): ?int
    {
        return $this->productCode;
    }

    /**
     * Set the value of productCode
     *
     * @param ?int $productCode The value for productCode
     *
     * @return void
     */
    public function setProductCode(?int $productCode): void
    {
        $this->productCode = $productCode;
    }

    /**
     * Get the value of service
     *
     * @return ?string The value of service
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @param ?string $service The value for service
     *
     * @return void
     */
    public function setService(?string $service): void
    {
        $this->service = $service;
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
     * Get the value of sourceSiteId
     *
     * @return ?int The value of sourceSiteId
     */
    public function getSourceSiteId(): ?int
    {
        return $this->sourceSiteId;
    }

    /**
     * Set the value of sourceSiteId
     *
     * @param ?int $sourceSiteId The value for sourceSiteId
     *
     * @return void
     */
    public function setSourceSiteId(?int $sourceSiteId): void
    {
        $this->sourceSiteId = $sourceSiteId;
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

    /**
     * Get the value of reminderSent
     *
     * @return ?int The value of reminderSent
     */
    public function getReminderSent(): ?int
    {
        return $this->reminderSent;
    }

    /**
     * Set the value of reminderSent
     *
     * @param ?int $reminderSent The value for reminderSent
     *
     * @return void
     */
    public function setReminderSent(?int $reminderSent): void
    {
        $this->reminderSent = $reminderSent;
    }

    /**
     * Get the value of updateCnt
     *
     * @return ?int The value of updateCnt
     */
    public function getUpdateCnt(): ?int
    {
        return $this->updateCnt;
    }

    /**
     * Set the value of updateCnt
     *
     * @param ?int $updateCnt The value for updateCnt
     *
     * @return void
     */
    public function setUpdateCnt(?int $updateCnt): void
    {
        $this->updateCnt = $updateCnt;
    }

    /**
     * Get the value of iovationBb
     *
     * @return ?string The value of iovationBb
     */
    public function getIovationBb(): ?string
    {
        return $this->iovationBb;
    }

    /**
     * Set the value of iovationBb
     *
     * @param ?string $iovationBb The value for iovationBb
     *
     * @return void
     */
    public function setIovationBb(?string $iovationBb): void
    {
        $this->iovationBb = $iovationBb;
    }

    /**
     * Get the value of language
     *
     * @return ?string The value of language
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @param ?string $language The value for language
     *
     * @return void
     */
    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    /**
     * {@inheritDoc}
     */
    public function login(string $password): bool
    {
        return $this->validatePassword($password);
    }

    /**
     * {@inheritDoc}
     */
    public function autoLogin(): bool
    {
        return false;
    }

    /**
     * Check if a plaintext password is valid for the platform user
     *
     * @param string $password A plaintext password to validate
     *
     * @return bool Returns true for a validate password or false if the password is invalid
     */
    private function validatePassword(string $password): bool
    {
        //
        //  Check for an exact match
        //
        if ($password === $this->password) {
            return true;
        }

        //
        //  Check for a match using an MD5 hash
        //
        if ($this->password !== null && $password === md5($this->password)) {
            return true;
        }

        //
        //  Check for a match using an SHA1 hash
        //
        if ($this->salt !== null && $this->password === sha1($this->salt . sha1($password))) { // Yes, we double hash with SHA1 - this is how the legacy system was designed
            return true;
        }

        //
        //  Check for a match using a bcrypt hash
        //
        return $this->password !== null && $password === password_hash($this->password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_ROUNDS_PASSWORD]);
    }
}
