<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Enum\Broadcaster\BroadcasterStatus;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a broadcaster
 *
 * @see \Ubix\Tests\Model\BroadcasterTest PHPUnit test case
 */
final class Broadcaster extends Model
{
    /**
     * Constructor
     *
     * @param ?int               $id         The broadcaster's ID
     * @param ?string            $name       The broadcaster's name
     * @param ?BroadcasterStatus $status     The broadcaster's status
     * @param ?string            $checkName  The broadcaster's check name
     * @param ?string            $canRecruit The broadcaster's ability to recruit
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?BroadcasterStatus $status = null,
        private ?string $checkName = null,
        private ?string $canRecruit = null,
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
     * Get the value of status
     *
     * @return ?BroadcasterStatus The value of status
     */
    public function getStatus(): ?BroadcasterStatus
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?BroadcasterStatus $status The value for status
     *
     * @return void
     */
    public function setStatus(?BroadcasterStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of canRecruit
     *
     * @return ?string The value of canRecruit
     */
    public function getCanRecruit(): ?string
    {
        return $this->canRecruit;
    }

    /**
     * Set the value of canRecruit
     *
     * @param ?string $canRecruit The value for canRecruit
     *
     * @return void
     */
    public function setCanRecruit(?string $canRecruit): void
    {
        $this->canRecruit = $canRecruit;
    }

    /**
     * Get the value of checkName
     *
     * @return ?string The value of checkName
     */
    public function getCheckName(): ?string
    {
        return $this->checkName;
    }

    /**
     * Set the value of checkName
     *
     * @param ?string $checkName The value for checkName
     *
     * @return void
     */
    public function setCheckName(?string $checkName): void
    {
        $this->checkName = $checkName;
    }
}
