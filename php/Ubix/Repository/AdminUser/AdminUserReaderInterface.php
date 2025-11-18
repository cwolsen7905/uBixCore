<?php

declare(strict_types=1);

namespace Ubix\Repository\AdminUser;

use Ubix\Model\AdminUser;

/**
 * Interface for reading admin user data
 */
interface AdminUserReaderInterface
{
    /**
     * Get admin user(s) by email address
     *
     * @param int $id The admin user's ID
     *
     * @return AdminUser[] An array of matching admin user(s)
     */
    public function getById(int $id): array;

    /**
     * Get admin user(s) by email address
     *
     * @param string $emailAddress The admin user's email address
     *
     * @return AdminUser[] An array of matching admin user(s)
     */
    public function getByEmailAddress(string $emailAddress): array;

    /**
     * Get admin user(s) by notification type
     *
     * @param string $notificationType The notification type
     *
     * @return AdminUser[] An array of matching admin user(s)
     */
    public function getByNotificationType(string $notificationType): array;
}
