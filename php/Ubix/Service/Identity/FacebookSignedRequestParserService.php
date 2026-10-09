<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\DataTransferObject\Identity\FacebookSignedRequest;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Base64Service;
use Ubix\Service\JsonService;

/**
 * Verifies a Meta `signed_request` -- the body of the data-deletion and deauthorize callbacks
 *
 * Meta refuses App Review for Facebook Login without a data-deletion callback
 * (or instructions URL). The callback is a POST whose `signed_request` field is
 * `<signature>.<payload>`, both base64url: the signature is HMAC-SHA256 over the
 * still-encoded payload, keyed with the app secret. This class checks that
 * signature in constant time, refuses any algorithm but `HMAC-SHA256`, and only
 * then reads the payload.
 *
 * | Env var               | Meaning           | Source |
 * |-----------------------|-------------------|--------|
 * | `FACEBOOK_APP_SECRET` | The Meta app secret | uBixVault in every deployed environment; `.env` only for local dev |
 *
 * The same secret as {@see FacebookIdentityProviderService}. With none, every
 * call fails closed. What a host does with the user id (delete, anonymise,
 * record a confirmation code for Meta's status URL) is its own policy.
 *
 * @see \Ubix\Tests\Service\Identity\FacebookSignedRequestParserServiceTest PHPUnit test case
 */
final class FacebookSignedRequestParserService
{
    private const ALGORITHM = 'HMAC-SHA256';
    private const INVALID   = 'That request could not be verified.';

    /**
     * Constructor
     *
     * @param Logger        $logger        The Monolog logger
     * @param JsonService   $jsonService   JSON codec
     * @param Base64Service $base64Service Base64 codec
     * @param string        $appSecret     Meta app secret, or empty to read `FACEBOOK_APP_SECRET`
     */
    public function __construct(
        private Logger $logger,
        private JsonService $jsonService,
        private Base64Service $base64Service,
        private string $appSecret = '',
    ) {
    }

    /**
     * Verify a `signed_request` and return what it says
     *
     * @param string $signedRequest The `signed_request` form field, as posted
     *
     * @return FacebookSignedRequest The verified request
     *
     * @throws DtoException When the secret is missing, or the request is malformed, wrongly signed, uses another algorithm or names no user
     */
    public function parse(string $signedRequest): FacebookSignedRequest
    {
        $appSecret = $this->getAppSecret();

        $parts = explode('.', $signedRequest);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            $this->logRefusal('malformed');

            throw new DtoException(self::INVALID, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        [$encodedSignature, $encodedPayload] = $parts;

        $signature = $this->base64UrlDecode($encodedSignature);
        if ($signature === '' || !hash_equals(hash_hmac('sha256', $encodedPayload, $appSecret, true), $signature)) {
            $this->logRefusal('bad signature');

            throw new DtoException(self::INVALID, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        try {
            $payload = $this->jsonService->decode($this->base64UrlDecode($encodedPayload));
        } catch (Throwable $e) {
            $this->logger->info('Facebook signed_request payload was not JSON', ['reason' => $e->getMessage()]);
            $payload = [];
        }

        if (($payload['algorithm'] ?? null) !== self::ALGORITHM) {
            $this->logRefusal('unsupported algorithm');

            throw new DtoException(self::INVALID, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        $userId = is_scalar($payload['user_id'] ?? null) ? (string) $payload['user_id'] : '';
        if ($userId === '') {
            $this->logRefusal('no user id');

            throw new DtoException(self::INVALID, ExceptionCode::IDENTITY_TOKEN_INVALID->value);
        }

        return new FacebookSignedRequest(
            userId:                $userId,
            issuedAtUnixTimestamp: is_int($payload['issued_at'] ?? null) ? $payload['issued_at'] : 0,
        );
    }

    /**
     * Log why a request is refused, without logging any of it
     *
     * @param string $reason Why
     *
     * @return void
     */
    private function logRefusal(string $reason): void
    {
        $this->logger->warning('Facebook signed_request refused', ['reason' => $reason]);
    }

    /**
     * Strictly decode base64url (padding optional)
     *
     * @param string $encoded The encoding
     *
     * @return string The raw bytes, or empty when the input is not base64url
     */
    private function base64UrlDecode(string $encoded): string
    {
        if (preg_match('/^[A-Za-z0-9_-]+={0,2}$/', $encoded) !== 1) {
            return '';
        }

        try {
            return $this->base64Service->decode(strtr($encoded, '-_', '+/'));
        } catch (Throwable $e) {
            $this->logger->info('Facebook signed_request part was not base64url', ['reason' => $e->getMessage()]);

            return '';
        }
    }

    /**
     * Get the app secret, failing closed when none is configured
     *
     * @return string The app secret
     *
     * @throws DtoException When none is configured
     */
    private function getAppSecret(): string
    {
        $appSecret = $this->appSecret !== '' ? $this->appSecret : (string) getenv('FACEBOOK_APP_SECRET');
        if ($appSecret === '') {
            throw new DtoException('Facebook is not configured.', ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        }

        return $appSecret;
    }
}
