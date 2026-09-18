<?php

declare(strict_types=1);

namespace Ubix\Service\Media;

use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use DateTime;
use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ramsey\Uuid\Uuid;
use Ubix\DataTransferObject\Media\ObjectMetadata;
use Ubix\DataTransferObject\Media\PresignedUpload;
use Ubix\DataTransferObject\Media\SignedUrl;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Media\MediaStorageServiceInterface as MediaStorageService;

/**
 * S3-compatible implementation of {@see MediaStorageServiceInterface}
 *
 * The only class in the framework that imports the AWS SDK — every consumer
 * depends on {@see MediaStorageServiceInterface} instead, wired via PHP-DI, so
 * swapping the object-store vendor is a DI binding change, never a call-site
 * change (platform TDS §5's swappable-vendor rule).
 *
 * The AWS SDK's S3 client speaks the S3 API against any S3-compatible
 * endpoint, not only AWS itself, so this one class serves both a production
 * AWS S3 bucket and an in-cluster MinIO deployment: nothing here branches on
 * environment. What differs between them — endpoint URL, region, credentials,
 * and whether the endpoint needs path-style addressing (MinIO does; AWS does
 * not) — comes entirely from configuration, read once per environment:
 *
 * | Env var                            | Meaning                                                            | Source                                            |
 * |-------------------------------------|--------------------------------------------------------------------|----------------------------------------------------|
 * | `MEDIA_S3_ENDPOINT`                 | The S3-compatible endpoint URL                                     | plain per-environment config (k8s ConfigMap/`.env`) |
 * | `MEDIA_S3_REGION`                   | The bucket's region (MinIO accepts any non-empty value)             | plain per-environment config                        |
 * | `MEDIA_S3_BUCKET`                   | The bucket name                                                    | plain per-environment config                        |
 * | `MEDIA_S3_USE_PATH_STYLE_ENDPOINT`  | `'false'` to use virtual-hosted-style addressing; anything else (including unset) uses path-style | plain per-environment config |
 * | `MEDIA_S3_PUBLIC_BASE_URL`          | The base URL {@see self::publicUrl()} prefixes onto an object key (typically a CDN origin-pulling from the bucket) | plain per-environment config |
 * | `MEDIA_S3_ACCESS_KEY_ID`            | The bucket credential's access key                                  | **uBixVault** in every deployed environment — never committed, never baked into an image; `.env` only as a local-dev convenience |
 * | `MEDIA_S3_SECRET_ACCESS_KEY`        | The bucket credential's secret key                                  | **uBixVault**, same as above                        |
 *
 * The two credential values are exactly the kind of secret `Ubix\Service\Vault\VaultCredentialResolverService`
 * exists to hydrate into the environment before this class ever reads it —
 * see that class's `VAULT_APP_KV_PATH` opt-in mechanism (it hydrates any
 * upper-snake-case key from an app's own Vault KV secret, not only `MYSQL_*`).
 * A host wires this by pointing `VAULT_APP_KV_PATH` at a KV secret whose keys
 * are `MEDIA_S3_ACCESS_KEY_ID` / `MEDIA_S3_SECRET_ACCESS_KEY`; this class does
 * not need to know Vault exists, exactly like `Ubix\Service\Sql\MysqlPdoSqlService`
 * doesn't need to know where its `MYSQL_*` values came from.
 *
 * Object keys are minted here (a v4 UUID — random, not derived from any
 * caller input or counter) rather than accepted from a caller, which is what
 * makes {@see MediaStorageServiceInterface}'s "opaque, non-sequential key"
 * guarantee actually hold regardless of what any particular host does.
 *
 * @see \Ubix\Tests\Service\Media\S3MediaStorageServiceTest PHPUnit test case
 */
final class S3MediaStorageService implements MediaStorageService
{
    /**
     * S3Client objects available as singletons, keyed by the MD5 hash of the resolved configuration
     *
     * @var array<string, S3Client> $singletons
     */
    private static array $singletons = [];

