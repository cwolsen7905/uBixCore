<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload\Response;

use Ubix\Payload\Response\AttributionResponsePayload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Payload\Response\AttributionResponsePayload
 *
 * @coversDefaultClass \Ubix\Payload\Response\AttributionResponsePayload
 */
final class AttributionResponsePayloadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AttributionResponsePayload::class);
    }
}
