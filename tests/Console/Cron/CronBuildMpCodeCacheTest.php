<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Cron;

use Ubix\Console\Cron\CronBuildMpCodeCache;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Cron\CronBuildMpCodeCache
 *
 * @coversDefaultClass \Ubix\Console\Cron\CronBuildMpCodeCache
 */
final class CronBuildMpCodeCacheTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the enum is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CronBuildMpCodeCache::class);
    }
}
