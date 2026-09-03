<?php

declare(strict_types=1);

namespace Ubix\Service\Blob;

use DateTime;
use Filestack\FilestackClient;
use Filestack\FilestackSecurity;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\Blob\BlobServiceInterface as BlobService;

/**
 * Service to manage blob storage with the Filestack API
 *
 * @see \Ubix\Tests\Service\Blob\FilestackBlobServiceTest PHPUnit test case
 */
final class FilestackBlobService implements BlobService
{
    /**
     * An array of FilestackClient objects available as singletons stored with a key that is the MD5 hash of the API key
     *
     * @var array<string, FilestackClient> $filestackClientSingletons
     */
    private static array $filestackClientSingletons = [];

    /**
     * An array of FilestackSecurity objects available as singletons stored with a key that is the MD5 hash of the API secret and the expiry length
     *
     * @var array<string, FilestackSecurity> $filestackSecuritySingletons
     */
    private static array $filestackSecuritySingletons = [];

    /**
     * Constructor
     *
     * @param Logger $logger    Logger
     * @param string $apiKey    The Filestack API key
     * @param string $apiSecret The Filestack API secret
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private string $apiKey,
        private string $apiSecret,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function put(string $handle, string $contents): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE PUT() METHOD FOR FILESTACK HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function get(string $handle): string // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE GET() METHOD FOR FILESTACK HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:enable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * {@inheritDoc}
     */
    public function url(string $handle, int $expiry = FilestackSecurity::DEFAULT_EXPIRY): string
    {
        //
        //  Generate a URL for the file
        //
        $url = $this->getFilestackClient()->getCdnUrl($handle);
        assert(is_string($url));

        //
        //  Create a new FilestackSecurity object with the passed expiry and use it to add policy and signature parameters to the URL
        //
        $security = $this->getFilestackSecurity($expiry);
        return $security->signUrl($url);
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function delete(string $handle): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE DELETE() METHOD FOR FILESTACK HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function list(string $path): array // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE LIST() METHOD FOR FILESTACK HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:enable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * Get a Filestack client object
     *
     * @return FilestackClient A Filestack client
     */
    private function getFilestackClient(): FilestackClient
    {
        //
        //  Create a singleton if one doesn't already exist then return it
        //
        $singletonKey = md5($this->apiKey);

        if (!isset(self::$filestackClientSingletons[$singletonKey])) {
            self::$filestackClientSingletons[$singletonKey] = new FilestackClient($this->apiKey);
        }

        return self::$filestackClientSingletons[$singletonKey];
    }

    /**
     * Get a Filestack security object
     *
     * @param int $expiry The expiration (in seconds) of the URL, use zero for no expiration
     *
     * @throws InvalidArgumentException If the expiry value is invalid
     *
     * @return FilestackSecurity A Filestack security
     */
    private function getFilestackSecurity(int $expiry): FilestackSecurity
    {
        //
        //  Validate parameters
        //
        if ($expiry < 0) {
            throw new InvalidArgumentException('The expiry must be greater than or equal to zero.', ExceptionCode::FILESTACK_INVALID_EXPIRY->value);
        }

        //
        //  Create a singleton if one doesn't already exist then return it
        //
        $singletonKey = md5(serialize([$this->apiSecret, $expiry]));

        if (!isset(self::$filestackSecuritySingletons[$singletonKey])) {
            self::$filestackSecuritySingletons[$singletonKey] = new FilestackSecurity(
                $this->apiSecret,
                ['expiry' => (new DateTime())->modify('+' . $expiry . ' seconds')->format('U')],
            );
        }

        return self::$filestackSecuritySingletons[$singletonKey];
    }
}
