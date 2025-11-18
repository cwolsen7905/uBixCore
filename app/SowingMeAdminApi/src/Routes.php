<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\InternalAdminApi\AffiliateController;
use Ubix\Controller\InternalAdminApi\AuthController;

return static function (App $app): void {
    session_start();

	// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma -- disable this rule to allow for vertical spacing of the route parameters
    $app->map(['GET'],  '/affiliates',                     AffiliateController::class . ':list');
    $app->map(['GET'],  '/affiliate/{affiliateId:[0-9]+}', AffiliateController::class . ':get');
    $app->map(['GET'],  '/auth',                           AuthController::class . ':validate');
    $app->map(['POST'], '/auth',                           AuthController::class . ':authenticate');
    $app->map(['OPTIONS'], '/{routes:.*}',                 AuthController::class . ':options');
	// phpcs:enable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma

    //
    //  If no match is found with the existing routes then fallback to throwing a 404 exception
    //
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.*}',
        static function (Request $request): void {
            throw new HttpNotFoundException($request);
        },
    );

    $app->add(function ($request, $handler) {
        $origin = $request->getHeaderLine('Origin');

        $response = $handler->handle($request)
        ->withHeader('Access-Control-Allow-Origin', $origin) // Or specify your allowed origin
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');

        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(204);
        }

        // var_dump("TEST");
        return $response;
    });
};
