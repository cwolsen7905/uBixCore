<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Ubix\Enum\Env;
use ValueError;

/**
 * Resolves the `--target` / `--username` CLI options on the
 * migration / seed commands and applies the resolution to the
 * process env so the rest of the runner picks it up.
 *
 * The runner reads its connection params from process env vars in
 * two places — `MigrationCredentialResolverService::resolve()` for
 * shell-out paths (mariadb / mariadb-dump) and
 * `MigrationPdoSqlService::ensureInitialized()` for tracker
 * reads/writes. Both read the env at call time, so a CLI override
 * applied here via `putenv()` before either is touched flows
 * through transparently.
 *
 * Resolution rules:
 *
 * - `--target=<env>` → stamp `ENV=<env>` so the destructive guard
 *   (`migrations.md` §11.3) fires for the right tier. Host/port
 *   resolution is tier-specific:
 *     - `sandbox` looks up the full `SANDBOX_MYSQL_WRITE_*` block
 *       (host / port / username / password / database) and copies
 *       all five onto the plain `MYSQL_WRITE_*` equivalents, then
 *       clears any inherited `MYSQL_MIGRATION_USERNAME` /
 *       `MYSQL_MIGRATION_PASSWORD` from the operator's shell so
 *       the per-tier sandbox creds aren't shadowed by the
 *       `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence
 *       downstream. `--username=<user>` still wins — it re-stamps
 *       `MYSQL_MIGRATION_*` AFTER the sandbox-driven clear, so an
 *       operator can deliberately override the sandbox `root` creds
 *       when they have a reason to.
 *     - `test` follows the exact same shape as sandbox but reads
 *       from `TEST_MYSQL_WRITE_*` instead of `SANDBOX_MYSQL_WRITE_*`.
 *       Routes dual-use migration commands (`database:resetSchema`,
 *       `database:dropSchemas`) at the dedicated unit-test MariaDB
 *       so the lint-and-test CI stage can materialise + tear down
 *       prefixed schemas without ever pointing at a runtime cluster.
 *       The corresponding GitLab CI variables
 *       (`TEST_MYSQL_WRITE_HOST` / `_PORT` / `_USERNAME` /
 *       `_PASSWORD`) carry the unit-test instance's NodePort
 *       coordinates. Sandbox + test are the only two tiers with
 *       prefixed env blocks; dev / staging / prod read plain
 *       `MYSQL_*` env directly.
 *     - `dev` / `staging` / `prod` do NOT have tier-prefixed
 *       host/port env vars. The runner reads plain
 *       `MYSQL_WRITE_HOST` / `MYSQL_WRITE_PORT` from whatever
 *       deployment shell `--target=<env>` was invoked from. Use
 *       `--host=<host>` / `--port=<port>` (below) to point at a
 *       different cluster from the current shell.
 * - `--host=<host>` / `--port=<port>` → stamp `MYSQL_WRITE_HOST` /
 *   `MYSQL_WRITE_PORT` directly. Wins over any tier-derived value
 *   from `--target`. Useful for one-off cross-tier targeting
 *   (e.g. operator on a dev shell pointing at a staging cluster)
 *   without `export`ing tier env vars.
 * - `--username=<user>` (plus a hidden-stdin password prompt
 *   handled by the calling command) → set
 *   `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` so the
 *   existing migration-cred override path (`cutover-runbook.md`
 *   §2.1) takes effect for this single invocation. This still wins
 *   over per-tier creds via the downstream
 *   `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence in
 *   `MigrationCredentialResolverService` / `MigrationPdoSqlService`
 *   / `ResetSchemaCommand::buildMariadbPrefix`.
 *
 * When neither option is passed the service is a no-op — the
 * runner falls back to whatever `MYSQL_WRITE_*` /
 * `MYSQL_MIGRATION_*` already exist in the process env. This
 * preserves the pre-existing env-var workflow for CI and for
 * operators who prefer `export` over flags.
 *
 * `MYSQL_WRITE_DATABASE` is intentionally untouched. The tracker
 * table (`SYSTEMS.Schema_Migrations`) has the same name in every
 * Ubix cluster, so swapping clusters doesn't require swapping
 * the database env var; the per-migration target database comes
 * from each file's `Database:` header.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationConnectionTargetServiceTest PHPUnit test case
 */
final class MigrationConnectionTargetService
{
    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Resolve a `--target=<env>` value against the `Env` enum.
     * Returns null on miss so the caller can surface a clear error
     * with the list of acceptable values.
     *
     * @param ?string $target Raw `--target` option value (or null when omitted)
     *
     * @return ?Env Resolved environment, or null if `$target` is null / unknown
     */
    public function resolveTarget(?string $target): ?Env
    {
        if ($target === null || $target === '') {
            return null;
        }

        try {
            return Env::from($target);
        } catch (ValueError $e) {
            return null;
        }
    }

