<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to manage network information, IP addresses and CIDR ranges
 *
 * @see \Ubix\Tests\Service\NetworkingServiceTest PHPUnit test case
 */
final class NetworkingService
{
    /**
     * Constructor
     *
     * Both lists are the host's: which networks count as internal, and which addresses are
     * blocked, are operational facts about one deployment, never framework defaults. Entries
     * are CIDR ranges (`10.0.0.0/8`) or address prefixes (`192.168.1`).
     *
     * @param Logger   $logger              Logger
     * @param string[] $internalIpAddresses Networks treated as internal (default: none)
     * @param string[] $blockedIpAddresses  Addresses or prefixes that are blocked (default: none)
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private array $internalIpAddresses = [],
        private array $blockedIpAddresses = [],
    ) {
    }

    /**
     * Check if an IP address is blocked
     *
     * @param string $ipAddress The IP address
     *
     * @return bool Whether or not the IP address is blocked
     */
    public function isBlockedIpAddress(string $ipAddress): bool
    {
        foreach ($this->blockedIpAddresses as $blockedIpAddress) {
            if (str_starts_with($ipAddress, $blockedIpAddress)) { // Prefix match for non-CIDR entries
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP address should be treated as internal to the platform
     *
     * @param string $ipAddress The IP address
     *
     * @return bool Whether or not the IP address is internal
     */
    public function isInternalIpAddress(string $ipAddress): bool
    {
        foreach ($this->internalIpAddresses as $internalIpAddress) {
            if (strpos($internalIpAddress, '/') !== false) { // If a slash is present check the CIDR range
                if (
                    filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    && $this->isIpv4AddressInCidrRange($ipAddress, $internalIpAddress)
                ) {
                    return true;
                }
            } elseif (str_starts_with($ipAddress, $internalIpAddress)) { // Prefix match for non-CIDR entries
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if an IPv4 address falls within the CIDR range
     *
     * @param string $ipAddress The IP address
     * @param string $cidr      The CIDR range
     *
     * @throws Exception If the CIDR range is invalid
     *
     * @return bool Whether or not the IPv4 address falls within the CIDR range
     */
    public function isIpv4AddressInCidrRange(string $ipAddress, string $cidr): bool
    {
        if (strpos($cidr, '/') === false) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        [$network, $mask] = explode('/', $cidr);
        if (preg_match('/^[0-9]+$/', $mask)) {
            $mask = (int)$mask;
        } else {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        $networkDecimal  = ip2long($network);
        $ipDecimal       = ip2long($ipAddress);
        $wildcardDecimal = pow(2, 32 - $mask) - 1;
        $netmaskDecimal  = ~ $wildcardDecimal;
        return ($ipDecimal & $netmaskDecimal) === ($networkDecimal & $netmaskDecimal);
    }

    /**
     * Get the first usable host IP in an IPv4 CIDR block
     *
     * @param string $cidr CIDR range, e.g. '198.51.100.0/24'
     *
     * @return string The first usable IP address, e.g. '198.51.100.1'
     */
    public function getFirstUsableIpAddressInCidrRange(string $cidr): string
    {
        $network = ip2long($this->getNetworkAddress($cidr));
        // Avoid overflow for /32
        return long2ip($network + 1);
    }

    /**
     * Get the last usable host IP from an IPv4 CIDR block
     *
     * @param string $cidr CIDR range, e.g. '198.51.100.0/24'
     *
     * @throws Exception On bad input
     *
     * @return string The last usable IP address, e.g. '198.51.100.254'
     */
    public function getLastUsableIpAddressInCidrRange(string $cidr): string
    {
        if (strpos($cidr, '/') === false) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        [$network, $mask] = explode('/', $cidr, 2);
        if (preg_match('/^[0-9]+$/', $mask)) {
            $mask = (int)$mask;
        } else {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        $ipLong = ip2long($network);
        if ($ipLong === false || $mask < 0 || $mask > 32) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        // Number of host bits
        $hostBits = 32 - $mask;
        // Wildcard mask (all host bits set to 1)
        $wildcard = $hostBits === 0 ? 0 : (1 << $hostBits) - 1;

        // Compute network base
        $netmask = $mask === 0 ? 0 : ~(1 << $hostBits) - 1;
        $network = $ipLong & $netmask;
        // Compute broadcast address
        $broadcast = $network | $wildcard;

        // In a /31 or /32 there’s no “wasted” broadcast address:
        //  - /32 → only one address, we return that one.
        //  - /31 → two-address point-to-point: return the second.
        if ($mask >= 31) {
            return long2ip($broadcast);
        }

        // Otherwise, subtract 1 from broadcast to skip the broadcast address itself
        return long2ip($broadcast - 1);
    }

    /**
     * Get the next usable host IP in an IPv4 CIDR block after a given IP
     *
     * @param string $cidr             The CIDR block, e.g. '198.51.100.0/24'
     * @param string $currentIpAddress The “current” IP, e.g. '198.51.100.10'
     *
     * @throws Exception If input is invalid or no next IP exists
     *
     * @return string The next usable IP, e.g. '198.51.100.11'
     */
    public function getNextUsableIpAddressInCidrRange(string $cidr, string $currentIpAddress): string
    {
        if (strpos($cidr, '/') === false) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        [$network, $mask] = explode('/', $cidr, 2);
        if (preg_match('/^[0-9]+$/', $mask)) {
            $mask = (int)$mask;
        } else {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        $baseLong    = ip2long($network);
        $currentLong = ip2long($currentIpAddress);
        if ($baseLong === false || $currentLong === false || $mask < 0 || $mask > 32) {
            throw new Exception('The CIDR range or IP address is invalid.', ExceptionCode::CIDR_RANGE_OR_IP_ADDRESS_IS_INVALID->value);
        }

        // Compute number of host bits and masks
        $hostBits = 32 - $mask;
        $wildcard = $hostBits === 0 ? 0 : (1 << $hostBits) - 1;
        $netmask  = $mask === 0 ? 0 : ~(1 << $hostBits) - 1;

        // Network and broadcast addresses
        $network   = $baseLong & $netmask;
        $broadcast = $network | $wildcard;

        // Determine first and last usable
        if ($mask >= 31) {
            // /31: two-address p2p, both are usable; /32: one address
            $firstUsable = $network;
            $lastUsable  = $broadcast;
        } else {
            $firstUsable = $network + 1;
            $lastUsable  = $broadcast - 1;
        }

        // If current is below the first usable, return the first usable
        if ($currentLong < $firstUsable) {
            return long2ip($firstUsable);
        }

        // Compute next IP
        $next = $currentLong + 1;
        if ($next > $lastUsable) {
            throw new Exception('This is the last usable IP address and there is no next usable available.', ExceptionCode::LAST_USABLE_IP_ADDRESS_REACHED->value);
        }

        return long2ip($next);
    }

    /**
     * Get the (first) network address from an IPv4 CIDR block
     *
     * @param string $cidr CIDR Range, e.g. '198.51.100.0/24'
     *
     * @throws Exception On bad input
     *
     * @return string Network address, e.g. '198.51.100.0'
     */
    private function getNetworkAddress(string $cidr): string
    {
        if (strpos($cidr, '/') === false) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        [$network, $mask] = explode('/', $cidr, 2);
        if (preg_match('/^[0-9]+$/', $mask)) {
            $mask = (int)$mask;
        } else {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        $ipLong = ip2long($network);
        if ($ipLong === false || $mask < 0 || $mask > 32) {
            throw new Exception('The CIDR range is invalid.', ExceptionCode::CIDR_RANGE_IS_INVALID->value);
        }

        // Build the netmask: leading $mask bits set to 1
        $netmask = $mask === 0 ? 0 : ~(1 << 32 - $mask) - 1;

        $network = $ipLong & $netmask;

        return long2ip($network);
    }
}
