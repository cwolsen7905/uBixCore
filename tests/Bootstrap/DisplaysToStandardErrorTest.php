<?php

declare(strict_types=1);

namespace Ubix\Tests\Bootstrap;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Ubix\Bootstrap\displaysToStandardError;

/**
 * Behavioural tests for where dev-mode error output goes.
 *
 * `display_errors = "stderr"` is a CLI/CGI feature. Under PHP-FPM the string is
 * simply truthy, so PHP "displays" -- into the HTTP response. kitg's dev API
 * served a stripe-php deprecation after its JSON for weeks because of it, which
 * broke the client's parse and leaked server paths.
 *
 * @see \Ubix\Bootstrap\displaysToStandardError()
 */
#[CoversFunction('Ubix\Bootstrap\displaysToStandardError')]
final class DisplaysToStandardErrorTest extends TestCase
{
    /**
     * The SAPIs that honour `stderr`
     *
     * @return void
     */
    public function testCliAndCgiSapisUseStandardError(): void
    {
        foreach (['cli', 'cli-server', 'cgi', 'cgi-fcgi', 'phpdbg'] as $sapi) {
            $this->assertTrue(displaysToStandardError($sapi), $sapi . ' honours display_errors=stderr');
        }
    }

    /**
     * Everything serving HTTP keeps PHP's output out of the response
     *
     * @return void
     */
    public function testWebSapisDoNot(): void
    {
        foreach (['fpm-fcgi', 'apache2handler', 'litespeed', 'frankenphp'] as $sapi) {
            $this->assertFalse(displaysToStandardError($sapi), $sapi . ' would display into the response body');
        }
    }
}
