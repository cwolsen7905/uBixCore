<?php

declare(strict_types=1);

namespace Ubix\Service\Vault;

use GuzzleHttp\ClientInterface as Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Ubix\Service\JsonService;

/**
 * Minimal client for uBix Vault — the uBix family's HashiCorp-Vault-compatible
 * secrets manager (see ~/git/ubixvault). Speaks the standard Vault HTTP API:
 * token/Kubernetes auth, KV v2 secret reads, and dynamic database credentials.
 *
 * This is deliberately read-only and stateless: callers pass the Vault address
 * and (post-auth) token into each method, so a single instance can serve both
 * the login call and the subsequent secret reads without holding mutable auth
 * state. Secret values are never logged.
 *
 * @see \Ubix\Tests\Service\Vault\VaultServiceTest PHPUnit test case
 */
final class VaultService
{
    private const AUTH_KUBERNETES_LOGIN = '/v1/auth/kubernetes/login';

    private const KV_V2_READ_PREFIX = '/v1/secret/data/';

    private const DATABASE_CREDS_PREFIX = '/v1/database/creds/';

    private const VAULT_TOKEN_HEADER = 'X-Vault-Token';

    /**
     * Constructor
     *
     * @param Logger      $logger      Logger
     * @param Client      $httpClient  Guzzle HTTP client
     * @param JsonService $jsonService JSON encode/decode service
     */
    public function __construct(
        private Logger $logger,
        private Client $httpClient,
        private JsonService $jsonService,
    ) {
    }

    /**
     * Authenticate via the Kubernetes auth method and return a client token.
     *
     * The pod presents its service-account JWT; Vault validates it against the
     * bound role and issues a short-lived client token.
     *
     * @param string $vaultAddress      Base address of the Vault server (e.g. https://vault.internal:8200)
     * @param string $role              The Kubernetes auth role to log in as
     * @param string $serviceAccountJwt The pod's service-account JWT
     *
     * @throws RuntimeException If the login fails or returns no client token
     *
     * @return string The issued Vault client token
     */
    public function loginKubernetes(string $vaultAddress, string $role, string $serviceAccountJwt): string
    {
        $response = $this->send($vaultAddress, 'POST', self::AUTH_KUBERNETES_LOGIN, null, [
            'jwt'  => $serviceAccountJwt,
            'role' => $role,
        ]);

        $auth  = $response['auth'] ?? null;
        $token = is_array($auth) ? ($auth['client_token'] ?? null) : null;

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('uBix Vault Kubernetes login did not return a client_token');
        }

        return $token;
    }

    /**
     * Read a KV v2 secret and return its inner data map.
     *
     * @param string $vaultAddress Base address of the Vault server
     * @param string $token        A valid Vault client token
     * @param string $path         KV v2 path relative to the secret mount (e.g. `app/db`)
     *
     * @throws RuntimeException If the read fails or the response has no data map
     *
     * @return array<string, string> The secret's key/value pairs
     */
    public function readKvV2Secret(string $vaultAddress, string $token, string $path): array
    {
        $response = $this->send($vaultAddress, 'GET', self::KV_V2_READ_PREFIX . ltrim($path, '/'), $token);

        $outer = $response['data'] ?? null;
        $inner = is_array($outer) ? ($outer['data'] ?? null) : null;

        if (! is_array($inner)) {
            throw new RuntimeException('uBix Vault KV read for `' . $path . '` returned no data map');
        }

        $secret = [];
        foreach ($inner as $key => $value) {
            if (is_string($key) && (is_string($value) || is_int($value))) {
                $secret[$key] = (string) $value;
            }
        }

        return $secret;
    }

    /**
     * Read dynamic database credentials for a role.
     *
     * @param string $vaultAddress Base address of the Vault server
     * @param string $token        A valid Vault client token
     * @param string $role         The database role whose credentials to generate
     *
     * @throws RuntimeException If the read fails or returns no username/password
     *
     * @return array{username: string, password: string} The generated credentials
     */
    public function readDatabaseCredentials(string $vaultAddress, string $token, string $role): array
    {
        $response = $this->send($vaultAddress, 'GET', self::DATABASE_CREDS_PREFIX . rawurlencode($role), $token);

        $data     = $response['data'] ?? null;
        $username = is_array($data) ? ($data['username'] ?? null) : null;
        $password = is_array($data) ? ($data['password'] ?? null) : null;

        if (! is_string($username) || ! is_string($password) || $username === '') {
            throw new RuntimeException('uBix Vault database creds for role `' . $role . '` returned no username/password');
        }

        return ['username' => $username, 'password' => $password];
    }

    /**
     * Perform a Vault HTTP request and return the decoded JSON body.
     *
     * @param string                     $vaultAddress Base address of the Vault server
     * @param string                     $method       HTTP method
     * @param string                     $path         Request path (leading slash)
     * @param ?string                    $token        Vault token to send, or null for unauthenticated calls (login)
     * @param array<string, string>|null $jsonBody     Optional JSON request body
     *
     * @throws RuntimeException If the request fails at the transport level
     *
     * @return array<mixed> The decoded JSON response body
     */
    private function send(string $vaultAddress, string $method, string $path, ?string $token, ?array $jsonBody = null): array
    {
        $headers = ['Accept' => 'application/json'];
        if ($token !== null) {
            $headers[self::VAULT_TOKEN_HEADER] = $token;
        }

        $options = [
            'headers'     => $headers,
            'http_errors' => true,
            'timeout'     => 5,
        ];
        if ($jsonBody !== null) {
            $headers['Content-Type'] = 'application/json';
            $options['headers']      = $headers;
            $options['body']         = $this->jsonService->encode($jsonBody);
        }

        try {
            $response = $this->httpClient->request($method, rtrim($vaultAddress, '/') . $path, $options);
        } catch (GuzzleException $exception) {
            $this->logger->error('uBix Vault request failed', ['method' => $method, 'path' => $path]);
            throw new RuntimeException('uBix Vault request to `' . $path . '` failed: ' . $exception->getMessage(), 0, $exception);
        }

        return $this->jsonService->decode((string) $response->getBody());
    }
}
