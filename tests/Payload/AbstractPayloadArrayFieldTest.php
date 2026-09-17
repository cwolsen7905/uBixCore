<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ubix\Exception\DtoException;
use Ubix\Payload\AbstractPayload as Payload;

/**
 * A property typed `array` used to throw.
 *
 * `getProperty()` treated int/float/string/bool as assign-directly and sent
 * everything else to a `class_exists()` check, so `?array` produced
 * "Class does not exist for lines(array)." and any endpoint whose payload had a
 * list field returned 500. Sowing.me's `POST /creator/tiers` had never worked
 * for exactly this reason -- its `benefits` field is a list of strings.
 *
 * @see \Ubix\Payload\AbstractPayload::validateAndMapField()
 */
#[CoversClass(Payload::class)]
final class AbstractPayloadArrayFieldTest extends TestCase
{
    /**
     * A list field maps through without a DataType in between.
     *
     * @return void
     */
    public function testMapsAnArrayField(): void
    {
        $payload = new ArrayFieldPayloadStub(['first', 'second']);

        $this->assertSame(['first', 'second'], $payload->lines);
    }

    /**
     * An empty list is a value, not an absence.
     *
     * @return void
     */
    public function testMapsAnEmptyArray(): void
    {
        $payload = new ArrayFieldPayloadStub([]);

        $this->assertSame([], $payload->lines);
    }

    /**
     * A nullable list field accepts null.
     *
     * @return void
     */
    public function testAcceptsNullForANullableArray(): void
    {
        $payload = new ArrayFieldPayloadStub(null);

        $this->assertNull($payload->lines);
    }

    /**
     * Scalars alongside a list field still map.
     *
     * @return void
     */
    public function testStillMapsScalars(): void
    {
        $payload = new ArrayFieldPayloadStub(['a'], 'hello');

        $this->assertSame('hello', $payload->label);
    }

    /**
     * A missing required scalar is still a field error, not a crash.
     *
     * @return void
     */
    public function testStillReportsFieldErrors(): void
    {
        $this->expectException(DtoException::class);

        new ArrayFieldPayloadStub(['a'], null);
    }
}
