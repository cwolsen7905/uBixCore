<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\SowingMeApi\AuthController;
use Ubix\Controller\SowingMeApi\EmailConfirmationController;

return static function (App $app): void {
	// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma -- disable this rule to allow for vertical spacing of the route parameters
    $app->map(['POST'],    '/auth',             AuthController::class . ':authenticate');
	$app->map(['GET'],     '/auth',             AuthController::class . ':validateSession');
	$app->map(['POST'],    '/logout',           AuthController::class . ':logout');
	$app->map(['POST'],    '/register',         AuthController::class . ':register');
	$app->map(['GET'],     '/confirm-email',    EmailConfirmationController::class . ':confirmEmail');
	$app->map(['OPTIONS'], '/{routes:.*}',      AuthController::class . ':options');
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
};
