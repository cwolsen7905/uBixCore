<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\Studio;
use Ubix\Repository\Studio\StudioReaderInterface as StudioReader;

/**
 * Service to access studios
 *
 * @see \Ubix\Tests\Service\StudioServiceTest PHPUnit test case
 */
final class StudioService
{
    /**
     * Constructor
     *
     * @param Logger       $logger       Logger
     * @param StudioReader $studioReader Studio reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private StudioReader $studioReader,
    ) {
    }

    /**
     * Get studios by admin user ID
     *
     * @param int $adminUserId The admin user ID
     *
     * @return Studio[] The studio with matching admin user ID
     */
    public function getByAdminUserId(int $adminUserId): array
    {
        return $this->studioReader->getByAdminUserId($adminUserId);
    }
}
