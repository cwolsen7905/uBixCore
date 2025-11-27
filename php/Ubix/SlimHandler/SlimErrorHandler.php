<?php

declare(strict_types=1);

namespace Ubix\SlimHandler;

use Latte\Engine;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface as CallableResolver;

/**
 * Error handler that extends Slim's ErrorHandler class
 *
 * @see \Ubix\Tests\SlimHandler\SlimErrorHandlerTest PHPUnit test case
 */
final class SlimErrorHandler extends ErrorHandler
{
    /**
     * Constructor
     *
     * @param Logger           $logger           Logger
     * @param CallableResolver $callableResolver Callable resolver
     * @param ResponseFactory  $responseFactory  PSR response factory
     * @param Engine           $engine           Latte engine
     */
    public function __construct(
        protected Logger $logger,
        protected CallableResolver $callableResolver,
        protected ResponseFactory $responseFactory,
        protected Engine $engine,
    ) {
        parent::__construct($callableResolver, $responseFactory, $logger);
    }

    /**
     * Respond to the error
     *
     * @return Response PSR response
     */
    protected function respond(): Response
    {
        if ($this->contentType === 'text/html') { // Handle HTML errors
            $template = $this->statusCode === 404 ? 'Error/404.latte' : 'Error/default.latte';

            // NOT_IMPLEMENTED: this is duplicate code (DRY violation) from Ubix\Controller\AbstractController - figure out a single place to generate this lastUrl for both of them
            $lastUrl = $this->request->getUri()->getPath();
            if ($this->request->getUri()->getQuery() !== '') {
                $lastUrl .= '?' . $this->request->getUri()->getQuery();
            }

            $body = $this->engine->renderToString($template, [
                'code'            => $this->statusCode,
                'debug'           => true; // $this->displayErrorDetails,
                'file'            => $this->exception->getFile(),
                'lastUrl'         => $lastUrl,
                'line'            => $this->exception->getLine(),
                'loggedInAccount' => $this->request->getAttribute('loggedInAccount'),
                'message'         => $this->exception->getMessage(),
                'title'           => $this->exception instanceof HttpException ? $this->exception->getTitle() : $this->statusCode . ' - ' . $this->exception::class,
                'trace'           => $this->exception->getTraceAsString(),
                'type'            => $this->exception::class,
            ]);

            $response = $this->responseFactory->createResponse($this->statusCode)->withHeader('Content-Type', 'text/html');
            $response->getBody()->write($body);
            return $response;
        } else { // Don't handle non-HTML errors, let the parent respond
            return parent::respond();
        }
    }
}
