<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\ProjectRootService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\ProjectRootService
 *
 * @coversDefaultClass \Ubix\Service\ProjectRootService
 */
final class ProjectRootServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ProjectRootService::class);
    }

    /**
     * The root is normalised with realpath (no `..`, no trailing slash)
     *
     * @return void
     */
    public function testGetRootIsNormalised(): void
    {
        $service = $this->service(__DIR__ . '/../../tests/../');

        $this->assertSame(realpath(dirname(__DIR__, 2)), $service->getRoot());
    }

    /**
     * Segments join under the root with slashes normalised
     *
     * @return void
     */
    public function testGetPathJoinsSegments(): void
    {
        $service = $this->service(dirname(__DIR__, 2));
        $root    = $service->getRoot();

        $this->assertSame($root, $service->getPath());
        $this->assertSame($root . '/sql/ACME.sql', $service->getPath('sql', 'ACME.sql'));
        $this->assertSame($root . '/sql/migrations', $service->getPath('sql/', '/migrations/'));
        $this->assertSame($root . '/app/Foo', $service->getPath('', 'app', 'Foo'));
        $this->assertSame($root . '/vendor/bin/phpstan', $service->getVendorBinPath('phpstan'));
    }

    /**
     * A root that is not a directory is rejected at construction
     *
     * @return void
     */
    public function testInvalidRootThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('is not a directory');

        $this->service(dirname(__DIR__, 2) . '/does-not-exist-' . uniqid());
    }

    /**
     * Build the service with a stubbed logger
     *
     * @param string $root Project root to resolve
     *
     * @return ProjectRootService
     */
    private function service(string $root): ProjectRootService
    {
        return new ProjectRootService($this->createStub(Logger::class), $root);
    }
}
