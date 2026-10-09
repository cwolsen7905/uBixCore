<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Identity;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\NullLogger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Base64Service;
use Ubix\Service\Identity\FacebookSignedRequestParserService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Identity\FacebookSignedRequestParserService
 *
 * Requests are signed here exactly as Meta signs them: HMAC-SHA256 over the
 * base64url payload, keyed with the app secret, base64url-encoded.
 *
 * @coversDefaultClass \Ubix\Service\Identity\FacebookSignedRequestParserService
 */
final class FacebookSignedRequestParserServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const APP_SECRET = 'app-secret-1';

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(FacebookSignedRequestParserService::class);
    }

    /**
     * Test that a correctly signed data-deletion request yields its user id and issue time
     *
     * @return void
     */
    public function testValidRequestIsParsed(): void
    {
        $parsed = $this->parser()->parse($this->sign([
            'algorithm' => 'HMAC-SHA256',
            'expires'   => 1700003600,
            'issued_at' => 1700000000,
            'user_id'   => '10158000000000001',
        ]));

        $this->assertSame('10158000000000001', $parsed->userId);
        $this->assertSame(1700000000, $parsed->issuedAtUnixTimestamp);
    }

    /**
     * Test that a request signed with another secret is refused
     *
     * @return void
     */
    public function testBadSignatureIsRefused(): void
    {
        $this->expectRefused();
        $this->parser()->parse($this->sign(['algorithm' => 'HMAC-SHA256', 'user_id' => '1'], 'another-secret'));
    }

    /**
     * Test that a payload altered after signing is refused
     *
     * @return void
     */
    public function testTamperedPayloadIsRefused(): void
    {
        [$signature]   = explode('.', $this->sign(['algorithm' => 'HMAC-SHA256', 'user_id' => '1']));
        $forgedPayload = $this->base64Url($this->json(['algorithm' => 'HMAC-SHA256', 'user_id' => '2']));

        $this->expectRefused();
        $this->parser()->parse($signature . '.' . $forgedPayload);
    }

    /**
     * Test that any algorithm but HMAC-SHA256 is refused, even with a valid signature
     *
     * @return void
     */
    public function testOtherAlgorithmIsRefused(): void
    {
        $this->expectRefused();
        $this->parser()->parse($this->sign(['algorithm' => 'none', 'user_id' => '1']));
    }

    /**
     * Test that a payload with no algorithm at all is refused
     *
     * @return void
     */
    public function testMissingAlgorithmIsRefused(): void
    {
        $this->expectRefused();
        $this->parser()->parse($this->sign(['user_id' => '1']));
    }

    /**
     * Test that a correctly signed request naming no user is refused
     *
     * @return void
     */
    public function testMissingUserIdIsRefused(): void
    {
        $this->expectRefused();
        $this->parser()->parse($this->sign(['algorithm' => 'HMAC-SHA256']));
    }

    /**
     * Test that malformed input is refused, never half-parsed
     *
     * @param string $signedRequest The malformed value
     *
     * @return void
     */
    #[DataProvider('malformedRequests')]
    public function testMalformedRequestIsRefused(string $signedRequest): void
    {
        $this->expectRefused();
        $this->parser()->parse($signedRequest);
    }

    /**
     * Test that with no app secret configured the parser fails closed
     *
     * @return void
     */
    public function testMissingSecretFailsClosed(): void
    {
        putenv('FACEBOOK_APP_SECRET');

        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_PROVIDER_OPERATION_FAILED->value);
        $this->parser('')->parse($this->sign(['algorithm' => 'HMAC-SHA256', 'user_id' => '1']));
    }

    /**
     * Malformed `signed_request` values
     *
     * @return array<string, array{string}> Cases
     */
    public static function malformedRequests(): array
    {
        return [
            'empty'           => [''],
            'empty payload'   => ['abc.'],
            'empty signature' => ['.eyJhIjoxfQ'],
            'no separator'    => ['abc'],
            'not base64url'   => ['a+b/c.eyJhIjoxfQ'],
            'three parts'     => ['a.b.c'],
        ];
    }

    /**
     * Build the parser under test
     *
     * @param string $appSecret Meta app secret
     *
     * @return FacebookSignedRequestParserService The parser
     */
    private function parser(string $appSecret = self::APP_SECRET): FacebookSignedRequestParserService
    {
        return new FacebookSignedRequestParserService(new NullLogger(), new JsonService(new NullLogger()), new Base64Service(new NullLogger()), $appSecret);
    }

    /**
     * Sign a payload the way Meta does
     *
     * @param array<string, mixed> $payload The payload
     * @param string               $secret  The key
     *
     * @return string The `signed_request` value
     */
    private function sign(array $payload, string $secret = self::APP_SECRET): string
    {
        $encodedPayload = $this->base64Url($this->json($payload));

        return $this->base64Url(hash_hmac('sha256', $encodedPayload, $secret, true)) . '.' . $encodedPayload;
    }

    /**
     * Encode JSON
     *
     * @param array<string, mixed> $value The value
     *
     * @return string JSON
     */
    private function json(array $value): string
    {
        return (new JsonService(new NullLogger()))->encode($value);
    }

    /**
     * Base64url-encode without padding
     *
     * @param string $bytes Raw bytes
     *
     * @return string The encoding
     */
    private function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Expect the invalid-request refusal
     *
     * @return void
     */
    private function expectRefused(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::IDENTITY_TOKEN_INVALID->value);
    }
}
