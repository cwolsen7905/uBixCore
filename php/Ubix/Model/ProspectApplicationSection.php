<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\ProspectApplication\ProspectApplicationSectionStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a prospect application section
 *
 * @see \Ubix\Tests\Model\ProspectApplicationSectionTest PHPUnit test case
 */
final class ProspectApplicationSection extends Model
{
    public const NAME_EMAIL_ADDRESS_CONFIRMATION = 'email_address_confirmation';

    public const NAME_PERSONAL_DETAILS = 'personal_data';

    public const NAME_STAGE_NAME = 'stage_name';

    /**
     * Constructor
     *
     * @param ?string                           $name               The section's name
     * @param ?ProspectApplicationSectionStatus $status             The section's status
     * @param ?DateTimeInterface                $approvalTimestamp  When the section was approved
     * @param ?string                           $rejectionReason    The reason for the rejection
     * @param ?DateTimeInterface                $rejectionTimestamp When the section was rejected
     * @param ?DateTimeInterface                $dateCreated        When the section was created
     * @param ?DateTimeInterface                $dateUpdated        When the section was updated
     * @param ?string                           $rejectSuggest1     The first suggested name
     * @param ?string                           $rejectSuggest2     The second suggested name
     * @param ?string                           $rejectSuggest3     The third suggested name
     */
    public function __construct(
        private ?string $name = null,
        private ?ProspectApplicationSectionStatus $status = null,
        private ?DateTimeInterface $approvalTimestamp = null,
        private ?string $rejectionReason = null,
        private ?DateTimeInterface $rejectionTimestamp = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?DateTimeInterface $dateUpdated = null,
        private ?string $rejectSuggest1 = null,
        private ?string $rejectSuggest2 = null,
        private ?string $rejectSuggest3 = null,
    ) {
    }

    /**
     * Get the value of name
     *
     * @return ?string The value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param ?string $name The value for name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the value of status
     *
     * @return ?ProspectApplicationSectionStatus The value of status
     */
    public function getStatus(): ?ProspectApplicationSectionStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?ProspectApplicationSectionStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?ProspectApplicationSectionStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of approvalTimestamp
     *
     * @return ?DateTimeInterface The value of approvalTimestamp
     */
    public function getApprovalTimestamp(): ?DateTimeInterface
    {
        return $this->approvalTimestamp;
    }

    /**
     * Set the value of approvalTimestamp
     *
     * @param ?DateTimeInterface $approvalTimestamp The value for approvalTimestamp
     *
     * @return void
     */
    public function setApprovalTimestamp(?DateTimeInterface $approvalTimestamp): void
    {
        $this->approvalTimestamp = $approvalTimestamp;
    }

    /**
     * Get the value of rejectionReason
     *
     * @return ?string The value of rejectionReason
     */
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    /**
     * Set the value of rejectionReason
     *
     * @param ?string $rejectionReason The value for rejectionReason
     *
     * @return void
     */
    public function setRejectionReason(?string $rejectionReason): void
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the value of rejectionTimestamp
     *
     * @return ?DateTimeInterface The value of rejectionTimestamp
     */
    public function getRejectionTimestamp(): ?DateTimeInterface
    {
        return $this->rejectionTimestamp;
    }

    /**
     * Set the value of rejectionTimestamp
     *
     * @param ?DateTimeInterface $rejectionTimestamp The value for rejectionTimestamp
     *
     * @return void
     */
    public function setRejectionTimestamp(?DateTimeInterface $rejectionTimestamp): void
    {
        $this->rejectionTimestamp = $rejectionTimestamp;
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
     * Get the value of dateUpdated
     *
     * @return ?DateTimeInterface The value of dateUpdated
     */
    public function getDateUpdated(): ?DateTimeInterface
    {
        return $this->dateUpdated;
    }

    /**
     * Set the value of dateUpdated
     *
     * @param ?DateTimeInterface $dateUpdated The value for dateUpdated
     *
     * @return void
     */
    public function setDateUpdated(?DateTimeInterface $dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * Get the value of rejectSuggest1
     *
     * @return ?string The value of rejectSuggest1
     */
    public function getRejectSuggest1(): ?string
    {
        return $this->rejectSuggest1;
    }

    /**
     * Set the value of rejectSuggest1
     *
     * @param ?string $rejectSuggest1 The value for rejectSuggest1
     *
     * @return void
     */
    public function setRejectSuggest1(?string $rejectSuggest1): void
    {
        $this->rejectSuggest1 = $rejectSuggest1;
    }

    /**
     * Get the value of rejectSuggest2
     *
     * @return ?string The value of rejectSuggest2
     */
    public function getRejectSuggest2(): ?string
    {
        return $this->rejectSuggest2;
    }

    /**
     * Set the value of rejectSuggest2
     *
     * @param ?string $rejectSuggest2 The value for rejectSuggest2
     *
     * @return void
     */
    public function setRejectSuggest2(?string $rejectSuggest2): void
    {
        $this->rejectSuggest2 = $rejectSuggest2;
    }

    /**
     * Get the value of rejectSuggest3
     *
     * @return ?string The value of rejectSuggest3
     */
    public function getRejectSuggest3(): ?string
    {
        return $this->rejectSuggest3;
    }

    /**
     * Set the value of rejectSuggest3
     *
     * @param ?string $rejectSuggest3 The value for rejectSuggest3
     *
     * @return void
     */
    public function setRejectSuggest3(?string $rejectSuggest3): void
    {
        $this->rejectSuggest3 = $rejectSuggest3;
    }
}
