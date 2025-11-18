<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan club likes
 *
 * @see \Ubix\Repository\FanClubLike\FanClubLikeSqlRepository This DTO is used by the fan club like SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubLikeOptionsTest PHPUnit test case
 */
final readonly class FanClubLikeOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int $postId The liked post's ID (optional) (default: null)
     * @param ?int $userId The liker's user ID (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $postId = null,
        public readonly ?int $userId = null,
    ) {
    }
}
