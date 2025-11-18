<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan club posts
 *
 * @see \Ubix\Repository\FanClubPost\FanClubPostSqlRepository This DTO is used by the fan club post SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubPostOptionsTest PHPUnit test case
 */
final readonly class FanClubPostOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $activeUser               The ID of the active user accessing the post(s) (optional) (default: null)
     * @param ?bool   $alternativeDateFiltering Enable alternative date filtering (optional) (default: null)
     * @param ?bool   $dateFiltering            Enable date filtering (optional) (default: null)
     * @param ?int    $id                       The post's ID (optional) (default: null)
     * @param ?int    $limit                    The query's LIMIT value (optional) (default: null)
     * @param ?int    $modelId                  The poster model's ID (optional) (default: null)
     * @param ?bool   $purchasedOnly            Filter by purchased status: true = purchased only, false = unpurchased only, null = both (optional) (default: null)
     * @param ?string $orderBy                  The query's ORDER BY value (optional) (default: null)
     * @param ?string $status                   The post's status (optional) (default: null)
     * @param ?string $topHashtag               Filter by the top hashtag (optional) (default: null)
     * @param ?int    $unlockedFor              The ID of a user who has the post unlocked (optional) (default: null)
     * @param ?string $visibility               The post's visibility (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $activeUser = null,
        public readonly ?bool $alternativeDateFiltering = null,
        public readonly ?bool $dateFiltering = null,
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
        public readonly ?int $modelId = null,
        public readonly ?bool $purchasedOnly = null,
        public readonly ?string $orderBy = null,
        public readonly ?string $status = null,
        public readonly ?string $topHashtag = null,
        public readonly ?int $unlockedFor = null,
        public readonly ?string $visibility = null,
    ) {
    }
}
