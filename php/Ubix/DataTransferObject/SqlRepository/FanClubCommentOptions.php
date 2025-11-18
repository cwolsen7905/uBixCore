<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the SQL repository options of fan club comments
 *
 * @see \Ubix\Repository\FanClubComment\FanClubCommentSqlRepository This DTO is used by the fan club comment SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\FanClubCommentOptionsTest PHPUnit test case
 */
final readonly class FanClubCommentOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int    $id      The comment's ID (optional) (default: null)
     * @param ?int    $postId  The commented post's ID (optional) (default: null)
     * @param ?string $orderBy The query's ORDER BY value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $postId = null,
        public readonly ?string $orderBy = null,
    ) {
    }
}
