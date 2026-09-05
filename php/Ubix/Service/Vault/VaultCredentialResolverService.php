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
