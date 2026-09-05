<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Model\AbstractModel as Model;

/**
 * Model of a state
 *
 * @see \Ubix\Tests\Model\StateTest PHPUnit test case
 */
final class State extends Model // NOT_IMPLEMENTED: should "state" be renamed to "region" or "subdivision"? ISO uses "subdivision" - we could include an enum of subdivision type (state, province, etc) if we want to follow ISO's lead
{
    /**
     * Constructor
     *
     * @param ?string $name     The state's name
     * @param ?string $iso31662 The state's ISO 3166-2 code (two letter country code then a dash then two letter state code)
     */
    public function __construct(
        private ?string $name = null,
        private ?string $iso31662 = null,
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
     * Get the value of iso31662
     *
     * @return ?string The value of iso31662
     */
    public function getIso31662(): ?string
    {
        return $this->iso31662;
    }

    /**
     * Set the value of iso31662
     *
     * @param ?string $iso31662 The value for iso31662
     *
     * @return void
     */
    public function setIso31662(?string $iso31662): void
    {
        $this->iso31662 = $iso31662;
    }

    /**
     * Get the two letter code
     *
     * @return ?string The two letter code
     */
    public function getTwoLetterCode(): ?string
    {
        return $this->getIso31662();
    }
}
