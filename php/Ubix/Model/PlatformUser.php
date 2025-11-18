<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\AbstractModel as Model;
use Ubix\Model\AccountInterface as Account;

/**
 * Model of a platform user
 *
 * @see \Ubix\Tests\Model\PlatformUserTest PHPUnit test case
 */
final class PlatformUser extends Model implements Account
{
    public const STATUS_ACTIVE               = 'A';
    public const STATUS_BAD_BEHAVIOR         = 'B';
    public const STATUS_COMPROMISED          = 'C';
    public const STATUS_FRAUD_CLAIM          = 'F';
    public const STATUS_PENDING_VERIFICATION = 'P';
    public const STATUS_RISK_ASSESSMENT      = 'R';
    public const STATUS_USER_IMPOSED         = 'U';
    public const STATUS_GDPR_FORGOTTEN_USER  = 'X';

    private const BCRYPT_ROUNDS_PASSWORD = 10; // 10 is the PHP default

    /**
     * Constructor
     *
     * @param ?int                                 $userId                  Placeholder for userId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $username                Placeholder for username // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $password                Placeholder for password // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $salt                    Placeholder for salt // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $firstname               Placeholder for firstname // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $lastname                Placeholder for lastname // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $zip                     Placeholder for zip // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $email                   Placeholder for email // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $mpCode                  Placeholder for mpCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $domain                  Placeholder for domain // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $sitekey                 Placeholder for sitekey // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $activeDomain            Placeholder for activeDomain // NOT_IMPLEMENTED: replace placeholder
     * @param ?int                                 $timeleft                Placeholder for timeleft // NOT_IMPLEMENTED: replace placeholder
     * @param ?int                                 $timeleftBanked          Placeholder for timeleftBanked // NOT_IMPLEMENTED: replace placeholder
     * @param ?float                               $totalSpent              Placeholder for totalSpent // NOT_IMPLEMENTED: replace placeholder
     * @param ?float                               $billNext                Placeholder for billNext // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $vsVip                   Placeholder for vsVip // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $status                  Placeholder for status // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $bountyPaid              Placeholder for platformWhiteLabel // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface                   $dateCreated             Placeholder for dateCreated // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $spendingGroup           Placeholder for spendingGroup // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $ageVerified             Placeholder for ageVerified // NOT_IMPLEMENTED: replace placeholder
     * @param ?bool                                $paymentRecord           Placeholder for paymentRecord // NOT_IMPLEMENTED: replace placeholder
     * @param ?int                                 $oneclickProcessorId     Placeholder for oneclickProcessorId // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface                   $firstPurchaseDate       Placeholder for firstPurchaseDate // NOT_IMPLEMENTED: replace placeholder
     * @param ?string                              $screenName              Placeholder for screenName // NOT_IMPLEMENTED: replace placeholder
     * @param ?bool                                $hasXvtAccount           Placeholder for hasXvtAccount // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface                   $dateLastLogin           Placeholder for dateLastLogin // NOT_IMPLEMENTED: replace placeholder
     * @param ?ScreenName[]                        $screenNames             Placeholder for screenNames // NOT_IMPLEMENTED: replace placeholder
     * @param ?PlatformUserTwoFactorAuthentication $twoFactorAuthentication Placeholder for twoFactorAuthentication // NOT_IMPLEMENTED: replace placeholder
     * @param ?PlatformWhiteLabel                  $platformWhiteLabel      Placeholder for platformWhiteLabel // NOT_IMPLEMENTED: replace placeholder
     */
    public function __construct(
        private ?int $userId = null,
        private ?string $username = null,
        private ?string $password = null,
        private ?string $salt = null,
        private ?string $firstname = null,
        private ?string $lastname = null,
        private ?string $zip = null,
        private ?string $email = null,
        private ?string $mpCode = null,
        private ?string $domain = null,
        private ?string $sitekey = null,
        private ?string $activeDomain = null,
        private ?int $timeleft = null,
        private ?int $timeleftBanked = null,
        private ?float $totalSpent = null,
        private ?float $billNext = null,
        private ?string $vsVip = null,
        private ?string $status = null,
        private ?string $bountyPaid = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?string $spendingGroup = null,
        private ?string $ageVerified = null,
        private ?bool $paymentRecord = null,
        private ?int $oneclickProcessorId = null,
        private ?DateTimeInterface $firstPurchaseDate = null,
        private ?string $screenName = null,
        private ?bool $hasXvtAccount = null,
        private ?DateTimeInterface $dateLastLogin = null,
        private ?array $screenNames = null,
        private ?PlatformUserTwoFactorAuthentication $twoFactorAuthentication = null,
        private ?PlatformWhiteLabel $platformWhiteLabel = null,
    ) {
    }