    /**
     * Derive a `tlocal_<unix-user>_` prefix from `get_current_user()`,
     * sanitising any non-`[A-Za-z0-9_]` characters to underscores so
     * the result satisfies `AbstractMigrationCommand::applyTargetOptions()`'s
     * `--prefix=^[A-Za-z0-9_]+$` validation. Mirrors the sanitisation
     * in `tests/AbstractTestCase.php` so the prefix the operator
     * bootstraps from the CLI matches the prefix `phpunit` looks for
     * at query time — usernames with hyphens (e.g. `christopher-olsen`)
     * normalise to underscores consistently on both sides.
     *
     * Used by `database:resetSchema` / `database:dropSchemas` when
     * `--prefix` is omitted under `--target=test` / `--target=sandbox`
     * — the local-dev shorthand for "drop / materialise MY prefixed
     * schemas, no typing".
     *
     * @return string Sanitised local-dev prefix (e.g. `tlocal_christopher_olsen_`)
     */
    public function deriveLocalPrefix(): string
    {
        $safeUser = (string) preg_replace('/[^A-Za-z0-9_]/', '_', get_current_user());
        return 'tlocal_' . $safeUser . '_';
    }

    /**
     * Look up the host / port override for a named target. Only the
     * SANDBOX tier has a prefixed env-var pair
     * (`SANDBOX_MYSQL_WRITE_HOST` / `_PORT`); dev / staging / prod
     * deployments expose only plain `MYSQL_WRITE_HOST` / `_PORT`,
     * which downstream code already reads on its own, so for those
     * tiers this method intentionally returns null (the caller must
     * not treat that as a missing-config error).
     *
     * For SANDBOX, returns null when either env var is missing so
     * the caller can surface a clear "no `SANDBOX_MYSQL_WRITE_*`
     * configured" error.
     *
     * @param Env $target Resolved target environment
     *
     * @return ?array{host: string, port: string} Host + port pair from `SANDBOX_MYSQL_WRITE_*` (sandbox only), or null otherwise
     */
    public function getTargetHostPort(Env $target): ?array
    {
        $prefix = match ($target) {
            Env::SANDBOX => 'SANDBOX_',
            Env::TEST    => 'TEST_',
            default      => null,
        };
        if ($prefix === null) {
            return null;
        }

        $host = (string) getenv($prefix . 'MYSQL_WRITE_HOST');
        $port = (string) getenv($prefix . 'MYSQL_WRITE_PORT');

        if ($host === '' || $port === '') {
            return null;
        }

        return ['host' => $host, 'port' => $port];
    }

    /**
     * Look up the per-tier baseline write credentials for a named
     * target. Sandbox-only (matches `getTargetHostPort()`'s
     * sandbox-only resolution rule). Returns null when EITHER env
     * var is missing — partial config means "use whatever cred
     * source the operator already has" rather than silently mixing
     * tier defaults with shell defaults.
     *
     * @param Env $target Resolved target environment
     *
     * @return ?array{username: string, password: string} Cred pair from `SANDBOX_MYSQL_WRITE_*` (sandbox only), or null otherwise
     */
    public function getTargetCredentials(Env $target): ?array
    {
        $prefix = match ($target) {
            Env::SANDBOX => 'SANDBOX_',
            Env::TEST    => 'TEST_',
            default      => null,
        };
        if ($prefix === null) {
            return null;
        }

        $username = (string) getenv($prefix . 'MYSQL_WRITE_USERNAME');
        $password = (string) getenv($prefix . 'MYSQL_WRITE_PASSWORD');

        if ($username === '' || $password === '') {
            return null;
        }

        return ['username' => $username, 'password' => $password];
    }

    /**
     * Look up the per-tier write database for a named target.
     * Sandbox-only (matches the rest of the `SANDBOX_*`-exclusive
     * resolution rule). Returns null when the env var is missing or
     * the target isn't sandbox — the downstream resolver falls back
     * to the plain `MYSQL_WRITE_DATABASE` env, which is the right
     * default for dev / staging / prod and for sandbox shells that
     * happen to share the database name with their default.
     *
     * @param Env $target Resolved target environment
     *
     * @return ?string Database name from `SANDBOX_MYSQL_WRITE_DATABASE` (sandbox only), or null otherwise
     */
    public function getTargetDatabase(Env $target): ?string
    {
        $prefix = match ($target) {
            Env::SANDBOX => 'SANDBOX_',
            Env::TEST    => 'TEST_',
            default      => null,
        };
        if ($prefix === null) {
            return null;
        }

        $database = (string) getenv($prefix . 'MYSQL_WRITE_DATABASE');
        return $database !== '' ? $database : null;
    }

