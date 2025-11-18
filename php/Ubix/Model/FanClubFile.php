<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a fan club file
 *
 * @see \Ubix\Tests\Model\FanClubFileTest PHPUnit test case
 */
final class FanClubFile extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id              The file's ID
     * @param ?string            $fileHash        The file's hash
     * @param ?string            $originalName    The file's original name
     * @param ?string            $mimeType        The file's MIME type
     * @param ?int               $sizeBytes       The file's size in bytes
     * @param ?int               $width           The media file's width
     * @param ?int               $height          The media file's height
     * @param ?float             $duration        The video file's duration
     * @param ?string            $storageType     Type of storage
     * @param ?string            $handle          The file's handle
     * @param ?int               $isTransformed   If the file has been transformed
     * @param ?string            $status          The file's status
     * @param ?string            $transformUuid   The UUID for the transformation
     * @param ?string            $thumbnailHandle The thumbnail's handle
     * @param ?DateTimeInterface $createdAt       The timestamp of when the file was created
     * @param ?DateTimeInterface $updatedAt       The timestamp of when the file was updated
     * @param ?string            $fileUrl         The file's URL
     * @param ?string            $thumbnailUrl    The file's thumbnail
     */
    public function __construct(
        private ?int $id = null,
        private ?string $fileHash = null,
        private ?string $originalName = null,
        private ?string $mimeType = null,
        private ?int $sizeBytes = null,
        private ?int $width = null,
        private ?int $height = null,
        private ?float $duration = null,
        private ?string $storageType = null,
        private ?string $handle = null,
        private ?int $isTransformed = null,
        private ?string $status = null,
        private ?string $transformUuid = null,
        private ?string $thumbnailHandle = null,
        private ?DateTimeInterface $createdAt = null,
        private ?DateTimeInterface $updatedAt = null,
        private ?string $fileUrl = null,
        private ?string $thumbnailUrl = null,
    ) {
    }

    /**
     * Gets the type of file
     *
     * @return ?string Either 'video', 'image' or null
     */
    public function getFileType(): ?string
    {
        if ($this->getMimeType() === null) {
            return null;
        } elseif (str_starts_with($this->getMimeType(), 'video/')) {
            return 'video';
        } else {
            return str_starts_with($this->getMimeType(), 'image/') ? 'image' : null;
        }
    }

    /**
     * Get the value of id
     *
     * @return ?int The value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id The value for id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of fileHash
     *
     * @return ?string The value of fileHash
     */
    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    /**
     * Set the value of fileHash
     *
     * @param ?string $fileHash The value for fileHash
     *
     * @return void
     */
    public function setFileHash(?string $fileHash): void
    {
        $this->fileHash = $fileHash;
    }

    /**
     * Get the value of originalName
     *
     * @return ?string The value of originalName
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * Set the value of originalName
     *
     * @param ?string $originalName The value for originalName
     *
     * @return void
     */
    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    /**
     * Get the value of mimeType
     *
     * @return ?string The value of mimeType
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Set the value of mimeType
     *
     * @param ?string $mimeType The value for mimeType
     *
     * @return void
     */
    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * Get the value of sizeBytes
     *
     * @return ?int The value of sizeBytes
     */
    public function getSizeBytes(): ?int
    {
        return $this->sizeBytes;
    }

    /**
     * Set the value of sizeBytes
     *
     * @param ?int $sizeBytes The value for sizeBytes
     *
     * @return void
     */
    public function setSizeBytes(?int $sizeBytes): void
    {
        $this->sizeBytes = $sizeBytes;
    }

    /**
     * Get the value of width
     *
     * @return ?int The value of width
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Set the value of width
     *
     * @param ?int $width The value for width
     *
     * @return void
     */
    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    /**
     * Get the value of height
     *
     * @return ?int The value of height
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * Set the value of height
     *
     * @param ?int $height The value for height
     *
     * @return void
     */
    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    /**
     * Get the value of duration
     *
     * @return ?float The value of duration
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * Set the value of duration
     *
     * @param ?float $duration The value for duration
     *
     * @return void
     */
    public function setDuration(?float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Get the value of storageType
     *
     * @return ?string The value of storageType
     */
    public function getStorageType(): ?string
    {
        return $this->storageType;
    }

    /**
     * Set the value of storageType
     *
     * @param ?string $storageType The value for storageType
     *
     * @return void
     */
    public function setStorageType(?string $storageType): void
    {
        $this->storageType = $storageType;
    }

    /**
     * Get the value of handle
     *
     * @return ?string The value of handle
     */
    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * Set the value of handle
     *
     * @param ?string $handle The value for handle
     *
     * @return void
     */
    public function setHandle(?string $handle): void
    {
        $this->handle = $handle;
    }

    /**
     * Get the value of isTransformed
     *
     * @return ?int The value of isTransformed
     */
    public function getIsTransformed(): ?int
    {
        return $this->isTransformed;
    }

    /**
     * Set the value of isTransformed
     *
     * @param ?int $isTransformed The value for isTransformed
     *
     * @return void
     */
    public function setIsTransformed(?int $isTransformed): void
    {
        $this->isTransformed = $isTransformed;
    }

    /**
     * Get the value of status
     *
     * @return ?string The value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?string $status The value for status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of transformUuid
     *
     * @return ?string The value of transformUuid
     */
    public function getTransformUuid(): ?string
    {
        return $this->transformUuid;
    }

    /**
     * Set the value of transformUuid
     *
     * @param ?string $transformUuid The value for transformUuid
     *
     * @return void
     */
    public function setTransformUuid(?string $transformUuid): void
    {
        $this->transformUuid = $transformUuid;
    }

    /**
     * Get the value of thumbnailHandle
     *
     * @return ?string The value of thumbnailHandle
     */
    public function getThumbnailHandle(): ?string
    {
        return $this->thumbnailHandle;
    }

    /**
     * Set the value of thumbnailHandle
     *
     * @param ?string $thumbnailHandle The value for thumbnailHandle
     *
     * @return void
     */
    public function setThumbnailHandle(?string $thumbnailHandle): void
    {
        $this->thumbnailHandle = $thumbnailHandle;
    }

    /**
     * Get the value of createdAt
     *
     * @return ?DateTimeInterface The value of createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param ?DateTimeInterface $createdAt The value for createdAt
     *
     * @return void
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get the value of updatedAt
     *
     * @return ?DateTimeInterface The value of updatedAt
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param ?DateTimeInterface $updatedAt The value for updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get the value of fileUrl
     *
     * @return ?string The value of fileUrl
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * Set the value of fileUrl
     *
     * @param ?string $fileUrl The value for fileUrl
     *
     * @return void
     */
    public function setFileUrl(?string $fileUrl): void
    {
        $this->fileUrl = $fileUrl;
    }

    /**
     * Get the value of thumbnailUrl
     *
     * @return ?string The value of thumbnailUrl
     */
    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    /**
     * Set the value of thumbnailUrl
     *
     * @param ?string $thumbnailUrl The value for thumbnailUrl
     *
     * @return void
     */
    public function setThumbnailUrl(?string $thumbnailUrl): void
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }
}
