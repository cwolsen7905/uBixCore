<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform user payment
 *
 * @see \Ubix\Tests\Model\PlatformUserPaymentTest PHPUnit test case
 */
final class PlatformUserPayment extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id                  Placeholder for id // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $userId              Placeholder for userId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $processorId         Placeholder for processorId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $processorInvId      Placeholder for processorInvId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $paymentId           Placeholder for paymentId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $affiliatePaymentId  Placeholder for affiliatePaymentId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $pin                 Placeholder for pin // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $bin                 Placeholder for bin // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $lastDigits          Placeholder for lastDigits // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cardExpire          Placeholder for cardExpire // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cardExpireLastUsed  Placeholder for cardExpireLastUsed // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $paymentTypeId       Placeholder for paymentTypeId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $externalAccountId   Placeholder for externalAccountId // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateCreated         Placeholder for dateCreated // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateLastPurchased   Placeholder for dateLastPurchased // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $status              Placeholder for status // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $accountType         Placeholder for accountType // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $paymentType         Placeholder for paymentType // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $distributionGroupId Placeholder for distributionGroupId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cardCategory        Placeholder for cardCategory // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $typeOfCard          Placeholder for typeOfCard // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $countryCode         Placeholder for countryCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $cardBrand           Placeholder for cardBrand // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $issuingBank         Placeholder for issuingBank // NOT_IMPLEMENTED: replace placeholder
     */
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private ?int $processorId = null,
        private ?string $processorInvId = null,
        private ?string $paymentId = null,
        private ?string $affiliatePaymentId = null,
        private ?string $pin = null,
        private ?string $bin = null,
        private ?string $lastDigits = null,
        private ?string $cardExpire = null,
        private ?string $cardExpireLastUsed = null,
        private ?int $paymentTypeId = null,
        private ?int $externalAccountId = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?DateTimeInterface $dateLastPurchased = null,
        private ?int $status = null,
        private ?string $accountType = null,
        private ?string $paymentType = null,
        private ?int $distributionGroupId = null,
        private ?string $cardCategory = null,
        private ?string $typeOfCard = null,
        private ?string $countryCode = null,
        private ?string $cardBrand = null,
        private ?string $issuingBank = null,
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
     * Get the value of userId
     *
     * @return ?int The value of userId
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param ?int $userId The value for userId
     *
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get the value of processorId
     *
     * @return ?int The value of processorId
     */
    public function getProcessorId(): ?int
    {
        return $this->processorId;
    }

    /**
     * Set the value of processorId
     *
     * @param ?int $processorId The value for processorId
     *
     * @return void
     */
    public function setProcessorId(?int $processorId): void
    {
        $this->processorId = $processorId;
    }

    /**
     * Get the value of processorInvId
     *
     * @return ?string The value of processorInvId
     */
    public function getProcessorInvId(): ?string
    {
        return $this->processorInvId;
    }

    /**
     * Set the value of processorInvId
     *
     * @param ?string $processorInvId The value for processorInvId
     *
     * @return void
     */
    public function setProcessorInvId(?string $processorInvId): void
    {
        $this->processorInvId = $processorInvId;
    }

    /**
     * Get the value of paymentId
     *
     * @return ?string The value of paymentId
     */
    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    /**
     * Set the value of paymentId
     *
     * @param ?string $paymentId The value for paymentId
     *
     * @return void
     */
    public function setPaymentId(?string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
     * Get the value of affiliatePaymentId
     *
     * @return ?string The value of affiliatePaymentId
     */
    public function getAffiliatePaymentId(): ?string
    {
        return $this->affiliatePaymentId;
    }

    /**
     * Set the value of affiliatePaymentId
     *
     * @param ?string $affiliatePaymentId The value for affiliatePaymentId
     *
     * @return void
     */
    public function setAffiliatePaymentId(?string $affiliatePaymentId): void
    {
        $this->affiliatePaymentId = $affiliatePaymentId;
    }

    /**
     * Get the value of pin
     *
     * @return ?string The value of pin
     */
    public function getPin(): ?string
    {
        return $this->pin;
    }

    /**
     * Set the value of pin
     *
     * @param ?string $pin The value for pin
     *
     * @return void
     */
    public function setPin(?string $pin): void
    {
        $this->pin = $pin;
    }

    /**
     * Get the value of bin
     *
     * @return ?string The value of bin
     */
    public function getBin(): ?string
    {
        return $this->bin;
    }

    /**
     * Set the value of bin
     *
     * @param ?string $bin The value for bin
     *
     * @return void
     */
    public function setBin(?string $bin): void
    {
        $this->bin = $bin;
    }

    /**
     * Get the value of lastDigits
     *
     * @return ?string The value of lastDigits
     */
    public function getLastDigits(): ?string
    {
        return $this->lastDigits;
    }

    /**
     * Set the value of lastDigits
     *
     * @param ?string $lastDigits The value for lastDigits
     *
     * @return void
     */
    public function setLastDigits(?string $lastDigits): void
    {
        $this->lastDigits = $lastDigits;
    }

    /**
     * Get the value of cardExpire
     *
     * @return ?string The value of cardExpire
     */
    public function getCardExpire(): ?string
    {
        return $this->cardExpire;
    }

    /**
     * Set the value of cardExpire
     *
     * @param ?string $cardExpire The value for cardExpire
     *
     * @return void
     */
    public function setCardExpire(?string $cardExpire): void
    {
        $this->cardExpire = $cardExpire;
    }

    /**
     * Get the value of cardExpireLastUsed
     *
     * @return ?string The value of cardExpireLastUsed
     */
    public function getCardExpireLastUsed(): ?string
    {
        return $this->cardExpireLastUsed;
    }

    /**
     * Set the value of cardExpireLastUsed
     *
     * @param ?string $cardExpireLastUsed The value for cardExpireLastUsed
     *
     * @return void
     */
    public function setCardExpireLastUsed(?string $cardExpireLastUsed): void
    {
        $this->cardExpireLastUsed = $cardExpireLastUsed;
    }

    /**
     * Get the value of paymentTypeId
     *
     * @return ?int The value of paymentTypeId
     */
    public function getPaymentTypeId(): ?int
    {
        return $this->paymentTypeId;
    }

    /**
     * Set the value of paymentTypeId
     *
     * @param ?int $paymentTypeId The value for paymentTypeId
     *
     * @return void
     */
    public function setPaymentTypeId(?int $paymentTypeId): void
    {
        $this->paymentTypeId = $paymentTypeId;
    }

    /**
     * Get the value of externalAccountId
     *
     * @return ?int The value of externalAccountId
     */
    public function getExternalAccountId(): ?int
    {
        return $this->externalAccountId;
    }

    /**
     * Set the value of externalAccountId
     *
     * @param ?int $externalAccountId The value for externalAccountId
     *
     * @return void
     */
    public function setExternalAccountId(?int $externalAccountId): void
    {
        $this->externalAccountId = $externalAccountId;
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
     * Get the value of dateLastPurchased
     *
     * @return ?DateTimeInterface The value of dateLastPurchased
     */
    public function getDateLastPurchased(): ?DateTimeInterface
    {
        return $this->dateLastPurchased;
    }

    /**
     * Set the value of dateLastPurchased
     *
     * @param ?DateTimeInterface $dateLastPurchased The value for dateLastPurchased
     *
     * @return void
     */
    public function setDateLastPurchased(?DateTimeInterface $dateLastPurchased): void
    {
        $this->dateLastPurchased = $dateLastPurchased;
    }

    /**
     * Get the value of status
     *
     * @return ?int The value of status
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?int $status The value for status
     *
     * @return void
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of accountType
     *
     * @return ?string The value of accountType
     */
    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    /**
     * Set the value of accountType
     *
     * @param ?string $accountType The value for accountType
     *
     * @return void
     */
    public function setAccountType(?string $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * Get the value of paymentType
     *
     * @return ?string The value of paymentType
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * Set the value of paymentType
     *
     * @param ?string $paymentType The value for paymentType
     *
     * @return void
     */
    public function setPaymentType(?string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    /**
     * Get the value of distributionGroupId
     *
     * @return ?int The value of distributionGroupId
     */
    public function getDistributionGroupId(): ?int
    {
        return $this->distributionGroupId;
    }

    /**
     * Set the value of distributionGroupId
     *
     * @param ?int $distributionGroupId The value for distributionGroupId
     *
     * @return void
     */
    public function setDistributionGroupId(?int $distributionGroupId): void
    {
        $this->distributionGroupId = $distributionGroupId;
    }

    /**
     * Get the value of cardCategory
     *
     * @return ?string The value of cardCategory
     */
    public function getCardCategory(): ?string
    {
        return $this->cardCategory;
    }

    /**
     * Set the value of cardCategory
     *
     * @param ?string $cardCategory The value for cardCategory
     *
     * @return void
     */
    public function setCardCategory(?string $cardCategory): void
    {
        $this->cardCategory = $cardCategory;
    }

    /**
     * Get the value of typeOfCard
     *
     * @return ?string The value of typeOfCard
     */
    public function getTypeOfCard(): ?string
    {
        return $this->typeOfCard;
    }

    /**
     * Set the value of typeOfCard
     *
     * @param ?string $typeOfCard The value for typeOfCard
     *
     * @return void
     */
    public function setTypeOfCard(?string $typeOfCard): void
    {
        $this->typeOfCard = $typeOfCard;
    }

    /**
     * Get the value of countryCode
     *
     * @return ?string The value of countryCode
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * Set the value of countryCode
     *
     * @param ?string $countryCode The value for countryCode
     *
     * @return void
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get the value of cardBrand
     *
     * @return ?string The value of cardBrand
     */
    public function getCardBrand(): ?string
    {
        return $this->cardBrand;
    }

    /**
     * Set the value of cardBrand
     *
     * @param ?string $cardBrand The value for cardBrand
     *
     * @return void
     */
    public function setCardBrand(?string $cardBrand): void
    {
        $this->cardBrand = $cardBrand;
    }

    /**
     * Get the value of issuingBank
     *
     * @return ?string The value of issuingBank
     */
    public function getIssuingBank(): ?string
    {
        return $this->issuingBank;
    }

    /**
     * Set the value of issuingBank
     *
     * @param ?string $issuingBank The value for issuingBank
     *
     * @return void
     */
    public function setIssuingBank(?string $issuingBank): void
    {
        $this->issuingBank = $issuingBank;
    }
}
