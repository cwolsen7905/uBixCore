<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for an S3 object
 *
 * @see \Ubix\Tests\DataTransferObject\S3ObjectTest PHPUnit test case
 */
final readonly class S3Object implements Dto
{
    /**
     * Constructor
     *
     * @param string $bucket The S3 bucket name
     * @param string $key    The S3 object key
     */
    public function __construct(
        public readonly string $bucket,
        public readonly string $key,
    ) {
    }
}
