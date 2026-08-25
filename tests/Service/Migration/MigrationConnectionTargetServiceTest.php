<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Migration;

use Psr\Log\NullLogger;
use Symfony\Component\Console\Output\BufferedOutput;
use Ubix\Enum\Env;
use Ubix\Service\Migration\MigrationConnectionTargetService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubis\Service\Migration\MigrationConnectionTargetService
 *
 * @coversDefaultClass \Ubix\Service\Migration\MigrationConnectionTargetService
 * @coversDefaultClass \Ubis\Service\Migration\MigrationConnectionTargetService
 */
final class MigrationConnectionTargetServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Process-global env vars these tests mutate via `apply()`. They are
     * snapshotted before each test and restored after, so a test can never
     * leak a changed (or unset) value into the rest of the shared-process
     * suite. The critical one is `DATABASE_PREFIX`: it is read live by
     * `UbixDatabase::databaseName()` in every later repository test's
     * seed SQL, so leaving it unset here would make those tests query the
     * unprefixed schema (`ntl_db` instead of `t<pipeline_id>_ntl_db`).
     */
    private const PRESERVED_ENV_KEYS = [
        'DATABASE_PREFIX',
        'ENV',
        'MYSQL_MIGRATION_PASSWORD',
        'MYSQL_MIGRATION_USERNAME',
        'MYSQL_WRITE_DATABASE',
        'MYSQL_WRITE_HOST',
        'MYSQL_WRITE_PASSWORD',
        'MYSQL_WRITE_PORT',
        'MYSQL_WRITE_USERNAME',
    ];

    /**
     * @var array<string, string|false> $envSnapshot
     */
    private array $envSnapshot = [];

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MigrationConnectionTargetService::class);
    }

    /**
     * Every `Env` case is a valid `--target=` value.
     *
     * @return void
     * @covers ::resolveTarget
     */
    public function testResolveTargetAcceptsEveryEnvCase(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());
        foreach (Env::cases() as $envCase) {
            $this->assertSame(
                $envCase,
                $service->resolveTarget($envCase->value),
                sprintf('Expected `%s` to resolve to Env::%s', $envCase->value, $envCase->name),
            );
        }
    }

    /**
     * Garbage / empty / null `--target=` values resolve to null so the
     * caller can surface a structured error rather than throwing.
     *
     * @return void
     * @covers ::resolveTarget
     */
    public function testResolveTargetReturnsNullForBadInput(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());
        $this->assertNull($service->resolveTarget(null));
        $this->assertNull($service->resolveTarget(''));
        $this->assertNull($service->resolveTarget('production'));
        $this->assertNull($service->resolveTarget('DEV'));
    }

    /**
     * Local-dev prefix derivation produces a `tlocal_<sanitized>_`
     * string that passes the `^[A-Za-z0-9_]+$` validation
     * `AbstractMigrationCommand::applyTargetOptions()` applies to
     * `--prefix`. The whole point of the auto-derive is that a
     * username with a hyphen (`christopher-olsen`) can't get rejected
     * by the validation, so the contract under test is "the result
     * always matches the prefix grammar" — the inner username value
     * itself is hostile to assert against because `get_current_user()`
     * varies per shell.
     *
     * @return void
     * @covers ::deriveLocalPrefix
     */
    public function testDeriveLocalPrefixMatchesPrefixGrammar(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());
        $derived = $service->deriveLocalPrefix();
        $this->assertMatchesRegularExpression(
            '/^tlocal_[A-Za-z0-9_]+_$/',
            $derived,
            'deriveLocalPrefix() must return a string the --prefix validator accepts',
        );
    }

    /**
     * Missing or partial `SANDBOX_MYSQL_WRITE_*` env vars surface as
     * null so the caller can produce the "no host/port configured"
     * error instead of building a half-baked DSN.
     *
     * @return void
     * @covers ::getTargetHostPort
     */
    public function testGetTargetHostPortReturnsNullWhenEitherSandboxEnvIsMissing(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_HOST');
        putenv('SANDBOX_MYSQL_WRITE_PORT');
        $this->assertNull($service->getTargetHostPort(Env::SANDBOX));

        putenv('SANDBOX_MYSQL_WRITE_HOST=127.0.0.1');
        $this->assertNull($service->getTargetHostPort(Env::SANDBOX));

        putenv('SANDBOX_MYSQL_WRITE_HOST');
        putenv('SANDBOX_MYSQL_WRITE_PORT=30306');
        $this->assertNull($service->getTargetHostPort(Env::SANDBOX));

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_PORT');
    }

    /**
     * A complete `SANDBOX_MYSQL_WRITE_HOST` / `_PORT` pair resolves
     * to the expected array shape.
     *
     * @return void
     * @covers ::getTargetHostPort
     */
    public function testGetTargetHostPortReturnsPairWhenBothSandboxEnvsSet(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_HOST=127.0.0.1');
        putenv('SANDBOX_MYSQL_WRITE_PORT=30306');

        $this->assertSame(
            ['host' => '127.0.0.1', 'port' => '30306'],
            $service->getTargetHostPort(Env::SANDBOX),
        );

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_HOST');
        putenv('SANDBOX_MYSQL_WRITE_PORT');
    }

    /**
     * Non-sandbox tiers (dev / staging / prod) have no tier-prefixed
     * host/port env-var pair — those tiers read plain
     * `MYSQL_WRITE_HOST` / `_PORT` downstream, so this method must
     * return null unconditionally even when `<ENV>_MYSQL_WRITE_HOST`
     * / `_PORT` happen to be set in the operator's shell. Returning
     * null is NOT an error here; the caller distinguishes by
     * checking `$target === Env::SANDBOX`.
     *
     * @return void
     * @covers ::getTargetHostPort
     */
    public function testGetTargetHostPortReturnsNullForNonSandboxTargets(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        // Set tier-prefixed values for the non-sandbox tiers to prove
        // they are deliberately ignored.
        putenv('DEV_MYSQL_WRITE_HOST=dev-db.example');
        putenv('DEV_MYSQL_WRITE_PORT=3306');
        putenv('STAGING_MYSQL_WRITE_HOST=staging-db.example');
        putenv('STAGING_MYSQL_WRITE_PORT=3306');
        putenv('PROD_MYSQL_WRITE_HOST=prod-db.example');
        putenv('PROD_MYSQL_WRITE_PORT=3306');

        $this->assertNull($service->getTargetHostPort(Env::DEV));
        $this->assertNull($service->getTargetHostPort(Env::STAGING));
        $this->assertNull($service->getTargetHostPort(Env::PROD));

        // Clean up.
        putenv('DEV_MYSQL_WRITE_HOST');
        putenv('DEV_MYSQL_WRITE_PORT');
        putenv('STAGING_MYSQL_WRITE_HOST');
        putenv('STAGING_MYSQL_WRITE_PORT');
        putenv('PROD_MYSQL_WRITE_HOST');
        putenv('PROD_MYSQL_WRITE_PORT');
    }

    /**
     * Missing or partial `SANDBOX_MYSQL_WRITE_USERNAME` /
     * `_PASSWORD` env vars surface as null so `apply()` leaves the
     * existing `MYSQL_WRITE_*` cred set in place rather than mixing
     * tier defaults with shell defaults.
     *
     * @return void
     * @covers ::getTargetCredentials
     */
    public function testGetTargetCredentialsReturnsNullWhenEitherSandboxEnvIsMissing(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_USERNAME');
        putenv('SANDBOX_MYSQL_WRITE_PASSWORD');
        $this->assertNull($service->getTargetCredentials(Env::SANDBOX));

        putenv('SANDBOX_MYSQL_WRITE_USERNAME=root');
        $this->assertNull($service->getTargetCredentials(Env::SANDBOX));

        putenv('SANDBOX_MYSQL_WRITE_USERNAME');
        putenv('SANDBOX_MYSQL_WRITE_PASSWORD=SandboxTestPassword1234');
        $this->assertNull($service->getTargetCredentials(Env::SANDBOX));

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_PASSWORD');
    }

    /**
     * A complete `SANDBOX_MYSQL_WRITE_USERNAME` / `_PASSWORD` pair
     * resolves to the expected array shape so `apply()` can stamp
     * the per-tier baseline creds.
     *
     * @return void
     * @covers ::getTargetCredentials
     */
    public function testGetTargetCredentialsReturnsPairWhenBothSandboxEnvsSet(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_USERNAME=root');
        putenv('SANDBOX_MYSQL_WRITE_PASSWORD=SandboxTestPassword1234');

        $this->assertSame(
            ['username' => 'root', 'password' => 'SandboxTestPassword1234'],
            $service->getTargetCredentials(Env::SANDBOX),
        );

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_USERNAME');
        putenv('SANDBOX_MYSQL_WRITE_PASSWORD');
    }

    /**
     * `getTargetCredentials()` is sandbox-only — dev / staging / prod
     * tiers have no `<ENV>_MYSQL_WRITE_USERNAME` / `_PASSWORD` env
     * vars and must return null even when those vars happen to be
     * set in the operator's shell.
     *
     * @return void
     * @covers ::getTargetCredentials
     */
    public function testGetTargetCredentialsReturnsNullForNonSandboxTargets(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('DEV_MYSQL_WRITE_USERNAME=dba_user');
        putenv('DEV_MYSQL_WRITE_PASSWORD=s3cret');
        putenv('STAGING_MYSQL_WRITE_USERNAME=staging_user');
        putenv('STAGING_MYSQL_WRITE_PASSWORD=staging_s3cret');
        putenv('PROD_MYSQL_WRITE_USERNAME=prod_user');
        putenv('PROD_MYSQL_WRITE_PASSWORD=prod_s3cret');

        $this->assertNull($service->getTargetCredentials(Env::DEV));
        $this->assertNull($service->getTargetCredentials(Env::STAGING));
        $this->assertNull($service->getTargetCredentials(Env::PROD));

        // Clean up.
        putenv('DEV_MYSQL_WRITE_USERNAME');
        putenv('DEV_MYSQL_WRITE_PASSWORD');
        putenv('STAGING_MYSQL_WRITE_USERNAME');
        putenv('STAGING_MYSQL_WRITE_PASSWORD');
        putenv('PROD_MYSQL_WRITE_USERNAME');
        putenv('PROD_MYSQL_WRITE_PASSWORD');
    }

    /**
     * `getTargetDatabase()` reads `SANDBOX_MYSQL_WRITE_DATABASE`
     * when target is sandbox.
     *
     * @return void
     * @covers ::getTargetDatabase
     */
    public function testGetTargetDatabaseReturnsSandboxDatabaseWhenSet(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_DATABASE=ntl_db');
        $this->assertSame('ntl_db', $service->getTargetDatabase(Env::SANDBOX));

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_DATABASE');
    }

    /**
     * `getTargetDatabase()` is sandbox-only and tolerates a missing
     * env var — the downstream resolver falls back to plain
     * `MYSQL_WRITE_DATABASE` so partial sandbox config doesn't fail
     * the apply path.
     *
     * @return void
     * @covers ::getTargetDatabase
     */
    public function testGetTargetDatabaseReturnsNullWhenUnsetOrNonSandbox(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());

        putenv('SANDBOX_MYSQL_WRITE_DATABASE');
        $this->assertNull($service->getTargetDatabase(Env::SANDBOX));

        putenv('SANDBOX_MYSQL_WRITE_DATABASE=ntl_db');
        $this->assertNull($service->getTargetDatabase(Env::DEV));
        $this->assertNull($service->getTargetDatabase(Env::STAGING));
        $this->assertNull($service->getTargetDatabase(Env::PROD));

        // Clean up.
        putenv('SANDBOX_MYSQL_WRITE_DATABASE');
    }

    /**
     * `apply()` mutates `ENV`, `MYSQL_WRITE_HOST`, `MYSQL_WRITE_PORT`,
     * `MYSQL_WRITE_USERNAME`, `MYSQL_WRITE_PASSWORD`,
     * `MYSQL_MIGRATION_USERNAME`, and `MYSQL_MIGRATION_PASSWORD` so the
     * downstream resolver + PDO service pick the override up. Also
     * prints a one-line banner per applied field. The per-tier
     * password value never appears in the banner.
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyPutenvsAllResolvedFields(): void
    {
        // Reset baseline.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');

        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        $service->apply(
            target:              Env::DEV,
            host:                'dev-db.example',
            port:                '3306',
            writeUsername:       'tier_baseline',
            writePassword:       'tier-password-1234',
            writeDatabase:       null,
            clearMigrationCreds: false,
            username:            'dba_user',
            password:            's3cret',
            databasePrefix:      null,
            output:              $output,
        );

        $this->assertSame('dev', getenv('ENV'));
        $this->assertSame('dev-db.example', getenv('MYSQL_WRITE_HOST'));
        $this->assertSame('3306', getenv('MYSQL_WRITE_PORT'));
        $this->assertSame('tier_baseline', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('tier-password-1234', getenv('MYSQL_WRITE_PASSWORD'));
        $this->assertSame('dba_user', getenv('MYSQL_MIGRATION_USERNAME'));
        $this->assertSame('s3cret', getenv('MYSQL_MIGRATION_PASSWORD'));

        $banner = $output->fetch();
        $this->assertStringContainsString('Migration target overrides:', $banner);
        $this->assertStringContainsString('ENV → dev', $banner);
        $this->assertStringContainsString('MYSQL_WRITE_HOST → dev-db.example', $banner);
        $this->assertStringContainsString('MYSQL_WRITE_PORT → 3306', $banner);
        $this->assertStringContainsString('MYSQL_WRITE_USERNAME → tier_baseline (per-tier)', $banner);
        $this->assertStringContainsString('MYSQL_WRITE_PASSWORD → (set from per-tier env)', $banner);
        $this->assertStringContainsString('MYSQL_MIGRATION_USERNAME → dba_user', $banner);
        $this->assertStringContainsString('MYSQL_MIGRATION_PASSWORD → (set from prompt)', $banner);
        $this->assertStringNotContainsString('tier-password-1234', $banner);
        $this->assertStringNotContainsString('s3cret', $banner);

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');
    }

    /**
     * Per-tier cred stamping is all-or-nothing — passing only the
     * username (or only the password) leaves both `MYSQL_WRITE_*`
     * cred env vars untouched and emits no banner lines for them.
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyIgnoresPerTierCredsWhenOnlyOneIsSet(): void
    {
        putenv('MYSQL_WRITE_USERNAME=baseline_user');
        putenv('MYSQL_WRITE_PASSWORD=baseline_password');

        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        $service->apply(
            target:              Env::DEV,
            host:                'dev-db.example',
            port:                '3306',
            writeUsername:       'tier_baseline',
            writePassword:       null,
            writeDatabase:       null,
            clearMigrationCreds: false,
            username:            null,
            password:            null,
            databasePrefix:      null,
            output:              $output,
        );

        $this->assertSame('baseline_user', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('baseline_password', getenv('MYSQL_WRITE_PASSWORD'));

        $banner = $output->fetch();
        $this->assertStringNotContainsString('MYSQL_WRITE_USERNAME', $banner);
        $this->assertStringNotContainsString('MYSQL_WRITE_PASSWORD', $banner);

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
    }

    /**
     * `apply()` with all-null overrides is a no-op — no env mutation
     * and no banner output. Preserves the pre-existing env-var
     * workflow for operators who don't use `--target` / `--username`.
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyWithNoOverridesIsANoOp(): void
    {
        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        putenv('ENV=test-baseline');
        putenv('MYSQL_WRITE_HOST=test-baseline-host');

        $service->apply(
            target:              null,
            host:                null,
            port:                null,
            writeUsername:       null,
            writePassword:       null,
            writeDatabase:       null,
            clearMigrationCreds: false,
            username:            null,
            password:            null,
            databasePrefix:      null,
            output:              $output,
        );

        $this->assertSame('test-baseline', getenv('ENV'));
        $this->assertSame('test-baseline-host', getenv('MYSQL_WRITE_HOST'));
        $this->assertSame('', $output->fetch());

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
    }

    /**
     * Sandbox-specific behavior: when `clearMigrationCreds` is true,
     * `apply()` unsets any inherited `MYSQL_MIGRATION_*` from the
     * shell BEFORE stamping `--username` / `--password` overrides.
     * Closes the bug where `.env`'s
     * `MYSQL_MIGRATION_USERNAME=ubix-migrations` shadowed the
     * sandbox `root` creds via the downstream precedence rule and
     * silently failed auth against the local docker MySQL.
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyClearsMigrationCredsWhenSandboxFlagIsSet(): void
    {
        putenv('MYSQL_MIGRATION_USERNAME=ubix-migrations');
        putenv('MYSQL_MIGRATION_PASSWORD=inherited_password');

        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        $service->apply(
            target:              Env::SANDBOX,
            host:                '127.0.0.1',
            port:                '30306',
            writeUsername:       'root',
            writePassword:       'SandboxTestPassword1234',
            writeDatabase:       'ntl_db',
            clearMigrationCreds: true,
            username:            null,
            password:            null,
            databasePrefix:      null,
            output:              $output,
        );

        $this->assertSame('sandbox', getenv('ENV'));
        $this->assertSame('127.0.0.1', getenv('MYSQL_WRITE_HOST'));
        $this->assertSame('30306', getenv('MYSQL_WRITE_PORT'));
        $this->assertSame('root', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('SandboxTestPassword1234', getenv('MYSQL_WRITE_PASSWORD'));
        $this->assertSame('ntl_db', getenv('MYSQL_WRITE_DATABASE'));
        $this->assertFalse(getenv('MYSQL_MIGRATION_USERNAME'));
        $this->assertFalse(getenv('MYSQL_MIGRATION_PASSWORD'));

        $banner = $output->fetch();
        $this->assertStringContainsString('MYSQL_WRITE_DATABASE → ntl_db (per-tier)', $banner);
        $this->assertStringContainsString('MYSQL_MIGRATION_USERNAME → (cleared for sandbox)', $banner);
        $this->assertStringContainsString('MYSQL_MIGRATION_PASSWORD → (cleared for sandbox)', $banner);

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
        putenv('MYSQL_WRITE_DATABASE');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');
    }

    /**
     * `--username=<user>` re-stamps `MYSQL_MIGRATION_*` AFTER the
     * sandbox-driven clear, so the operator can still deliberately
     * override the per-tier sandbox creds when they have a reason
     * to (e.g. testing migrations against the sandbox MySQL as a
     * non-root user).
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyClearThenUsernameOverrideStampsMigrationCreds(): void
    {
        putenv('MYSQL_MIGRATION_USERNAME=ubix-migrations');
        putenv('MYSQL_MIGRATION_PASSWORD=inherited_password');

        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        $service->apply(
            target:              Env::SANDBOX,
            host:                '127.0.0.1',
            port:                '30306',
            writeUsername:       'root',
            writePassword:       'SandboxTestPassword1234',
            writeDatabase:       'ntl_db',
            clearMigrationCreds: true,
            username:            'dba_user',
            password:            'prompt_pw',
            databasePrefix:      null,
            output:              $output,
        );

        $this->assertSame('dba_user', getenv('MYSQL_MIGRATION_USERNAME'));
        $this->assertSame('prompt_pw', getenv('MYSQL_MIGRATION_PASSWORD'));

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
        putenv('MYSQL_WRITE_DATABASE');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');
    }

    /**
     * `apply()` stamps `$databasePrefix` onto the `DATABASE_PREFIX`
     * env var so downstream consumers (`UbixDatabase::databaseName()`,
     * `SchemaMigrationSqlRepository::trackerTable()`, the migration
     * apply-path body rewrite in `MigrationApplyService::runBodyViaMariadbCli()`)
     * pick up the prefix on their next env read. Used by the
     * `--prefix=TEST_` flow that backs test-DB isolation.
     *
     * @return void
     * @covers ::apply
     */
    public function testApplyStampsDatabasePrefixEnvVar(): void
    {
        putenv('DATABASE_PREFIX');

        $service = new MigrationConnectionTargetService(new NullLogger());
        $output  = new BufferedOutput();

        $service->apply(
            target:              Env::SANDBOX,
            host:                '127.0.0.1',
            port:                '30306',
            writeUsername:       'root',
            writePassword:       'SandboxTestPassword1234',
            writeDatabase:       'ntl_db',
            clearMigrationCreds: true,
            username:            null,
            password:            null,
            databasePrefix:      'TEST_',
            output:              $output,
        );

        $this->assertSame('TEST_', getenv('DATABASE_PREFIX'));

        $banner = $output->fetch();
        $this->assertStringContainsString('DATABASE_PREFIX → TEST_', $banner);

        // Clean up.
        putenv('ENV');
        putenv('MYSQL_WRITE_HOST');
        putenv('MYSQL_WRITE_PORT');
        putenv('MYSQL_WRITE_USERNAME');
        putenv('MYSQL_WRITE_PASSWORD');
        putenv('MYSQL_WRITE_DATABASE');
        putenv('MYSQL_MIGRATION_USERNAME');
        putenv('MYSQL_MIGRATION_PASSWORD');
        putenv('DATABASE_PREFIX');
    }

    /**
     * Snapshot the process-global env this test will mutate.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        foreach (self::PRESERVED_ENV_KEYS as $key) {
            $this->envSnapshot[$key] = getenv($key);
        }
    }

    /**
     * Restore the env to its pre-test state so no mutation leaks into the
     * rest of the suite.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->envSnapshot as $key => $value) {
            if ($value === false) {
                putenv($key);
            } else {
                putenv($key . '=' . $value);
            }
        }
        parent::tearDown();
    }
}
