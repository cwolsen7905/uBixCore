<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Vault;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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
        'VAULT_TOKEN', 'VAULT_DB_KV_PATH', 'VAULT_APP_KV_PATH', 'VAULT_DB_STRATEGY',
        'MYSQL_READ_USERNAME', 'MYSQL_READ_PASSWORD', 'MYSQL_WRITE_USERNAME', 'MYSQL_WRITE_PASSWORD',
        'API_BEARER_TOKENS', 'lower_case_key', 'VAULT_ADDR_OVERRIDE',
    ];

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
     * Clear every env var these tests can set
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach (self::ENV as $name) {
            putenv($name);
        }

        parent::tearDown();
    }

    /**
     * Build a resolver over a real VaultService whose HTTP client replays the given responses
     *
     * @param array<int, Response> $responses Queued responses, in request order
     *
     * @return VaultCredentialResolverService
     */
    private function resolver(array $responses): VaultCredentialResolverService
    {
        $logger = $this->createStub(Logger::class);
        $client = new Client(['handler' => HandlerStack::create(new MockHandler($responses))]);

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
}
