<?php

declare(strict_types=1);

namespace Ubix\Console\Command;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Console\Question\Question;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\Enum\Env;
use Ubix\Service\Migration\MigrationConnectionTargetService;

/**
 * Shared base for `migrate:*` and `seed:*` console commands that
 * need to optionally retarget at runtime via `--target` /
 * `--host` / `--port` / `--username`.
 *
 * Subclasses must:
 *
 * 1. Call `$this->configureTargetOptions()` from their own
 *    `configure()` after the command-specific options are
 *    registered.
 * 2. Call `$this->applyTargetOptions($input, $output)` at the
 *    very top of `execute()` and return `Command::FAILURE` when
 *    it returns false — that path covers a bad `--target` value,
 *    a missing per-env config, or a `--username` passed in a
 *    non-interactive shell with no way to prompt for the
 *    password.
 *
 * Why the indirection: `bin/ubix` eagerly resolves the DI
 * container at boot, which means `MigrationPdoSqlService`'s
 * connection params would normally be locked in before any CLI
 * option was parsed. The service that backs this class
 * (`MigrationConnectionTargetService`) `putenv()`s the resolved
 * values onto the process env; the migration PDO service reads
 * env lazily on first use via `ensureInitialized()`, so the
 * override flows through. Shell-out paths
 * (`MigrationCredentialResolverService::resolve()`) already read
 * env at call time so they pick the override up for free.
 *
 * The class lives one level up from `Migrate/` and `Seed/` so
 * both command families can share it without a sibling
 * dependency between the two namespaces.
 *
 * @see \Ubix\Tests\Console\Command\AbstractMigrationCommandTest PHPUnit test case
 */
abstract class AbstractMigrationCommand extends Command
{
    /**
     * Constructor
     *
     * @param Logger                           $logger                  Logger
     * @param MigrationConnectionTargetService $connectionTargetService Resolves and applies `--target` / `--username`
     */
    public function __construct(
        Logger $logger,
        private MigrationConnectionTargetService $connectionTargetService,
    ) {
        parent::__construct($logger);
    }

    /**
     * Register the `--target` and `--username` options on the
     * subclass command. Idempotent — calling it twice is a no-op
     * because Symfony Console already errors on duplicate option
     * names, which is louder than we'd like; we guard against
     * that here so subclasses can call us freely from `configure()`.
     *
     * @return void
     */
    protected function configureTargetOptions(): void
    {
        $definition = $this->getDefinition();

        if (! $definition->hasOption('target')) {
            $this->addOption(
                'target',
                null,
                InputOption::VALUE_REQUIRED,
                'Target environment (dev / sandbox / staging / prod / test). Stamps ENV so the destructive guard fires for the right tier. For sandbox / test, also resolves SANDBOX_MYSQL_WRITE_* / TEST_MYSQL_WRITE_* onto MYSQL_WRITE_*; for dev/staging/prod, host/port come from MYSQL_WRITE_HOST / _PORT directly (override with --host / --port). `--target=test` points the dual-use migration commands at the dedicated unit-test MariaDB without ever touching the runtime cluster.',
            );
        }

        if (! $definition->hasOption('host')) {
            $this->addOption(
                'host',
                null,
                InputOption::VALUE_REQUIRED,
                'Override MYSQL_WRITE_HOST for this invocation. Wins over any tier-derived host from --target. Useful for one-off cross-tier targeting without exporting env vars.',
            );
        }

        if (! $definition->hasOption('port')) {
            $this->addOption(
                'port',
                null,
                InputOption::VALUE_REQUIRED,
                'Override MYSQL_WRITE_PORT for this invocation. Wins over any tier-derived port from --target.',
            );
        }

        if (! $definition->hasOption('username')) {
            $this->addOption(
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'Migration-tier MySQL username. Triggers a hidden password prompt; sets MYSQL_MIGRATION_USERNAME / _PASSWORD for this invocation. Omit to fall back to MYSQL_MIGRATION_* / MYSQL_WRITE_* env vars.',
            );
        }

        if (! $definition->hasOption('prefix')) {
            $this->addOption(
                'prefix',
                null,
                InputOption::VALUE_REQUIRED,
                'Apply migrations against prefixed schema names (e.g. --prefix=TEST_ targets TEST_<DB> for each migration). Used by test-DB setup so unit tests can run against parallel schemas without polluting sandbox runtime data. Tracker becomes <prefix>SYSTEMS.Schema_Migrations to match. Must be empty or alphanumeric + underscores; refused against staging / prod.',
            );
        }
    }

