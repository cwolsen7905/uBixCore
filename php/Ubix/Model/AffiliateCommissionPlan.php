<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\DataType\Bool\Boolean;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\AffiliateCommissionPlanId;
use Ubix\DataType\Int\CommissionTypeId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of an Affiliate
 *
 * @see \Ubix\Tests\Model\AffiliateTest PHPUnit test case
 * @see \Ubix\Tests\Model\AffiliateCommisionPlanTest PHPUnit test case
 * @see \Ubix\Tests\Model\AffiliateCommissionPlanTest PHPUnit test case
 */
final class AffiliateCommissionPlan extends Model
{
    /**
     * Constructor
     *
     * @param ?AffiliateCommissionPlanId $commissionPlanId The commission plan ID
     * @param ?Varchar                   $name             The name of the commission plan
     * @param ?Text                      $description      The description of the commission plan
     * @param ?CommissionTypeId          $commissionTypeId The commission type ID
     * @param ?AffiliateRateType         $rateType         The rate type of the commission plan
     * @param ?AffiliateProductType      $productType      The product type of the commission plan
     * @param ?UsdCurrency               $processingFees   The processing fees of the commission plan
     * @param ?Boolean                   $isDefault        Whether the commission plan is the default plan
     * @param ?Boolean                   $status           The status of the commission plan
     * @param ?MpCode                    $mpCode           The marketplace code associated with the commission plan
     * @param ?UbixDateTime               $dateTimeStart    The starttimestamp
     * @param ?UbixDateTime               $dateTimeEnd      The end timestamp
     */
    public function __construct(
        private ?AffiliateCommissionPlanId $commissionPlanId = null,
        private ?Varchar $name = null,
        private ?Text $description = null,
        private ?CommissionTypeId $commissionTypeId = null,
        private ?AffiliateRateType $rateType = null,
        private ?AffiliateProductType $productType = null,
        private ?UsdCurrency $processingFees = null,
        private ?Boolean $isDefault = null,
        private ?Boolean $status = null,
        private ?MpCode $mpCode = null,
        private ?UbixDateTime $dateTimeStart = null,
        private ?UbixDateTime $dateTimeEnd = null,
    ) {
    }

   /**
    * Set Affiliate Commission Plan ID
    *
    * @param AffiliateCommissionPlanId $commissionPlanId The commission plan ID
    *
    * @return void
    */
    public function setCommissionPlanId(?AffiliateCommissionPlanId $commissionPlanId): void
    {
        $this->commissionPlanId = $commissionPlanId;
        $this->markChanged('commissionPlanId');
    }

    /**
     * Get Affiliate Commission Plan ID
     *
     * @return ?AffiliateCommissionPlanId The commission plan ID
     */
    public function getCommissionPlanId(): ?AffiliateCommissionPlanId
    {
        return $this->commissionPlanId;
    }

    /**
     * Set Name
     *
     * @param Varchar $name The name of the commission plan
     *
     * @return void
     */
    public function setName(?Varchar $name): void
    {
        $this->name = $name;
        $this->markChanged('name');
    }

    /**
     * Get Name
     *
     * @return ?Varchar The name of the commission plan
     */
    public function getName(): ?Varchar
    {
        return $this->name;
    }

    /**
     * Set Description
     *
     * @param Text $description The description of the commission plan
     *
     * @return void
     */
    public function setDescription(?Text $description): void
    {
        $this->description = $description;
        $this->markChanged('description');
    }

    /**
     * Get Description
     *
     * @return ?Text The description of the commission plan
     */
    public function getDescription(): ?Text
    {
        return $this->description;
    }

    /**
     * Set Commission Type ID
     *
     * @param CommissionTypeId $commissionTypeId The commission type ID
     *
     * @return void
     */
    public function setCommissionTypeId(?CommissionTypeId $commissionTypeId): void
    {
        $this->commissionTypeId = $commissionTypeId;
        $this->markChanged('commissionTypeId');
    }

    /**
     * Get Commission Type ID
     *
     * @return ?CommissionTypeId The commission type ID
     */
    public function getCommissionTypeId(): ?CommissionTypeId
    {
        return $this->commissionTypeId;
    }

