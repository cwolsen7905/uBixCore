<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\SqlRepository;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatus;

/**
 * Data transfer object for the SQL repository options of solo acquisition campaigns
 *
 * @see \Ubix\Repository\FanClubComment\SoloAcquisitionCampaignSqlRepository This DTO is used by the solo acquisition campaign SQL repository
 * @see \Ubix\Tests\DataTransferObject\SqlRepository\SoloAcquisitionCampaignOptionsTest PHPUnit test case
 */
final readonly class SoloAcquisitionCampaignOptions implements Dto
{
    /**
     * Constructor
     *
     * @param ?int                           $id     The campaign's ID (optional) (default: null)
     * @param ?SoloAcquisitionCampaignStatus $status The campaign's status (optional) (null)
     * @param ?int                           $limit  The query's LIMIT value (optional) (default: null)
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?SoloAcquisitionCampaignStatus $status = null,
        public readonly ?int $limit = null,
    ) {
    }
}
