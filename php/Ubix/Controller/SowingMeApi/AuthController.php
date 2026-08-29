<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use DateTime;
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
use Ubix\Service\EmailConfirmationTokenService;
use Ubix\Service\EmailService;
use Ubix\Service\JsonService;
use Ubix\Service\UserService;

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
     * @param Logger                        $logger       The Monolog logger
     * @param TemplateRenderer              $view         The template renderer
     * @param JsonService                   $jsonService  The JSON service
     * @param UserService                   $userService  The user service
     * @param EmailConfirmationTokenService $tokenService The email confirmation token service
     * @param EmailService                  $emailService The email service
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view, // -> Needed Always
        protected JsonService $jsonService, // -> Needed Always
        protected UserService $userService,
        protected EmailConfirmationTokenService $tokenService,
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
            assert($payload instanceof AuthenticationRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ]);
        }

        // Log the payload for debugging
        $this->logger->debug('Authentication payload', [
            'debug'           => $payload->debug,
            'email'           => $payload->email->value,
            'password_length' => strlen($payload->password->value),
        ]);

        // Lookup the user by email or return a json error response status 401
        try {
            $user = $this->userService->getUserByEmail($payload->email);
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
                'email' => $payload->email->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Invalid displayName or password',
            ], StatusCode::UNAUTHORIZED);
        }

        // Check user status
        if ($user->getStatus()?->value !== 'active') {
            $this->logger->info('Authentication failed: user not active', [
                'email'  => $payload->email->value,
                'status' => $user->getStatus()?->value,
            ]);

            return $this->renderJson($response, [
                'message' => 'Account is not active',
            ], StatusCode::UNAUTHORIZED);
        }

        // Set session data
        $_SESSION['user'] = [
            'creatorName' => $user->getCreatorName(),
            'displayName' => $user->getDisplayName(),
            'email'       => $user->getEmail(),
            'firstName'   => $user->getFirstName(),
            'id'          => $user->getId(),
            'lastName'    => $user->getLastName(),
            'roles'       => $user->getRoles(),
        ];

        $this->logger->info('Authentication successful', [
            'displayName' => $user->getDisplayName(),
            'user_id'     => $user->getId(),
        ]);

        return $this->renderJson($response, [
            'message' => 'Authentication successful',
            'status'  => 'success',
            'user'    => [
                'creatorName' => $user->getCreatorName(),
                'displayName' => $user->getDisplayName(),
                'email'       => $user->getEmail(),
                'firstName'   => $user->getFirstName(),
                'id'          => $user->getId(),
                'lastName'    => $user->getLastName(),
                'roles'       => $user->getRoles(),
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
    public function validateSession(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- Slim route signature
    {
        $sessionUser = is_array($_SESSION['user'] ?? null) ? $_SESSION['user'] : [];
        $data        = [
            'creatorName' => $sessionUser['creatorName'] ?? null,
            'displayName' => $sessionUser['displayName'] ?? null,
            'email'       => $sessionUser['email'] ?? null,
            'firstName'   => $sessionUser['firstName'] ?? null,
            'id'          => $sessionUser['id'] ?? null,
            'lastName'    => $sessionUser['lastName'] ?? null,
            'roles'       => $sessionUser['roles'] ?? null,
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
    public function logout(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- Slim route signature
    {
        $sessionUser = is_array($_SESSION['user'] ?? null) ? $_SESSION['user'] : [];
        $userId      = $sessionUser['id'] ?? null;
        $displayName = $sessionUser['displayName'] ?? null;

        // Clear session data
        $_SESSION = [];

        // Destroy the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->logger->info('User logged out', [
            'displayName' => $displayName,
            'user_id'     => $userId,
        ]);

        return $this->renderJson($response, [
            'message' => 'Logged out successfully',
            'status'  => 'success',
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
            assert($payload instanceof RegistrationRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        // Log the registration attempt
        $this->logger->debug('Registration attempt', [
            'displayName' => $payload->displayName->value,
            'email'       => $payload->email->value,
            'first_name'  => $payload->firstName->value,
            'last_name'   => $payload->lastName->value,
        ]);

        // Validate password confirmation
        if ($payload->password->value !== $payload->confirmPassword->value) {
            return $this->renderJson($response, [
                'fields'  => [
                    'confirm_password' => ['Passwords do not match'],
                ],
                'message' => 'Passwords do not match',
                'status'  => 'error',
            ], StatusCode::BAD_REQUEST);
        }

        // Check if displayName already exists
        if ($this->userService->displayNameExists($payload->displayName)) {
            $this->logger->info('Registration failed: displayName already exists', [
                'email' => $payload->email->value,
            ]);

            return $this->renderJson($response, [
                'fields'  => [
                    'displayName' => ['This displayName is already taken'],
                ],
                'message' => 'This displayName is already taken',
                'status'  => 'error',
            ], StatusCode::CONFLICT);
        }

        // Check if email already exists
        if ($this->userService->emailExists($payload->email)) {
            $this->logger->info('Registration failed: email already exists', [
                'email' => $payload->email->value,
            ]);

            return $this->renderJson($response, [
                'fields'  => [
                    'email' => ['This email is already registered'],
                ],
                'message' => 'An account with this email already exists',
                'status'  => 'error',
            ], StatusCode::CONFLICT);
        }

        // Use the provided displayName
        $displayName = $payload->displayName->value;

        // Create the user
        $user = new User(
            id:                  null,
            displayName:         $displayName,
            passwordHash:        password_hash($payload->password->value, PASSWORD_DEFAULT),
            email:               $payload->email->value,
            firstName:           $payload->firstName->value,
            lastName:            $payload->lastName->value,
            status:              UserStatus::PENDING,
            roles:               'user',
            failedLoginAttempts: 0,
            lastFailedLogin:     null,
            lastLogin:           null,
            createdAt:           new DateTime(),
            updatedAt:           new DateTime(),
        );

        try {
            $userId = $this->userService->createUser($user);

            $this->logger->info('User registration successful', [
                'display_name' => $payload->displayName->value,
                'email'        => $payload->email->value,
                'first_name'   => $payload->firstName->value,
                'last_name'    => $payload->lastName->value,
                'user_id'      => $userId,
            ]);

            // Generate confirmation token
            $token     = bin2hex(random_bytes(32));
            $expiresAt = new DateTime('+24 hours');

            $confirmationToken = new EmailConfirmationToken(
                id:        null,
                userId:    $userId,
                token:     $token,
                expiresAt: $expiresAt,
                createdAt: new DateTime(),
                usedAt:    null,
            );

            try {
                $this->tokenService->createToken($confirmationToken);
            } catch (Exception $e) {
                $this->logger->error('Failed to create confirmation token', [
                    'error'   => $e->getMessage(),
                    'user_id' => $userId,
                ]);
            }

            // Send confirmation email
            try {
                $confirmationUrl = ((getenv('APP_URL') ?: 'http://127.0.0.1:5173')) . '/confirm-email?token=' . $token;

                $this->emailService->sendRegistrationConfirmation(
                    $payload->email->value,
                    $payload->firstName->value,
                    $payload->lastName->value,
                    $confirmationUrl,
                );
            } catch (Exception $e) {
                // Log the error but don't fail the registration
                $this->logger->error('Failed to send registration email', [
                    'email'   => $payload->email->value,
                    'error'   => $e->getMessage(),
                    'user_id' => $userId,
                ]);
            }

            return $this->renderJson($response, [
                'message' => 'Registration successful! Please check your email for confirmation.',
                'status'  => 'success',
                'userId'  => $userId,
            ], StatusCode::CREATED);
        } catch (Exception $e) {
            $this->logger->error('User registration failed', [
                'email' => $payload->email->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderJson($response, [
                'message' => 'Registration failed. Please try again later.',
                'status'  => 'error',
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
            'origin'   => $origin,
            'referrer' => $request->getHeaderLine('Referer'),
        ]);

        return $response
        ->withHeader('Access-Control-Allow-Origin', $origin) // Or specify your allowed origin
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withStatus(204);
    }
}
