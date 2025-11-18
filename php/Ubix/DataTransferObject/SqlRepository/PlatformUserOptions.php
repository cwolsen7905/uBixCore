<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of platform users
 *
 * @see \Ubix\Repository\PlatformUser\PlatformUserSqlRepository This DTO is used by the platform user SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PlatformUserOptionsTest PHPUnit test case
 */
final readonly class PlatformUserOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $limit         The query's LIMIT value (optional) (default: null)
     * @param ?string $passwordMd5   The platform user's password (MD5 hash) (optional) (default: null)
     * @param ?string $screenName    The platform user's screen name (optional) (default: null)
     * @param ?string $sitekey       The platform user's sitekey (optional) (default: null)
     * @param ?string $username      The platform user's username (optional) (default: null)
     * @param ?int    $userId        The platform user's ID (optional) (default: null)
     * @param ?string $oauthProvider The platform user's OAuth provider (optional) (default: null)
     * @param ?string $oauthGuid     The platform user's OAuth GUID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?string $passwordMd5 = null,
        public readonly ?string $screenName = null,
        public readonly ?string $sitekey = null,
        public readonly ?string $username = null,
        public readonly ?int $userId = null,
        public readonly ?string $oauthProvider = null,
        public readonly ?string $oauthGuid = null,
    ) {
    }
}
