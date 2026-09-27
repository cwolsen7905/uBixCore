<?php

declare(strict_types=1);

namespace Ubix\Service\Vault;

use Psr\Log\LoggerInterface as Logger;
use RuntimeException;

/**
 * Resolves the application's database credentials from uBix Vault at process
 * startup and injects them into the environment, so the rest of the app
 * (notably {@see \Ubix\Service\Sql\MysqlPdoSqlService}) keeps reading plain
 * `MYSQL_*` env vars and needs no Vault awareness.
 *
 * Wired into both entry points (`bin/ubix` and `public/index.php`) immediately
 * after Dotenv loads. It is a NO-OP unless `VAULT_ADDR` is set — so local
 * development keeps using a git-ignored `.env`, while deployed pods pull
 * credentials from Vault instead of from any committed secret file.
 *
 * Auth: prefers a static `VAULT_TOKEN` (dev/CI); otherwise, in-cluster, logs in
 * with the Kubernetes auth method using the pod's mounted service-account JWT
 * and `VAULT_K8S_ROLE`.
 *
 * @see \Ubix\Tests\Service\Vault\VaultCredentialResolverServiceTest PHPUnit test case
 */
final class VaultCredentialResolverService
{
    private const KUBERNETES_SERVICE_ACCOUNT_JWT = '/var/run/secrets/kubernetes.io/serviceaccount/token';

    /**
     * Map of Vault KV keys -> the environment variable each populates. The KV
     * secret at `VAULT_DB_KV_PATH` is expected to carry these keys.
     */
    private const KV_KEY_TO_ENV = [
        'read_password'  => 'MYSQL_READ_PASSWORD',
        'read_username'  => 'MYSQL_READ_USERNAME',
        'write_password' => 'MYSQL_WRITE_PASSWORD',
        'write_username' => 'MYSQL_WRITE_USERNAME',
    ];

    /**
     * Map of Vault KV keys -> the `TEST_MYSQL_WRITE_*` variable each populates.
     *
     * The unit-test connection is one credential pair, not a read/write split, so
     * the secret is flat: the whole connection lives in Vault rather than only its
     * password. A test database's host and name are not sensitive, but splitting
     * them across Vault and a committed file is how a `.env` ends up holding the
     * password anyway "just to keep them together".
     */
    private const TEST_KV_KEY_TO_ENV = [
        'database' => 'TEST_MYSQL_WRITE_DATABASE',
        'host'     => 'TEST_MYSQL_WRITE_HOST',
        'password' => 'TEST_MYSQL_WRITE_PASSWORD',
        'port'     => 'TEST_MYSQL_WRITE_PORT',
        'username' => 'TEST_MYSQL_WRITE_USERNAME',
    ];

    /**
     * Constructor
     *
     * @param Logger       $logger       Logger
     * @param VaultService $vaultService Client for the uBix Vault server
     */
    public function __construct(
        private Logger $logger,
        private VaultService $vaultService,
    ) {
    }

    /**
     * Resolve database credentials from Vault and inject them into the
     * environment via `putenv()`.
     *
     * Authentication or read failure propagates as a RuntimeException from the
     * callees (fail-closed — when Vault is configured we never silently fall
     * back to potentially-stale environment values).
     *
     * @param string $vaultAddress Base address of the Vault server
     *
     * @return void
     */
    public function hydrateEnvironment(string $vaultAddress): void
    {
        $token       = $this->resolveToken($vaultAddress);
        $credentials = $this->resolveCredentials($vaultAddress, $token);

        foreach ($credentials as $envName => $value) {
            // MysqlPdoSqlService reads credentials via getenv(), so putenv() is
            // sufficient — no need to touch the $_ENV / $_SERVER superglobals.
            putenv($envName . '=' . $value);
        }

        $this->logger->info('Hydrated database credentials from uBix Vault', [
            'vars' => array_keys($credentials), // Names only — never the values.
        ]);

        $this->hydrateAppSecrets($vaultAddress, $token);
    }

