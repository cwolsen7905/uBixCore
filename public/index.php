<?php

declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Dotenv\Dotenv;
use Slim\App;
use Ubix\Enum\Exception\ExceptionCode;

//
//  Set the timezone
//
date_default_timezone_set('America/New_York');

//
//  Load Composer packages
//
require_once '../vendor/autoload.php';

//
//  Initialize Dotenv to read our .env file
//
(Dotenv::createUnsafeImmutable(__DIR__ . '/../'))->load();

//
//  Temporary error output for sandbox testing
//
if (getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    error_reporting(E_ALL);

    register_shutdown_function(function (): void {
        $error = error_get_last();
        if ($error !== null) {
            echo '<pre>Fatal error in ', $error['file'], ' on line ', $error['line'], ':', PHP_EOL, $error['message'], '</pre>';
        }
    });
}

//
//  Determine which app is active and define its folder
//
$appName = getenv('APP_NAME');
if ($appName === false || !is_string($appName) || trim($appName) === '') {
    throw new Exception('No app name found', ExceptionCode::APP_NAME_MISSING->value);
}

$appFolder = __DIR__ . '/../app/' . $appName;

//
//  Create a Slim app with a PHP-DI container
//
/**
 * @var callable():?Container $buildContainer
 */
$buildContainer = require $appFolder . '/src/Dependencies.php';
assert(is_callable($buildContainer));

$container = $buildContainer();
assert($container === null || $container instanceof Container);

/**
 * @var App<Container> $slimApp
 */
$slimApp = Bridge::create($container);

//
//  Apply middleware and routes
//
/**
 * @var callable(App<Container>):void $applyMiddleware
 */
$applyMiddleware = require $appFolder . '/src/Middleware.php';
assert(is_callable($applyMiddleware));
$applyMiddleware($slimApp);


/**
 * @var callable(App<Container>):void $applyRoutes
 */
$applyRoutes = require $appFolder . '/src/Routes.php';
assert(is_callable($applyRoutes));
$applyRoutes($slimApp);


/*
$slimApp->add(function ($request, $handler) {

    $response = $handler->handle($request)
    ->withHeader('Access-Control-Allow-Origin', 'Balls') // Or specify your allowed origin
    ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    ->withHeader('Access-Control-Allow-Credentials', 'true');

    // Handle preflight OPTIONS request
    if ($request->getMethod() === 'OPTIONS') {
        return $response->withStatus(200);
    }

    // var_dump("TEST");
    return $response;
});
*/

//
//  Run the Slim application
//
$slimApp->run();
