<?php

declare(strict_types=1);

namespace Ubix\Renderer\ModelSignupApi;

use Psr\Log\LoggerInterface as Logger;
use Slim\Interfaces\ErrorRendererInterface as ErrorRenderer;
use Throwable;
use Ubix\Service\JsonService;

/**
 * Renderer to handle JSON errors on the model signup app
 *
 * @see \Ubix\Tests\Renderer\ModelSignupApi\JsonErrorRendererTest PHPUnit test case
 */
final class JsonErrorRenderer implements ErrorRenderer
{
    /**
     * Constructor
     *
     * @param Logger      $logger      Logger
     * @param JsonService $jsonService JSON service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private JsonService $jsonService,
    ) {
    }

    /**
     * Invoke method
     *
     * @param Throwable $exception The exception that was thrown to trigger the error renderer
     * @param bool      $unused    Unused but required by the interface
     *
     * @return string JSON error
     */
    public function __invoke(
        Throwable $exception,
        bool $unused, // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- This parameter is unused but required by the interface
    ): string {
        return $this->jsonService->encode([
            'data' => [
                'error' => $exception->getMessage(),
            ],
        ]);
    }
}
