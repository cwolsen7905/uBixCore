<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\SowingMeApi\AuthController;
use Ubix\Controller\SowingMeApi\CreatorController;
use Ubix\Controller\SowingMeApi\EmailConfirmationController;
use Ubix\Controller\SowingMeApi\PasswordResetController;
use Ubix\Controller\SowingMeApi\TierController;
use Ubix\Middleware\RoleAuthorizationMiddleware;

return static function (App $app): void {
	// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma -- disable this rule to allow for vertical spacing of the route parameters
    $app->map(['POST'],    '/auth',                             AuthController::class . ':authenticate');
    $app->map(['GET'],     '/auth',                             AuthController::class . ':validateSession');
    $app->map(['POST'],    '/auth/password-reset/request',      PasswordResetController::class . ':request');
    $app->map(['POST'],    '/auth/password-reset/confirm',      PasswordResetController::class . ':confirm');
    $app->map(['POST'],    '/logout',                           AuthController::class . ':logout');
    $app->map(['POST'],    '/register',                         AuthController::class . ':register');
    $app->map(['GET'],     '/confirm-email',                    EmailConfirmationController::class . ':confirmEmail');
    $app->map(['POST'],    '/creator/profile',                  CreatorController::class . ':createProfile')->add(RoleAuthorizationMiddleware::class);
    $app->map(['GET'],     '/creator/profile',                  CreatorController::class . ':getOwnProfile')->add(RoleAuthorizationMiddleware::class);
    $app->map(['GET'],     '/creator/onboarding',               CreatorController::class . ':getOnboarding')->add(RoleAuthorizationMiddleware::class);
    $app->map(['GET'],     '/creators/{slug}',                  CreatorController::class . ':getBySlug');
    $app->map(['GET'],     '/creators/{slug}/tiers',            TierController::class . ':publicList');
    $app->map(['POST'],    '/creator/tiers',                    TierController::class . ':create')->add(RoleAuthorizationMiddleware::class);
    $app->map(['GET'],     '/creator/tiers',                    TierController::class . ':listOwn')->add(RoleAuthorizationMiddleware::class);
    $app->map(['POST'],    '/creator/tiers/reorder',            TierController::class . ':reorder')->add(RoleAuthorizationMiddleware::class);
    $app->map(['PATCH'],   '/creator/tiers/{id:[0-9]+}',        TierController::class . ':update')->add(RoleAuthorizationMiddleware::class);
    $app->map(['PATCH'],   '/creator/tiers/{id:[0-9]+}/status', TierController::class . ':updateStatus')->add(RoleAuthorizationMiddleware::class);
    $app->map(['OPTIONS'], '/{routes:.*}',                      AuthController::class . ':options');
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
