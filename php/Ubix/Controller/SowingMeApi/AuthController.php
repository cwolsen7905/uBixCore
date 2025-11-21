<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Payload\Request\AuthenticationRequestPayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\JsonService;
use Ubix\Enum\StatusCode;
use Ubix\Exception\DtoException;
use Ubix\Repository\User\UserReaderInterface as UserReader;

/**
 * Controller to handle API calls involving models
 *
 * @see \Ubix\Tests\Controller\SowingMeApi\AuthControllerTest PHPUnit test case
 */
final class AuthController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger             $logger             The Monolog logger
     * @param TemplateRenderer   $view               The template renderer
     * @param JsonService        $jsonService        The JSON service
     * @param UserReader         $userReader         The user reader
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view, // -> Needed Always
        protected JsonService $jsonService, // -> Needed Always
		protected UserReader $userReader, // -> Needed for user lookups
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Authenticate the user and return a success response
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function authenticate(Request $request, Response $response): Response
    {

		try {
        $payload = AuthenticationRequestPayload::getRequest($request);
		} catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ]);//, StatusCode::BAD_REQUEST);
        }

		// Log the payload for debugging
		$this->logger->debug('Authentication payload', [
			'username' => $payload->username->value,
			'password_length' => strlen($payload->password->value),
			'debug' => $payload->debug?->value,
		]);	

        // Lookup the user by username or return a json error response status 401
        try {
            $user = $this->userReader->getUserByUsername($payload->username);
        } catch (Exception $e) {
            $this->logger->info('Authentication failed: user not found', [
                'username' => $payload->username->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Invalid username or password',
            ], StatusCode::UNAUTHORIZED);
        }

        // Verify password
        if (!password_verify($payload->password->value, $user->getPasswordHash() ?? '')) {
            $this->logger->info('Authentication failed: invalid password', [
                'username' => $payload->username->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Invalid username or password',
            ], StatusCode::UNAUTHORIZED);
        }

        // Check user status
        if ($user->getStatus()?->value !== 'active') {
            $this->logger->info('Authentication failed: user not active', [
                'username' => $payload->username->value,
                'status'   => $user->getStatus()?->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Account is not active',
            ], StatusCode::UNAUTHORIZED);
        }

        // Set session data
        $_SESSION['user'] = [
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'roles'    => $user->getRoles(),
			'firstName' => $user->getFirstName(),
			'lastName'  => $user->getLastName(),
        ];

        $this->logger->info('Authentication successful', [
            'user_id'  => $user->getId(),
            'username' => $user->getUsername(),
        ]);

        return $this->renderJson($response, [
            'status'  => 'success',
            'message' => 'Authentication successful',
            'user'    => [
                'id'        => $user->getId(),
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName'  => $user->getLastName(),
            ],
        ]);
    }

	/**
	 * Validate the current session
	 * 
	 * @param Request  $request  The HTTP request object containing client data.
	 * @param Response $response The HTTP response object used to send data back to the client
	 * 
	 * @return Response The modified response object with the operation result.
	 */
	public function validateSession(Request $request, Response $response): Response
	{
		$data = [
			'id'        => $_SESSION['user']['id'] ?? null,
			'username'  => $_SESSION['user']['username'] ?? null,
			'email'     => $_SESSION['user']['email'] ?? null,
			'firstName' => $_SESSION['user']['firstName'] ?? null,
			'lastName'  => $_SESSION['user']['lastName'] ?? null
		];

		return $this->renderJson($response, $data);
	}

    /**
     * Handle OPTIONS request for CORS preflight
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with CORS headers.
     */
    public function options(Request $request, Response $response): Response
    {
        $origin = $request->getHeaderLine('Origin');

		// Debug log this CORS request, the request address the referrer and the origin
		$this->logger->debug('CORS Preflight Request', [
			'referrer' => $request->getHeaderLine('Referer'),
			'origin'   => $origin,
		]);

        return $response
        ->withHeader('Access-Control-Allow-Origin', $origin) // Or specify your allowed origin
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withStatus(204);
    }

}
