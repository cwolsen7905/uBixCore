<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ramsey\Uuid\Uuid;

/**
 * Service to handle UUID's
 *
 * @see \Ubix\Tests\Service\UuidServiceTest PHPUnit test case
 */
final class UuidService
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Generate a new UUID (v4)
     *
     * @return string The UUID (v4)
     */
    public function getUuid4(): string
    {
        return Uuid::uuid4()->toString();
    }
}
