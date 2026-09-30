<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Identity\OneTimeCode;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Issues and checks short numeric codes emailed to prove control of an address
 *
 * The code a passwordless sign-in mails out, and the same mechanism for proving an
 * existing account before a provider is linked to it. Two properties carry the whole
 * design, and both are easy to get wrong in a way nothing visibly fails on.
 *
 * **A six-digit code is not a secret you may hash and store.** The keyspace is 10^6,
 * so a stolen table of SHA-256 digests is reversed exhaustively in milliseconds on a
 * laptop. The digest here is an HMAC keyed with a server-side **pepper** the database
 * never sees, which is what makes a leaked table useless on its own.
 *
 * **A code is bound to what it is for and who it is for.** `purpose` and `subjectKey`
 * go into the HMAC input, so a code mailed to one address cannot verify another, and a
 * code issued to sign in cannot complete an account link. Without that binding a host
 * with two code flows has one flow, whatever its tables say.
 *
 * **What this deliberately does not do:** expiry, single use, attempt caps and rate
 * limits. Those need storage and a clock the host already has, and they are where the
 * real strength is — at 10^6, a code is worth exactly as much as the cap on guesses
 * against it. `verify()` answering true says only "this is the code that was issued
 * for this purpose and this subject", never "and it is still valid".
 *
 * @see \Ubix\Tests\Service\Identity\OneTimeCodeServiceTest PHPUnit test case
 */
final class OneTimeCodeService
{
    /**
     * How many digits a code has
     *
     * Six, because it is typed by hand from a phone, often twice. The keyspace this
     * costs is answered by the host's attempt cap rather than by more digits.
     */
    private const int CODE_DIGITS = 6;

    /**
     * The shortest pepper this will accept
     *
     * 32 bytes, the output size of the hash it keys. A short pepper is the one failure
     * mode that leaves everything working and the digests brute-forceable anyway.
     */
    private const int PEPPER_MINIMUM_BYTES = 32;

    /**
     * Constructor
     *
     * @param Logger $logger The Monolog logger
     * @param string $pepper A server-side secret from the host's secret store, never the database; at least 32 bytes
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private string $pepper,
    ) {
    }

    /**
     * Mint a code for one purpose and one subject
     *
     * The plaintext is returned once, to be sent to the subject and then forgotten; only
     * the digest is for storing. Nothing here logs either. An empty purpose or subject, or
     * a pepper shorter than the hash it keys, is refused by {@see guard()}.
     *
     * @param string $purpose    What the code is for (`sign-in`, `link-proof`); part of the digest, so codes cannot cross flows
     * @param string $subjectKey Who it is for — normally a normalised email address
     *
     * @return OneTimeCode The plaintext to send and the digest to store
     */
    public function issue(string $purpose, string $subjectKey): OneTimeCode
    {
        $this->guard($purpose, $subjectKey);

        // `random_int` and not `rand`: this is a credential. Zero-padded rather than
        // ranged from 100000, so every one of the 10^6 codes is equally likely and
        // "000123" is a code like any other.
        $code = str_pad((string) random_int(0, 10 ** self::CODE_DIGITS - 1), self::CODE_DIGITS, '0', STR_PAD_LEFT);

        return new OneTimeCode($code, $this->digest($code, $purpose, $subjectKey));
    }

    /**
     * Whether a presented code is the one issued for this purpose and subject
     *
     * Says nothing about expiry, single use or attempt counts — see the class docblock.
     * Refuses an unbound call or a short pepper through {@see guard()}, as `issue()` does.
     *
     * @param string $presented  What the user typed
     * @param string $digest     The stored digest from {@see issue()}
     * @param string $purpose    The same purpose the code was issued for
     * @param string $subjectKey The same subject the code was issued for
     *
     * @return bool
     */
    public function verify(string $presented, string $digest, string $purpose, string $subjectKey): bool
    {
        $this->guard($purpose, $subjectKey);

        // Constant-time, so the comparison cannot be turned into an oracle that leaks
        // the digest a character at a time.
        return hash_equals($digest, $this->digest(trim($presented), $purpose, $subjectKey));
    }

    /**
     * The stored form of a code
     *
     * @param string $code       The plaintext code
     * @param string $purpose    What the code is for
     * @param string $subjectKey Who it is for
     *
     * @return string A 64-character hexadecimal digest
     */
    private function digest(string $code, string $purpose, string $subjectKey): string
    {
        // The separator matters: without it, purpose "sign" + subject "-in|x" and purpose
        // "sign-in" + subject "x" would hash identically. A vertical bar cannot appear in
        // a purpose this library defines, and a host that puts one in a subject key still
        // gets a distinct string because the purpose is fixed-position.
        return hash_hmac('sha256', $purpose . '|' . $subjectKey . '|' . $code, $this->pepper);
    }

    /**
     * Refuse a call that cannot be safe
     *
     * @param string $purpose    What the code is for
     * @param string $subjectKey Who it is for
     *
     * @throws InvalidArgumentException If the purpose or subject is empty, or the pepper is too short
     *
     * @return void
     */
    private function guard(string $purpose, string $subjectKey): void
    {
        if (strlen($this->pepper) < self::PEPPER_MINIMUM_BYTES) {
            throw new InvalidArgumentException(
                'The one-time-code pepper must be at least ' . self::PEPPER_MINIMUM_BYTES . ' bytes',
                ExceptionCode::ONE_TIME_CODE_PEPPER_TOO_SHORT->value,
            );
        }

        if (trim($purpose) === '' || trim($subjectKey) === '') {
            throw new InvalidArgumentException(
                'A one-time code needs both a purpose and a subject key',
                ExceptionCode::ONE_TIME_CODE_BINDING_MISSING->value,
            );
        }
    }
}
