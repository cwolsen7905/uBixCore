<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Latte\Engine;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\UidProcessor;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Ubix\HttpClient\CurlHttpClient;
use Ubix\Service\Ci\ApprovalWebhookService;

use function DI\autowire;
use function DI\get;

/**
 * PHP-DI container for uBixOps, the framework's own operations app
 *
 * Served when `APP_NAME=UbixOpsApi` and the host has no `app/UbixOpsApi/` of its own
 * (`Ubix\Bootstrap\http()` falls back to this folder). Everything comes from the
 * environment, so the published image and any host image run it the same way:
 *
 *   GITLAB_API_URL         e.g. https://gitlab.example.com/api/v4
 *   GITLAB_WEBHOOK_SECRET  the secret set on each project's webhook
 *   GITLAB_PROJECT_TOKENS  `projectId=token,projectId=token` (Developer role, `api` scope)
 *   APPROVAL_JOB_NAMES     optional; default `require-approval,require-approval-mr`
 *
 * Logs go to stderr, where a container's logs belong.
 */

return static function (): Container {
    $jobNames = getenv('APPROVAL_JOB_NAMES') ?: 'require-approval,require-approval-mr';

    $container = new ContainerBuilder();

    $container->addDefinitions([
        ApprovalWebhookService::class => autowire()
            ->constructorParameter('apiUrl', (string) getenv('GITLAB_API_URL'))
            ->constructorParameter('secret', (string) getenv('GITLAB_WEBHOOK_SECRET'))
            ->constructorParameter('projectTokens', (string) getenv('GITLAB_PROJECT_TOKENS'))
            ->constructorParameter('jobNames', array_values(array_filter(array_map('trim', explode(',', $jobNames))))),
        Engine::class                 => autowire(),
        HttpClient::class             => autowire(CurlHttpClient::class)->constructorParameter('timeout', 20),
        Logger::class                 => autowire(MonologLogger::class)->constructorParameter('name', 'UbixOpsApi')->constructorParameter('handlers', [new StreamHandler('php://stderr', Level::Info)])->constructorParameter('processors', [new UidProcessor()]),
        Psr17Factory::class           => autowire(Psr17Factory::class),
        RequestFactory::class         => get(Psr17Factory::class),
        ResponseFactory::class        => autowire(SlimResponseFactory::class),
        StreamFactory::class          => get(Psr17Factory::class),
    ]);

    return $container->build();
};
