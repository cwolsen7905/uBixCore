<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\Ops\OpsController;

/**
 * The uBixOps routes. Each is unauthenticated by session: the webhook proves itself with
 * GitLab's secret token, checked in ApprovalWebhookService.
 */

return static function (App $app): void {
    // phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma -- vertical alignment of the route table
    $app->map(['GET'],  '/health',                    OpsController::class . ':health');
    $app->map(['POST'], '/gitlab/merge-request-hook', OpsController::class . ':gitlabMergeRequestHook');
    // phpcs:enable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma

    //
    //  Anything else is a 404 rendered by the JSON error handler
    //
    $app->map(
        ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        '/{routes:.+}',
        static function (Request $request): void {
            throw new HttpNotFoundException($request);
        },
    );
};
