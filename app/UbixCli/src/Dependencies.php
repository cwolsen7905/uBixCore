<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\UidProcessor;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Log\LoggerInterface as Logger;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;

use function DI\autowire;

return static function (): Container {
    $appName = 'NeptuneCli';

    $container = new ContainerBuilder();

    $container->addDefinitions([
        Logger::class          => autowire(MonologLogger::class)->constructorParameter('name', $appName)->constructorParameter('handlers', [new StreamHandler(getenv('LOGGER_PATH') . '/' . $appName . '.log', getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true' ? Level::Debug : Level::Info)])->constructorParameter('processors', [new UidProcessor()]),
        ResponseFactory::class => autowire(SlimResponseFactory::class),
    ]);

    return $container->build();
};
