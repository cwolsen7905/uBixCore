<?php

declare(strict_types=1);

namespace Ubix\Repository\Performer;

use Ubix\Model\Performer;

/**
 * Interface for reading performer data
 */
interface PerformerReaderInterface
{
    /**
     * Get performer(s) by username
     *
     * @param string $username The username
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getByUsername(string $username): array;

    /**
     * Get performer(s) by autologin values
     *
     * @param string $username    The username
     * @param string $passwordMd5 The md5 hash of an encrypted password
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getForAutoLogin(string $username, string $passwordMd5): array;

    /**
     * Get performer(s) by slug
     *
     * @param string $slug The performer slug
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getBySlug(string $slug): array;

    /**
     * Get performer(s) by performer ID
     *
     * @param int $id The performer ID
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getById(int $id): array;

    /**
     * Get performer(s) by email address
     *
     * @param string $emailAddress The performer's email address
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getByEmailAddress(string $emailAddress): array;

    /**
     * Get performer(s) by name
     *
     * @param string $name The performer's name
     *
     * @return Performer[] An array of matching performer(s)
     */
    public function getByName(string $name): array;

    /**
     * Get performer(s) by partial match
     *
     * @param string $name The performer's name
     *
     * @return Performer[] An array of partially matching performer(s)
     */
    public function getPartialMatches(string $name): array;
}
