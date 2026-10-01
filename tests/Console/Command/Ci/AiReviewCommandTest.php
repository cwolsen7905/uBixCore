<?php

declare(strict_types=1);

namespace Ubix\Tests\Console\Command\Ci;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Tester\CommandTester;
use Ubix\Console\Command\Ci\AiReviewCommand;
use Ubix\Service\Ci\AiReviewService;
use Ubix\Service\JsonService;
use Ubix\Service\ProjectRootService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Console\Command\Ci\AiReviewCommand
 *
 * @coversDefaultClass \Ubix\Console\Command\Ci\AiReviewCommand
 */
final class AiReviewCommandTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AiReviewCommand::class);
    }

    /**
     * Without its keys the job skips rather than fails, and sends nothing
     *
     * A host's MR gate must not turn red because a secret has not been set up yet.
     *
     * @return void
     */
    public function testWithoutKeysItSkips(): void
    {
        putenv('GEMINI_API_KEY');
        putenv('AI_REVIEW_GITLAB_TOKEN');

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $logger  = $this->createStub(Logger::class);
        $service = new AiReviewService($logger, $httpClient, new Psr17Factory(), new Psr17Factory(), new JsonService($logger));

        $tester = new CommandTester(new AiReviewCommand($logger, $service, new ProjectRootService($logger, sys_get_temp_dir())));
        $status = $tester->execute(['diff-file' => __FILE__]);

        $this->assertSame(0, $status);
        $this->assertStringContainsString('Skipped', $tester->getDisplay());
    }
}
