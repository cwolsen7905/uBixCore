<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of a PDO error
 *
 * @see \Ubix\Service\JsonService This DTO is used by the JSON service
 * @see \Ubix\Tests\DataTransferObject\MediaBuyingDetailsTest PHPUnit test case
 */
final readonly class MediaBuyingDetails implements Dto
{
    /**
     * Constructor
     *
     * @param bool   $isMediaBuying Whether the affiliate is media buying
     * @param string $type          The type of media buying (e.g., 'mb_campaign_aff', 'mb_deal_aff', or '')
     */
    public function __construct(
        public readonly bool $isMediaBuying,
        public readonly string $type,
    ) {
    }
}
