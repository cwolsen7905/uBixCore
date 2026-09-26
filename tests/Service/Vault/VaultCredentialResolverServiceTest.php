<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Vault;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Service\JsonService;
use Ubix\Service\Vault\VaultCredentialResolverService;
use Ubix\Service\Vault\VaultService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Vault\VaultCredentialResolverService
 *
 * @coversDefaultClass \Ubix\Service\Vault\VaultCredentialResolverService
 */
final class VaultCredentialResolverServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const ENV = [
        'VAULT_TOKEN', 'VAULT_K8S_ROLE', 'VAULT_DB_KV_PATH', 'VAULT_APP_KV_PATH', 'VAULT_DB_STRATEGY',
        'VAULT_DB_ROLE', 'VAULT_TEST_DB_KV_PATH',
        'MYSQL_READ_USERNAME', 'MYSQL_READ_PASSWORD', 'MYSQL_WRITE_USERNAME', 'MYSQL_WRITE_PASSWORD',
        'TEST_MYSQL_WRITE_HOST', 'TEST_MYSQL_WRITE_PORT', 'TEST_MYSQL_WRITE_DATABASE',
        'TEST_MYSQL_WRITE_USERNAME', 'TEST_MYSQL_WRITE_PASSWORD',
        'API_BEARER_TOKENS', 'lower_case_key', 'VAULT_ADDR_OVERRIDE',
    ];

    private const VAULT_ADDRESS = 'https://vault.test';

    private const FULL_DB_SECRET = [
        'read_password'  => 'read-pass',
        'read_username'  => 'reader',
        'write_password' => 'write-pass',
        'write_username' => 'writer',
    ];

    /**
     * Values the managed env vars held before the test (false = unset).
     *
     * @var array<string, string|false>
     */
    private array $originalEnv = [];

    /**
     * Requests the resolver's VaultService sent, in order.
     *
     * @var array<int, Request>
     */
    private array $requests = [];

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(VaultCredentialResolverService::class);
    }

    /**
     * Without VAULT_APP_KV_PATH only the database credentials are hydrated
     *
     * @return void
     */
    public function testAppSecretsAreOptIn(): void
    {
        putenv('VAULT_TOKEN=test-token');

        $this->resolver([$this->kv(['read_username' => 'r', 'read_password' => 'rp', 'write_username' => 'w', 'write_password' => 'wp'])])
            ->hydrateEnvironment('https://vault.test');

        $this->assertSame('r', getenv('MYSQL_READ_USERNAME'));
        $this->assertFalse(getenv('API_BEARER_TOKENS'));
    }

    /**
     * UPPER_SNAKE_CASE keys become env vars; lower-case and VAULT_* / MYSQL_* keys are refused
     *
     * @return void
     */
    public function testHydratesUpperSnakeKeysAndRefusesReservedNamespaces(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_APP_KV_PATH=app/api');

        $this->resolver([
            $this->kv(['read_username' => 'r', 'read_password' => 'rp', 'write_username' => 'w', 'write_password' => 'wp']),
            $this->kv([
                'API_BEARER_TOKENS'    => 'one,two',
                'lower_case_key'       => 'ignored',
                'MYSQL_WRITE_PASSWORD' => 'hijacked',
                'VAULT_ADDR_OVERRIDE'  => 'https://attacker.test',
            ]),
        ])->hydrateEnvironment('https://vault.test');

        $this->assertSame('one,two', getenv('API_BEARER_TOKENS'));
        $this->assertFalse(getenv('lower_case_key'));
        $this->assertFalse(getenv('VAULT_ADDR_OVERRIDE'));
        $this->assertSame('wp', getenv('MYSQL_WRITE_PASSWORD'), 'An app secret must not replace the resolved DB credentials');
    }

    /**
     * A configured app path that yields nothing usable fails closed
     *
     * @return void
     */
    public function testFailsClosedWhenAppSecretIsEmpty(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_APP_KV_PATH=app/api');

        $this->expectException(RuntimeException::class);

        $this->resolver([
            $this->kv(['read_username' => 'r', 'read_password' => 'rp', 'write_username' => 'w', 'write_password' => 'wp']),
            $this->kv(['lower_case_only' => 'x']),
        ])->hydrateEnvironment('https://vault.test');
    }

    /**
     * The test-database hydration is opt-in and silent without its path
     *
     * @return void
     */
    public function testTestDatabaseHydrationIsOptIn(): void
    {
        putenv('VAULT_TOKEN=test-token');

        // No VAULT_TEST_DB_KV_PATH: no HTTP call is queued, so reaching one would
        // fail the mock handler rather than pass quietly.
        $this->resolver([])->hydrateTestDatabase('https://vault.test');

        $this->assertFalse(getenv('TEST_MYSQL_WRITE_HOST'));
    }

    /**
     * The whole test connection comes from Vault, so no file needs the password
     *
     * @return void
     */
    public function testTestDatabaseConnectionIsHydratedFromVault(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_TEST_DB_KV_PATH=ubixcore/test-db');

        $this->resolver([
            $this->kv([
                'database' => 'ubixcore_test',
                'host'     => 'db.internal',
                'password' => 'secret',
                'port'     => '30306',
                'username' => 'ubixcore_test',
            ]),
        ])->hydrateTestDatabase('https://vault.test');

        $this->assertSame('db.internal', getenv('TEST_MYSQL_WRITE_HOST'));
        $this->assertSame('30306', getenv('TEST_MYSQL_WRITE_PORT'));
        $this->assertSame('ubixcore_test', getenv('TEST_MYSQL_WRITE_DATABASE'));
        $this->assertSame('ubixcore_test', getenv('TEST_MYSQL_WRITE_USERNAME'));
        $this->assertSame('secret', getenv('TEST_MYSQL_WRITE_PASSWORD'));
    }

    /**
     * Vault wins over a stale value the environment already carried
     *
     * @return void
     */
    public function testVaultOverridesAnExistingEnvironmentValue(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_TEST_DB_KV_PATH=ubixcore/test-db');
        putenv('TEST_MYSQL_WRITE_HOST=127.0.0.1');

        $this->resolver([$this->kv(['host' => 'db.internal', 'password' => 'secret'])])
            ->hydrateTestDatabase('https://vault.test');

        // The retired sandbox host is exactly the stale value this replaces.
        $this->assertSame('db.internal', getenv('TEST_MYSQL_WRITE_HOST'));
    }

    /**
     * A configured test-db path that yields nothing usable fails closed
     *
     * @return void
     */
    public function testFailsClosedWhenTestDatabaseSecretIsEmpty(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_TEST_DB_KV_PATH=ubixcore/test-db');

        $this->expectException(RuntimeException::class);

        // Falling back to whatever the environment held would point the suite at
        // another database without saying so.
        $this->resolver([$this->kv(['unexpected' => 'x'])])->hydrateTestDatabase('https://vault.test');
    }

    /**
     * A static VAULT_TOKEN (trimmed) reads the default `app/db` KV path into all four MYSQL_* variables
     *
     * @return void
     */
    public function testStaticTokenHydratesAllDatabaseCredentialsFromDefaultPath(): void
    {
        putenv("VAULT_TOKEN=  test-token\n");

        $this->resolver([$this->kv(self::FULL_DB_SECRET)])->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertSame('reader', getenv('MYSQL_READ_USERNAME'));
        $this->assertSame('read-pass', getenv('MYSQL_READ_PASSWORD'));
        $this->assertSame('writer', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('write-pass', getenv('MYSQL_WRITE_PASSWORD'));

        $this->assertCount(1, $this->requests, 'A static token needs no login call');
        $request = $this->requests[0];
        $this->assertSame(self::VAULT_ADDRESS . '/v1/secret/data/app/db', (string) $request->getUri());
        $this->assertSame('test-token', $request->getHeaderLine('X-Vault-Token'));
    }

    /**
     * VAULT_DB_KV_PATH selects which KV secret holds the credentials
     *
     * @return void
     */
    public function testKvPathIsConfigurable(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_DB_KV_PATH=kitg/db-prod');

        $this->resolver([$this->kv(self::FULL_DB_SECRET)])->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertSame(self::VAULT_ADDRESS . '/v1/secret/data/kitg/db-prod', (string) $this->requests[0]->getUri());
    }

    /**
     * Keys missing or empty in the secret leave their variables as they were
     *
     * @return void
     */
    public function testPartialSecretOnlyOverwritesTheKeysItCarries(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('MYSQL_WRITE_USERNAME=env-writer');
        putenv('MYSQL_WRITE_PASSWORD=env-write-pass');

        $this->resolver([$this->kv(['read_username' => 'reader', 'read_password' => 'read-pass', 'write_password' => ''])])
            ->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertSame('reader', getenv('MYSQL_READ_USERNAME'));
        $this->assertSame('read-pass', getenv('MYSQL_READ_PASSWORD'));
        $this->assertSame('env-writer', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('env-write-pass', getenv('MYSQL_WRITE_PASSWORD'), 'An empty Vault value must not blank a credential');
    }

    /**
     * A KV secret with none of the expected keys fails closed and changes nothing
     *
     * @return void
     */
    public function testFailsClosedWhenDatabaseSecretHasNoExpectedKeys(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('MYSQL_READ_PASSWORD=stale');

        try {
            $this->resolver([$this->kv(['username' => 'wrong-shape', 'password' => 'x'])])->hydrateEnvironment(self::VAULT_ADDRESS);
            $this->fail('Expected a RuntimeException for a secret with none of the expected keys');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('`app/db` had none of the expected keys', $exception->getMessage());
        }

        $this->assertSame('stale', getenv('MYSQL_READ_PASSWORD'));
    }

    /**
     * The dynamic strategy reads the default `app` role and uses its one pair for read and write
     *
     * @return void
     */
    public function testDynamicStrategyUsesOneGeneratedPairForReadAndWrite(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_DB_STRATEGY=dynamic');

        $this->resolver([$this->creds('v-app-123', 'generated')])->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertSame(self::VAULT_ADDRESS . '/v1/database/creds/app', (string) $this->requests[0]->getUri());
        $this->assertSame('v-app-123', getenv('MYSQL_READ_USERNAME'));
        $this->assertSame('v-app-123', getenv('MYSQL_WRITE_USERNAME'));
        $this->assertSame('generated', getenv('MYSQL_READ_PASSWORD'));
        $this->assertSame('generated', getenv('MYSQL_WRITE_PASSWORD'));
    }

    /**
     * VAULT_DB_ROLE selects the dynamic database role
     *
     * @return void
     */
    public function testDynamicStrategyRoleIsConfigurable(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_DB_STRATEGY=dynamic');
        putenv('VAULT_DB_ROLE=kitg-api');

        $this->resolver([$this->creds('v-kitg', 'p')])->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertSame(self::VAULT_ADDRESS . '/v1/database/creds/kitg-api', (string) $this->requests[0]->getUri());
    }

    /**
     * With neither a token nor a Kubernetes role, the resolver refuses before contacting Vault
     *
     * @return void
     */
    public function testThrowsWithoutAnyAuthBeforeContactingVault(): void
    {
        try {
            $this->resolver([])->hydrateEnvironment(self::VAULT_ADDRESS);
            $this->fail('Expected a RuntimeException with no auth configured');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('no auth is available', $exception->getMessage());
        }

        $this->assertSame([], $this->requests);
    }

    /**
     * A Vault error propagates and leaves the existing credentials in place (fail closed)
     *
     * @return void
     */
    public function testVaultErrorPropagatesAndLeavesEnvironmentUntouched(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('MYSQL_WRITE_PASSWORD=stale');

        try {
            $this->resolver([new Response(403, [], '{"errors":["permission denied"]}')])->hydrateEnvironment(self::VAULT_ADDRESS);
            $this->fail('Expected the Vault 403 to propagate');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('failed', $exception->getMessage());
        }

        $this->assertSame('stale', getenv('MYSQL_WRITE_PASSWORD'));
    }

    /**
     * The success log names the hydrated variables and never carries their values
     *
     * @return void
     */
    public function testLogsVariableNamesButNeverValues(): void
    {
        putenv('VAULT_TOKEN=test-token');

        // An exact context match: names only, so no value can ride along.
        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'Hydrated database credentials from uBix Vault',
                ['vars' => ['MYSQL_READ_PASSWORD', 'MYSQL_READ_USERNAME', 'MYSQL_WRITE_PASSWORD', 'MYSQL_WRITE_USERNAME']],
            );

        $this->resolver([$this->kv(self::FULL_DB_SECRET)], $logger)->hydrateEnvironment(self::VAULT_ADDRESS);
    }

    /**
     * App secrets are read with the same token and after the database credentials
     *
     * @return void
     */
    public function testAppSecretsAreReadAfterDatabaseCredentialsWithTheSameToken(): void
    {
        putenv('VAULT_TOKEN=test-token');
        putenv('VAULT_APP_KV_PATH=app/api');

        $this->resolver([$this->kv(self::FULL_DB_SECRET), $this->kv(['API_BEARER_TOKENS' => 'one'])])
            ->hydrateEnvironment(self::VAULT_ADDRESS);

        $this->assertCount(2, $this->requests);
        $this->assertSame(self::VAULT_ADDRESS . '/v1/secret/data/app/db', (string) $this->requests[0]->getUri());
        $this->assertSame(self::VAULT_ADDRESS . '/v1/secret/data/app/api', (string) $this->requests[1]->getUri());
        $this->assertSame('test-token', $this->requests[1]->getHeaderLine('X-Vault-Token'));
    }

    /**
     * Capture then clear every env var these tests can set, so a developer's own
     * VAULT_* / MYSQL_* shell variables neither leak in nor get wiped
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::ENV as $name) {
            $this->originalEnv[$name] = getenv($name);
            putenv($name);
        }
    }

    /**
     * Restore every env var these tests can set
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->originalEnv as $name => $value) {
            putenv($value === false ? $name : $name . '=' . $value);
        }

        parent::tearDown();
    }

    /**
     * Build a resolver over a real VaultService whose HTTP client replays the given responses
     *
     * @param array<int, Response> $responses Queued responses, in request order
     * @param ?Logger              $logger    Logger to inject, or a stub when null
     *
     * @return VaultCredentialResolverService
     */
    private function resolver(array $responses, ?Logger $logger = null): VaultCredentialResolverService
    {
        $logger ??= $this->createStub(Logger::class);

        $this->requests = [];
        $stack          = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::mapRequest(function (Request $request): Request {
            $this->requests[] = $request;

            return $request;
        }));
        $client = new Client(['handler' => $stack]);

        return new VaultCredentialResolverService($logger, new VaultService($logger, $client, new JsonService($logger)));
    }

    /**
     * A KV v2 read response carrying the given data map
     *
     * @param array<string, string> $data Secret key/value pairs
     *
     * @return Response
     */
    private function kv(array $data): Response
    {
        $json = new JsonService($this->createStub(Logger::class));

        return new Response(200, ['Content-Type' => 'application/json'], $json->encode(['data' => ['data' => $data]]));
    }

    /**
     * A dynamic database-credentials response carrying the given pair
     *
     * @param string $username Generated username
     * @param string $password Generated password
     *
     * @return Response
     */
    private function creds(string $username, string $password): Response
    {
        $json = new JsonService($this->createStub(Logger::class));

        return new Response(200, ['Content-Type' => 'application/json'], $json->encode(['data' => ['username' => $username, 'password' => $password]]));
    }
}