    /**
     * This is an alias until we can rename `ntl_db.optiusers.userId`
     *
     * @return ?int The value of userId
     */
    public function getId(): ?int
    {
        return $this->userId;
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
     * Get the value of timeleft
     *
     * @return ?int The value of timeleft
     */
    public function getTimeleft(): ?int
    {
        return $this->timeleft;
    }

    /**
     * Set the value of timeleft
     *
     * @param ?int $timeleft The value for timeleft
     *
     * @return void
     */
    public function setTimeleft(?int $timeleft): void
    {
        $this->timeleft = $timeleft;
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
     * Get the value of paymentRecord
     *
     * @return ?bool The value of paymentRecord
     */
    public function getPaymentRecord(): ?bool
    {
        return $this->paymentRecord;
    }

    /**
     * Set the value of paymentRecord
     *
     * @param ?bool $paymentRecord The value for paymentRecord
     *
     * @return void
     */
    public function setPaymentRecord(?bool $paymentRecord): void
    {
        $this->paymentRecord = $paymentRecord;
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
     * Get the value of firstname
     *
     * @return ?string The value of firstname
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Get the value of bountyPaid
     *
     * @return ?string The value of bountyPaid
     */
    public function getBountyPaid(): ?string
    {
        return $this->bountyPaid;
    }

    /**
     * Set the value of bountyPaid
     *
     * @param ?string $bountyPaid The value for bountyPaid
     *
     * @return void
     */
    public function setBountyPaid(?string $bountyPaid): void
    {
        $this->bountyPaid = $bountyPaid;
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
     * Get the value of zip
     *
     * @return ?string The value of zip
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * Set the value of zip
     *
     * @param ?string $zip The value for zip
     *
     * @return void
     */
    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
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
     * Get the value of active domain
     *
     * @return ?string The value of active domain
     */
    public function getActiveDomain(): ?string
    {
        return $this->activeDomain;
    }

    /**
     * Set the value of active domain
     *
     * @param ?string $activeDomain The value for active domain
     *
     * @return void
     */
    public function setActiveDomain(?string $activeDomain): void
    {
        $this->activeDomain = $activeDomain;
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
     * Get the value of timeleftBanked
     *
     * @return ?int The value of timeleftBanked
     */
    public function getTimeleftBanked(): ?int
    {
        return $this->timeleftBanked;
    }

    /**
     * Set the value of timeleftBanked
     *
     * @param ?int $timeleftBanked The value for timeleftBanked
     *
     * @return void
     */
    public function setTimeleftBanked(?int $timeleftBanked): void
    {
        $this->timeleftBanked = $timeleftBanked;
    }

    /**
     * Get the value of totalSpent
     *
     * @return ?float The value of totalSpent
     */
    public function getTotalSpent(): ?float
    {
        return $this->totalSpent;
    }

    /**
     * Set the value of totalSpent
     *
     * @param ?float $totalSpent The value for totalSpent
     *
     * @return void
     */
    public function setTotalSpent(?float $totalSpent): void
    {
        $this->totalSpent = $totalSpent;
    }

    /**
     * Get the value of billNext
     *
     * @return ?float The value of billNext
     */
    public function getBillNext(): ?float
    {
        return $this->billNext;
    }

    /**
     * Set the value of billNext
     *
     * @param ?float $billNext The value for billNext
     *
     * @return void
     */
    public function setBillNext(?float $billNext): void
    {
        $this->billNext = $billNext;
    }

    /**
     * Get the value of vsVip
     *
     * @return ?string The value of vsVip
     */
    public function getVsVip(): ?string
    {
        return $this->vsVip;
    }

    /**
     * Set the value of vsVip
     *
     * @param ?string $vsVip The value for vsVip
     *
     * @return void
     */
    public function setVsVip(?string $vsVip): void
    {
        $this->vsVip = $vsVip;
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
     * Get the value of spendingGroup
     *
     * @return ?string The value of spendingGroup
     */
    public function getSpendingGroup(): ?string
    {
        return $this->spendingGroup;
    }

    /**
     * Set the value of spendingGroup
     *
     * @param ?string $spendingGroup The value for spendingGroup
     *
     * @return void
     */
    public function setSpendingGroup(?string $spendingGroup): void
    {
        $this->spendingGroup = $spendingGroup;
    }

    /**
     * Get the value of ageVerified
     *
     * @return ?string The value of ageVerified
     */
    public function getAgeVerified(): ?string
    {
        return $this->ageVerified;
    }

    /**
     * Set the value of ageVerified
     *
     * @param ?string $ageVerified The value for ageVerified
     *
     * @return void
     */
    public function setAgeVerified(?string $ageVerified): void
    {
        $this->ageVerified = $ageVerified;
    }

    /**
     * Get the value of hasXvtAccount
     *
     * @return ?bool The value of hasXvtAccount
     */
    public function getHasXvtAccount(): ?bool
    {
        return $this->hasXvtAccount;
    }

    /**
     * Set the value of hasXvtAccount
     *
     * @param ?bool $hasXvtAccount The value for hasXvtAccount
     *
     * @return void
     */
    public function setHasXvtAccount(?bool $hasXvtAccount): void
    {
        $this->hasXvtAccount = $hasXvtAccount;
    }

    /**
     * Get the value of screenNames
     *
     * @return ScreenName[] The value of screenNames
     */
    public function getScreenNames(): ?array
    {
        return $this->screenNames;
    }

    /**
     * Set the value of screenNames
     *
     * @param ScreenName[] $screenNames The value for screenNames
     *
     * @return void
     */
    public function setScreenNames(?array $screenNames): void
    {
        $this->screenNames = $screenNames;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If a user is blocked based on status
     */
    public function login(string $password): bool
    {
        //
        //  Ensure the password passed is valid
        //
        if (!$this->validatePassword($password)) {
            return false;
        }

        //
        //  Throw an exception if the user has a flagged relating to the GDPR's right to be forgotten
        //
        if ($this->getStatus() === self::STATUS_GDPR_FORGOTTEN_USER) {
            throw new Exception('We were unable to log you into your account, please try again. (Error: 312)', ExceptionCode::GDPR_FORGOTTEN_PLATFORM_USER_STATUS->value); // I don't know what 312 is, I just copied the error messaging from the previous version of this code but it has no use within Project Neptune
        }

        //
        //  Throw an exception if the platform user's status isn't active
        //
        if ($this->getStatus() !== self::STATUS_ACTIVE) {
            throw new Exception('Your account has been blocked. Please contact customer support to resolve this issue. You may visit http://www.flirt4free.com/support.php for contact information.', ExceptionCode::INVALID_PLATFORM_USER_STATUS->value);
        }

        //
        //  If we made it this far allow the login
        //
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function autoLogin(): bool
    {
        //
        //  Ensure the user has a valid status
        //
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    /**
     * Get the value of twoFactorAuthentication
     *
     * @return ?PlatformUserTwoFactorAuthentication The value of twoFactorAuthentication
     */
    public function getTwoFactorAuthentication(): ?PlatformUserTwoFactorAuthentication
    {
        return $this->twoFactorAuthentication;
    }

    /**
     * Set the value of twoFactorAuthentication
     *
     * @param ?PlatformUserTwoFactorAuthentication $twoFactorAuthentication The value for twoFactorAuthentication
     *
     * @return void
     */
    public function setTwoFactorAuthentication(?PlatformUserTwoFactorAuthentication $twoFactorAuthentication): void
    {
        $this->twoFactorAuthentication = $twoFactorAuthentication;
    }

    /**
     * Get the value of platformWhiteLabel
     *
     * @return ?PlatformWhiteLabel The value of platformWhiteLabel
     */
    public function getPlatformWhiteLabel(): ?PlatformWhiteLabel
    {
        return $this->platformWhiteLabel;
    }

    /**
     * Set the value of platformWhiteLabel
     *
     * @param ?PlatformWhiteLabel $platformWhiteLabel The value for platformWhiteLabel
     *
     * @return void
     */
    public function setPlatformWhiteLabel(?PlatformWhiteLabel $platformWhiteLabel): void
    {
        $this->platformWhiteLabel = $platformWhiteLabel;
    }

    /**
     * Get the value of dateLastLogin
     *
     * @return ?DateTimeInterface The value of dateLastLogin
     */
    public function getDateLastLogin(): ?DateTimeInterface
    {
        return $this->dateLastLogin;
    }

    /**
     * Set the value of dateLastLogin
     *
     * @param ?DateTimeInterface $dateLastLogin The value for dateLastLogin
     *
     * @return void
     */
    public function setDateLastLogin(?DateTimeInterface $dateLastLogin): void
    {
        $this->dateLastLogin = $dateLastLogin;
    }

    /**
     * Get the value of oneclickProcessorId
     *
     * @return ?int The value of oneclickProcessorId
     */
    public function getOneclickProcessorId(): ?int
    {
        return $this->oneclickProcessorId;
    }

    /**
     * Set the value of oneclickProcessorId
     *
     * @param ?int $oneclickProcessorId The value for oneclickProcessorId
     *
     * @return void
     */
    public function setOneclickProcessorId(?int $oneclickProcessorId): void
    {
        $this->oneclickProcessorId = $oneclickProcessorId;
    }

    /**
     * Get the value of firstPurchaseDate
     *
     * @return ?DateTimeInterface The value of firstPurchaseDate
     */
    public function getFirstPurchaseDate(): ?DateTimeInterface
    {
        return $this->firstPurchaseDate;
    }

    /**
     * Set the value of firstPurchaseDate
     *
     * @param ?DateTimeInterface $firstPurchaseDate The value for firstPurchaseDate
     *
     * @return void
     */
    public function setFirstPurchaseDate(?DateTimeInterface $firstPurchaseDate): void
    {
        $this->firstPurchaseDate = $firstPurchaseDate;
    }

    /**
     * Check if the users account is protected in attribution to being within a week of registration
     *
     * @param string $transactionDate The date of the transaction to compare against, if not provided it will compare against now
     *
     * @return bool Returns true if the user is within week protection, false if beyond the week
     *
     * @throws Exception If dateCreated is null and transactionDate is provided
     */
    public function isWeekProtected(string $transactionDate = ''): bool
    {
        // If transaction date is provided, compare confirm_datetime to it
        if (!empty($transactionDate)) {
            $transaction = new DateTime($transactionDate);
            if ($this->dateCreated === null) {
                throw new Exception('dateCreated is null, cannot determine week protection', ExceptionCode::DATE_CREATED_NULL->value);
            }
            $dateCreated = new DateTime($this->dateCreated->format(DateTimeInterface::ATOM));
            $endRange    = $dateCreated->add(new DateInterval('P7D'));

            return $transaction >= $this->dateCreated && $transaction <= $endRange;
        }

        // Otherwise, fallback to comparing confirm_datetime to now
        $now          = new DateTime('now');
        $sevenDaysAgo = (clone $now)->sub(new DateInterval('P7D'));
        return $this->dateCreated >= $sevenDaysAgo && $this->dateCreated <= $now;
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
