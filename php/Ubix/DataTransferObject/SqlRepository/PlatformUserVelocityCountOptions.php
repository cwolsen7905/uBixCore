<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of platform user velocity counts
 *
 * @see \Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepository This DTO is used by the platform user velocity count SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\PlatformUserVelocityCountOptionsTest PHPUnit test case
 */
final readonly class PlatformUserVelocityCountOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $ipAddress               The IP address (optional) (default: null)
     * @param ?string $username                The username (optional) (default: null)
     * @param ?int    $secondsInThePast        The number of seconds to look back when doing the count (optional) (default: null)
     * @param ?int    $ipAddressCacheThreshold The threshold that must be hit to cache the IP address results (optional) (default: null)
     * @param ?int    $usernameCacheThreshold  The threshold that must be hit to cache the username results (optional) (default: null)
     */
    public function __construct(
        public readonly ?string $ipAddress = null,
        public readonly ?string $username = null,
        public readonly ?int $secondsInThePast = null,
        public readonly ?int $ipAddressCacheThreshold = null,
        public readonly ?int $usernameCacheThreshold = null,
    ) {
    }
}
