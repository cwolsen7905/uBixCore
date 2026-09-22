<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Log\NullLogger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\SessionService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\SessionService
 *
 * Sessions are process-global, so the behavioural tests run in their own process.
 *
 * @coversDefaultClass \Ubix\Service\SessionService
 */
final class SessionServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SessionService::class);
    }

    /**
     * Test that signing in rotates the session id before the payload is written
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testSigningInRotatesTheSessionId(): void
    {
        ini_set('session.use_cookies', '0');
        ini_set('session.cache_limiter', '');
        ini_set('session.save_path', sys_get_temp_dir());
        session_id('planted' . bin2hex(random_bytes(8)));
        session_start();
        $planted = session_id();

        (new SessionService(new NullLogger()))->startAuthenticatedSession(['id' => 42]);

        $this->assertNotSame($planted, session_id(), 'a pre-sign-in session id must not survive sign-in');
        $this->assertSame(['id' => 42], $_SESSION['user']);
    }

    /**
     * Test that signing in without an active session is refused rather than silently doing nothing
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testSigningInWithoutASessionIsRefused(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionCode(ExceptionCode::SESSION_NOT_STARTED->value);
        (new SessionService(new NullLogger()))->startAuthenticatedSession(['id' => 42]);
    }
}
