<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUser;

use Exception;
use Ubix\Model\PlatformUser;

/**
 * Interface for reading platform user data
 */
interface PlatformUserReaderInterface
{
    /**
     * Get platform user(s) by username (and optionally sitekey)
     *
     * @param string  $username The platform user's username
     * @param ?string $sitekey  The platform user's sitekey (optional)
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    public function getByUsername(string $username, ?string $sitekey = null): array;

    /**
     * Get platform user(s) by screen name (and optionally sitekey)
     *
     * @param string  $screenName The platform user's screen name
     * @param ?string $sitekey    The platform user's sitekey (optional)
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    public function getByScreenName(string $screenName, ?string $sitekey = null): array;

    /**
     * Get platform user(s) for AutoLogin
     *
     * @param string  $username    The platform user's username
     * @param string  $passwordMd5 The platform user's password
     * @param ?string $sitekey     The platform user's sitekey (optional)
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    public function getForAutoLogin(string $username, string $passwordMd5, ?string $sitekey = null): array;

    /**
     * Get platform user(s) by ID
     *
     * @param int     $userId  The platform user's ID
     * @param ?string $sitekey The platform user's sitekey (optional)
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    public function getById(int $userId, ?string $sitekey = null): array;

    /**
     * Get platform user(s) by OAuth account
     *
     * @param string $oauthProvider The OAuth provider
     * @param string $oauthGuid     The OAuth GUID
     *
     * @return PlatformUser[] An array of matching platform user(s)
     */
    public function getByOauth(string $oauthProvider, string $oauthGuid): array;

    /**
     * @param PlatformUser $user Platform User Object used to look up earliest account by email
     *
     * @return PlatformUser The earliest platform user account
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByEmail(PlatformUser $user): PlatformUser;

    /**
     * @param PlatformUser $user Platform User Object used to look up earliest account by email
     *
     * @return PlatformUser The earliest platform user account
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByFingerprint(PlatformUser $user): PlatformUser;

    /**
     * Looks Up Earliest Account By Iovation
     *
     * @param PlatformUser $user Platform User Object
     *
     * @return PlatformUser The earliest platform user account found by Iovation
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByIovation(PlatformUser $user): PlatformUser;

    /**
     * @param PlatformUser $user Platform User Object
     *
     * @return PlatformUser The earliest platform user account found by details
     *
     * @throws Exception If no account is found
     */
    public function getEarliestAccountByDetails(PlatformUser $user): PlatformUser;

    /**
     * Looks Up Earliest Payment Account
     *
     * @param PlatformUser $user Platform User Object
     *
     * @return PlatformUser The earliest platform user account found by payment account
     *
     * @throws Exception If no account is found
     */
    public function getAllPriorAccountsByPaymentAccount(PlatformUser $user): PlatformUser;
}
