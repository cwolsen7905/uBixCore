<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTime;
use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of an age verification restricted region
 *
 * @see \Ubix\Tests\Model\AgeVerificationRestrictedRegionTest PHPUnit test case
 */
final class AgeVerificationRestrictedRegion extends Model
{
    public const CODE_FOR_WHOLE_COUNTRY = 'WHOLE_COUNTRY';

    /**
     * Constructor
     *
     * @param ?string                     $code                The region code (optional) (default: null)
     * @param bool|DateTimeInterface|null $active              Whether or not the age verification restrictions for the region are active; if true is passed they are active, if false is passed they are inactive and if a DateTime object is passed then the restrictions will be inactive up to that point in time then active (optional) (default: null)
     * @param ?bool                       $blocked             Whether or not the region is totally blocked as opposed to simply an age verification requirement (optional) (default: null)
     * @param ?DateTimeInterface          $creditCardCheckDate The date to check when verifying customer's cards (optional) (default: null)
     * @param ?string                     $avsRegulationCode   The AVS regulation code (optional) (default: null)
     */
    public function __construct(
        private ?string $code = null,
        private bool|DateTimeInterface|null $active = null,
        private ?bool $blocked = null,
        private ?DateTimeInterface $creditCardCheckDate = null,
        private ?string $avsRegulationCode = null,
    ) {
    }

    /**
     * Check if the restricted region is active
     *
     * @return bool Whether or not the restricted region is active
     */
    public function isActive(): bool
    {
        //
        //  Return true if active is true // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- phpcs is thinking the comment is commented out code (it's not)
        //
        if ($this->active === true) {
            return true;
        }

        //
        //  Return true if active is a DateTime object that isn't in the future
        //
        return is_object($this->active) && $this->active <= new DateTime();
    }

    /**
     * Get the value of code
     *
     * @return ?string The value of code
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @param ?string $code The value for code
     *
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get the value of active
     *
     * @return bool|DateTimeInterface|null The value of active
     */
    public function getActive(): bool|DateTimeInterface|null
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @param bool|DateTimeInterface|null $active The value for active
     *
     * @return void
     */
    public function setActive(bool|DateTimeInterface|null $active): void
    {
        $this->active = $active;
    }

    /**
     * Get the value of blocked
     *
     * @return ?bool The value of blocked
     */
    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    /**
     * Set the value of blocked
     *
     * @param ?bool $blocked The value for blocked
     *
     * @return void
     */
    public function setBlocked(?bool $blocked): void
    {
        $this->blocked = $blocked;
    }

    /**
     * Get the value of creditCardCheckDate
     *
     * @return ?DateTimeInterface The value of creditCardCheckDate
     */
    public function getCreditCardCheckDate(): ?DateTimeInterface
    {
        return $this->creditCardCheckDate;
    }

    /**
     * Set the value of creditCardCheckDate
     *
     * @param ?DateTimeInterface $creditCardCheckDate The value for creditCardCheckDate
     *
     * @return void
     */
    public function setCreditCardCheckDate(?DateTimeInterface $creditCardCheckDate): void
    {
        $this->creditCardCheckDate = $creditCardCheckDate;
    }

    /**
     * Get the value of avsRegulationCode
     *
     * @return ?string The value of avsRegulationCode
     */
    public function getAvsRegulationCode(): ?string
    {
        return $this->avsRegulationCode;
    }

    /**
     * Set the value of avsRegulationCode
     *
     * @param ?string $avsRegulationCode The value for avsRegulationCode
     *
     * @return void
     */
    public function setAvsRegulationCode(?string $avsRegulationCode): void
    {
        $this->avsRegulationCode = $avsRegulationCode;
    }
}