    /**
     * Resolve the unit-test database connection from Vault into the environment
     *
     * Opt-in via `VAULT_TEST_DB_KV_PATH`, and a no-op without it — a host with no
     * Vault available keeps using its git-ignored `.env`, exactly as the runtime
     * credentials do.
     *
     * This exists so a developer machine does not need a database password in a
     * plaintext file at all. The remaining local secret is one Vault token, which
     * is revocable, scoped and expiring, where a copied `.env` password is none of
     * those things and is only ever rotated by remembering to.
     *
     * @param string $vaultAddress Base address of the Vault server
     *
     * @throws RuntimeException When the configured secret yields none of the expected keys
     *
     * @return void
     */
    public function hydrateTestDatabase(string $vaultAddress): void
    {
        $path = $this->readEnv('VAULT_TEST_DB_KV_PATH');

        if ($path === '') {
            return;
        }

        $secret   = $this->vaultService->readKvV2Secret($vaultAddress, $this->resolveToken($vaultAddress), $path);
        $resolved = [];

        foreach (self::TEST_KV_KEY_TO_ENV as $kvKey => $envName) {
            if (isset($secret[$kvKey]) && $secret[$kvKey] !== '') {
                $resolved[$envName] = (string) $secret[$kvKey];
            }
        }

        if ($resolved === []) {
            // Fail closed: a configured path that yields nothing means the secret is
            // wrong, and falling back to whatever the environment already held would
            // silently point the suite at another database.
            throw new RuntimeException(
                'uBix Vault KV secret `' . $path . '` (VAULT_TEST_DB_KV_PATH) had none of the expected keys (' . implode(', ', array_keys(self::TEST_KV_KEY_TO_ENV)) . ').',
            );
        }

        foreach ($resolved as $envName => $value) {
            putenv($envName . '=' . $value);
        }

        $this->logger->info('Hydrated test database connection from uBix Vault', [
            'vars' => array_keys($resolved), // Names only — never the values.
        ]);
    }

    /**
     * Hydrate the host application's own secrets (API tokens, webhook signing keys, ...)
     *
     * Opt-in via `VAULT_APP_KV_PATH` (one KV v2 path) and/or `VAULT_APP_KV_PATHS` (a
     * comma-separated list). Every key in those secrets whose name is an UPPER_SNAKE_CASE
     * environment-variable name becomes an environment variable, the same way the database
     * credentials do — so hosts never need a local shim, and never need a secret in source
     * or in a committed `.env`.
     *
     * Several paths exist so that each secret is stored once: an app that needs its own
     * keys *and* keys another app owns reads both paths instead of holding a second copy
     * that has to be rotated in step. `VAULT_APP_KV_PATH` is read first, then the list in
     * order. When two paths define the same key, **the first one wins** — list an app's own
     * path first and it can override a shared value — and the collision is logged by name.
     *
     * Keys in the `VAULT_*` and `MYSQL_*` namespaces are refused: those are this
     * resolver's own inputs and outputs, and an app secret must not be able to redirect
     * Vault auth or replace the database credentials resolved above.
     *
     * @param string $vaultAddress Base address of the Vault server
     * @param string $token        A valid Vault client token
     *
     * @return void
     *
     * @throws RuntimeException When a configured path yields no usable keys
     */
    private function hydrateAppSecrets(string $vaultAddress, string $token): void
    {
        $paths = $this->appSecretPaths();

        if ($paths === []) {
            return;
        }

        $hydrated = [];

        foreach ($paths as $path) {
            $usable = 0;

            foreach ($this->vaultService->readKvV2Secret($vaultAddress, $token, $path) as $key => $value) {
                if (
                    preg_match('/^[A-Z][A-Z0-9_]*$/', (string) $key) !== 1
                    || str_starts_with((string) $key, 'VAULT_')
                    || str_starts_with((string) $key, 'MYSQL_')
                    || $value === ''
                ) {
                    continue;
                }

                $usable++;

                if (isset($hydrated[$key])) {
                    $this->logger->warning('App secret defined in more than one uBix Vault path; the first wins', [
                        'kept' => $hydrated[$key],
                        'key'  => $key, // The name only — never the value.
                        'path' => $path,
                    ]);

                    continue;
                }

                putenv($key . '=' . $value);
                $hydrated[$key] = $path;
            }

            if ($usable === 0) {
                // Fail closed, per path: a configured path that yields nothing means a
                // misconfigured or empty secret, and the app would otherwise boot without
                // the credentials that path was added for.
                throw new RuntimeException('uBix Vault KV secret `' . $path . '` (VAULT_APP_KV_PATH/VAULT_APP_KV_PATHS) yielded no usable UPPER_SNAKE_CASE keys.');
            }
        }

        $this->logger->info('Hydrated app secrets from uBix Vault', [
            'paths' => $paths,
            'vars'  => array_keys($hydrated), // Names only — never the values.
        ]);
    }

