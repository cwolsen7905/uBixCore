<?php

declare(strict_types=1);

namespace Ubix\Service\Blob;

/**
 * Interface for managing the storage of blobs
 */
interface BlobServiceInterface
{
    /**
     * Upload a blob to be stored
     *
     * @param string $key      The key to store the blob as
     * @param string $contents The contents of the blob
     *
     * @return void
     */
    public function put(string $key, string $contents): void;

    /**
     * Get the contents of a stored blob
     *
     * @param string $key The key of the blob
     *
     * @return string The contents of the blob
     */
    public function get(string $key): string;

    /**
     * Get a URL to access a stored blob
     *
     * @param string $key    The key of the blob
     * @param int    $expiry The expiration (in seconds) of the URL, use zero for no expiration (default: 0) (optional)
     *
     * @return string The URL to access the stored blob
     */
    public function url(string $key, int $expiry = 0): string;

    /**
     * Delete a stored blob
     *
     * @param string $key The key of the blob
     *
     * @return void
     */
    public function delete(string $key): void;

    /**
     * List stored blobs
     *
     * @param string $path The path to list
     *
     * @return object[] An array of stored blobs in the path
     */
    public function list(string $path): array;
}
