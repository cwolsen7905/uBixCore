<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\PerformerNameSuggestion;

use Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionSqlRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionSqlRepository
 *
 * @coversDefaultClass \Ubix\Repository\PerformerNameSuggestion\PerformerNameSuggestionSqlRepository
 */
final class PerformerNameSuggestionSqlRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PerformerNameSuggestionSqlRepository::class);
    }
}
