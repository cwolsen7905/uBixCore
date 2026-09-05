<?php

declare(strict_types=1);

namespace Ubix\Model;

use Ubix\Model\AbstractModel as Model;

/**
 * Model of a country
 *
 * @see \Ubix\Tests\Model\CountryTest PHPUnit test case
 */
final class Country extends Model
{
    /**
     * Constructor
     *
     * @param ?string $name           The country's name
     * @param ?string $iso31661Alpha2 The country's ISO 3166-1 alpha-2 code (two letter code)
     * @param ?string $iso31661Alpha3 The country's ISO 3166-1 alpha-3 code (three letter code)
     */
    public function __construct(
        private ?string $name = null,
        private ?string $iso31661Alpha2 = null,
        private ?string $iso31661Alpha3 = null,
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
     * Get the value of iso31661Alpha2
     *
     * @return ?string The value of iso31661Alpha2
     */
    public function getIso31661Alpha2(): ?string
    {
        return $this->iso31661Alpha2;
    }

    /**
     * Set the value of iso31661Alpha2
     *
     * @param ?string $iso31661Alpha2 The value for iso31661Alpha2
     *
     * @return void
     */
    public function setIso31661Alpha2(?string $iso31661Alpha2): void
    {
        $this->iso31661Alpha2 = $iso31661Alpha2;
    }

    /**
     * Get the value of iso31661Alpha3
     *
     * @return ?string The value of iso31661Alpha3
     */
    public function getIso31661Alpha3(): ?string
    {
        return $this->iso31661Alpha3;
    }

    /**
     * Set the value of iso31661Alpha3
     *
     * @param ?string $iso31661Alpha3 The value for iso31661Alpha3
     *
     * @return void
     */
    public function setIso31661Alpha3(?string $iso31661Alpha3): void
    {
        $this->iso31661Alpha3 = $iso31661Alpha3;
    }

    /**
     * Get the two letter code
     *
     * @return ?string The two letter code
     */
    public function getTwoLetterCode(): ?string
    {
        return $this->getIso31661Alpha2();
    }

    /**
     * Get the three letter code
     *
     * @return ?string The three letter code
     */
    public function getThreeLetterCode(): ?string
    {
        return $this->getIso31661Alpha3();
    }
}
