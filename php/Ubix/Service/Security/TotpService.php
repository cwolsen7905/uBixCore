<?php

declare(strict_types=1);

namespace Ubix\Service\Security;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Time-based one-time passwords (RFC 6238, over HOTP RFC 4226)
 *
 * The codes an authenticator app shows: HMAC-SHA1 over a 30-second time step,
 * six digits, with base32 secrets and `otpauth://` provisioning URIs, which
 * is what every mainstream authenticator app expects.
 *
 * Time is always passed in, never read here, so callers control the clock
 * and tests need no mocking. `verify()` returns the time step that matched so
 * a caller can store it and refuse the same code twice (RFC 6238 §5.2), which
 * this service cannot do on its own because it keeps no state.
 *
 * @see \Ubix\Tests\Service\Security\TotpServiceTest PHPUnit test case
 */
final class TotpService
{
    /**
     * Seconds per time step (RFC 6238 default)
     */
    public const int PERIOD = 30;

    /**
     * Digits per code
     */
    public const int DIGITS = 6;

    /**
     * RFC 4648 base32 alphabet
     */
    private const string BASE32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Constructor
     *
     * @param Logger $logger The Monolog logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * A new random secret, base32-encoded
     *
     * 20 bytes (160 bits) is the RFC 4226 recommendation for HMAC-SHA1.
     *
     * @param int $bytes Secret length in bytes, at least 16
     *
     * @throws InvalidArgumentException When the length is too short to be safe
     *
     * @return string The base32 secret, without padding
     */
    public function generateSecret(int $bytes = 20): string
    {
        if ($bytes < 16) {
            throw new InvalidArgumentException('A TOTP secret must be at least 16 bytes', ExceptionCode::VALIDATION_FAILED->value);
        }

        return $this->base32Encode(random_bytes($bytes));
    }

    /**
     * The time step a Unix timestamp falls in
     *
     * @param int $unixTime Seconds since the epoch
     *
     * @return int The step counter
     */
    public function stepAt(int $unixTime): int
    {
        return intdiv($unixTime, self::PERIOD);
    }

    /**
     * The code for a secret at a time step
     *
     * @param string $secret The base32 secret
     * @param int    $step   The time step
     * @param int    $digits How many digits (6 for apps; 8 for the RFC test vectors)
     *
     * @return string The zero-padded code
     */
    public function codeAt(string $secret, int $step, int $digits = self::DIGITS): string
    {
        $key  = $this->base32Decode($secret);
        $hmac = hash_hmac('sha1', pack('J', $step), $key, true);

        // RFC 4226 §5.4 dynamic truncation.
        $offset = ord($hmac[19]) & 0x0f;
        $binary = ((ord($hmac[$offset]) & 0x7f) << 24) | (ord($hmac[$offset + 1]) << 16) | (ord($hmac[$offset + 2]) << 8) | ord($hmac[$offset + 3]);

        return str_pad((string) ($binary % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Check a code, allowing one step of clock drift either side
     *
     * @param string $secret       The base32 secret
     * @param string $code         What the user typed; spaces are ignored
     * @param int    $unixTime     The current time
     * @param ?int   $lastUsedStep The step of the last code this user redeemed, to refuse a replay
     *
     * @return ?int The matched step, which the caller should store as the new last-used step; null when the code is wrong or replayed
     */
    public function verify(string $secret, string $code, int $unixTime, ?int $lastUsedStep = null): ?int
    {
        $code = str_replace(' ', '', $code);
        if (preg_match('/^\d{' . self::DIGITS . '}$/', $code) !== 1) {
            return null;
        }

        $current = $this->stepAt($unixTime);

        foreach ([$current, $current - 1, $current + 1] as $step) {
            // A code at or before the last one redeemed is a replay, however
            // valid it would otherwise be.
            if ($lastUsedStep !== null && $step <= $lastUsedStep) {
                continue;
            }

            if (hash_equals($this->codeAt($secret, $step), $code)) {
                return $step;
            }
        }

        return null;
    }

    /**
     * The `otpauth://` URI an authenticator app scans as a QR code
     *
     * @param string $secret  The base32 secret
     * @param string $issuer  The service name the app shows
     * @param string $account The account label, usually an email
     *
     * @return string The provisioning URI
     */
    public function provisioningUri(string $secret, string $issuer, string $account): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account) . '?' . http_build_query([
            'algorithm' => 'SHA1',
            'digits'    => self::DIGITS,
            'issuer'    => $issuer,
            'period'    => self::PERIOD,
            'secret'    => $secret,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * RFC 4648 base32, no padding
     *
     * @param string $bytes Raw bytes
     *
     * @return string The encoding
     */
    private function base32Encode(string $bytes): string
    {
        $bits = '';
        foreach (str_split($bytes) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= substr(self::BASE32, (int) bindec(str_pad($chunk, 5, '0')), 1);
        }

        return $out;
    }

    /**
     * RFC 4648 base32 decode, case- and padding-insensitive
     *
     * @param string $encoded The encoding
     *
     * @throws InvalidArgumentException When it contains a character outside the alphabet
     *
     * @return string Raw bytes
     */
    private function base32Decode(string $encoded): string
    {
        $encoded = strtoupper(rtrim(str_replace(' ', '', $encoded), '='));
        if ($encoded === '' || strspn($encoded, self::BASE32) !== strlen($encoded)) {
            throw new InvalidArgumentException('A TOTP secret must be base32', ExceptionCode::VALIDATION_FAILED->value);
        }

        $bits = '';
        foreach (str_split($encoded) as $char) {
            $bits .= str_pad(decbin((int) strpos(self::BASE32, $char)), 5, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr((int) bindec($byte) & 0xff);
            }
        }

        return $out;
    }
}
