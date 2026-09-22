<?php

declare(strict_types=1);

namespace Ubix\Service\Realtime;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use SensitiveParameter;
use Ubix\DataTransferObject\Realtime\SubjectGrant;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Base64Service;
use Ubix\Service\JsonService;

/**
 * Signs and verifies short-lived real-time subject grants
 *
 * A host decides who may see what, then hands the browser a grant naming the
 * subjects it may subscribe to. A gateway that holds the browser's socket
 * verifies the grant with the same key and subscribes; it has no database and
 * makes no access decision of its own.
 *
 * The token is language-neutral so a gateway in any language can verify it:
 *
 *     v1.<payload>.<signature>
 *
 *     payload   = base64url(JSON {"exp": int, "iat": int, "sub": string, "subjects": [string]})
 *     signature = base64url(HMAC-SHA256(key, "v1." + payload))
 *
 * base64url is RFC 4648 §5 without padding. Verification recomputes the
 * signature over the received `v1.<payload>` bytes, compares in constant time,
 * and only then decodes the payload, so the JSON's key order never matters.
 * Subject patterns are dot-separated tokens of `[A-Za-z0-9_-]` or `*`, and a
 * final `>` token matches everything after it (NATS wildcard semantics).
 *
 * Time is always passed in, so callers own the clock and tests need no mocks.
 *
 * @see \Ubix\Tests\Service\Realtime\SubjectGrantServiceTest PHPUnit test case
 */
final class SubjectGrantService
{
    /**
     * Token format version, the first dot-separated part
     */
    public const string VERSION = 'v1';

    /**
     * Shortest signing key accepted, in bytes (the HMAC-SHA256 output size)
     */
    public const int MINIMUM_KEY_BYTES = 32;

    /**
     * One subject pattern
     */
    private const string PATTERN = '/^(?:[A-Za-z0-9_-]+|\*)(?:\.(?:[A-Za-z0-9_-]+|\*))*(?:\.>)?$|^>$/';

    /**
     * Constructor
     *
     * @param Logger        $logger        The Monolog logger
     * @param JsonService   $jsonService   JSON encoding
     * @param Base64Service $base64Service Base64 decoding
     * @param string        $signingKey    Shared with the gateway; at least 32 bytes
     *
     * @throws InvalidArgumentException When the key is too short
     */
    public function __construct(
        private Logger $logger,
        private JsonService $jsonService,
        private Base64Service $base64Service,
        #[SensitiveParameter]
        private string $signingKey,
    ) {
        if (strlen($this->signingKey) < self::MINIMUM_KEY_BYTES) {
            throw new InvalidArgumentException('A subject-grant signing key must be at least ' . self::MINIMUM_KEY_BYTES . ' bytes', ExceptionCode::REALTIME_GRANT_INVALID->value);
        }
    }

    /**
     * Sign a grant
     *
     * @param string             $holder     Who it is for (the host's user id)
     * @param array<int, string> $subjects   Subject patterns the holder may subscribe to
     * @param int                $issuedAt   The current Unix time
     * @param int                $ttlSeconds How long it is valid; keep it short, it cannot be revoked
     *
     * @throws InvalidArgumentException When a subject pattern is malformed, none are given, or the TTL is not positive
     *
     * @return string The token
     */
    public function issue(string $holder, array $subjects, int $issuedAt, int $ttlSeconds): string
    {
        if ($subjects === []) {
            throw new InvalidArgumentException('A subject grant must name at least one subject', ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        foreach ($subjects as $subject) {
            if (preg_match(self::PATTERN, $subject) !== 1) {
                throw new InvalidArgumentException('Not a valid subject pattern: ' . $subject, ExceptionCode::REALTIME_GRANT_INVALID->value);
            }
        }

        if ($ttlSeconds <= 0) {
            throw new InvalidArgumentException('A subject grant needs a positive lifetime', ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        $payload = $this->base64UrlEncode($this->jsonService->encode([
            'exp'      => $issuedAt + $ttlSeconds,
            'iat'      => $issuedAt,
            'sub'      => $holder,
            'subjects' => array_values($subjects),
        ]));

        $signed = self::VERSION . '.' . $payload;

        return $signed . '.' . $this->sign($signed);
    }

    /**
     * Verify a grant
     *
     * @param string $token    The token
     * @param int    $unixTime The current Unix time
     *
     * @throws InvalidArgumentException When the token is malformed, forged, from another key, or expired
     *
     * @return SubjectGrant The verified grant
     */
    public function verify(string $token, int $unixTime): SubjectGrant
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3 || $parts[0] !== self::VERSION) {
            throw new InvalidArgumentException($this->refusal('malformed'), ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        [$version, $payload, $signature] = $parts;

        if (!hash_equals($this->sign($version . '.' . $payload), $signature)) {
            throw new InvalidArgumentException($this->refusal('bad signature'), ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        $json = $this->base64UrlDecode($payload);

        try {
            $claims = $this->jsonService->decode($json);
        } catch (DtoException $e) {
            throw new InvalidArgumentException($this->refusal('payload is not JSON'), ExceptionCode::REALTIME_GRANT_INVALID->value, $e);
        }

        if (
            !is_string($claims['sub'] ?? null)
            || !is_int($claims['iat'] ?? null)
            || !is_int($claims['exp'] ?? null)
            || !is_array($claims['subjects'] ?? null)
            || !array_is_list($claims['subjects'])
        ) {
            throw new InvalidArgumentException($this->refusal('payload is missing a claim'), ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        $subjects = [];
        foreach ($claims['subjects'] as $subject) {
            if (!is_string($subject) || preg_match(self::PATTERN, $subject) !== 1) {
                throw new InvalidArgumentException($this->refusal('payload holds a malformed subject'), ExceptionCode::REALTIME_GRANT_INVALID->value);
            }

            $subjects[] = $subject;
        }

        if ($unixTime >= $claims['exp']) {
            throw new InvalidArgumentException($this->refusal('expired'), ExceptionCode::REALTIME_GRANT_INVALID->value);
        }

        return new SubjectGrant($claims['sub'], $subjects, $claims['iat'], $claims['exp']);
    }

    /**
     * The base64url HMAC-SHA256 of a string
     *
     * @param string $data What to sign
     *
     * @return string The signature
     */
    private function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->signingKey, true));
    }

    /**
     * Log a refusal (never the token itself) and return the exception message
     *
     * @param string $reason Why
     *
     * @return string The message
     */
    private function refusal(string $reason): string
    {
        $this->logger->info('Refused a real-time subject grant', ['reason' => $reason]);

        return 'Invalid subject grant: ' . $reason;
    }

    /**
     * RFC 4648 §5 base64url, no padding
     *
     * @param string $bytes Raw bytes
     *
     * @return string The encoding
     */
    private function base64UrlEncode(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * RFC 4648 §5 base64url decode
     *
     * @param string $encoded The encoding, padded or not
     *
     * @throws InvalidArgumentException When it is not base64url
     *
     * @return string Raw bytes
     */
    private function base64UrlDecode(string $encoded): string
    {
        try {
            return $this->base64Service->decode(strtr($encoded, '-_', '+/'));
        } catch (Exception $e) {
            throw new InvalidArgumentException($this->refusal('payload is not base64url'), ExceptionCode::REALTIME_GRANT_INVALID->value, $e);
        }
    }
}
