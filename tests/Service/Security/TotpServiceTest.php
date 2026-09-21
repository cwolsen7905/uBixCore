<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Security;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Security\TotpService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Security\TotpService
 *
 * @coversDefaultClass \Ubix\Service\Security\TotpService
 */
final class TotpServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * The RFC 6238 SHA1 secret, "12345678901234567890", in base32
     */
    private const string RFC_SECRET = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TotpService::class);
    }

    /**
     * RFC 6238 Appendix B, SHA1, eight digits
     *
     * @return void
     */
    public function testTheRfc6238ReferenceVectors(): void
    {
        $service = $this->service();

        $vectors = [
            59          => '94287082',
            1111111109  => '07081804',
            1111111111  => '14050471',
            1234567890  => '89005924',
            2000000000  => '69279037',
            20000000000 => '65353130',
        ];

        foreach ($vectors as $time => $expected) {
            $this->assertSame($expected, $service->codeAt(self::RFC_SECRET, $service->stepAt($time), 8), 'T=' . $time);
        }
    }

    /**
     * A current code verifies, one step of drift either side is tolerated, two is not
     *
     * @return void
     */
    public function testVerificationToleratesOneStepOfDrift(): void
    {
        $service = $this->service();
        $now     = 1758300000;
        $step    = $service->stepAt($now);

        $this->assertSame($step, $service->verify(self::RFC_SECRET, $service->codeAt(self::RFC_SECRET, $step), $now));
        $this->assertSame($step - 1, $service->verify(self::RFC_SECRET, $service->codeAt(self::RFC_SECRET, $step - 1), $now));
        $this->assertNull($service->verify(self::RFC_SECRET, $service->codeAt(self::RFC_SECRET, $step - 2), $now));
    }

    /**
     * A code already redeemed is refused, even inside its window
     *
     * @return void
     */
    public function testAReplayedCodeIsRefused(): void
    {
        $service = $this->service();
        $now     = 1758300000;
        $step    = $service->stepAt($now);
        $code    = $service->codeAt(self::RFC_SECRET, $step);

        $this->assertSame($step, $service->verify(self::RFC_SECRET, $code, $now));
        $this->assertNull($service->verify(self::RFC_SECRET, $code, $now, $step));
    }

    /**
     * Malformed input is refused rather than compared
     *
     * @return void
     */
    public function testMalformedCodesAreRefused(): void
    {
        $service = $this->service();

        foreach (['', '12345', '1234567', 'abcdef', '12 34 5x'] as $code) {
            $this->assertNull($service->verify(self::RFC_SECRET, $code, 1758300000), $code);
        }
    }

    /**
     * A generated secret round-trips through base32 and produces codes; a short one is refused
     *
     * @return void
     */
    public function testGeneratedSecretsWorkAndShortOnesAreRefused(): void
    {
        $service = $this->service();
        $secret  = $service->generateSecret();

        $this->assertSame(32, strlen($secret));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $service->codeAt($secret, 1));

        $this->expectException(InvalidArgumentException::class);
        $service->generateSecret(10);
    }

    /**
     * The provisioning URI carries what authenticator apps read
     *
     * @return void
     */
    public function testTheProvisioningUri(): void
    {
        $uri = $this->service()->provisioningUri('JBSWY3DPEHPK3PXP', 'Example App', 'ops@example.com');

        $this->assertStringStartsWith('otpauth://totp/Example%20App%3Aops%40example.com?', $uri);
        $this->assertStringContainsString('secret=JBSWY3DPEHPK3PXP', $uri);
        $this->assertStringContainsString('issuer=Example%20App', $uri);
        $this->assertStringContainsString('digits=6', $uri);
    }

    /**
     * The service under test
     *
     * @return TotpService The service
     */
    private function service(): TotpService
    {
        return new TotpService($this->createStub(Logger::class));
    }
}
