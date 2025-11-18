<?php

declare(strict_types=1);

namespace Ubix\Renderer;

use Latte\Engine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface as Logger;

/**
 * Renderer to handle Latte templates
 *
 * @see \Ubix\Tests\Renderer\TemplateRendererTest PHPUnit test case
 */
final class TemplateRenderer
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     * @param Engine $engine Latte templating engine
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private Engine $engine,
    ) {
    }

    /**
     * Generate template output for a PSR response
     *
     * @param Response             $response PSR response
     * @param string               $template The template file
     * @param array<string, mixed> $data     Data to pass to the template
     *
     * @return Response PSR response
     */
    public function template(
        Response $response,
        string $template,
        array $data = [],
    ): Response {
        $response->getBody()->write(
            $this->engine->renderToString($template, $data),
        );

        return $response;
    }
}
