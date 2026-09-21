<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\NullLogger;
use Ubix\Service\Identity\ReturnToValidatorService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\ReturnToValidatorService
 *
 * @coversDefaultClass \Ubix\Service\Identity\ReturnToValidatorService
 */
final class ReturnToValidatorServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ReturnToValidatorService::class);
    }

    /**
     * Test which return addresses are allowed through
     *
     * @param string $candidate The requested return address
     * @param bool   $allowed   Whether it should be allowed through
     *
     * @return void
     */
    #[DataProvider('candidates')]
    public function testReturnAddresses(string $candidate, bool $allowed): void
    {
        $validator = new ReturnToValidatorService(new NullLogger(), ['https://app.example.com'], '/home');

        $this->assertSame($allowed ? $candidate : '/home', $validator->getSafeReturnTo($candidate));
    }

    /**
     * Return addresses and whether each is safe
     *
     * @return array<string, array{string, bool}> Cases
     */
    public static function candidates(): array
    {
        return [
            'allowed host, other port'   => ['https://app.example.com:8443/', false],
            'allowed host, other scheme' => ['http://app.example.com/', false],
            'allowed origin'             => ['https://app.example.com/c/grace', true],
            'allowed origin, other case' => ['HTTPS://APP.EXAMPLE.COM/x', true],
            'backslash trick'            => ['/\\evil.example', false],
            'empty'                      => ['', false],
            'javascript URL'             => ['javascript:alert(1)', false],
            'lookalike subdomain'        => ['https://app.example.com.evil.example/', false],
            'newline'                    => ["/ok\nLocation: https://evil.example", false],
            'other origin'               => ['https://evil.example/x', false],
            'relative without slash'     => ['evil.example', false],
            'same-origin path'           => ['/creator/onboarding?step=2', true],
            'scheme-relative'            => ['//evil.example/x', false],
            'userinfo'                   => ['https://app.example.com@evil.example/', false],
        ];
    }
}
