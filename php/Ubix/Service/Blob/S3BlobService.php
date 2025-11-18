<?php

declare(strict_types=1);

namespace Ubix\Service\Blob;

use Aws\S3\S3Client;
use Exception;
use LogicException;
use Psr\Http\Message\StreamInterface as Stream;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\S3BlobServiceParameters;
use Ubix\DataTransferObject\S3Object;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Blob\BlobServiceInterface as BlobService;

/**
 * Service to manage blob storage with the S3 API
 *
 * @see \Ubix\Tests\Service\Blob\S3BlobServiceTest PHPUnit test case
 */
final class S3BlobService implements BlobService
{
    /**
     * An array of S3Client objects available as singletons stored with a key that is the MD5 hash of the credentials
     *
     * @var array<string, S3Client> $singletons
     */
    private static array $singletons = [];

    /**
     * Constructor
     *
     * @param Logger $logger    Logger
     * @param string $accessKey The S3 access key
     * @param string $secretKey The S3 secret key
     * @param string $region    The S3 region
     * @param string $endpoint  The S3 endpoint
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private string $accessKey,
        private string $secretKey,
        private string $region,
        private string $endpoint,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $key, string $contents): void
    {
        $s3Object = $this->getS3Object($key);

        $this->getClient()->putObject([
            'ACL'    => 'private',
            'Body'   => $contents,
            'Bucket' => $s3Object->bucket,
            'Key'    => $s3Object->key,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If we fail to retrieve the blob from S3
     */
    public function get(string $key): string
    {
        $s3Object = $this->getS3Object($key);

        try {
            $result = $this->getClient()->getObject([
                'Bucket' => $s3Object->bucket,
                'Key'    => $s3Object->key,
            ]);

            $body = $result['Body'];
            assert($body instanceof Stream);

            return $body->getContents();
        } catch (Exception $e) {
            throw new Exception('Failed retrieving blob from S3', ExceptionCode::S3_FAILED_TO_RETRIEVE_BLOB->value);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function url(string $key, int $expiry = 0): string // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE URL() METHOD FOR S3 HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function delete(string $key): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE DELETE() METHOD FOR S3 HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If this unimplemented method is called
     */
    public function list(string $path): array // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused right now as the method hasn't been implemented (remove once this method has been implemented)
    {
        throw new LogicException('THE LIST() METHOD FOR S3 HAS NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
    }

	// phpcs:enable Squiz.Commenting.FunctionComment.InvalidNoReturn -- This method has not been implemented yet, so it does not return anything right now (remove once this method has been implemented)

    /**
     * Get an S3Client object
     *
     * @throws DtoException If the S3Client fails to initialize
     *
     * @return S3Client The S3Client object
     */
    private function getClient(): S3Client
    {
        $singletonKey = md5(serialize([$this->accessKey, $this->secretKey, $this->region, $this->endpoint]));

        if (!isset(self::$singletons[$singletonKey])) {
            try {
                self::$singletons[$singletonKey] = new S3Client([
                    'credentials'             => [
                        'key'    => $this->accessKey,
                        'secret' => $this->secretKey,
                    ],
                    'endpoint'                => $this->endpoint,
                    'region'                  => $this->region,
                    'use_path_style_endpoint' => true, // Required for CDN77
                    'version'                 => 'latest',
                ]);
            } catch (Exception $e) {
                throw new DtoException(
                    'S3Client failed to initialize',
                    ExceptionCode::S3_CLIENT_FAILED_TO_INITIALIZE->value,
                    new S3BlobServiceParameters(
                        accessKey: $this->accessKey,
                        secretKey: $this->secretKey,
                        region:    $this->region,
                        endpoint:  $this->endpoint,
                    ),
                );
            }
        }

        return self::$singletons[$singletonKey];
    }

    /**
     * Get an S3 object from a key in the format "bucket/key"
     *
     * @param string $key The S3 key (in the format "bucket/key")
     *
     * @throws Exception If the key is not in the correct format
     *
     * @return S3Object The S3 object
     */
    private function getS3Object(string $key): S3Object
    {
        $slashPosition = strpos($key, '/');
        if ($slashPosition === false) {
            throw new Exception(
                'S3Client failed to initialize',
                ExceptionCode::S3_KEY_MISSING_SLASH->value,
            );
        }

        return new S3Object(
            bucket: substr($key, 0, $slashPosition),
            key:    substr($key, $slashPosition + 1),
        );
    }
}
