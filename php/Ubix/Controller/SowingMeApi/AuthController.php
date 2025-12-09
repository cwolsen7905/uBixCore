<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Enum\User\UserStatus;
use Ubix\Exception\DtoException;
use Ubix\Model\EmailConfirmationToken;
use Ubix\Model\User;
use Ubix\Payload\Request\AuthenticationRequestPayload;
use Ubix\Payload\Request\RegistrationRequestPayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as TokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as TokenWriter;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;
use Ubix\Service\EmailService;
use Ubix\Service\JsonService;

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
     * @param UserWriter         $userWriter         The user writer
     * @param TokenReader        $tokenReader        The email confirmation token reader
     * @param TokenWriter        $tokenWriter        The email confirmation token writer
     * @param EmailService       $emailService       The email service
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view, // -> Needed Always
        protected JsonService $jsonService, // -> Needed Always
		protected UserReader $userReader, // -> Needed for user lookups
		protected UserWriter $userWriter, // -> Needed for user creation
		protected TokenReader $tokenReader, // -> Needed for token lookups
		protected TokenWriter $tokenWriter, // -> Needed for token creation
		protected EmailService $emailService, // -> Needed for sending emails
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
			'email' => $payload->email->value,
			'password_length' => strlen($payload->password->value),
			'debug' => $payload->debug?->value,
		]);	

        // Lookup the user by email or return a json error response status 401
        try {
            $user = $this->userReader->getUserByEmail($payload->email);
        } catch (Exception $e) {
            $this->logger->info('Authentication failed: user not found', [
                'email' => $payload->email->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Invalid displayName or password',
            ], StatusCode::UNAUTHORIZED);
        }

        // Verify password
        if (!password_verify($payload->password->value, $user->getPasswordHash() ?? '')) {
            $this->logger->info('Authentication failed: invalid password', [
                'displayName' => $payload->displayName->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Invalid displayName or password',
            ], StatusCode::UNAUTHORIZED);
        }

        // Check user status
        if ($user->getStatus()?->value !== 'active') {
            $this->logger->info('Authentication failed: user not active', [
                'displayName' => $payload->displayName->value,
                'status'   => $user->getStatus()?->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Account is not active',
            ], StatusCode::UNAUTHORIZED);
        }

        // Set session data
        $_SESSION['user'] = [
            'id'       => $user->getId(),
            'displayName' => $user->getDisplayName(),
            'email'    => $user->getEmail(),
            'roles'    => $user->getRoles(),
			'firstName' => $user->getFirstName(),
			'lastName'  => $user->getLastName(),
			'creatorName' => $user->getCreatorName(),
        ];

        $this->logger->info('Authentication successful', [
            'user_id'  => $user->getId(),
            'displayName' => $user->getDisplayName(),
        ]);

        return $this->renderJson($response, [
            'status'  => 'success',
            'message' => 'Authentication successful',
            'user'    => [
                'id'        => $user->getId(),
                'displayName'  => $user->getDisplayName(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName'  => $user->getLastName(),
                'creatorName' => $user->getCreatorName(),
                'roles'     => $user->getRoles(),
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
			'displayName'  => $_SESSION['user']['displayName'] ?? null,
			'email'     => $_SESSION['user']['email'] ?? null,
			'firstName' => $_SESSION['user']['firstName'] ?? null,
			'lastName'  => $_SESSION['user']['lastName'] ?? null,
			'creatorName' => $_SESSION['user']['creatorName'] ?? null,
			'roles'     => $_SESSION['user']['roles'] ?? null
		];

		return $this->renderJson($response, $data);
	}

    /**
     * Logout the user by destroying the session
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function logout(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $displayName = $_SESSION['user']['displayName'] ?? null;

        // Clear session data
        $_SESSION = [];

        // Destroy the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->logger->info('User logged out', [
            'user_id'  => $userId,
            'displayName' => $displayName,
        ]);

        return $this->renderJson($response, [
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Register a new user account
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function register(Request $request, Response $response): Response
    {

        try {
            $payload = RegistrationRequestPayload::getRequest($request);
        } catch (DtoException $e) {
			print "Test2";
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

		print "Test";

        // Log the registration attempt
        $this->logger->debug('Registration attempt', [
            'displayName'  => $payload->displayName->value,
            'email'      => $payload->email->value,
            'first_name' => $payload->firstName->value,
            'last_name'  => $payload->lastName->value,
        ]);

        // Validate password confirmation
        if ($payload->password->value !== $payload->confirmPassword->value) {
            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'Passwords do not match',
                'fields'  => [
                    'confirm_password' => ['Passwords do not match'],
                ],
            ], StatusCode::BAD_REQUEST);
        }

        // Check if displayName already exists
        if ($this->userReader->displayNameExists($payload->displayName)) {
            $this->logger->info('Registration failed: displayName already exists', [
                'displayName' => $payload->displayName->value,
            ]);

            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'This displayName is already taken',
                'fields'  => [
                    'displayName' => ['This displayName is already taken'],
                ],
            ], StatusCode::CONFLICT);
        }

        // Check if email already exists
        if ($this->userReader->emailExists($payload->email->value)) {
            $this->logger->info('Registration failed: email already exists', [
                'email' => $payload->email->value,
            ]);

            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'An account with this email already exists',
                'fields'  => [
                    'email' => ['This email is already registered'],
                ],
            ], StatusCode::CONFLICT);
        }

        // Use the provided displayName
        $displayName = $payload->displayName->value;

        // Create the user
        $user = new User(
            id: null,
            displayName: $displayName,
            passwordHash: password_hash($payload->password->value, PASSWORD_DEFAULT),
            email: $payload->email->value,
            firstName: $payload->firstName->value,
            lastName: $payload->lastName->value,
            status: UserStatus::PENDING,
            roles: 'user',
            failedLoginAttempts: 0,
            lastFailedLogin: null,
            lastLogin: null,
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
        );

        try {
            $userId = $this->userWriter->createUser($user);

            $this->logger->info('User registration successful', [
                'user_id'    => $userId,
                'email'      => $payload->email->value,
                'display_name'   => $payload->displayName->value,
                'first_name' => $payload->firstName->value,
                'last_name'  => $payload->lastName->value,
            ]);

            // Generate confirmation token
            $token = bin2hex(random_bytes(32));
            $expiresAt = new \DateTime('+24 hours');

            $confirmationToken = new EmailConfirmationToken(
                id: null,
                userId: $userId,
                token: $token,
                expiresAt: $expiresAt,
                createdAt: new \DateTime(),
                usedAt: null,
            );

            try {
                $this->tokenWriter->createToken($confirmationToken);
            } catch (Exception $e) {
                $this->logger->error('Failed to create confirmation token', [
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);
            }

            // Send confirmation email
            try {
                $confirmationUrl = ($_ENV['APP_URL'] ?? 'http://127.0.0.1:5173') . '/confirm-email?token=' . $token;

                $this->emailService->sendRegistrationConfirmation(
                    $payload->email->value,
                    $payload->firstName->value,
                    $payload->lastName->value,
                    $confirmationUrl
                );
            } catch (Exception $e) {
                // Log the error but don't fail the registration
                $this->logger->error('Failed to send registration email', [
                    'user_id' => $userId,
                    'email'   => $payload->email->value,
                    'error'   => $e->getMessage(),
                ]);
            }

            return $this->renderJson($response, [
                'status'  => 'success',
                'message' => 'Registration successful! Please check your email for confirmation.',
                'userId'  => $userId,
            ], StatusCode::CREATED);
        } catch (Exception $e) {
            $this->logger->error('User registration failed', [
                'email' => $payload->email->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'Registration failed. Please try again later.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
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
