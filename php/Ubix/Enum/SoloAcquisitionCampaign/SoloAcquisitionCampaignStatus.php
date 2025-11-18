<?php

declare(strict_types=1);

namespace Ubix\Enum\SoloAcquisitionCampaign;

/**
 * Enumeration of solo acquisition campaign statuses
 *
 * @see \Ubix\Tests\Enum\SoloAcquisitionCampaign\SoloAcquisitionCampaignStatusTest PHPUnit test case
 */
enum SoloAcquisitionCampaignStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
}
