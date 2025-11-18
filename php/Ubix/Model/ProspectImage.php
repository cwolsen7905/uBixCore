<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a prospect image
 *
 * @see \Ubix\Tests\Model\ProspectImageTest PHPUnit test case
 */
final class ProspectImage extends Model
{
    public const DISPLAY_URL_PATH = '/api/application/image/';
    public const FILE_PATH        = '/mnt/vol-upload-tpls/model-signup/';

    /**
     * Constructor
     *
     * @param ?string            $md5Hash                The image's MD5 hash
     * @param ?int               $prospectId             The prospect's ID
     * @param ?DateTimeInterface $uploadDate             The image's upload date
     * @param ?string            $fileName               The image's file name
     * @param ?float             $fileSize               The image's file size
     * @param ?string            $fileType               The image's file type (MIME type)
     * @param ?ProspectImageType $imageType              The image's type
     * @param ?string            $binaryImageData        The image's binary data
     * @param ?int               $buildStatus            The build status
     * @param ?DateTimeInterface $purgeRequestedDatetime The timestamp of the purge request
     */
    public function __construct(
        private ?string $md5Hash = null,
        private ?int $prospectId = null,
        private ?DateTimeInterface $uploadDate = null,
        private ?string $fileName = null,
        private ?float $fileSize = null,
        private ?string $fileType = null,
        private ?ProspectImageType $imageType = null,
        private ?string $binaryImageData = null,
        private ?int $buildStatus = null,
        private ?DateTimeInterface $purgeRequestedDatetime = null,
    ) {
    }

    /**
     * Get the value of md5Hash
     *
     * @return ?string The value of md5Hash
     */
    public function getMd5Hash(): ?string
    {
        return $this->md5Hash;
    }

    /**
     * Set the value of md5Hash
     *
     * @param ?string $md5Hash The value for md5Hash
     *
     * @return void
     */
    public function setMd5Hash(?string $md5Hash): void
    {
        $this->md5Hash = $md5Hash;
    }

    /**
     * Get the value of prospectId
     *
     * @return ?int The value of prospectId
     */
    public function getProspectId(): ?int
    {
        return $this->prospectId;
    }

    /**
     * Set the value of prospectId
     *
     * @param ?int $prospectId The value for prospectId
     *
     * @return void
     */
    public function setProspectId(?int $prospectId): void
    {
        $this->prospectId = $prospectId;
    }

    /**
     * Get the value of uploadDate
     *
     * @return ?DateTimeInterface The value of uploadDate
     */
    public function getUploadDate(): ?DateTimeInterface
    {
        return $this->uploadDate;
    }

    /**
     * Set the value of uploadDate
     *
     * @param ?DateTimeInterface $uploadDate The value for uploadDate
     *
     * @return void
     */
    public function setUploadDate(?DateTimeInterface $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * Get the value of fileName
     *
     * @return ?string The value of fileName
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     *
     * @param ?string $fileName The value for fileName
     *
     * @return void
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * Get the value of fileSize
     *
     * @return ?float The value of fileSize
     */
    public function getFileSize(): ?float
    {
        return $this->fileSize;
    }

    /**
     * Set the value of fileSize
     *
     * @param ?float $fileSize The value for fileSize
     *
     * @return void
     */
    public function setFileSize(?float $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * Get the value of fileType
     *
     * @return ?string The value of fileType
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * Set the value of fileType
     *
     * @param ?string $fileType The value for fileType
     *
     * @return void
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * Get the value of imageType
     *
     * @return ?ProspectImageType The value of imageType
     */
    public function getImageType(): ?ProspectImageType
    {
        return $this->imageType;
    }

    /**
     * Set the value of imageType
     *
     * @param ?ProspectImageType $imageType The value for imageType
     *
     * @return void
     */
    public function setImageType(?ProspectImageType $imageType): void
    {
        $this->imageType = $imageType;
    }

    /**
     * Get the value of binaryImageData
     *
     * @return ?string The value of binaryImageData
     */
    public function getBinaryImageData(): ?string
    {
        return $this->binaryImageData;
    }

    /**
     * Set the value of binaryImageData
     *
     * @param ?string $binaryImageData The value for binaryImageData
     *
     * @return void
     */
    public function setBinaryImageData(?string $binaryImageData): void
    {
        $this->binaryImageData = $binaryImageData;
    }

    /**
     * Get the value of buildStatus
     *
     * @return ?int The value of buildStatus
     */
    public function getBuildStatus(): ?int
    {
        return $this->buildStatus;
    }

    /**
     * Set the value of buildStatus
     *
     * @param ?int $buildStatus The value for buildStatus
     *
     * @return void
     */
    public function setBuildStatus(?int $buildStatus): void
    {
        $this->buildStatus = $buildStatus;
    }

    /**
     * Get the value of purgeRequestedDatetime
     *
     * @return ?DateTimeInterface The value of purgeRequestedDatetime
     */
    public function getPurgeRequestedDatetime(): ?DateTimeInterface
    {
        return $this->purgeRequestedDatetime;
    }

    /**
     * Set the value of purgeRequestedDatetime
     *
     * @param ?DateTimeInterface $purgeRequestedDatetime The value for purgeRequestedDatetime
     *
     * @return void
     */
    public function setPurgeRequestedDatetime(?DateTimeInterface $purgeRequestedDatetime): void
    {
        $this->purgeRequestedDatetime = $purgeRequestedDatetime;
    }

    /**
     * Get the display URL
     *
     * @return string The display URL
     */
    public function getDisplayUrl(): string
    {
        return self::DISPLAY_URL_PATH . ($this->getMd5Hash() ?? '');
    }

    /**
     * Get the image path
     *
     * @return string The image path
     */
    public function getImagePath(): string
    {
        return self::FILE_PATH . ($this->getMd5Hash() ?? '') . match ($this->getFileType()) {
            'image/jpeg'  => '.jpg',
            'image/jpg'   => '.jpg',
            'image/pjpeg' => '.jpg',
            'image/png'   => '.png',
            'image/tiff'  => '.tiff',
            default       => '',
        };
    }
}
