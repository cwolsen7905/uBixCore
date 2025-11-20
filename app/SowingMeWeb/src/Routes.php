<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\SowingMeWeb\SowingMeWebController;

return static function (App $app): void {
	// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma -- disable this rule to allow for vertical spacing of the route parameters
	$app->map(['GET'], '/',              SowingMeWebController::class . ':home');
	$app->map(['GET'], '/signup',        SowingMeWebController::class . ':signup');
	$app->map(['GET'], '/for-creators',  SowingMeWebController::class . ':forCreators');
	$app->map(['GET'], '/how-it-works',  SowingMeWebController::class . ':howItWorks');
	$app->map(['GET'], '/pricing',       SowingMeWebController::class . ':pricing');
	$app->map(['GET'], '/about',         SowingMeWebController::class . ':about');
	$app->map(['GET'], '/faq',           SowingMeWebController::class . ':faq');
	$app->map(['GET'], '/testimonials',  SowingMeWebController::class . ':testimonials');
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
