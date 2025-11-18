<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformWhiteLabel;

use Ubix\Model\PlatformWhiteLabel;

/**
 * Interface for reading platform white label data
 */
interface PlatformWhiteLabelReaderInterface
{
    /**
     * Get all platform white labels by domain
     *
     * @param string $domain The white label's domain
     *
     * @return PlatformWhiteLabel[] An array of platform white label objects
     */
    public function getByDomain(string $domain): array;
}
