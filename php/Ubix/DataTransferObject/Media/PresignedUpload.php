<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Media;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a browser-direct upload ticket
 *
 * Returned by {@see \Ubix\Service\Media\MediaStorageServiceInterface::createPresignedUpload()}.
 * Carries everything an end-user's browser needs to `PUT` bytes straight to the
 * object store without ever routing the payload through an app pod or seeing
 * storage credentials — and nothing else, so a host cannot accidentally leak
 * the underlying bucket name or credentials into a page it hands to a client.
 *
 * @see \Ubix\Tests\DataTransferObject\Media\PresignedUploadTest PHPUnit test case
 */
final readonly class PresignedUpload implements Dto
{
    /**
     * Constructor
     *
     * @param string                $objectKey              The opaque, server-generated key the uploaded bytes will occupy once the client finishes the `PUT` — never derived from caller input, so it cannot be guessed or enumerated (SRS NFR-MED-2 equivalent: opaque, non-sequential keys)
     * @param string                $uploadUrl              The presigned URL the client `PUT`s the object's bytes to directly
     * @param string                $httpMethod             The HTTP method the presigned URL is valid for; carried explicitly (rather than assumed to always be `PUT`) so a future presign strategy (e.g. a POST policy) doesn't silently break a caller that hard-coded the method
     * @param array<string, string> $requiredHeaders        Headers the client must send byte-for-byte unmodified with the upload for the signature to validate (typically `Content-Type`); a caller that omits or alters one of these gets a signature-mismatch error from the object store, not from this framework
     * @param int                   $expiresAtUnixTimestamp Unix timestamp after which the upload URL stops working; callers use this to bound how long they hold an upload open, never as a source of truth for whether the object exists (call {@see \Ubix\Service\Media\MediaStorageServiceInterface::inspectObject()} for that)
     */
    public function __construct(
        public readonly string $objectKey = '',
        public readonly string $uploadUrl = '',
        public readonly string $httpMethod = 'PUT',
        public readonly array $requiredHeaders = [],
        public readonly int $expiresAtUnixTimestamp = 0,
    ) {
    }
}