    /**
     * Set Rate Type
     *
     * @param AffiliateRateType $rateType The rate type of the commission plan
     *
     * @return void
     */
    public function setRateType(?AffiliateRateType $rateType): void
    {
        $this->rateType = $rateType;
        $this->markChanged('rateType');
    }

    /**
     * Get Rate Type
     *
     * @return ?AffiliateRateType The rate type of the commission plan
     */
    public function getRateType(): ?AffiliateRateType
    {
        return $this->rateType;
    }

    /**
     * Set Product Type
     *
     * @param AffiliateProductType $productType The product type of the commission plan
     *
     * @return void
     */
    public function setProductType(?AffiliateProductType $productType): void
    {
        $this->productType = $productType;
        $this->markChanged('productType');
    }

    /**
     * Get Rate
     *
     * @return ?AffiliateProductType The product type of the commission plan
     */
    public function getProductType(): ?AffiliateProductType
    {
        return $this->productType;
    }

    /**
     * Set Processing Fees
     *
     * @param UsdCurrency $processingFees The processing fees for the commission plan
     *
     * @return void
     */
    public function setProcessingFees(?UsdCurrency $processingFees): void
    {
        $this->processingFees = $processingFees;
        $this->markChanged('processingFees');
    }

    /**
     * Get Processing Fees
     *
     * @return ?UsdCurrency The processing fees for the commission plan
     */
    public function getProcessingFees(): ?UsdCurrency
    {
        return $this->processingFees;
    }

    /**
     * Set Is Default
     *
     * @param Boolean $isDefault Whether the commission plan is the default plan
     *
     * @return void
     */
    public function setIsDefault(?Boolean $isDefault): void
    {
        $this->isDefault = $isDefault;
        $this->markChanged('isDefault');
    }

    /**
     * Get Is Default
     *
     * @return ?Boolean Whether the commission plan is the default plan
     */
    public function getIsDefault(): ?Boolean
    {
        return $this->isDefault;
    }

    /**
     * Set Status
     *
     * @param Boolean $status The status of the commission plan
     *
     * @return void
     */
    public function setStatus(?Boolean $status): void
    {
        $this->status = $status;
        $this->markChanged('status');
    }

    /**
     * Get Status
     *
     * @return ?Boolean The status of the commission plan
     */
    public function getStatus(): ?Boolean
    {
        return $this->status;
    }

    /**
     * Set MP Code
     *
     * @param MpCode $mpCode The marketplace code associated with the commission plan
     *
     * @return void
     */
    public function setMpCode(?MpCode $mpCode): void
    {
        $this->mpCode = $mpCode;
        $this->markChanged('mpCode');
    }

    /**
     * Get MP Code
     *
     * @return ?MpCode The marketplace code associated with the commission plan
     */
    public function getMpCode(): ?MpCode
    {
        return $this->mpCode;
    }

    /**
     * Get the value of dateTimeStart
     *
     * @return ?UbixDateTime The value of dateTimeStart
     */
    public function getDateTimeStart(): ?UbixDateTime
    {
        return $this->dateTimeStart;
    }

    /**
     * Set the value of dateTimeStart
     *
     * @param ?UbixDateTime $dateTimeStart The value for dateTimeStart
     *
     * @return void
     */
    public function setDateTimeStart(?UbixDateTime $dateTimeStart): void
    {
        $this->dateTimeStart = $dateTimeStart;
        $this->markChanged('dateTimeStart');
    }

    /**
     * Get the value of dateTimeEnd
     *
     * @return ?UbixDateTime The value of dateTimeEnd
     */
    public function getDateTimeEnd(): ?UbixDateTime
    {
        return $this->dateTimeEnd;
    }

    /**
     * Set the value of dateTimeEnd
     *
     * @param ?UbixDateTime $dateTimeEnd The value for dateTimeEnd
     *
     * @return void
     */
    public function setDateTimeEnd(?UbixDateTime $dateTimeEnd): void
    {
        $this->dateTimeEnd = $dateTimeEnd;
        $this->markChanged('dateTimeEnd');
    }
}
