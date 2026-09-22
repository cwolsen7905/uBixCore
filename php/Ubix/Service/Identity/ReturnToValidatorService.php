<?php

declare(strict_types=1);

namespace Ubix\Service\Identity;

use Psr\Log\LoggerInterface as Logger;

/**
 * Decides whether a post-sign-in return address is safe to redirect to
 *
 * A sign-in callback that redirects wherever a `returnTo` parameter says is an
 * open redirect with a login page in front of it -- the most convincing phishing
 * link there is. This accepts only a same-origin path or an absolute URL whose
 * origin is on the host's allow-list (typically the same list its CORS
 * middleware uses), and answers anything else with the host's fallback.
 *
 * @see \Ubix\Tests\Service\Identity\ReturnToValidatorServiceTest PHPUnit test case
 */
final class ReturnToValidatorService
{
    /**
     * Constructor
     *
     * @param Logger   $logger         The Monolog logger
     * @param string[] $allowedOrigins Origins (`https://app.example.com`, no path, no trailing slash) an absolute return address may point at
     * @param string   $fallback       Where to go when the candidate is not acceptable (a same-origin path or an allowed absolute URL)
     */
    public function __construct(
        private Logger $logger,
        private array $allowedOrigins = [],
        private string $fallback = '/',
    ) {
    }

    /**
     * Get a return address that is safe to redirect to
     *
     * @param string $candidate The requested return address
     *
     * @return string The candidate when acceptable, otherwise the fallback
     */
    public function getSafeReturnTo(string $candidate): string
    {
        if ($this->isSafe($candidate)) {
            return $candidate;
        }

        if ($candidate !== '') {
            $this->logger->info('Refused a sign-in return address', ['candidate' => substr($candidate, 0, 100)]);
        }

        return $this->fallback;
    }

    /**
     * Check whether a return address is acceptable
     *
     * @param string $candidate The requested return address
     *
     * @return bool True when it is a plain same-origin path or an allowed absolute URL
     */
    private function isSafe(string $candidate): bool
    {
        // Control characters and backslashes: browsers normalise `\` to `/`, so
        // `/\evil.example` is really `//evil.example`.
        if ($candidate === '' || preg_match('/[\x00-\x1f\x7f\\\\]/', $candidate) === 1) {
            return false;
        }

        // A same-origin path: one leading slash, never two (`//host` is scheme-relative).
        if (str_starts_with($candidate, '/')) {
            return !str_starts_with($candidate, '//');
        }

        $parts = parse_url($candidate);
        if ($parts === false || !isset($parts['scheme'], $parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        $origin = strtolower($parts['scheme']) . '://' . strtolower($parts['host']) . (isset($parts['port']) ? ':' . $parts['port'] : '');

        return in_array($origin, array_map('strtolower', $this->allowedOrigins), true);
    }
}
