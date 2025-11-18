<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\Int\TransactionId;
use Ubix\DataType\String\MpCode;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a transacton
 *
 * @see \Ubix\Tests\Model\TransactionTest PHPUnit test case
 */
final class Transaction extends Model
{
    /**
     * Constructor
     *
     * @param ?TransactionId  $id         The transaction's ID
     * @param ?PlatformUserId $userId     The transaction's user id
     * @param ?MpCode         $mpCode     The transaction's mp_code
     * @param ?UsdCurrency    $amount     The transaction's amount
     * @param ?UbixDateTime    $datetime   The transaction's datetime
     * @param ?MpCode         $userMpCode The transaction's user mp_code
     */
    public function __construct(
        private ?TransactionId $id = null,
        private ?PlatformUserId $userId = null,
        private ?MpCode $mpCode = null,
        private ?UsdCurrency $amount = null,
        private ?UbixDateTime $datetime = null,
        private ?MpCode $userMpCode = null,
    ) {
    }

    /**
     * Get the value of id
     *
     * @return ?TransactionId The value of id
     */
    public function getId(): ?TransactionId
    {
        return $this->id;
    }

    /**
     * Get the value of userId
     *
     * @return ?PlatformUserId The value of userId
     */
    public function getUserId(): ?PlatformUserId
    {
        return $this->userId;
    }

    /**
     * Get the value of mp_code
     *
     * @return ?MpCode The value of mp_code
     */
    public function getMpCode(): ?MpCode
    {
        return $this->mpCode;
    }

    /**
     * Get the value of amount
     *
     * @return ?UsdCurrency The value of amount
     */
    public function getAmount(): ?UsdCurrency
    {
        return $this->amount;
    }

    /**
     * Get the value of datetime
     *
     * @return ?UbixDateTime The value of datetime
     */
    public function getDatetime(): ?UbixDateTime
    {
        return $this->datetime;
    }

    /**
     * Get the value of user_mp_code
     *
     * @return ?MpCode The value of user_mp_code
     */
    public function getUserMpCode(): ?MpCode
    {
        return $this->userMpCode;
    }

    /**
     * Set the value of id
     *
     * @param ?TransactionId $id The value for id
     *
     * @return void
     */
    public function setId(?TransactionId $id): void
    {
        $this->id = $id;
    }

    /**
     * Set the value of userId
     *
     * @param ?PlatformUserId $userId The value for userId
     *
     * @return void
     */
    public function setUserId(?PlatformUserId $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Set the value of mpCode
     *
     * @param ?MpCode $mpCode The value for mpCode
     *
     * @return void
     */
    public function setMpCode(?MpCode $mpCode): void
    {
        $this->mpCode = $mpCode;
    }

    /**
     * Set the value of amount
     *
     * @param ?UsdCurrency $amount The value for amount
     *
     * @return void
     */
    public function setAmount(?UsdCurrency $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Set the value of datetime
     *
     * @param ?UbixDateTime $datetime The value for datetime
     *
     * @return void
     */
    public function setDatetime(?UbixDateTime $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * Set the value of userMpCode
     *
     * @param ?MpCode $userMpCode The value for userMpCode
     *
     * @return void
     */
    public function setUserMpCode(?MpCode $userMpCode): void
    {
        $this->userMpCode = $userMpCode;
    }
}
