<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserVelocityCount;

use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Ubix\DataTransferObject\SqlRepository\PlatformUserVelocityCountOptions;
use Ubix\Model\PlatformUserVelocityCount;
use Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountReaderInterface as PlatformUserVelocityCountReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading platform user velocity counts data
 *
 * @see \Ubix\Tests\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepositoryTest PHPUnit test case
 */
final class PlatformUserVelocityCountSqlRepository implements PlatformUserVelocityCountReader
{
    private const SIMPLE_CACHE_KEY_IP_ADDRESS = 'PUVCPdoRepo_IpAddress';
    private const SIMPLE_CACHE_KEY_USERNAME   = 'PUVCPdoRepo_Username';

    /**
     * Constructor
     *
     * @param Logger      $logger      Logger
     * @param SqlService  $sqlService  SQL service
     * @param SimpleCache $simpleCache Simple cache
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
        private SimpleCache $simpleCache,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function get(
        string $ipAddress,
        string $username,
        int $secondsInThePast,
        int $ipAddressCacheThreshold = PHP_INT_MAX,
        int $usernameCacheThreshold = PHP_INT_MAX,
    ): array {
        return $this->query(new PlatformUserVelocityCountOptions(
            ipAddress:               $ipAddress,
            ipAddressCacheThreshold: $ipAddressCacheThreshold,
            secondsInThePast:        $secondsInThePast,
            username:                $username,
            usernameCacheThreshold:  $usernameCacheThreshold,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PlatformUserVelocityCountOptions $options DTO of options to generate the query
     *
     * @return PlatformUserVelocityCount[] An array of matching platform user velocity count
     */
    private function query(PlatformUserVelocityCountOptions $options): array
    {
        //
        //  Create a platform user velocity count object for all of our counts
        //
        $platformUserVelocityCount = new PlatformUserVelocityCount(
            ipAddress: $options->ipAddress,
            username:  $options->username,
        );

        //
        //  Generate the IP address count
        //
        if ($options->ipAddress !== null) {
            $cacheKey = self::SIMPLE_CACHE_KEY_IP_ADDRESS . '~' . md5($options->ipAddress);

            $cachedCount = $this->simpleCache->get($cacheKey);
            if ($cachedCount !== null && is_int($cachedCount)) { // If we have a cached result use it
                $platformUserVelocityCount->setIpAddressCount($cachedCount);
            } else { // If we don't have a cached result run a SQL query
                $sql = 'SELECT COUNT(*)
						FROM ntl_db.optiusers_site_logins osl
						WHERE osl.ip_address_long = :ip_address';
                if ($options->secondsInThePast !== null) {
                    $sql .= ' AND osl.datetime >= DATE_SUB(NOW(), INTERVAL ' . $options->secondsInThePast . ' SECOND)';
                }

                $count = $this->sqlService->getColumn($sql, ['ip_address' => $options->ipAddress]);
                assert(is_int($count));

                if ($options->ipAddressCacheThreshold !== null && $count >= $options->ipAddressCacheThreshold) { // If the count is greater than or equal to the threshold then cache it
                    $this->simpleCache->set($cacheKey, $count, $options->secondsInThePast);
                }

                $platformUserVelocityCount->setIpAddressCount($count);
            }
        }

        //
        //  Generate the username count
        //
        if ($options->username !== null) {
            $cacheKey = self::SIMPLE_CACHE_KEY_USERNAME . '~' . md5($options->username);

            $cachedCount = $this->simpleCache->get($cacheKey);
            if ($cachedCount !== null && is_int($cachedCount)) { // If we have a cached result use it
                $platformUserVelocityCount->setUsernameCount($cachedCount);
            } else { // If we don't have a cached result run a SQL query
                $sql = 'SELECT COUNT(*)
						FROM ntl_db.optiusers_site_logins osl
						WHERE osl.username = :username';
                if ($options->secondsInThePast !== null) {
                    $sql .= ' AND osl.datetime >= DATE_SUB(NOW(), INTERVAL ' . $options->secondsInThePast . ' SECOND)';
                }

                $count = $this->sqlService->getColumn($sql, ['username' => $options->username]);
                assert(is_int($count));

                if ($options->usernameCacheThreshold !== null && $count >= $options->usernameCacheThreshold) { // If the count is greater than or equal to the threshold then cache it
                    $this->simpleCache->set($cacheKey, $count, $options->secondsInThePast);
                }

                $platformUserVelocityCount->setUsernameCount($count);
            }
        }

        //
        //  Return our object in an array (it will always return one - never zero or multiple)
        //
        return [$platformUserVelocityCount];
    }
}
