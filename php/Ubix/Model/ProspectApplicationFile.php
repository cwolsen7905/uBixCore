<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\ProspectDocument\ProspectDocumentType;
use Ubix\Enum\ProspectImage\ProspectImageType;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a prospect application file
 *
 * @see \Ubix\Tests\Model\ProspectApplicationFileTest PHPUnit test case
 */
final class ProspectApplicationFile extends Model
{
    /**
     * Constructor
     *
     * @param ProspectDocumentType|ProspectImageType|null $type        The file's type
     * @param ?string                                     $url         The file's URL
     * @param ?string                                     $md5Hash     The file's MD5 hash
     * @param ?DateTimeInterface                          $dateCreated When the section was created
     */
    public function __construct(
        private ProspectDocumentType|ProspectImageType|null $type = null,
        private ?string $url = null,
        private ?string $md5Hash = null,
        private ?DateTimeInterface $dateCreated = null,
    ) {
    }

    /**
     * Get the value of type
     *
     * @return ProspectDocumentType|ProspectImageType|null The value of type
     */
    public function getType(): ProspectDocumentType|ProspectImageType|null
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param ProspectDocumentType|ProspectImageType|null $type The value for type
     *
     * @return void
     */
    public function setType(ProspectDocumentType|ProspectImageType|null $type): void
    {
        $this->type = $type;
    }

    /**
     * Get the value of url
     *
     * @return ?string The value of url
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param ?string $url The value for url
     *
     * @return void
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
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
     * Get the value of dateCreated
     *
     * @return ?DateTimeInterface The value of dateCreated
     */
    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?DateTimeInterface $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }
}
