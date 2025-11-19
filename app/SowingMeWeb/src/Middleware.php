<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface as Logger;
use Slim\App;
use Ubix\Middleware\BearerTokenAuthenticationMiddleware;
use Ubix\Middleware\NormalizedHostMiddleware;
use Ubix\Middleware\NormalizedIpAddressMiddleware;
use Ubix\Renderer\JsonErrorRenderer;
use Ubix\SlimHandler\SlimErrorHandler;

/**
 * Apply middleware to a Slim Framework application
 *
 * @param App<Container> $app The Slim Framework application
 *
 * @return void
 */

return static function (App $app): void {
    //
    //  Create a logger
    //
    $logger = $app->getContainer()?->get(Logger::class);
    assert($logger instanceof Logger || $logger === null);

    //
    //  Middleware to handle routing
    //
    $app->addRoutingMiddleware();

    //
    //  Application specific middleware
    //
    $app->add(NormalizedHostMiddleware::class);
    $app->add(NormalizedIpAddressMiddleware::class);

    //
    //  Middleware to parse the post body
    //
    $app->addBodyParsingMiddleware();

    //
    //  Middleware to handle errors
    //
    $slimErrorHandler = $app->getContainer()?->get(SlimErrorHandler::class);
    assert($slimErrorHandler instanceof SlimErrorHandler);
    $slimErrorHandler->registerErrorRenderer('application/json', JsonErrorRenderer::class);
    $slimErrorHandler->setDefaultErrorRenderer('application/json', JsonErrorRenderer::class);

    $errorMiddleware = $app->addErrorMiddleware(false, true, true, $logger);
    $errorMiddleware->setDefaultErrorHandler($slimErrorHandler);

};