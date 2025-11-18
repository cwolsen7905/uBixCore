<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\AdminUser\AdminUserStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a admin user
 *
 * @see \Ubix\Tests\Model\AdminUserTest PHPUnit test case
 */
final class AdminUser extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                             XXX
     * @param ?string            $name                           XXX
     * @param ?string            $username                       XXX
     * @param ?string            $forumUsername                  XXX
     * @param ?string            $password                       XXX
     * @param ?string            $encPassword                    XXX
     * @param ?string            $salt                           XXX
     * @param ?string            $twofaSecret                    XXX
     * @param ?string            $email                          XXX
     * @param ?string            $telephone                      XXX
     * @param ?string            $mailAddress                    XXX
     * @param ?string            $twitterHandle                  XXX
     * @param ?DateTimeInterface $date                           XXX
     * @param ?int               $canMonitor                     XXX
     * @param ?int               $vsMonitor                      XXX
     * @param ?string            $monitorUsername                XXX
     * @param ?int               $editable                       XXX
     * @param ?int               $solo                           XXX
     * @param ?string            $adult                          XXX
     * @param ?string            $vsAccountManager               XXX
     * @param ?string            $psychic                        XXX
     * @param ?DateTimeInterface $dateLastLogin                  XXX
     * @param ?DateTimeInterface $lastOpened                     XXX
     * @param ?DateTimeInterface $lastClicked                    XXX
     * @param ?AdminUserStatus   $status                         XXX
     * @param ?DateTimeInterface $cookiePolicyAcceptanceDatetime XXX
     * @param ?string            $cookiePolicyAcceptanceIp       XXX
     * @param ?string            $cookiePolicyAcceptanceVersion  XXX
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?string $username = null,
        private ?string $forumUsername = null,
        private ?string $password = null,
        private ?string $encPassword = null,
        private ?string $salt = null,
        private ?string $twofaSecret = null,
        private ?string $email = null,
        private ?string $telephone = null,
        private ?string $mailAddress = null,
        private ?string $twitterHandle = null,
        private ?DateTimeInterface $date = null,
        private ?int $canMonitor = null,
        private ?int $vsMonitor = null,
        private ?string $monitorUsername = null,
        private ?int $editable = null,
        private ?int $solo = null,
        private ?string $adult = null,
        private ?string $vsAccountManager = null,
        private ?string $psychic = null,
        private ?DateTimeInterface $dateLastLogin = null,
        private ?DateTimeInterface $lastOpened = null,
        private ?DateTimeInterface $lastClicked = null,
        private ?AdminUserStatus $status = null,
        private ?DateTimeInterface $cookiePolicyAcceptanceDatetime = null,
        private ?string $cookiePolicyAcceptanceIp = null,
        private ?string $cookiePolicyAcceptanceVersion = null,
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
     * Get the value of name
     *
     * @return ?string The value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param ?string $name The value for name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
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
     * Get the value of forumUsername
     *
     * @return ?string The value of forumUsername
     */
    public function getForumUsername(): ?string
    {
        return $this->forumUsername;
    }

    /**
     * Set the value of forumUsername
     *
     * @param ?string $forumUsername The value for forumUsername
     *
     * @return void
     */
    public function setForumUsername(?string $forumUsername): void
    {
        $this->forumUsername = $forumUsername;
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
     * Get the value of encPassword
     *
     * @return ?string The value of encPassword
     */
    public function getEncPassword(): ?string
    {
        return $this->encPassword;
    }

    /**
     * Set the value of encPassword
     *
     * @param ?string $encPassword The value for encPassword
     *
     * @return void
     */
    public function setEncPassword(?string $encPassword): void
    {
        $this->encPassword = $encPassword;
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
     * Get the value of twofaSecret
     *
     * @return ?string The value of twofaSecret
     */
    public function getTwofaSecret(): ?string
    {
        return $this->twofaSecret;
    }

    /**
     * Set the value of twofaSecret
     *
     * @param ?string $twofaSecret The value for twofaSecret
     *
     * @return void
     */
    public function setTwofaSecret(?string $twofaSecret): void
    {
        $this->twofaSecret = $twofaSecret;
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
     * Get the value of telephone
     *
     * @return ?string The value of telephone
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @param ?string $telephone The value for telephone
     *
     * @return void
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * Get the value of mailAddress
     *
     * @return ?string The value of mailAddress
     */
    public function getMailAddress(): ?string
    {
        return $this->mailAddress;
    }

    /**
     * Set the value of mailAddress
     *
     * @param ?string $mailAddress The value for mailAddress
     *
     * @return void
     */
    public function setMailAddress(?string $mailAddress): void
    {
        $this->mailAddress = $mailAddress;
    }

    /**
     * Get the value of twitterHandle
     *
     * @return ?string The value of twitterHandle
     */
    public function getTwitterHandle(): ?string
    {
        return $this->twitterHandle;
    }

    /**
     * Set the value of twitterHandle
     *
     * @param ?string $twitterHandle The value for twitterHandle
     *
     * @return void
     */
    public function setTwitterHandle(?string $twitterHandle): void
    {
        $this->twitterHandle = $twitterHandle;
    }

    /**
     * Get the value of date
     *
     * @return ?DateTimeInterface The value of date
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @param ?DateTimeInterface $date The value for date
     *
     * @return void
     */
    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    /**
     * Get the value of canMonitor
     *
     * @return ?int The value of canMonitor
     */
    public function getCanMonitor(): ?int
    {
        return $this->canMonitor;
    }

    /**
     * Set the value of canMonitor
     *
     * @param ?int $canMonitor The value for canMonitor
     *
     * @return void
     */
    public function setCanMonitor(?int $canMonitor): void
    {
        $this->canMonitor = $canMonitor;
    }

    /**
     * Get the value of vsMonitor
     *
     * @return ?int The value of vsMonitor
     */
    public function getVsMonitor(): ?int
    {
        return $this->vsMonitor;
    }

    /**
     * Set the value of vsMonitor
     *
     * @param ?int $vsMonitor The value for vsMonitor
     *
     * @return void
     */
    public function setVsMonitor(?int $vsMonitor): void
    {
        $this->vsMonitor = $vsMonitor;
    }

    /**
     * Get the value of monitorUsername
     *
     * @return ?string The value of monitorUsername
     */
    public function getMonitorUsername(): ?string
    {
        return $this->monitorUsername;
    }

    /**
     * Set the value of monitorUsername
     *
     * @param ?string $monitorUsername The value for monitorUsername
     *
     * @return void
     */
    public function setMonitorUsername(?string $monitorUsername): void
    {
        $this->monitorUsername = $monitorUsername;
    }

    /**
     * Get the value of editable
     *
     * @return ?int The value of editable
     */
    public function getEditable(): ?int
    {
        return $this->editable;
    }

    /**
     * Set the value of editable
     *
     * @param ?int $editable The value for editable
     *
     * @return void
     */
    public function setEditable(?int $editable): void
    {
        $this->editable = $editable;
    }

    /**
     * Get the value of solo
     *
     * @return ?int The value of solo
     */
    public function getSolo(): ?int
    {
        return $this->solo;
    }

    /**
     * Set the value of solo
     *
     * @param ?int $solo The value for solo
     *
     * @return void
     */
    public function setSolo(?int $solo): void
    {
        $this->solo = $solo;
    }

    /**
     * Get the value of adult
     *
     * @return ?string The value of adult
     */
    public function getAdult(): ?string
    {
        return $this->adult;
    }

    /**
     * Set the value of adult
     *
     * @param ?string $adult The value for adult
     *
     * @return void
     */
    public function setAdult(?string $adult): void
    {
        $this->adult = $adult;
    }

    /**
     * Get the value of vsAccountManager
     *
     * @return ?string The value of vsAccountManager
     */
    public function getVsAccountManager(): ?string
    {
        return $this->vsAccountManager;
    }

    /**
     * Set the value of vsAccountManager
     *
     * @param ?string $vsAccountManager The value for vsAccountManager
     *
     * @return void
     */
    public function setVsAccountManager(?string $vsAccountManager): void
    {
        $this->vsAccountManager = $vsAccountManager;
    }

    /**
     * Get the value of psychic
     *
     * @return ?string The value of psychic
     */
    public function getPsychic(): ?string
    {
        return $this->psychic;
    }

    /**
     * Set the value of psychic
     *
     * @param ?string $psychic The value for psychic
     *
     * @return void
     */
    public function setPsychic(?string $psychic): void
    {
        $this->psychic = $psychic;
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
     * Get the value of lastOpened
     *
     * @return ?DateTimeInterface The value of lastOpened
     */
    public function getLastOpened(): ?DateTimeInterface
    {
        return $this->lastOpened;
    }

    /**
     * Set the value of lastOpened
     *
     * @param ?DateTimeInterface $lastOpened The value for lastOpened
     *
     * @return void
     */
    public function setLastOpened(?DateTimeInterface $lastOpened): void
    {
        $this->lastOpened = $lastOpened;
    }

    /**
     * Get the value of lastClicked
     *
     * @return ?DateTimeInterface The value of lastClicked
     */
    public function getLastClicked(): ?DateTimeInterface
    {
        return $this->lastClicked;
    }

    /**
     * Set the value of lastClicked
     *
     * @param ?DateTimeInterface $lastClicked The value for lastClicked
     *
     * @return void
     */
    public function setLastClicked(?DateTimeInterface $lastClicked): void
    {
        $this->lastClicked = $lastClicked;
    }

    /**
     * Get the value of status
     *
     * @return ?AdminUserStatus The value of status
     */
    public function getStatus(): ?AdminUserStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?AdminUserStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?AdminUserStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of cookiePolicyAcceptanceDatetime
     *
     * @return ?DateTimeInterface The value of cookiePolicyAcceptanceDatetime
     */
    public function getCookiePolicyAcceptanceDatetime(): ?DateTimeInterface
    {
        return $this->cookiePolicyAcceptanceDatetime;
    }

    /**
     * Set the value of cookiePolicyAcceptanceDatetime
     *
     * @param ?DateTimeInterface $cookiePolicyAcceptanceDatetime The value for cookiePolicyAcceptanceDatetime
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceDatetime(?DateTimeInterface $cookiePolicyAcceptanceDatetime): void
    {
        $this->cookiePolicyAcceptanceDatetime = $cookiePolicyAcceptanceDatetime;
    }

    /**
     * Get the value of cookiePolicyAcceptanceIp
     *
     * @return ?string The value of cookiePolicyAcceptanceIp
     */
    public function getCookiePolicyAcceptanceIp(): ?string
    {
        return $this->cookiePolicyAcceptanceIp;
    }

    /**
     * Set the value of cookiePolicyAcceptanceIp
     *
     * @param ?string $cookiePolicyAcceptanceIp The value for cookiePolicyAcceptanceIp
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceIp(?string $cookiePolicyAcceptanceIp): void
    {
        $this->cookiePolicyAcceptanceIp = $cookiePolicyAcceptanceIp;
    }

    /**
     * Get the value of cookiePolicyAcceptanceVersion
     *
     * @return ?string The value of cookiePolicyAcceptanceVersion
     */
    public function getCookiePolicyAcceptanceVersion(): ?string
    {
        return $this->cookiePolicyAcceptanceVersion;
    }

    /**
     * Set the value of cookiePolicyAcceptanceVersion
     *
     * @param ?string $cookiePolicyAcceptanceVersion The value for cookiePolicyAcceptanceVersion
     *
     * @return void
     */
    public function setCookiePolicyAcceptanceVersion(?string $cookiePolicyAcceptanceVersion): void
    {
        $this->cookiePolicyAcceptanceVersion = $cookiePolicyAcceptanceVersion;
    }
}
