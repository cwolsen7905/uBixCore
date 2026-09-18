<?php

declare(strict_types=1);

namespace Ubix\Service\Media;

use Ubix\DataTransferObject\Media\ObjectMetadata;
use Ubix\DataTransferObject\Media\PresignedUpload;
use Ubix\DataTransferObject\Media\SignedUrl;

/**
 * Interface for a vendor-agnostic object-storage seam
 *
 * A host that stores user-generated files (images, audio, video, exports —
 * anything binary and larger than belongs in a database row) depends on this
 * interface, never on a specific object-store SDK. The concrete implementation
 * is free to be S3, an S3-compatible self-hosted store, or anything else that
 * can satisfy these six operations; swapping it is a DI binding change, not a
 * call-site change.
 *
 * Two upload paths are covered because they have genuinely different trust
 * boundaries: {@see self::createPresignedUpload()} hands upload capability to
 * an untrusted end-user's browser without the object ever passing through the
 * host's own servers, while {@see self::putObject()} is for trusted server-side
 * code that already holds the bytes locally (a batch job, a transcoding
 * pipeline, a CLI import) and uploads them directly. Both paths converge on the
 * same key space and the same {@see ObjectMetadata} shape, so a host's data
 * model does not need to special-case which path populated a given object.
 *
 * Object keys are deliberately opaque and are minted by the implementation,
 * never accepted from a caller: a host that could choose its own keys could
 * make them predictable (sequential ids, slugs, timestamps), which would let
 * one caller guess or enumerate another's objects. A caller that needs to
 * remember which key belongs to which of its own records stores the returned
 * key itself; this interface has no notion of ownership, ACL, quota, or any
 * other host-specific concept — that all lives above this seam.
 *
 * @see \Ubix\Service\Media\S3MediaStorageService The S3-compatible implementation
 */
interface MediaStorageServiceInterface
{
    /**
     * Reserve a new object key and issue a short-lived, single-use presigned upload for it
     *
     * The returned {@see PresignedUpload} lets an end-user's browser `PUT` the
     * object's bytes straight to the store; the host's own servers never see
     * the payload. The declared `$contentType` is only a hint used to shape
     * the signature — a host must not trust it as the object's real content
     * type once uploaded (see {@see self::inspectObject()}).
     *
     * @param string $contentType The MIME type the client declares it will upload; carried into the signature so the object is written with this content type, but not verified until {@see self::inspectObject()} reads it back from the store
     * @param int    $ttlSeconds  How long the presigned URL remains valid, in seconds; short-lived and scoped to exactly one object, per the platform's signed-URL security posture
     *
     * @return PresignedUpload The upload ticket
     */
    public function createPresignedUpload(string $contentType, int $ttlSeconds): PresignedUpload;

    /**
     * Read an object's store-verified metadata
     *
     * Used to confirm that a presigned upload actually landed, and to learn
     * the object's real size and content type as the store recorded them —
     * never as the uploading client declared them. A host's "confirm this
     * upload" step calls this and compares the result against whatever it
     * provisionally recorded when it issued the presigned upload.
     *
     * @param string $objectKey The object's key, as returned by {@see self::createPresignedUpload()} or {@see self::putObject()}
     *
     * @throws \Ubix\Exception\DtoException If no object exists at that key (an upload that never completed, or a key that was never issued)
     *
     * @return ObjectMetadata The object's store-verified metadata
     */
    public function inspectObject(string $objectKey): ObjectMetadata;

    /**
     * Upload a local file directly, on behalf of trusted server-side code
     *
     * No presigning is involved because the caller is already-authenticated
     * server code that holds the bytes locally (for example, a transcoding
     * job writing out a derivative, or a CLI import) rather than an end
     * user's browser — see {@see self::createPresignedUpload()} for that path.
     *
     * @param string $localFilePath The absolute path of the local file to upload; the caller is responsible for cleaning it up afterwards, this method never deletes its input
     * @param string $contentType   The object's content type as the caller (not an end user) has determined it
     *
     * @throws \Ubix\Exception\DtoException If the local file cannot be read, or the upload fails
     *
     * @return ObjectMetadata The newly-stored object's metadata, including its minted key
     */
    public function putObject(string $localFilePath, string $contentType): ObjectMetadata;

    /**
     * Mint a short-lived URL for reading one object
     *
     * The delivery path for gated or private content: every read gets a
     * freshly-derived URL rather than one persisted or cached client-side, so
     * revoking access is as simple as no longer minting new URLs (platform
     * TDS §9).
     *
     * @param string $objectKey  The object's key
     * @param int    $ttlSeconds How long the signed URL remains valid, in seconds
     *
     * @return SignedUrl The signed, single-object URL
     */
    public function createSignedReadUrl(string $objectKey, int $ttlSeconds): SignedUrl;

    /**
     * The stable, non-expiring URL for an object that is meant to be public
     *
     * Only for objects a host has already decided are safe to serve to
     * anyone with the URL (typically fronted by a CDN) — this method performs
     * no access check of its own, because this interface has no concept of
     * visibility or gating; a host must not call this for an object it has
     * not already decided is public.
     *
     * @param string $objectKey The object's key
     *
     * @return string The object's public URL
     */
    public function publicUrl(string $objectKey): string;

    /**
     * Permanently remove an object
     *
     * @param string $objectKey The object's key
     *
     * @throws \Ubix\Exception\DtoException If the delete fails
     *
     * @return void
     */
    public function deleteObject(string $objectKey): void;
}
