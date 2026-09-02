<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\Int\MinorUnitAmount;
use Ubix\Enum\Tier\TierBillingInterval;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Request payload for POST /creator/tiers and PATCH /creator/tiers/{id}
 *
 * @see \Ubix\Tests\Payload\Request\TierRequestPayloadTest PHPUnit test case
 */
final class TierRequestPayload extends Payload implements RequestPayload
{
    public string $name;

    public ?string $description;

    public MinorUnitAmount $priceAmount;

    public ?string $priceCurrency;

    public TierBillingInterval $billingInterval;

    /**
     * @var array<int, mixed>|null Ordered benefit lines
     */
    public ?array $benefits;

    /**
     * Constructor
     *
     * @param ?string                $name            Tier name (FR-101)
     * @param ?string                $description     Tier description
     * @param ?int                   $priceAmount     Price in minor units (FR-104)
     * @param ?string                $priceCurrency   ISO 4217 currency code (default USD)
     * @param ?string                $billingInterval Month or year
     * @param array<int, mixed>|null $benefits        Ordered benefit lines (strings)
     */
    public function __construct(
        ?string $name,
        ?string $description,
        ?int $priceAmount,
        ?string $priceCurrency,
        ?string $billingInterval,
        ?array $benefits,
    ) {
        $this->validateAndMapField('name', 'name', $name);
        $this->validateAndMapField('description', 'description', $description);
        $this->validateAndMapField('priceAmount', 'priceAmount', $priceAmount);
        $this->validateAndMapField('priceCurrency', 'priceCurrency', $priceCurrency);
        $this->validateAndMapField('billingInterval', 'billingInterval', $billingInterval);
        $this->validateAndMapField('benefits', 'benefits', $benefits);

        parent::__construct();
    }
}
