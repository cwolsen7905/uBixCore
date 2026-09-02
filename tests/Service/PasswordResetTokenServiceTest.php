<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\PasswordResetToken;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenReaderInterface as PasswordResetTokenReader;
use Ubix\Repository\PasswordResetToken\PasswordResetTokenWriterInterface as PasswordResetTokenWriter;
use Ubix\Service\PasswordResetTokenService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\PasswordResetTokenService
 *
 * @coversDefaultClass \Ubix\Service\PasswordResetTokenService
 */
final class PasswordResetTokenServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private ?PasswordResetToken $capturedToken = null;

    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(PasswordResetTokenService::class);
    }

    /**
     * Issue supersedes outstanding tokens and returns a raw token whose hash is stored
     *
     * @return void
     */
    public function testIssueTokenSupersedesAndStoresHash(): void
    {
        $writer = $this->createMock(PasswordResetTokenWriter::class);
        $writer->expects($this->once())->method('invalidateOutstandingTokensForUser')->with(7);
        $writer->expects($this->once())->method('createToken')->willReturnCallback(
            function (PasswordResetToken $token): void {
                $this->capturedToken = $token;
                $token->setId(1);
            },
        );

        $service  = new PasswordResetTokenService($this->createStub(Logger::class), $this->createStub(PasswordResetTokenReader::class), $writer);
        $rawToken = $service->issueToken(7);

        $this->assertSame(64, strlen($rawToken));
        $this->assertInstanceOf(PasswordResetToken::class, $this->capturedToken);
        $this->assertSame(hash('sha256', $rawToken), $this->capturedToken->getTokenHash());
        $this->assertGreaterThan(new DateTime('+55 minutes'), $this->capturedToken->getExpiresAt());
    }

    /**
     * A used or expired token is rejected; a live one is returned
     *
     * @return void
     */
    public function testGetValidTokenStateMachine(): void
    {
        $live    = new PasswordResetToken(id: 1, userId: 7, tokenHash: hash('sha256', 'raw'), expiresAt: new DateTime('+30 minutes'));
        $used    = new PasswordResetToken(id: 2, userId: 7, tokenHash: hash('sha256', 'used'), expiresAt: new DateTime('+30 minutes'), usedAt: new DateTime());
        $expired = new PasswordResetToken(id: 3, userId: 7, tokenHash: hash('sha256', 'old'), expiresAt: new DateTime('-1 minute'));

        $reader = $this->createStub(PasswordResetTokenReader::class);
        $reader->method('getTokenByHash')->willReturnMap([
            [hash('sha256', 'raw'), $live],
            [hash('sha256', 'used'), $used],
            [hash('sha256', 'old'), $expired],
            [hash('sha256', 'unknown'), null],
        ]);

        $service = new PasswordResetTokenService($this->createStub(Logger::class), $reader, $this->createStub(PasswordResetTokenWriter::class));

        $this->assertSame($live, $service->getValidToken('raw'));
        $this->assertNull($service->getValidToken('used'));
        $this->assertNull($service->getValidToken('old'));
        $this->assertNull($service->getValidToken('unknown'));
    }

    /**
     * Consuming a token marks it used and supersedes the user's other tokens
     *
     * @return void
     */
    public function testConsumeTokenMarksUsedAndSupersedes(): void
    {
        $writer = $this->createMock(PasswordResetTokenWriter::class);
        $writer->expects($this->once())->method('markTokenAsUsed')->with(9);
        $writer->expects($this->once())->method('invalidateOutstandingTokensForUser')->with(7);

        $service = new PasswordResetTokenService($this->createStub(Logger::class), $this->createStub(PasswordResetTokenReader::class), $writer);
        $service->consumeToken(new PasswordResetToken(id: 9, userId: 7));
    }
}
