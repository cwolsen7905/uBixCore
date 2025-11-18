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
    //
    //  This list was copy/pasted from PHPCoreClasses/Networking.cl on 2025-07-16
    //
    private const INTERNAL_IP_ADDRESSES = [ // NOT_IMPLEMENTED: In a perfect world these would not be a class constant but stored in a database
        '64.60.43.128/26/*', // HQ - Telepacific
        '66.209.126.80/28', // HQ - SkyRiver
        '4.35.153.192/26', // HQ - CenturyLink
        '10.1.0.0/16', // HQ - Employees & Servers
        '172.16.240.0/24', // HQ - Legacy Employees
        '172.16.244.0/24', // HQ - Legacy Servers
        '76.53.61.194/32', // HQ - New Office
        '192.168.0', // VS Media IP
        '192.168.1', // VS Media IP
        '192.168.2', // VS Media IP
        '207.178.215', // VS Media IP
        '66.52.58', // VS Media IP
        '66.42.57', // VS Media IP
        '64.60.194', // VS Media IP (OFFICE)
        '64.60.194.220', // VS Media IP (WIRELESS)
        '64.60.43', // VS Media IP (NEW OFFICE)
        '66.209.126.82', // VS Media IP (NEW OFFICE - Sky River backup)
        '172.16.250', // VS Media Private IP
        '172.16.244.1', // Office - VPN egress IP
        '204.8.234.0/24', // LA3
        '10.2.0.0/16', // LA3
        '172.16.240', // VS Media office network block as of 2018-01-08
        '10.3.0.0/16', // Prague
        '10.3.20.0/16', // PR-VPN
        '76.79.204.194/32', // Task 445324
        '185.249.113.117/32', // Task 868a6gbkt
    ];

    //
    //  This list was copy/pasted from PHPCoreClasses/Networking.cl on 2025-07-16
    //
    private const BLOCKED_IP_ADDRESSES = [ // NOT_IMPLEMENTED: why are ProspectService::BLOCKED_IP_ADDRESSES different to NetworkingService::BLOCKED_IP_ADDRESSES?
        '68.60.224.8',
        '85.110.57.111',
        '186.154.93.170',
    ];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
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
        foreach (self::BLOCKED_IP_ADDRESSES as $blockedIpAddress) {
            if (str_starts_with($ipAddress, $blockedIpAddress)) { // NOT_IMPLEMENTED: We are using str_starts_with because the previous system worked that way - it would probably be better to convert all non-ranges in self::INTERNAL_IP_ADDRESSES into ranges and not do string matching at all
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP address should be treated as internal to VSM
     *
     * @param string $ipAddress The IP address
     *
     * @return bool Whether or not the IP address is internal
     */
    public function isInternalIpAddress(string $ipAddress): bool
    {
        foreach (self::INTERNAL_IP_ADDRESSES as $internalIpAddress) {
            if (strpos($internalIpAddress, '/') !== false) { // If a slash is present check the CIDR range
                if (
                    filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    && $this->isIpv4AddressInCidrRange($ipAddress, $internalIpAddress)
                ) {
                    return true;
                }
            } elseif (str_starts_with($ipAddress, $internalIpAddress)) { // NOT_IMPLEMENTED: We are using str_starts_with because the previous system worked that way - it would probably be better to convert all non-ranges in self::BLOCKED_IP_ADDRESSES into ranges and not do string matching at all
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
     * @param string $cidr CIDR range, e.g. '204.8.234.0/24'
     *
     * @return string The first usable IP address, e.g. '204.8.234.1'
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
     * @param string $cidr CIDR range, e.g. '204.8.234.0/24'
     *
     * @throws Exception On bad input
     *
     * @return string The last usable IP address, e.g. '204.8.234.254'
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
     * @param string $cidr             The CIDR block, e.g. '204.8.234.0/24'
     * @param string $currentIpAddress The “current” IP, e.g. '204.8.234.10'
     *
     * @throws Exception If input is invalid or no next IP exists
     *
     * @return string The next usable IP, e.g. '204.8.234.11'
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
     * @param string $cidr CIDR Range, e.g. '204.8.234.0/24'
     *
     * @throws Exception On bad input
     *
     * @return string Network address, e.g. '204.8.234.0'
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