    /**
     * Constructor
     *
     * @param Logger    $logger   Logger
     * @param ?S3Client $s3Client A pre-built S3 client to use instead of one built from `MEDIA_S3_*` environment configuration (optional) (default: null) — production wiring never sets this; it exists so tests can inject an `Aws\MockHandler`-backed client and exercise this class with no network access
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private ?S3Client $s3Client = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function createPresignedUpload(string $contentType, int $ttlSeconds): PresignedUpload
    {
        $objectKey = $this->generateObjectKey();

        $command = $this->getClient()->getCommand('PutObject', [
            'Bucket'      => $this->bucket(),
            'ContentType' => $contentType,
            'Key'         => $objectKey,
        ]);

        $request = $this->getClient()->createPresignedRequest($command, '+' . $ttlSeconds . ' seconds');

        return new PresignedUpload(
            objectKey:              $objectKey,
            uploadUrl:              (string) $request->getUri(),
            httpMethod:             'PUT',
            requiredHeaders:        ['Content-Type' => $contentType],
            expiresAtUnixTimestamp: (new DateTime())->getTimestamp() + $ttlSeconds,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException If no object exists at that key
     */
    public function inspectObject(string $objectKey): ObjectMetadata
    {
        try {
            $result = $this->getClient()->headObject([
                'Bucket' => $this->bucket(),
                'Key'    => $objectKey,
            ]);
        } catch (S3Exception $e) {
            throw new DtoException(
                'No object exists at that key',
                ExceptionCode::MEDIA_OBJECT_NOT_FOUND->value,
                previous: $e,
            );
        }

        $contentType  = $result['ContentType'] ?? '';
        $byteSize     = $result['ContentLength'] ?? 0;
        $etag         = $result['ETag'] ?? null;
        $lastModified = $result['LastModified'] ?? null;

        return new ObjectMetadata(
            objectKey:                 $objectKey,
            contentType:               is_string($contentType) ? $contentType : '',
            byteSize:                  is_int($byteSize) ? $byteSize : 0,
            checksum:                  is_string($etag) ? trim($etag, '"') : null,
            lastModifiedUnixTimestamp: $lastModified instanceof DateTimeInterface ? $lastModified->getTimestamp() : 0,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException If the local file cannot be read, or the upload fails
     */
    public function putObject(string $localFilePath, string $contentType): ObjectMetadata
    {
        if (!is_readable($localFilePath)) {
            throw new DtoException(
                'Local file is not readable',
                ExceptionCode::MEDIA_LOCAL_FILE_UNREADABLE->value,
            );
        }

        $objectKey = $this->generateObjectKey();

        try {
            $this->getClient()->putObject([
                'ACL'         => 'private',
                'Bucket'      => $this->bucket(),
                'ContentType' => $contentType,
                'Key'         => $objectKey,
                'SourceFile'  => $localFilePath,
            ]);
        } catch (AwsException $e) {
            throw new DtoException(
                'Failed to upload local file to the object store',
                ExceptionCode::MEDIA_STORAGE_OPERATION_FAILED->value,
                previous: $e,
            );
        }

        return $this->inspectObject($objectKey);
    }

    /**
     * {@inheritDoc}
     */
    public function createSignedReadUrl(string $objectKey, int $ttlSeconds): SignedUrl
    {
        $command = $this->getClient()->getCommand('GetObject', [
            'Bucket' => $this->bucket(),
            'Key'    => $objectKey,
        ]);

        $request = $this->getClient()->createPresignedRequest($command, '+' . $ttlSeconds . ' seconds');

        return new SignedUrl(
            url:                    (string) $request->getUri(),
            expiresAtUnixTimestamp: (new DateTime())->getTimestamp() + $ttlSeconds,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function publicUrl(string $objectKey): string
    {
        return rtrim($this->publicBaseUrl(), '/') . '/' . ltrim($objectKey, '/');
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException If the delete fails
     */
    public function deleteObject(string $objectKey): void
    {
        try {
            $this->getClient()->deleteObject([
                'Bucket' => $this->bucket(),
                'Key'    => $objectKey,
            ]);
        } catch (AwsException $e) {
            throw new DtoException(
                'Failed to delete object from the object store',
                ExceptionCode::MEDIA_STORAGE_OPERATION_FAILED->value,
                previous: $e,
            );
        }
    }

    /**
     * Mint a new opaque object key
     *
     * A random v4 UUID rather than anything derived from caller input, a
     * counter, or a timestamp, so keys are never sequential or guessable.
     *
     * @return string The generated object key
     */
    private function generateObjectKey(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Get the configured bucket name
     *
     * @return string The bucket name
     */
    private function bucket(): string
    {
        return (string) getenv('MEDIA_S3_BUCKET');
    }

    /**
     * Get the configured public base URL
     *
     * @return string The public base URL
     */
    private function publicBaseUrl(): string
    {
        return (string) getenv('MEDIA_S3_PUBLIC_BASE_URL');
    }

    /**
     * Get an S3Client, either the one injected at construction (tests only) or one built from `MEDIA_S3_*` environment configuration
     *
     * @throws DtoException If the S3Client fails to initialize
     *
     * @return S3Client The S3 client
     */
    private function getClient(): S3Client
    {
        if ($this->s3Client !== null) {
            return $this->s3Client;
        }

        $accessKeyId     = (string) getenv('MEDIA_S3_ACCESS_KEY_ID');
        $secretAccessKey = (string) getenv('MEDIA_S3_SECRET_ACCESS_KEY');
        $region          = (string) getenv('MEDIA_S3_REGION');
        $endpoint        = (string) getenv('MEDIA_S3_ENDPOINT');
        $usePathStyle    = getenv('MEDIA_S3_USE_PATH_STYLE_ENDPOINT') !== 'false';

        $singletonKey = md5(serialize([$accessKeyId, $secretAccessKey, $region, $endpoint, $usePathStyle]));

        if (!isset(self::$singletons[$singletonKey])) {
            try {
                self::$singletons[$singletonKey] = new S3Client([
                    'credentials'             => [
                        'key'    => $accessKeyId,
                        'secret' => $secretAccessKey,
                    ],
                    'endpoint'                => $endpoint,
                    'region'                  => $region,
                    'use_path_style_endpoint' => $usePathStyle,
                    'version'                 => 'latest',
                ]);
            } catch (Exception $e) {
                throw new DtoException(
                    'S3Client failed to initialize',
                    ExceptionCode::MEDIA_STORAGE_OPERATION_FAILED->value,
                    previous: $e,
                );
            }
        }

        return self::$singletons[$singletonKey];
    }
}