    /**
     * Resolve and apply the `--target` / `--host` / `--port` /
     * `--username` options. Returns false (and writes the failure
     * message to `$output`) when the operator passed something the
     * service can't act on:
     *
     * - `--target` omitted entirely. `--target` is required on every
     *   migration command to force an explicit tier choice — no
     *   implicit fallback to whatever `MYSQL_WRITE_*` happens to be
     *   loaded in the shell. `database:resetSchema` translates its
     *   positional `env` argument into `--target` before this method
     *   runs (see `ResetSchemaCommand::reconcilePositionalTargetArg`)
     *   so the shorthand still satisfies the requirement.
     * - `--target=<value>` where `<value>` isn't a known `Env` case.
     * - `--target=sandbox` where no `SANDBOX_MYSQL_WRITE_HOST` /
     *   `_PORT` pair is configured in the environment AND the
     *   operator didn't supply explicit `--host` / `--port` overrides.
     *   (dev / staging / prod don't have tier-prefixed host/port
     *   envs — they read plain `MYSQL_WRITE_*`, so missing config
     *   for those tiers isn't an error here.)
     * - `--username` passed in a non-interactive run with no way
     *   to prompt for the password.
     *
     * Explicit `--host` / `--port` overrides win over any
     * tier-derived value from `--target`.
     *
     * @param Input  $input  Symfony console input
     * @param Output $output Symfony console output
     *
     * @return bool True when the command can proceed; false on error
     */
    protected function applyTargetOptions(Input $input, Output $output): bool
    {
        $targetRaw   = $this->stringOption($input, 'target');
        $hostRaw     = $this->stringOption($input, 'host');
        $portRaw     = $this->stringOption($input, 'port');
        $usernameRaw = $this->stringOption($input, 'username');
        $prefixRaw   = $this->stringOption($input, 'prefix');

        if ($prefixRaw !== null && preg_match('/^[A-Za-z0-9_]+$/', $prefixRaw) !== 1) {
            $output->writeln(sprintf(
                '<error>Invalid --prefix=%s. Must be alphanumeric + underscores only (e.g. TEST_).</error>',
                $prefixRaw,
            ));
            return false;
        }

        if ($targetRaw === null) {
            $validValues = [];
            foreach (Env::cases() as $envCase) {
                $validValues[] = $envCase->value;
            }
            $output->writeln(sprintf(
                '<error>--target is required. Pass one of: %s.</error>',
                implode(', ', $validValues),
            ));
            return false;
        }

        $resolvedTarget = $this->connectionTargetService->resolveTarget($targetRaw);
        if ($resolvedTarget === null) {
            $validValues = [];
            foreach (Env::cases() as $envCase) {
                $validValues[] = $envCase->value;
            }
            $output->writeln(sprintf(
                '<error>Unknown --target=%s. Expected one of: %s.</error>',
                $targetRaw,
                implode(', ', $validValues),
            ));
            return false;
        }

        if ($prefixRaw !== null && ($resolvedTarget === Env::STAGING || $resolvedTarget === Env::PROD)) {
            $output->writeln(sprintf(
                '<error>--prefix is refused against --target=%s. Prefixed schemas are a sandbox/dev workflow only.</error>',
                $resolvedTarget->value,
            ));
            return false;
        }

        $host          = null;
        $port          = null;
        $writeUsername = null;
        $writePassword = null;
        $writeDatabase = null;

        $hostPort = $this->connectionTargetService->getTargetHostPort($resolvedTarget);
        if ($hostPort !== null) {
            $host = $hostPort['host'];
            $port = $hostPort['port'];
        } elseif (($resolvedTarget === Env::SANDBOX || $resolvedTarget === Env::TEST) && ($hostRaw === null || $portRaw === null)) {
            $prefix = $resolvedTarget === Env::SANDBOX ? 'SANDBOX_' : 'TEST_';
            $output->writeln(sprintf(
                '<error>No host/port configured for --target=%s — set %sMYSQL_WRITE_HOST and %sMYSQL_WRITE_PORT in the environment (or .env), or pass --host and --port explicitly, and retry.</error>',
                $resolvedTarget->value,
                $prefix,
                $prefix,
            ));
            return false;
        }

        $credentials = $this->connectionTargetService->getTargetCredentials($resolvedTarget);
        if ($credentials !== null) {
            $writeUsername = $credentials['username'];
            $writePassword = $credentials['password'];
        }

        $writeDatabase = $this->connectionTargetService->getTargetDatabase($resolvedTarget);

        // For sandbox / test, the per-tier `SANDBOX_MYSQL_WRITE_*` /
        // `TEST_MYSQL_WRITE_*` block is the only source of truth —
        // clear any inherited `MYSQL_MIGRATION_*` from .env / shell so
        // it doesn't shadow the per-tier creds via the downstream
        // `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence.
        // `--username=<user>` re-stamps `MYSQL_MIGRATION_*` AFTER
        // this clear inside `apply()`, so the operator-driven
        // override still wins when explicitly requested.
        $clearMigrationCreds = ($resolvedTarget === Env::SANDBOX || $resolvedTarget === Env::TEST) && $usernameRaw === null;

        if ($hostRaw !== null) {
            $host = $hostRaw;
        }
        if ($portRaw !== null) {
            $port = $portRaw;
        }

        $password = null;
        if ($usernameRaw !== null) {
            if (! $input->isInteractive()) {
                $output->writeln('<error>--username requires a TTY for the hidden password prompt; set MYSQL_MIGRATION_USERNAME / MYSQL_MIGRATION_PASSWORD env vars for non-interactive runs instead.</error>');
                return false;
            }

            $password = $this->promptForPassword($input, $output, $usernameRaw, $resolvedTarget);
            if ($password === null || $password === '') {
                $output->writeln('<error>Empty password; aborting before any DB connect.</error>');
                return false;
            }
        }

        $this->connectionTargetService->apply(
            target:              $resolvedTarget,
            host:                $host,
            port:                $port,
            writeUsername:       $writeUsername,
            writePassword:       $writePassword,
            writeDatabase:       $writeDatabase,
            clearMigrationCreds: $clearMigrationCreds,
            username:            $usernameRaw,
            password:            $password,
            databasePrefix:      $prefixRaw,
            output:              $output,
        );

        return true;
    }

