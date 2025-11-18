<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\EmailAccount;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\AdminUser;
use Ubix\Repository\AdminUser\AdminUserReaderInterface as AdminUserReader;

/**
 * Service to access admin users
 *
 * @see \Ubix\Tests\Service\AdminUserServiceTest PHPUnit test case
 */
final class AdminUserService // TEMPORARY: ANDREW:: Should "AdminUser" be "StudioAdminUser"? if yes that applies to multiple classes
{
    /**
     * Constructor
     *
     * @param Logger          $logger          Logger
     * @param AdminUserReader $adminUserReader Admin user reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private AdminUserReader $adminUserReader,
    ) {
    }

    /**
     * Get admin users by email address
     *
     * @param string $emailAddress The admin user's email address
     *
     * @return AdminUser[] An array of admin user with a matching email address
     */
    public function getByEmailAddress(string $emailAddress): array
    {
        return $this->adminUserReader->getByEmailAddress($emailAddress);
    }

    /**
     * Get an admin user by ID
     *
     * @param int $id The admin user ID
     *
     * @throws Exception If a admin user is not found with a matching ID
     *
     * @return AdminUser The admin user with matching ID
     */
    public function getById(int $id): AdminUser
    {
        $adminUsers = $this->adminUserReader->getById($id);
        if (count($adminUsers) > 0) {
            return $adminUsers[0];
        } else {
            throw new Exception('No admin user found with the ID `' . $id . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_ADMIN_USER_ID->value);
        }
    }

    /**
     * Get email accounts by notification type
     *
     * @param string $notificationType The notification type
     *
     * @return EmailAccount[] An array of email accounts flagged to receive a notification type
     */
    public function getEmailAccountsByNotificationType(string $notificationType): array
    {
        $emailAccounts = [];
        foreach ($this->adminUserReader->getByNotificationType($notificationType) as $adminUser) {
            $emailAccounts[] = new EmailAccount($adminUser->getEmail() ?? '', $adminUser->getName());
        }
        return $emailAccounts;
    }
}
