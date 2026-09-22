<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use Firebase\JWT\JWT;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use OpenSSLAsymmetricKey;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Ubix\Service\Base64Service;
use Ubix\Service\Identity\OidcIdTokenVerifierService;
use Ubix\Service\JsonService;

/**
 * Shared fixture for the identity-seam tests: a local RSA signing key, the JWKS
 * that publishes it, and a PSR-18 client that answers from a route table and
 * records what it was sent. Nothing here touches the network.
 */
final class IdentityFixture implements HttpClient
{
    public const KEY_ID = 'test-key-1';

    private OpenSSLAsymmetricKey $privateKey;

    /**
     * Responses by URL prefix
     *
     * @var array<string, Response>
     */
    private array $routes = [];

    /**
     * Every request sent, in order
     *
     * @var Request[]
     */
    private array $sent = [];

    /**
     * Constructor: generate a fresh signing key
     *
     * @throws RuntimeException When OpenSSL cannot generate a key
     */
    public function __construct()
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if ($key === false) {
            throw new RuntimeException('Could not generate a test RSA key');
        }

        $this->privateKey = $key;
    }

    /**
     * Answer requests whose URL starts with a prefix
     *
     * @param string $prefix URL prefix
     * @param int    $status HTTP status
     * @param string $body   Response body
     *
     * @return void
     */
    public function addRoute(string $prefix, int $status, string $body): void
    {
        $this->routes[$prefix] = new Psr7Response($status, ['Content-Type' => 'application/json'], $body);
    }

    /**
     * Get every request sent so far
     *
     * @return Request[] The requests
     */
    public function getSentRequests(): array
    {
        return $this->sent;
    }

    /**
     * Get the JWKS document publishing the fixture's public key
     *
     * @throws RuntimeException When the key cannot be read
     *
     * @return string JSON
     */
    public function getJwks(): string
    {
        $details = openssl_pkey_get_details($this->privateKey);
        $rsa     = is_array($details) && is_array($details['rsa'] ?? null) ? $details['rsa'] : [];
        if (!is_string($rsa['n'] ?? null) || !is_string($rsa['e'] ?? null)) {
            throw new RuntimeException('Could not read the test RSA key');
        }

        return $this->getJsonService()->encode(['keys' => [
            [
                'alg' => 'RS256',
                'e'   => $this->base64Url($rsa['e']),
                'kid' => self::KEY_ID,
                'kty' => 'RSA',
                'n'   => $this->base64Url($rsa['n']),
                'use' => 'sig',
            ],
        ],
        ]);
    }

    /**
     * Sign claims as an RS256 ID token with the fixture key
     *
     * @param array<string, mixed> $claims The claims
     *
     * @return string The token
     */
    public function sign(array $claims): string
    {
        $pem = '';
        openssl_pkey_export($this->privateKey, $pem);

        return JWT::encode($claims, is_string($pem) ? $pem : '', 'RS256', self::KEY_ID);
    }

    /**
     * Get a JSON codec
     *
     * @return JsonService The codec
     */
    public function getJsonService(): JsonService
    {
        return new JsonService(new NullLogger());
    }

    /**
     * Build a verifier wired to this fixture's client and a fresh in-memory cache
     *
     * @return OidcIdTokenVerifierService The verifier
     */
    public function getVerifier(): OidcIdTokenVerifierService
    {
        $logger = new NullLogger();

        return new OidcIdTokenVerifierService(
            $logger,
            $this,
            new Psr17Factory(),
            new ArrayAdapter(),
            new JsonService($logger),
            new Base64Service($logger),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sendRequest(Request $request): Response
    {
        $this->sent[] = $request;

        $url = (string) $request->getUri();
        foreach ($this->routes as $prefix => $response) {
            if (str_starts_with($url, $prefix)) {
                $response->getBody()->rewind();

                return $response;
            }
        }

        return new Psr7Response(404, [], 'no route for ' . $url);
    }

    /**
     * Base64url-encode without padding
     *
     * @param string $bytes Raw bytes
     *
     * @return string The encoding
     */
    private function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
