<?php

declare(strict_types=1);

namespace Ubix\Tests\Repository\State;

use Ubix\Repository\State\StateHardCodedRepository;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Repository\State\StateHardCodedRepository
 *
 * @coversDefaultClass \Ubix\Repository\State\StateHardCodedRepository
 */
final class StateHardCodedRepositoryTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(StateHardCodedRepository::class);
    }
}
