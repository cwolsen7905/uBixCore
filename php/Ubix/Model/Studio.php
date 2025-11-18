<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Enum\Studio\StudioStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a studio
 *
 * @see \Ubix\Tests\Model\StudioTest PHPUnit test case
 */
final class Studio extends Model
{
    /**
     * Constructor
     *
     * @param ?string       $code           The studio's code
     * @param ?string       $name           The studio's name
     * @param ?StudioStatus $status         The studio's status
     * @param ?string       $contactDetails The studio's contact details
     */
    public function __construct(
        private ?string $code = null,
        private ?string $name = null,
        private ?StudioStatus $status = null,
        private ?string $contactDetails = null,
    ) {
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
     * @return ?StudioStatus The value of status
     */
    public function getStatus(): ?StudioStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?StudioStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?StudioStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of contactDetails
     *
     * @return ?string The value of contactDetails
     */
    public function getContactDetails(): ?string
    {
        return $this->contactDetails;
    }

    /**
     * Set the value of contactDetails
     *
     * @param ?string $contactDetails The value for contactDetails
     *
     * @return void
     */
    public function setContactDetails(?string $contactDetails): void
    {
        $this->contactDetails = $contactDetails;
    }
}
