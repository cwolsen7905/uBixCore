<?php

declare(strict_types=1);

namespace Ubix\Tests\Payload\Request;

use Ubix\Payload\Request\TierRequestPayload;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Payload\Request\TierRequestPayload
 *
 * @coversDefaultClass \Ubix\Payload\Request\TierRequestPayload
 */
final class TierRequestPayloadTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TierRequestPayload::class);
    }
}
