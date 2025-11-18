<?php

declare(strict_types=1);

namespace Ubix\Middleware\FanClubWeb;

use Exception;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Slim\Exception\HttpNotFoundException;
use Ubix\Controller\FanClubWeb\PerformerController;
use Ubix\Model\Performer;
use Ubix\Service\SlugService;

/**
 * Middleware to detect if a URL is connected with a slug in the database
 *
 * @see \Ubix\Tests\Middleware\FanClubWeb\SlugMiddlewareTest PHPUnit test case
 */
final class SlugMiddleware implements Middleware
{
    /**
     * Constructor
     *
     * @param Logger              $logger              Logger
     * @param ResponseFactory     $responseFactory     Response factory
     * @param SlugService         $slugService         Slug service
     * @param PerformerController $performerController Performer controller
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private ResponseFactory $responseFactory,
        private SlugService $slugService,
        private PerformerController $performerController,
    ) {
    }

    /**
     * Process the request and handle slugs when routing fails
     *
     * @param Request $request PSR request
     * @param Handler $handler PSR handler
     *
     * @throws HttpNotFoundException If the slug is not found
     *
     * @return Response PSR response
     */
    public function process(Request $request, Handler $handler): Response
    {
        //
        //  Try to handle the request using existing routes and if that works, return the response without going any further
        //
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException $e) {
            //
            //  Load the pieces of the URL that we need to resolve the slug
            //
            $slug = explode('/', trim($request->getUri()->getPath(), '/'));

            //
            //  Try to load an object from the first portion of the URL, if an exception is thrown rather than an object returned then show a 404
            //
            try {
                $slugObject = $this->slugService->getObjectFromSlug($slug[0]);

                //
                //  Handle the slug based upon its type and any secondary URL slugs
                //
                switch (get_class($slugObject)) {
                    case Performer::class: // The slug is attached to a performer
                        switch ($slug[1] ?? '') {
                            case '': // No secondary URL slug so show the performer profile
                                return $this->performerController->profile($request, $this->responseFactory->createResponse(), $slugObject);

                            case 'statistics':
                                return $this->performerController->statistics($request, $this->responseFactory->createResponse(), $slugObject);

                            case 'join':
                                return $this->performerController->joinFanClub($request, $this->responseFactory->createResponse(), $slugObject);
                        }
                }
            } catch (Exception $e) {
                throw new HttpNotFoundException($request);
            }

            throw new HttpNotFoundException($request);
        }
    }
}
