<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Request payload for POST /creator/tiers/reorder (FR-105)
 *
 * @see \Ubix\Tests\Payload\Request\TierReorderRequestPayloadTest PHPUnit test case
 */
final class TierReorderRequestPayload extends Payload implements RequestPayload
{
    /**
     * @var array<int, mixed> The creator's tier ids in order
     */
    public array $tierIds;

    /**
     * Constructor
     *
     * @param array<int, mixed>|null $tierIds The creator's tier ids in the desired order
     */
    public function __construct(
        ?array $tierIds,
    ) {
        $this->validateAndMapField('tierIds', 'tierIds', $tierIds);

        parent::__construct();
    }
}
