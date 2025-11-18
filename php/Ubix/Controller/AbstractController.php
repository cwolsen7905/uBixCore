<?php

declare(strict_types=1);

namespace Ubix\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\StatusCode;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\JsonService;

/**
 * Abstract controller for use in all websites, APIs and web services
 */
abstract class AbstractController
{
    /**
     * Variables to be passed to the Latte template
     *
     * @var array<string, mixed> $templateVariables
     */
    protected array $templateVariables = [];

    /**
     * Constructor
     *
     * @param Logger           $logger           PSR logger
     * @param TemplateRenderer $templateRenderer Template renderer
     * @param JsonService      $jsonService      JSON service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $templateRenderer,
        protected JsonService $jsonService,
    ) {
    }

    /**
     * Load the last URL into the template variables
     *
     * @param Request $request PSR request
     *
     * @return void
     */
    protected function loadLastUrl(Request $request): void
    {
        // NOT_IMPLEMENTED: this is duplicate code (DRY violation) from Ubix\SlimHandler\SlimErrorHandler - figure out a single place to generate this lastUrl for both of them
        if (!isset($this->templateVariables['lastUrl'])) {
            $lastUrl = $request->getUri()->getPath();
            if ($request->getUri()->getQuery() !== '') {
                $lastUrl .= '?' . $request->getUri()->getQuery();
            }
            $this->sendToTemplate('lastUrl', $lastUrl);
        }
    }

    /**
     * Send data as a variable to a Latte template
     *
     * @param string $name  The name of the variable
     * @param mixed  $value The value of the variable
     *
     * @return void
     */
    protected function sendToTemplate(string $name, mixed $value): void
    {
        $this->templateVariables[$name] = $value;
    }

    /**
     * Render a response using a Latte template
     *
     * @param Response   $response   PSR response
     * @param string     $template   The Latte template to render
     * @param StatusCode $statusCode The HTTP status code for the response (optional) (default: 200 OK)
     *
     * @return Response PSR response
     */
    protected function renderTemplate(Response $response, string $template, StatusCode $statusCode = StatusCode::OK): Response
    {
        //
        //  Add the payloads http code to the response
        //
        $response = $response->withStatus($statusCode->value);

        if (!isset($this->templateVariables['lastUrl'])) {
            // TEMPORARY: ANDREW:: should I just pass $request into this method?
            // $this->loadLastUrl($request);
            $this->sendToTemplate('lastUrl', '/');
        }

        return $this->templateRenderer->template($response, $template, $this->templateVariables);
    }

    /**
     * Render a response using JSON
     *
     * @param Response                                         $response   PSR response
     * @param array<string, mixed>|array<array<string, mixed>> $output     The data to render in JSON format
     * @param StatusCode                                       $statusCode The HTTP status code for the response (optional) (default: 200 OK)
     *
     * @return Response PSR response
     */
    protected function renderJson(Response $response, array $output, StatusCode $statusCode = StatusCode::OK): Response
    {
        //
        //  Add the payloads http code to the response
        //
        $response = $response->withStatus($statusCode->value);
        //
        //  Add the encoded JSON to the response
        //
        $response->getBody()->write(
            $this->jsonService->encode($output),
        );

        //
        //  Return the response with a JSON header
        //
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Render a response using JSON
     *
     * @param Response        $response   PSR response
     * @param ResponsePayload $payload    The response payload
     * @param StatusCode      $statusCode The HTTP status code for the response (optional) (default: 200 OK)
     *
     * @return Response PSR response
     */
    protected function renderJsonWithPayload(Response $response, ResponsePayload $payload, StatusCode $statusCode = StatusCode::OK): Response
    {
        //
        //  Add the payloads http code to the response
        //
        $response = $response->withStatus($statusCode->value);

        //
        //  Add the encoded JSON to the response
        //
        $response->getBody()->write(
            $this->jsonService->encode($payload->getResponseData()),
        );

        //
        //  Return the response with a JSON header
        //
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Redirect the user to a different URL
     *
     * @param Response $response PSR response
     * @param string   $url      The URL to redirect to
     * @param int      $httpCode The HTTP code of the redirect (optional) (default: 302)
     *
     * @return Response PSR response
     */
    protected function redirect(Response $response, string $url, int $httpCode = 302): Response
    {
        //
        //  Add an HTTP code for 301 and 302 redirects
        //
        switch ($httpCode) {
            case 301:
            case 302:
                $response = $response->withStatus($httpCode);
                break;
        }

        //
        //  Return a response with a Location header to redirect the user
        //
        return $response->withHeader('Location', $url);
    }
}
