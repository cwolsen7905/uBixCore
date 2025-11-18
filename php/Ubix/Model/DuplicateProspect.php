<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\DuplicateProspect\DuplicateProspectStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a admin user
 *
 * @see \Ubix\Tests\Model\DuplicateProspectTest PHPUnit test case
 */
final class DuplicateProspect extends Model
{
    /**
     * Constructor
     *
     * @param ?int                     $id             The duplicate's ID
     * @param ?DuplicateProspectStatus $reviewStatus   The duplicate's review's status
     * @param ?int                     $reviewAdminId  The duplicate's review's admin ID
     * @param ?string                  $reviewComments The duplicate's review's comments
     * @param ?DateTimeInterface       $reviewDate     The duplicate's review's date
     * @param ?DateTimeInterface       $dateCreated    The duplicate's creation time
     * @param ?string                  $submissionJson The duplicate's submission in JSON format
     */
    public function __construct(
        private ?int $id = null,
        private ?DuplicateProspectStatus $reviewStatus = null,
        private ?int $reviewAdminId = null,
        private ?string $reviewComments = null,
        private ?DateTimeInterface $reviewDate = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?string $submissionJson = null,
    ) {
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
     * Get the value of reviewStatus
     *
     * @return ?DuplicateProspectStatus The value of reviewStatus
     */
    public function getReviewStatus(): ?DuplicateProspectStatus
    {
        return $this->reviewStatus;
    }

    /**
     * Set the value of reviewStatus
     *
     * @param ?DuplicateProspectStatus $reviewStatus The value for reviewStatus
     *
     * @return void
     */
    public function setReviewStatus(?DuplicateProspectStatus $reviewStatus): void
    {
        $this->reviewStatus = $reviewStatus;
    }

    /**
     * Get the value of reviewAdminId
     *
     * @return ?int The value of reviewAdminId
     */
    public function getReviewAdminId(): ?int
    {
        return $this->reviewAdminId;
    }

    /**
     * Set the value of reviewAdminId
     *
     * @param ?int $reviewAdminId The value for reviewAdminId
     *
     * @return void
     */
    public function setReviewAdminId(?int $reviewAdminId): void
    {
        $this->reviewAdminId = $reviewAdminId;
    }

    /**
     * Get the value of reviewComments
     *
     * @return ?string The value of reviewComments
     */
    public function getReviewComments(): ?string
    {
        return $this->reviewComments;
    }

    /**
     * Set the value of reviewComments
     *
     * @param ?string $reviewComments The value for reviewComments
     *
     * @return void
     */
    public function setReviewComments(?string $reviewComments): void
    {
        $this->reviewComments = $reviewComments;
    }

    /**
     * Get the value of reviewDate
     *
     * @return ?DateTimeInterface The value of reviewDate
     */
    public function getReviewDate(): ?DateTimeInterface
    {
        return $this->reviewDate;
    }

    /**
     * Set the value of reviewDate
     *
     * @param ?DateTimeInterface $reviewDate The value for reviewDate
     *
     * @return void
     */
    public function setReviewDate(?DateTimeInterface $reviewDate): void
    {
        $this->reviewDate = $reviewDate;
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

    /**
     * Get the value of submissionJson
     *
     * @return ?string The value of submissionJson
     */
    public function getSubmissionJson(): ?string
    {
        return $this->submissionJson;
    }

    /**
     * Set the value of submissionJson
     *
     * @param ?string $submissionJson The value for submissionJson
     *
     * @return void
     */
    public function setSubmissionJson(?string $submissionJson): void
    {
        $this->submissionJson = $submissionJson;
    }
}
