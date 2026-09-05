<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the parameters of an S3 blob service
 *
 * @see \Ubix\Service\Pdo\Blob\S3BlobService This DTO is used by the S3 blob service
 * @see \Ubix\Tests\DataTransferObject\S3BlobServiceParametersTest PHPUnit test case
 */
final readonly class S3BlobServiceParameters implements Dto
{
    /**
     * Constructor
     *
     * @param string $accessKey The S3 access key
     * @param string $secretKey The S3 secret key
     * @param string $region    The S3 region
     * @param string $endpoint  The S3 endpoint
     */
    public function __construct(
        public readonly string $accessKey,
        public readonly string $secretKey,
        public readonly string $region,
        public readonly string $endpoint,
    ) {
    }
}