    /**
     * Local-dev shorthand: when `--prefix` is omitted under
     * `--target=test` / `--target=sandbox`, auto-derive a
     * `tlocal_<sanitized-user>_` prefix from `get_current_user()`
     * and stamp it onto the input so the rest of `execute()` runs as
     * if the operator passed `--prefix=<derived>` explicitly. Mirrors
     * `tests/AbstractTestCase.php`'s prefix-derivation so the CLI
     * bootstrap and phpunit converge on the same schema namespace by
     * construction. No-op for `--target=dev|staging|prod` (every
     * prefix flag there must be explicit; the destructive blast
     * radius is too wide for an auto-derive) and no-op when
     * `--prefix` is already set.
     *
     * Announces the derivation in `<comment>` so the operator can
     * sanity-check what's about to be created or dropped.
     *
     * Call sites: `ResetSchemaCommand::execute()` and
     * `DropSchemasCommand::execute()`, before `applyTargetOptions()`.
     *
     * @param Input  $input  Symfony console input
     * @param Output $output Symfony console output
     *
     * @return void
     */
    protected function autoDeriveLocalPrefix(Input $input, Output $output): void
    {
        $prefixRaw = $input->getOption('prefix');
        if (is_string($prefixRaw) && $prefixRaw !== '') {
            return;
        }

        $targetRaw = $input->getOption('target');
        if (! is_string($targetRaw) || ! in_array($targetRaw, [Env::TEST->value, Env::SANDBOX->value], true)) {
            return;
        }

        $derived = $this->connectionTargetService->deriveLocalPrefix();
        $input->setOption('prefix', $derived);
        $output->writeln(sprintf(
            '<comment>--prefix auto-derived from get_current_user(): %s</comment>',
            $derived,
        ));
    }

