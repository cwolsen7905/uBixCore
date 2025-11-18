<?php

declare(strict_types=1);

namespace Ubix\Tests\SlimApp;

use Ubix\SlimApp\RecordingApp;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\SlimApp\RecordingApp
 *
 * @coversDefaultClass \Ubix\SlimApp\RecordingApp
 */
final class RecordingAppTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(RecordingApp::class);
    }
}
