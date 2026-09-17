<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload;

use Ubix\Payload\AbstractPayload as Payload;

/**
 * A payload with a list-valued field, constructed the way real payloads are.
 *
 * Test fixture only: it exists to exercise `array` property handling in
 * AbstractPayload and has no place in the framework itself.
 *
 * @see \Ubix\Tests\Payload\AbstractPayloadArrayFieldTest PHPUnit test case
 */
final class ArrayFieldPayloadStub extends Payload
{
    /**
     * @var array<int, mixed>|null Ordered lines, as a request would send them
     */
    public ?array $lines;

    /**
     * @var string Scalar alongside it, to prove the change did not disturb those
     */
    public string $label;

    /**
     * Constructor
     *
     * @param array<int, mixed>|null $lines Ordered lines.
     * @param ?string                $label A scalar field.
     */
    public function __construct(
        ?array $lines,
        ?string $label = 'x',
    ) {
        $this->validateAndMapField('lines', 'lines', $lines);
        $this->validateAndMapField('label', 'label', $label);

        parent::__construct();
    }
}
