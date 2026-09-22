<?php

declare(strict_types=1);

namespace Ubix\Tests\Bootstrap;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Ubix\Bootstrap\fatalErrorReport;

/**
 * Behavioural tests for the dev-mode shutdown report.
 *
 * `error_get_last()` returns the last error of any level, so a request that
 * succeeded after a deprecation still has one. Reporting it appended "Fatal
 * error" to a finished JSON body and broke every dev API call that touched
 * stripe-php on PHP 8.5.
 *
 * @see \Ubix\Bootstrap\fatalErrorReport()
 */
#[CoversFunction('Ubix\Bootstrap\fatalErrorReport')]
final class FatalErrorReportTest extends TestCase
{
    /**
     * A deprecation, warning or notice leaves the response alone
     *
     * @return void
     */
    public function testANonFatalErrorIsNotReported(): void
    {
        foreach ([E_DEPRECATED, E_USER_DEPRECATED, E_WARNING, E_NOTICE, E_USER_WARNING] as $type) {
            $this->assertNull(fatalErrorReport(['type' => $type, 'message' => 'curl_close() is deprecated', 'file' => '/x.php', 'line' => 1]));
        }
    }

    /**
     * A request with no error at all reports nothing
     *
     * @return void
     */
    public function testNoErrorIsNotReported(): void
    {
        $this->assertNull(fatalErrorReport(null));
    }

    /**
     * A genuinely fatal error is still shown to the developer
     *
     * @return void
     */
    public function testAFatalErrorIsReported(): void
    {
        $report = fatalErrorReport(['type' => E_ERROR, 'message' => 'Allowed memory size exhausted', 'file' => '/x.php', 'line' => 7]);

        $this->assertNotNull($report);
        $this->assertStringContainsString('Allowed memory size exhausted', $report);
        $this->assertStringContainsString('line 7', $report);
    }
}