    /**
     * Resolve the runtime environment from the `ENV` env var,
     * defaulting to `Env::DEV` when unset / unknown so a
     * mis-configured runner doesn't accidentally bypass the
     * destructive guard or notify the wrong channel.
     *
     * Distinct from the nullable `resolveEnvironment(): ?Env` that
     * `DropSchemasCommand` / `ResetSchemaCommand` define privately —
     * those refuse on an unresolved env rather than defaulting, since
     * defaulting a destructive drop/reset to dev would be dangerous.
     *
     * @return Env Resolved environment
     */
    protected function resolveRuntimeEnvironment(): Env
    {
        $raw = getenv('ENV');
        if (! is_string($raw) || $raw === '') {
            return Env::DEV;
        }
        return Env::tryFrom($raw) ?? Env::DEV;
    }

    /**
     * Resolve the actor identity stamped into `applied_by` and shown
     * in Slack notifications. In a GitLab CI run the migrate-apply
     * container is passed `GITLAB_USER_LOGIN` (the GitLab user who
     * triggered the pipeline / ran the job); a local shell falls back
     * to `USER`. Returns `unknown` when neither is available (e.g. a
     * scheduled pipeline with no human actor).
     *
     * @return string Identity token, never empty
     */
    protected function resolveActorIdentity(): string
    {
        foreach (['GITLAB_USER_LOGIN', 'USER'] as $envName) {
            $value = getenv($envName);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }
        return 'unknown';
    }

    /**
     * Whether the command is running inside a GitLab CI pipeline,
     * detected via the `CI` / `GITLAB_CI` env vars (passed into the
     * migrate-apply container by `.gitlab-ci.yml`). Drives the `ci:`
     * vs `cli:` prefix on `applied_by`.
     *
     * @return bool True when running under CI
     */
    protected function isCiRun(): bool
    {
        foreach (['CI', 'GITLAB_CI'] as $envName) {
            $value = getenv($envName);
            if (is_string($value) && $value !== '' && $value !== 'false') {
                return true;
            }
        }
        return false;
    }

    /**
     * Read a string option, normalising empty strings to null.
     *
     * @param Input  $input Symfony console input
     * @param string $name  Option name
     *
     * @return ?string Option value, or null when absent / empty
     */
    private function stringOption(Input $input, string $name): ?string
    {
        $raw = $input->getOption($name);
        return is_string($raw) && $raw !== '' ? $raw : null;
    }

    /**
     * Prompt the operator for the migration-cred password with
     * stdin echo disabled. Returns null when the prompt returned
     * nothing (treated as an empty-password error by the caller).
     *
     * @param Input  $input    Symfony console input
     * @param Output $output   Symfony console output
     * @param string $username Username the password is for (shown in the prompt for clarity)
     * @param ?Env   $target   Resolved target (shown in the prompt for clarity)
     *
     * @return ?string Password as typed, or null when the helper returned non-string
     */
    private function promptForPassword(Input $input, Output $output, string $username, ?Env $target): ?string
    {
        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);

        $promptLabel = $target !== null ? sprintf('Password for %s@%s: ', $username, $target->value) : sprintf('Password for %s: ', $username);

        $question = new Question($promptLabel);
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $answer = $helper->ask($input, $output, $question);
        return is_string($answer) ? $answer : null;
    }
}
