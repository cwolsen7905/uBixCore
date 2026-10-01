<?php

declare(strict_types=1);

namespace Ubix\Tests\Bootstrap;

use Exception;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Ubix\Bootstrap\appFolder;

/**
 * PHPUnit test case for \Ubix\Bootstrap\appFolder()
 *
 * A host's own app always wins; an app the framework ships is the fallback; anything
 * else is an error, and a name can never reach outside the framework's apps folder.
 */
#[CoversFunction('Ubix\Bootstrap\appFolder')]
final class AppFolderTest extends TestCase
{
    /**
     * A throwaway host project root
     */
    private string $root;

    /**
     * With no app of its own, the host is served the framework's
     *
     * @return void
     */
    public function testTheFrameworkAppIsTheFallback(): void
    {
        $folder = appFolder($this->root, 'UbixOpsApi');

        $this->assertStringEndsWith('/Bootstrap/apps/UbixOpsApi', $folder);
        $this->assertFileExists($folder . '/src/Routes.php');
    }

    /**
     * A host's own copy overrides the framework's
     *
     * @return void
     */
    public function testTheHostsOwnAppWins(): void
    {
        mkdir($this->root . '/app/UbixOpsApi');

        $this->assertSame($this->root . '/app/UbixOpsApi', appFolder($this->root, 'UbixOpsApi'));
    }

    /**
     * An app neither has is an error
     *
     * @return void
     */
    public function testAnUnknownAppIsAnError(): void
    {
        $this->expectException(Exception::class);

        appFolder($this->root, 'NoSuchApi');
    }

    /**
     * A path-shaped name is reduced to its last segment, never followed
     *
     * @return void
     */
    public function testANameCannotLeaveTheAppsFolder(): void
    {
        $this->assertStringEndsWith('/Bootstrap/apps/UbixOpsApi', appFolder($this->root, '../../../apps/UbixOpsApi'));
    }

    /**
     * A host project with no app folder of its own
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/ubix-app-folder-' . uniqid();
        mkdir($this->root . '/app', 0777, true);
    }

    /**
     * Remove the throwaway root
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ([$this->root . '/app/UbixOpsApi', $this->root . '/app', $this->root] as $dir) {
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
    }
}
