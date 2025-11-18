<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\AttributionLogId;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of an attribution log entry
 *
 * @see \Ubix\Tests\Model\AttributionLogTest PHPUnit test case
 */
final class AttributionLog extends Model
{
    /**
     * Constructor
     *
     * @param ?AttributionLogId $id            The ID
     * @param ?PlatformUserId   $userId        The user's ID
     * @param ?Varchar          $method        The method in the process
     * @param ?MpCode           $oldMpCode     The old MP code
     * @param ?MpCode           $newMpCode     The new MP code
     * @param ?Varchar          $reason        The reason for the change
     * @param ?Varchar          $bountyPaid    The bounty paid
     * @param ?UbixDateTime      $dateTimeEvent The date of the event
     */
    public function __construct(
        private ?AttributionLogId $id = null,
        private ?PlatformUserId $userId = null,
        private ?Varchar $method = null,
        private ?MpCode $oldMpCode = null,
        private ?MpCode $newMpCode = null,
        private ?Varchar $reason = null,
        private ?Varchar $bountyPaid = null,
        private ?UbixDateTime $dateTimeEvent = null,
    ) {
    }

    /**
     * Get the value of id
     *
     * @return AttributionLogId The value of id
     */
    public function getId(): ?AttributionLogId
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param AttributionLogId $id The value for id
     *
     * @return void
     */
    public function setId(?AttributionLogId $id): void
    {
        $this->id = $id;
        $this->markChanged('id');
    }

    /**
     * Get the value of userId
     *
     * @return PlatformUserId The value of userId
     */
    public function getUserId(): ?PlatformUserId
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param PlatformUserId $userId The value for userId
     *
     * @return void
     */
    public function setUserId(?PlatformUserId $userId): void
    {
        $this->userId = $userId;
        $this->markChanged('userId');
    }

    /**
     * Get the value of step
     *
     * @return Varchar The value of method
     */
    public function getMethod(): ?Varchar
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @param Varchar $method The value for method
     *
     * @return void
     */
    public function setMethod(?Varchar $method): void
    {
        $this->method = $method;
        $this->markChanged('method');
    }

    /**
     * Get the value of oldMpCode
     *
     * @return MpCode The value of oldMpCode
     */
    public function getOldMpCode(): ?MpCode
    {
        return $this->oldMpCode;
    }

    /**
     * Set the value of oldMpCode
     *
     * @param MpCode $oldMpCode The value for oldMpCode
     *
     * @return void
     */
    public function setOldMpCode(?MpCode $oldMpCode): void
    {
        $this->oldMpCode = $oldMpCode;
        $this->markChanged('oldMpCode');
    }

    /**
     * Get the value of newMpCode
     *
     * @return MpCode The value of newMpCode
     */
    public function getNewMpCode(): ?MpCode
    {
        return $this->newMpCode;
    }

    /**
     * Set the value of newMpCode
     *
     * @param MpCode $newMpCode The value for newMpCode
     *
     * @return void
     */
    public function setNewMpCode(?MpCode $newMpCode): void
    {
        $this->newMpCode = $newMpCode;
        $this->markChanged('newMpCode');
    }

    /**
     * Get the value of reason
     *
     * @return Varchar The value of reason
     */
    public function getReason(): ?Varchar
    {
        return $this->reason;
    }

    /**
     * Set the value of reason
     *
     * @param Varchar $reason The value for reason
     *
     * @return void
     */
    public function setReason(?Varchar $reason): void
    {
        $this->reason = $reason;
        $this->markChanged('reason');
    }

    /**
     * Get the value of bountyPaid
     *
     * @return Varchar The value of bountyPaid
     */
    public function getBountyPaid(): ?Varchar
    {
        return $this->bountyPaid;
    }

    /**
     * Set the value of bountyPaid
     *
     * @param Varchar $bountyPaid The value for bountyPaid
     *
     * @return void
     */
    public function setBountyPaid(?Varchar $bountyPaid): void
    {
        $this->bountyPaid = $bountyPaid;
        $this->markChanged('bountyPaid');
    }

    /**
     * Get the value of dateTimeEvent
     *
     * @return ?UbixDateTime The value of dateTimeEvent
     */
    public function getDateTimeEvent(): ?UbixDateTime
    {
        return $this->dateTimeEvent;
    }

    /**
     * Set the value of dateTimeEvent
     *
     * @param ?UbixDateTime $dateTimeEvent The value for dateTimeEvent
     *
     * @return void
     */
    public function setDateTimeEvent(?UbixDateTime $dateTimeEvent): void
    {
        $this->dateTimeEvent = $dateTimeEvent;
        $this->markChanged('dateTimeEvent');
    }
}
