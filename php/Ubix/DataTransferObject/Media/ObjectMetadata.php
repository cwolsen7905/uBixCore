<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Media;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for an object store's own record of a stored object
 *
 * Returned by {@see \Ubix\Service\Media\MediaStorageServiceInterface::inspectObject()} and
 * {@see \Ubix\Service\Media\MediaStorageServiceInterface::putObject()}. Every value here is
 * what the object store itself reports, never what an uploading client claimed — a
 * host that needs to validate an upload (declared size, sniffed content type, and so
 * on) compares the claim it stored earlier against this DTO, not the other way round.
 *
 * @see \Ubix\Tests\DataTransferObject\Media\ObjectMetadataTest PHPUnit test case
 */
final readonly class ObjectMetadata implements Dto
{
    /**
     * Constructor
     *
     * @param string  $objectKey                 The object's key in the store
     * @param string  $contentType               The content type as recorded by the object store, not the value a client declared at presign time
     * @param int     $byteSize                  The object's size in bytes, as reported by the object store
     * @param ?string $checksum                  An integrity checksum for the stored bytes (e.g. an ETag) if the backend exposes one, otherwise `null` — never guaranteed across every S3-compatible backend, so callers must treat this as an optional extra check, not a required one
     * @param int     $lastModifiedUnixTimestamp Unix timestamp of the object's last write, as reported by the object store
     */
    public function __construct(
        public readonly string $objectKey = '',
        public readonly string $contentType = '',
        public readonly int $byteSize = 0,
        public readonly ?string $checksum = null,
        public readonly int $lastModifiedUnixTimestamp = 0,
    ) {
    }
}
