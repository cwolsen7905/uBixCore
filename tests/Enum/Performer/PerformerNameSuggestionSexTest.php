<?php

declare(strict_types=1);

namespace Ubix\Tests\Enum\Performer;

use Ubix\Enum\Performer\PerformerNameSuggestionSex;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Enum\Performer\PerformerNameSuggestionSex
 *
 * @coversDefaultClass \Ubix\Enum\Performer\PerformerNameSuggestionSex
 */
final class PerformerNameSuggestionSexTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerNameSuggestionSex::class);
    }
}
