<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Broadcaster;
use Ubix\Repository\Broadcaster\BroadcasterReaderInterface as BroadcasterReader;

/**
 * Service to access broadcasters
 *
 * @see \Ubix\Tests\Service\BroadcasterServiceTest PHPUnit test case
 */
final class BroadcasterService
{
    /**
     * Constructor
     *
     * @param Logger            $logger            Logger
     * @param BroadcasterReader $broadcasterReader Broadcaster reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private BroadcasterReader $broadcasterReader,
    ) {
    }

    /**
     * Get a broadcaster by ID
     *
     * @param int $id The broadcaster ID
     *
     * @throws Exception If a broadcaster is not found with a matching ID
     *
     * @return Broadcaster The broadcaster with matching ID
     */
    public function getById(int $id): Broadcaster
    {
        $performers = $this->broadcasterReader->getById($id);
        if (count($performers) > 0) {
            return $performers[0];
        } else {
            throw new Exception('No broadcaster found with the ID `' . $id . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_BROADCASTER_ID->value);
        }
    }

    /**
     * Get broadcasters by partial match
     *
     * @param string $name       The broadcaster's name
     * @param string $studioName The broadcaster's studio name
     *
     * @return Broadcaster[] An array of partially matching broadcasters
     */
    public function getPartialMatches(string $name, string $studioName): array
    {
        return $this->broadcasterReader->getPartialMatches($name, $studioName);
    }
}
