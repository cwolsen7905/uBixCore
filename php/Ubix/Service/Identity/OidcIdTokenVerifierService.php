<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use Firebase\JWT\CachedKeySet;
use Firebase\JWT\JWT;
use Psr\Cache\CacheItemPoolInterface as CacheItemPool;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Base64Service;
use Ubix\Service\JsonService;

/**
 * Verifies an OpenID Connect ID token before any of its claims are believed
 *
 * Shared by every OIDC provider (Google, Apple). In order:
 *
 * 1. the header's `alg` must be on the allow-list -- `none` and every `HS*` are
 *    refused before a key is even looked up, which closes the classic
 *    "sign it with the public key as an HMAC secret" confusion;
 * 2. the signature must verify against the provider's published JWKS (cached;
 *    an unknown `kid` triggers a rate-limited refresh, so key rotation works);
 * 3. `exp`/`iat`/`nbf` are checked with 60 seconds of leeway for clock skew;
 * 4. `iss` must be one of the provider's issuers, `aud` must be this app's
 *    client id (with `azp` equal to it when `aud` is a list), and `nonce` must
 *    equal the one issued for this flow -- which is what stops a token minted
 *    for another app, or for another sign-in, being replayed here.
 *
 * Requires `firebase/php-jwt` (a `suggest` dependency: a host without social
 * sign-in installs nothing).
 *
 * @see \Ubix\Tests\Service\Identity\OidcIdTokenVerifierServiceTest PHPUnit test case
 */
final class OidcIdTokenVerifierService
{
    private const ALLOWED_ALGORITHMS = ['RS256'];
    private const JWKS_CACHE_SECONDS = 3600;
    private const LEEWAY_SECONDS     = 60;

    /**
     * Constructor
     *
     * @param Logger         $logger         The Monolog logger
     * @param HttpClient     $httpClient     PSR-18 client used to fetch the provider's JWKS
     * @param RequestFactory $requestFactory PSR-17 request factory
     * @param CacheItemPool  $cachePool      PSR-6 pool the JWKS is cached in (shared across pods in production)
     * @param JsonService    $jsonService    JSON codec
     * @param Base64Service  $base64Service  Base64 codec
     */
    public function __construct(
        private Logger $logger,
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private CacheItemPool $cachePool,
        private JsonService $jsonService,
        private Base64Service $base64Service,
    ) {
    }

    /**
     * Verify an ID token and return its claims
     *
     * @param string   $idToken  The compact-serialised ID token
     * @param string   $jwksUri  The provider's JWKS URL
     * @param string[] $issuers  Acceptable `iss` values
     * @param string   $audience This app's client id
     * @param string   $nonce    The nonce issued for this flow
     *
     * @throws DtoException When any check fails; the message is safe to show and deliberately says nothing about which check (the log says which)
     *
     * @return array<string, mixed> The verified claims
     */
    public function verifyIdToken(string $idToken, string $jwksUri, array $issuers, string $audience, string $nonce): array
    {
        $result = $this->getClaimsOrRefusal($idToken, $jwksUri, $issuers, $audience, $nonce);
        if (is_string($result)) {
            throw new DtoException('That sign-in didn\'t complete. Please try again.', ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        return $result;
    }

    /**
     * Run every check, returning the claims or the (already logged) reason for refusing
     *
     * @param string   $idToken  The compact-serialised ID token
     * @param string   $jwksUri  The provider's JWKS URL
     * @param string[] $issuers  Acceptable `iss` values
     * @param string   $audience This app's client id
     * @param string   $nonce    The nonce issued for this flow
     *
     * @return array<string, mixed>|string The verified claims, or why the token was refused
     */
    private function getClaimsOrRefusal(string $idToken, string $jwksUri, array $issuers, string $audience, string $nonce): array|string
    {
        $algorithm = $this->getHeaderAlgorithm($idToken);
        if (!in_array($algorithm, self::ALLOWED_ALGORITHMS, true)) {
            return $this->refuse('algorithm not allowed', ['alg' => substr($algorithm, 0, 16)]);
        }

        if ($audience === '' || $nonce === '') {
            return $this->refuse('no audience or nonce to check against');
        }

        $keys = new CachedKeySet(
            $jwksUri,
            $this->httpClient,
            $this->requestFactory,
            $this->cachePool,
            self::JWKS_CACHE_SECONDS,
            true,
            'RS256',
        );

        $previousLeeway = JWT::$leeway;
        JWT::$leeway    = self::LEEWAY_SECONDS;

        try {
            $decoded = JWT::decode($idToken, $keys);
            $claims  = $this->jsonService->decode($this->jsonService->encode($decoded));
        } catch (Throwable $e) {
            return $this->refuse('signature or time check failed', ['reason' => $e->getMessage()]);
        } finally {
            JWT::$leeway = $previousLeeway;
        }

        if (!isset($claims['exp'])) {
            return $this->refuse('no exp claim');
        }

        if (!in_array($claims['iss'] ?? null, $issuers, true)) {
            return $this->refuse('issuer mismatch');
        }

        $tokenAudience = $claims['aud'] ?? null;
        if (is_array($tokenAudience)) {
            if (!in_array($audience, $tokenAudience, true) || ($claims['azp'] ?? null) !== $audience) {
                return $this->refuse('audience mismatch');
            }
        } elseif ($tokenAudience !== $audience) {
            return $this->refuse('audience mismatch');
        }

        $tokenNonce = $claims['nonce'] ?? null;
        if (!is_string($tokenNonce) || !hash_equals($nonce, $tokenNonce)) {
            return $this->refuse('nonce mismatch');
        }

        if (!is_string($claims['sub'] ?? null) || $claims['sub'] === '') {
            return $this->refuse('no subject');
        }

        $verified = [];
        foreach ($claims as $name => $value) {
            $verified[(string) $name] = $value;
        }

        return $verified;
    }

    /**
     * Get the `alg` from a token's header without trusting anything else in it
     *
     * @param string $idToken The compact-serialised token
     *
     * @return string The algorithm, or empty when the header is unreadable
     */
    private function getHeaderAlgorithm(string $idToken): string
    {
        $segments = explode('.', $idToken);
        if (count($segments) !== 3) {
            return '';
        }

        try {
            $header = $this->jsonService->decode($this->base64Service->decode(strtr($segments[0], '-_', '+/')));
        } catch (Throwable $e) {
            $this->logger->info('ID token header unreadable', ['reason' => $e->getMessage()]);

            return '';
        }

        return is_string($header['alg'] ?? null) ? $header['alg'] : '';
    }

    /**
     * Log why a token was refused
     *
     * @param string               $reason  Why, for the log
     * @param array<string, mixed> $context Extra log context (never the token)
     *
     * @return string The reason
     */
    private function refuse(string $reason, array $context = []): string
    {
        $this->logger->warning('ID token refused: ' . $reason, $context);

        return $reason;
    }
}
