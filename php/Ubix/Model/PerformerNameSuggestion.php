<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a performer name suggestion
 *
 * @see \Ubix\Tests\Model\PerformerNameSuggestionTest PHPUnit test case
 */
final class PerformerNameSuggestion extends Model
{
    public const STATUS_ACTIVE   = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * Constructor
     *
     * @param ?int                        $id      The suggestion's ID
     * @param ?string                     $name    The suggested name
     * @param ?PerformerNameSuggestionSex $sex     The suggested name's sex
     * @param ?string                     $origin  The suggested name's origin
     * @param ?string                     $meaning The suggested name's meaning
     * @param ?int                        $adminId The admin ID
     * @param ?DateTimeInterface          $dateIn  The suggested name's creation date
     * @param ?int                        $status  The suggested name's status
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?PerformerNameSuggestionSex $sex = null,
        private ?string $origin = null,
        private ?string $meaning = null,
        private ?int $adminId = null,
        private ?DateTimeInterface $dateIn = null,
        private ?int $status = null,
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
     * Get the value of sex
     *
     * @return ?PerformerNameSuggestionSex The value of sex
     */
    public function getSex(): ?PerformerNameSuggestionSex
    {
        return $this->sex;
    }

    /**
     * Set the value of sex
     *
     * @param ?PerformerNameSuggestionSex $sex The value for sex
     *
     * @return void
     */
    public function setSex(?PerformerNameSuggestionSex $sex): void
    {
        $this->sex = $sex;
    }

    /**
     * Get the value of origin
     *
     * @return ?string The value of origin
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * Set the value of origin
     *
     * @param ?string $origin The value for origin
     *
     * @return void
     */
    public function setOrigin(?string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * Get the value of meaning
     *
     * @return ?string The value of meaning
     */
    public function getMeaning(): ?string
    {
        return $this->meaning;
    }

    /**
     * Set the value of meaning
     *
     * @param ?string $meaning The value for meaning
     *
     * @return void
     */
    public function setMeaning(?string $meaning): void
    {
        $this->meaning = $meaning;
    }

    /**
     * Get the value of adminId
     *
     * @return ?int The value of adminId
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * Set the value of adminId
     *
     * @param ?int $adminId The value for adminId
     *
     * @return void
     */
    public function setAdminId(?int $adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * Get the value of dateIn
     *
     * @return ?DateTimeInterface The value of dateIn
     */
    public function getDateIn(): ?DateTimeInterface
    {
        return $this->dateIn;
    }

    /**
     * Set the value of dateIn
     *
     * @param ?DateTimeInterface $dateIn The value for dateIn
     *
     * @return void
     */
    public function setDateIn(?DateTimeInterface $dateIn): void
    {
        $this->dateIn = $dateIn;
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
}
