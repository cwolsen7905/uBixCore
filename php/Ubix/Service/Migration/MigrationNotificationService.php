<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\DataTransferObject\Migration\AppliedMigration;
use Ubix\Enum\Env;
use Ubix\Service\SlackService;

/**
 * Posts a Slack notification to #databases after `migrate:up`
 * applies one or more migrations on dev / staging / prod.
 *
 * Wraps the shared `SlackService` so the runner stays thin and the
 * notification is reusable + testable. Sandbox and the unit-test
 * environment are deliberately skipped: sandbox runs are local
 * iteration noise and the test environment churns schemas on every
 * suite run (`database:resetSchema --target=test`), which must never
 * post to a live Slack channel. Slack failures are caught and logged
 * — a notification outage never fails a migration apply.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationNotificationServiceTest PHPUnit test case
 */
final class MigrationNotificationService
{
    private const CHANNEL = '#databases';

    // Match the deploy / #siteupdates-log branding (bin/deploy.sh) for a
    // consistent Ubix trident across all pipeline notifications.
    private const ICON = ':trident:';

    /**
     * Environments that notify. Sandbox / test are intentionally absent.
     */
    private const NOTIFIED_ENVIRONMENTS = [
        Env::DEV,
        Env::PROD,
        Env::STAGING,
    ];

    private const USERNAME = 'Ubix Migrations';

    /**
     * Constructor
     *
     * @param Logger       $logger       Logger
     * @param SlackService $slackService Slack transport (shared, #databases is whitelisted)
     */
    public function __construct(
        private Logger $logger,
        private SlackService $slackService,
    ) {
    }

    /**
     * Notify #databases that migrations were applied. No-op when the
     * environment is not one of dev / staging / prod, or when nothing
     * was applied (e.g. an apply that failed on the very first file).
     *
     * @param Env                $environment Resolved runtime environment
     * @param AppliedMigration[] $applied     Migrations that applied successfully this run
     *
     * @return void
     */
    public function notifyApplied(Env $environment, array $applied): void
    {
        $this->send(
            $environment,
            sprintf('*Migrations applied — %s* (%d)', strtoupper($environment->value), count($applied)),
            $applied,
        );
    }

    /**
     * Notify #databases that a migration was recorded as applied via
     * `migrate:reconcile` (the manual recovery path — the file was
     * applied out-of-band, not run by the runner). Same environment
     * gating as `notifyApplied()`.
     *
     * @param Env              $environment Resolved runtime environment
     * @param AppliedMigration $reconciled  The reconciled tracker row
     *
     * @return void
     */
    public function notifyReconciled(Env $environment, AppliedMigration $reconciled): void
    {
        $this->send(
            $environment,
            sprintf('*Migration reconciled — %s* (recorded as applied without running)', strtoupper($environment->value)),
            [$reconciled],
        );
    }

    /**
     * Post a notification to #databases: a header line, one bullet per
     * migration, and the actor footer. No-op when nothing is supplied
     * or the environment is not one of dev / staging / prod. Slack
     * failures are logged and swallowed so a notification outage never
     * fails the migration command.
     *
     * @param Env                $environment Resolved runtime environment
     * @param string             $header      Slack-markdown header line
     * @param AppliedMigration[] $migrations  Migrations to list (≥1 to send)
     *
     * @return void
     */
    private function send(Env $environment, string $header, array $migrations): void
    {
        //
        //  Slack is optional. With no configured endpoint the notification is a
        //  true no-op — the command never attempts (nor logs a failed) post, so
        //  `migrate:up` is never blocked or slowed by an unconfigured Slack.
        //
        if ((string) getenv('SLACK_API_ENDPOINT') === '') {
            return;
        }

        if ($migrations === [] || ! in_array($environment, self::NOTIFIED_ENVIRONMENTS, true)) {
            return;
        }

        $lines = [$header];
        foreach ($migrations as $migration) {
            $lines[] = sprintf('• %s (%s)', $migration->id, $migration->targetDatabase);
        }
        $lines[] = sprintf('by `%s`', $migrations[0]->appliedBy);

        try {
            $this->slackService->sendToChannel(
                message:  implode(PHP_EOL, $lines),
                channel:  self::CHANNEL,
                username: self::USERNAME,
                icon:     self::ICON,
            );
        } catch (Throwable $exception) {
            $this->logger->error('Failed to post migration notification to Slack', ['exception' => $exception]);
        }
    }
}