    /**
     * Apply the resolved target + cred override to the process env
     * via `putenv()`. Subsequent `getenv()` calls in
     * `MigrationCredentialResolverService` and
     * `MigrationPdoSqlService` pick up the new values.
     *
     * Print a one-line banner per applied field so the operator can
     * sanity-check what just changed.
     *
     * `$writeUsername` / `$writePassword` / `$writeDatabase` carry
     * the per-tier baseline values (sandbox only — from
     * `SANDBOX_MYSQL_WRITE_*`) and stamp the plain `MYSQL_WRITE_*`
     * equivalents. `$clearMigrationCreds` unsets any inherited
     * `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` so the
     * sandbox per-tier creds aren't shadowed by the
     * `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence in
     * `MigrationCredentialResolverService` / `MigrationPdoSqlService`.
     * The clear happens BEFORE `$username` / `$password` stamping,
     * so `--username=<user>` re-asserts the migration-cred override
     * on top.
     *
     * `$username` / `$password` carry the operator-driven override
     * from `--username` + hidden-stdin prompt — they stamp
     * `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD`, which
     * wins over `MYSQL_WRITE_*` everywhere downstream.
     *
     * @param ?Env    $target              Resolved target (null when `--target` was omitted)
     * @param ?string $host                Host to stamp on `MYSQL_WRITE_HOST` (null = no change)
     * @param ?string $port                Port to stamp on `MYSQL_WRITE_PORT` (null = no change)
     * @param ?string $writeUsername       Per-tier username for `MYSQL_WRITE_USERNAME` (null = no change)
     * @param ?string $writePassword       Per-tier password for `MYSQL_WRITE_PASSWORD` (null = no change)
     * @param ?string $writeDatabase       Per-tier database name for `MYSQL_WRITE_DATABASE` (null = no change)
     * @param bool    $clearMigrationCreds When true, unsets `MYSQL_MIGRATION_USERNAME` / `_PASSWORD` before stamping `$username` / `$password`. Used for sandbox + test so inherited migration creds don't shadow the per-tier root creds.
     * @param ?string $username            Operator-override username for `MYSQL_MIGRATION_USERNAME` (null = no change)
     * @param ?string $password            Operator-override password for `MYSQL_MIGRATION_PASSWORD` (null = no change)
     * @param ?string $databasePrefix      Prefix for `DATABASE_PREFIX` env var (null/empty = no change). Read by `SchemaMigrationSqlRepository::trackerTable()` and `MigrationApplyService::runBodyViaMariadbCli()` to retarget the tracker schema and migration `Database:` headers at `<prefix><DB>`. Used by the test-DB setup flow.
     * @param Output  $output              Console output for the banner
     *
     * @return void
     */
    public function apply(
        ?Env $target,
        ?string $host,
        ?string $port,
        ?string $writeUsername,
        ?string $writePassword,
        ?string $writeDatabase,
        bool $clearMigrationCreds,
        ?string $username,
        ?string $password,
        ?string $databasePrefix,
        Output $output,
    ): void {
        $changes = [];

        if ($target !== null) {
            putenv('ENV=' . $target->value);
            $changes[] = sprintf('ENV → %s', $target->value);
        }

        if ($host !== null && $host !== '') {
            putenv('MYSQL_WRITE_HOST=' . $host);
            $changes[] = sprintf('MYSQL_WRITE_HOST → %s', $host);
        }

        if ($port !== null && $port !== '') {
            putenv('MYSQL_WRITE_PORT=' . $port);
            $changes[] = sprintf('MYSQL_WRITE_PORT → %s', $port);
        }

        if ($writeUsername !== null && $writeUsername !== '' && $writePassword !== null && $writePassword !== '') {
            putenv('MYSQL_WRITE_USERNAME=' . $writeUsername);
            putenv('MYSQL_WRITE_PASSWORD=' . $writePassword);
            $changes[] = sprintf('MYSQL_WRITE_USERNAME → %s (per-tier)', $writeUsername);
            $changes[] = 'MYSQL_WRITE_PASSWORD → (set from per-tier env)';
        }

        if ($writeDatabase !== null && $writeDatabase !== '') {
            putenv('MYSQL_WRITE_DATABASE=' . $writeDatabase);
            $changes[] = sprintf('MYSQL_WRITE_DATABASE → %s (per-tier)', $writeDatabase);
        }

        if ($clearMigrationCreds) {
            putenv('MYSQL_MIGRATION_USERNAME');
            putenv('MYSQL_MIGRATION_PASSWORD');
            $tierLabel = $target !== null ? $target->value : 'per-tier';
            $changes[] = sprintf('MYSQL_MIGRATION_USERNAME → (cleared for %s)', $tierLabel);
            $changes[] = sprintf('MYSQL_MIGRATION_PASSWORD → (cleared for %s)', $tierLabel);
        }

        if ($username !== null && $username !== '') {
            putenv('MYSQL_MIGRATION_USERNAME=' . $username);
            $changes[] = sprintf('MYSQL_MIGRATION_USERNAME → %s', $username);
        }

        if ($password !== null && $password !== '') {
            putenv('MYSQL_MIGRATION_PASSWORD=' . $password);
            $changes[] = 'MYSQL_MIGRATION_PASSWORD → (set from prompt)';
        }

        if ($databasePrefix !== null && $databasePrefix !== '') {
            putenv('DATABASE_PREFIX=' . $databasePrefix);
            $changes[] = sprintf('DATABASE_PREFIX → %s', $databasePrefix);
        }

        if ($changes === []) {
            return;
        }

        $output->writeln('<comment>Migration target overrides:</comment>');
        foreach ($changes as $change) {
            $output->writeln('  ' . $change);
        }
        $output->writeln('');
    }
}