    /**
     * The app-secret paths to read, in precedence order, without blanks or repeats
     *
     * @return list<string> `VAULT_APP_KV_PATH` first, then `VAULT_APP_KV_PATHS` in order
     */
    private function appSecretPaths(): array
    {
        $paths = [];

        foreach ([$this->readEnv('VAULT_APP_KV_PATH'), ...explode(',', $this->readEnv('VAULT_APP_KV_PATHS'))] as $path) {
            $path = trim($path);

            if ($path !== '' && !in_array($path, $paths, true)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Obtain a Vault client token: a static VAULT_TOKEN when present, otherwise
     * a Kubernetes-auth login with the pod's service-account JWT.
     *
     * @param string $vaultAddress Base address of the Vault server
     *
     * @throws RuntimeException If no usable auth method is configured
     *
     * @return string A Vault client token
     */
    private function resolveToken(string $vaultAddress): string
    {
        $staticToken = $this->readEnv('VAULT_TOKEN');
        if ($staticToken !== '') {
            return $staticToken;
        }

        $role = $this->readEnv('VAULT_K8S_ROLE');
        if ($role !== '' && is_readable(self::KUBERNETES_SERVICE_ACCOUNT_JWT)) {
            $jwt = (string) file_get_contents(self::KUBERNETES_SERVICE_ACCOUNT_JWT);
            return $this->vaultService->loginKubernetes($vaultAddress, $role, trim($jwt));
        }

        throw new RuntimeException(
            'VAULT_ADDR is set but no auth is available: set VAULT_TOKEN, or VAULT_K8S_ROLE with a mounted service-account token.',
        );
    }

    /**
     * Resolve the four MYSQL_* credential env values from Vault, using the
     * configured strategy (`kv` — a KV v2 secret, the default; or `dynamic` —
     * generated database credentials).
     *
     * @param string $vaultAddress Base address of the Vault server
     * @param string $token        A valid Vault client token
     *
     * @throws RuntimeException If the configured KV secret is missing the expected keys
     *
     * @return array<string, string> Map of MYSQL_* env var name => value
     */
    private function resolveCredentials(string $vaultAddress, string $token): array
    {
        $strategy = $this->readEnv('VAULT_DB_STRATEGY');

        if ($strategy === 'dynamic') {
            $role  = $this->readEnv('VAULT_DB_ROLE') !== '' ? $this->readEnv('VAULT_DB_ROLE') : 'app';
            $creds = $this->vaultService->readDatabaseCredentials($vaultAddress, $token, $role);

            // Dynamic role issues one credential pair; use it for both read + write.
            return [
                'MYSQL_READ_PASSWORD'  => $creds['password'],
                'MYSQL_READ_USERNAME'  => $creds['username'],
                'MYSQL_WRITE_PASSWORD' => $creds['password'],
                'MYSQL_WRITE_USERNAME' => $creds['username'],
            ];
        }

        $path   = $this->readEnv('VAULT_DB_KV_PATH') !== '' ? $this->readEnv('VAULT_DB_KV_PATH') : 'app/db';
        $secret = $this->vaultService->readKvV2Secret($vaultAddress, $token, $path);

        $resolved = [];
        foreach (self::KV_KEY_TO_ENV as $kvKey => $envName) {
            if (isset($secret[$kvKey]) && $secret[$kvKey] !== '') {
                $resolved[$envName] = $secret[$kvKey];
            }
        }

        if ($resolved === []) {
            throw new RuntimeException(
                'uBix Vault KV secret `' . $path . '` had none of the expected keys (' . implode(', ', array_keys(self::KV_KEY_TO_ENV)) . ').',
            );
        }

        return $resolved;
    }

    /**
     * Read an environment variable as a trimmed string (empty when unset).
     *
     * @param string $name Environment variable name
     *
     * @return string The value, or '' when unset/false
     */
    private function readEnv(string $name): string
    {
        $value = getenv($name);
        return is_string($value) ? trim($value) : '';
    }
}
