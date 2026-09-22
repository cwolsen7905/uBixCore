<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Realtime;

use InvalidArgumentException;
use Psr\Log\NullLogger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\Base64Service;
use Ubix\Service\JsonService;
use Ubix\Service\Realtime\SubjectGrantService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Realtime\SubjectGrantService
 *
 * @coversDefaultClass \Ubix\Service\Realtime\SubjectGrantService
 */
final class SubjectGrantServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const string KEY = '0123456789abcdef0123456789abcdef';

    private const int T0 = 1800000000;

    /**
     * The token for KEY, holder "42", subject "app.user.42.>", T0, 60 s -- computed
     * independently (Python hmac/base64) so the format is pinned, not just
     * self-consistent. A gateway in another language verifies the same vector.
     */
    private const string VECTOR = 'v1.eyJleHAiOjE4MDAwMDAwNjAsImlhdCI6MTgwMDAwMDAwMCwic3ViIjoiNDIiLCJzdWJqZWN0cyI6WyJhcHAudXNlci40Mi4-Il19.SMPufMbZ-mT5p-2-jvZWasL_ff0Aw8Nviz9Zo069Z7I';

    private SubjectGrantService $grants;

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SubjectGrantService::class);
    }

    /**
     * Test that issuing produces exactly the published wire format
     *
     * @return void
     */
    public function testIssueMatchesTheFixedVector(): void
    {
        $this->assertSame(self::VECTOR, $this->grants->issue('42', ['app.user.42.>'], self::T0, 60));
    }

    /**
     * Test that a grant verifies into its claims until it expires
     *
     * @return void
     */
    public function testVerifyReturnsTheClaimsUntilExpiry(): void
    {
        $grant = $this->grants->verify(self::VECTOR, self::T0 + 59);

        $this->assertSame('42', $grant->holder);
        $this->assertSame(['app.user.42.>'], $grant->subjects);
        $this->assertSame(self::T0, $grant->issuedAt);
        $this->assertSame(self::T0 + 60, $grant->expiresAt);

        $this->assertRefused(self::VECTOR, self::T0 + 60, 'expired');
    }

    /**
     * Test that a changed payload, a changed signature, another key and junk are all refused
     *
     * @return void
     */
    public function testRefusesAnythingNotSignedWithThisKey(): void
    {
        [$version, $payload, $signature] = explode('.', self::VECTOR);

        $forgedPayload = rtrim(strtr(base64_encode('{"sub":"1","subjects":[">"],"iat":1800000000,"exp":1900000000}'), '+/', '-_'), '=');
        $this->assertRefused($version . '.' . $forgedPayload . '.' . $signature, self::T0, 'bad signature');
        $this->assertRefused($version . '.' . $payload . '.' . strrev($signature), self::T0, 'bad signature');

        $otherKey = new SubjectGrantService(new NullLogger(), new JsonService(new NullLogger()), new Base64Service(new NullLogger()), str_repeat('x', 32));
        $this->assertRefused($otherKey->issue('42', ['app.user.42.>'], self::T0, 60), self::T0, 'bad signature');

        $this->assertRefused('v2.' . $payload . '.' . $signature, self::T0, 'malformed');
        $this->assertRefused('not-a-token', self::T0, 'malformed');
    }

    /**
     * Test the subject patterns issue() accepts and refuses
     *
     * @return void
     */
    public function testIssueValidatesSubjectPatterns(): void
    {
        foreach (['a', 'a.b.c', 'a.*.c', 'a.b.>', '>', 'user_1.x-y'] as $ok) {
            $this->assertSame([$ok], $this->grants->verify($this->grants->issue('1', [$ok], self::T0, 60), self::T0)->subjects, $ok);
        }

        foreach (['', 'a..b', 'a.>.b', 'a b', 'a.b>', '.a', 'a.'] as $bad) {
            try {
                $this->grants->issue('1', [$bad], self::T0, 60);
                $this->fail('accepted malformed pattern: "' . $bad . '"');
            } catch (InvalidArgumentException $e) {
                $this->assertSame(ExceptionCode::REALTIME_GRANT_INVALID->value, $e->getCode());
            }
        }
    }

    /**
     * Test that a grant needs a subject and a positive lifetime
     *
     * @return void
     */
    public function testIssueRefusesEmptyOrNonPositiveGrants(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::REALTIME_GRANT_INVALID->value);

        try {
            $this->grants->issue('1', [], self::T0, 60);
        } catch (InvalidArgumentException $e) {
            // Expected: no subjects. A zero lifetime must be refused the same way.
            $this->assertSame(ExceptionCode::REALTIME_GRANT_INVALID->value, $e->getCode());
            $this->grants->issue('1', ['a'], self::T0, 0);
        }
    }

    /**
     * Test that a short key is refused at construction
     *
     * @return void
     */
    public function testRefusesAShortKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(ExceptionCode::REALTIME_GRANT_INVALID->value);

        new SubjectGrantService(new NullLogger(), new JsonService(new NullLogger()), new Base64Service(new NullLogger()), str_repeat('k', 31));
    }

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->grants = new SubjectGrantService(new NullLogger(), new JsonService(new NullLogger()), new Base64Service(new NullLogger()), self::KEY);
    }

    /**
     * Assert that verify() refuses a token for the given reason
     *
     * @param string $token    The token
     * @param int    $unixTime The time
     * @param string $reason   The expected reason
     *
     * @return void
     */
    private function assertRefused(string $token, int $unixTime, string $reason): void
    {
        try {
            $this->grants->verify($token, $unixTime);
            $this->fail('accepted a token that should be refused as: ' . $reason);
        } catch (InvalidArgumentException $e) {
            $this->assertSame('Invalid subject grant: ' . $reason, $e->getMessage());
            $this->assertSame(ExceptionCode::REALTIME_GRANT_INVALID->value, $e->getCode());
        }
    }
}
