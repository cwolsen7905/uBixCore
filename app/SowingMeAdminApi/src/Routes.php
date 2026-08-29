<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

return static function (App $app): void {
    session_start();


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

    $app->add(static function (Request $request, Handler $handler): Response {
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

        return $response;
    });
};
