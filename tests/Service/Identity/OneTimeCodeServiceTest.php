<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\Identity\OneTimeCodeService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\OneTimeCodeService
 *
 * @coversDefaultClass \Ubix\Service\Identity\OneTimeCodeService
 */
final class OneTimeCodeServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const string PEPPER = 'a-pepper-of-at-least-thirty-two-bytes';

    private const string PURPOSE = 'sign-in';

    private const string SUBJECT = 'reader@example.com';

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(OneTimeCodeService::class);
    }

    /**
     * A code is six digits, and every digit string is possible
     *
     * @return void
     *
     * @covers ::issue
     */
    public function testIssueMintsSixDigits(): void
    {
        $service = $this->service();

        for ($attempt = 0; $attempt < 50; $attempt++) {
            $issued = $service->issue(self::PURPOSE, self::SUBJECT);

            $this->assertMatchesRegularExpression('/^\d{6}$/', $issued->code);
        }
    }

    /**
     * The digest is a hex SHA-256 HMAC, and never the code itself
     *
     * @return void
     *
     * @covers ::issue
     */
    public function testIssueReturnsADigestThatIsNotTheCode(): void
    {
        $issued = $this->service()->issue(self::PURPOSE, self::SUBJECT);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $issued->digest);
        $this->assertStringNotContainsString($issued->code, $issued->digest);
    }

    /**
     * Two codes for the same subject differ, so a digest is not a subject identifier
     *
     * @return void
     *
     * @covers ::issue
     */
    public function testTwoIssuesForTheSameSubjectDiffer(): void
    {
        $service = $this->service();

        $digests = [];
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $digests[] = $service->issue(self::PURPOSE, self::SUBJECT)->digest;
        }

        // 20 draws from 10^6 colliding is a 1-in-5000 event; a constant digest would
        // fail every run, which is the failure worth catching.
        $this->assertGreaterThan(15, count(array_unique($digests)));
    }

    /**
     * The code that was issued verifies
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testVerifyAcceptsTheIssuedCode(): void
    {
        $service = $this->service();
        $issued  = $service->issue(self::PURPOSE, self::SUBJECT);

        $this->assertTrue($service->verify($issued->code, $issued->digest, self::PURPOSE, self::SUBJECT));
    }

    /**
     * Whitespace around a pasted code is forgiven
     *
     * A code arrives by email and is pasted; a trailing space is not a wrong code.
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testVerifyForgivesSurroundingWhitespace(): void
    {
        $service = $this->service();
        $issued  = $service->issue(self::PURPOSE, self::SUBJECT);

        $this->assertTrue($service->verify(' ' . $issued->code . "\n", $issued->digest, self::PURPOSE, self::SUBJECT));
    }

    /**
     * A code issued to one address does not verify for another
     *
     * Without this binding, a host that looks up "the newest unused code" has an
     * account-takeover bug rather than a sign-in flow.
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testACodeDoesNotVerifyForADifferentSubject(): void
    {
        $service = $this->service();
        $issued  = $service->issue(self::PURPOSE, self::SUBJECT);

        $this->assertFalse($service->verify($issued->code, $issued->digest, self::PURPOSE, 'someone.else@example.com'));
    }

    /**
     * A sign-in code cannot complete an account link
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testACodeDoesNotVerifyForADifferentPurpose(): void
    {
        $service = $this->service();
        $issued  = $service->issue(self::PURPOSE, self::SUBJECT);

        $this->assertFalse($service->verify($issued->code, $issued->digest, 'link-proof', self::SUBJECT));
    }

    /**
     * The purpose and subject cannot be run together to forge a match
     *
     * `sign` + `-in|x` must not hash the same as `sign-in` + `x`; the separator is what
     * stops it, and a host choosing its own purpose strings is what makes it matter.
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testTheBindingCannotBeSlidAcrossTheSeparator(): void
    {
        $service = $this->service();
        $issued  = $service->issue('sign', '-in|' . self::SUBJECT);

        $this->assertFalse($service->verify($issued->code, $issued->digest, 'sign-in', self::SUBJECT));
    }

    /**
     * A different pepper does not verify the same code
     *
     * Which is the point of the pepper: a leaked digest table is useless without it.
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testADigestFromAnotherPepperDoesNotVerify(): void
    {
        $issued = $this->service()->issue(self::PURPOSE, self::SUBJECT);
        $other  = $this->service('a-completely-different-pepper-of-enough-length');

        $this->assertFalse($other->verify($issued->code, $issued->digest, self::PURPOSE, self::SUBJECT));
    }

    /**
     * A wrong code is refused
     *
     * @return void
     *
     * @covers ::verify
     */
    public function testVerifyRefusesAWrongCode(): void
    {
        $service = $this->service();
        $issued  = $service->issue(self::PURPOSE, self::SUBJECT);
        $wrong   = $issued->code === '000000' ? '000001' : '000000';

        $this->assertFalse($service->verify($wrong, $issued->digest, self::PURPOSE, self::SUBJECT));
    }

    /**
     * A pepper too short to key the hash is refused, loudly
     *
     * The one misconfiguration that leaves every code working and every digest
     * brute-forceable, so it fails at the first call rather than silently.
     *
     * @return void
     *
     * @covers ::issue
     */
    public function testAShortPepperIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::ONE_TIME_CODE_PEPPER_TOO_SHORT->value);

        $this->service('too-short')->issue(self::PURPOSE, self::SUBJECT);
    }

    /**
     * A missing purpose or subject is refused rather than hashed as an empty string
     *
     * @return void
     *
     * @covers ::issue
     * @covers ::verify
     */
    public function testAnUnboundCodeIsRefused(): void
    {
        $service = $this->service();

        try {
            $service->issue('  ', self::SUBJECT);
            $this->fail('Expected an empty purpose to be refused');
        } catch (InvalidArgumentException $e) {
            $this->assertSame(ExceptionCode::ONE_TIME_CODE_BINDING_MISSING->value, $e->getCode());
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::ONE_TIME_CODE_BINDING_MISSING->value);

        $service->verify('123456', str_repeat('0', 64), self::PURPOSE, '');
    }

    /**
     * Build the service; nothing here is doubled but the logger
     *
     * @param ?string $pepper A pepper other than the test default
     *
     * @return OneTimeCodeService
     */
    private function service(?string $pepper = null): OneTimeCodeService
    {
        return new OneTimeCodeService($this->createStub(Logger::class), $pepper ?? self::PEPPER);
    }
}
