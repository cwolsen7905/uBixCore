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
use Ubix\Renderer\TemplateRenderer;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as TokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as TokenWriter;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;
use Ubix\Service\JsonService;
use Ubix\DataType\Int\UserId;

/**
 * Controller to handle email confirmation
 *
 * @see \Ubix\Tests\Controller\SowingMeApi\EmailConfirmationControllerTest PHPUnit test case
 */
final class EmailConfirmationController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger             $logger             The Monolog logger
     * @param TemplateRenderer   $view               The template renderer
     * @param JsonService        $jsonService        The JSON service
     * @param TokenReader        $tokenReader        The email confirmation token reader
     * @param TokenWriter        $tokenWriter        The email confirmation token writer
     * @param UserReader         $userReader         The user reader
     * @param UserWriter         $userWriter         The user writer
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected TokenReader $tokenReader,
        protected TokenWriter $tokenWriter,
        protected UserReader $userReader,
        protected UserWriter $userWriter,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Confirm email address using token
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function confirmEmail(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $token = $queryParams['token'] ?? null;

        if (!$token) {
            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'Confirmation token is required',
            ], StatusCode::BAD_REQUEST);
        }

        try {
            // Look up the token
            $confirmationToken = $this->tokenReader->getTokenByString($token);

            if ($confirmationToken === null) {
                $this->logger->info('Email confirmation failed: token not found', [
                    'token' => substr($token, 0, 10) . '...',
                ]);

                return $this->renderJson($response, [
                    'status'  => 'error',
                    'message' => 'Invalid confirmation token',
                ], StatusCode::BAD_REQUEST);
            }

            // Check if token is already used
            if ($confirmationToken->getUsedAt() !== null) {
                $this->logger->info('Email confirmation failed: token already used', [
                    'token_id' => $confirmationToken->getId(),
                    'user_id'  => $confirmationToken->getUserId(),
                ]);

                return $this->renderJson($response, [
                    'status'  => 'error',
                    'message' => 'This confirmation link has already been used',
                ], StatusCode::BAD_REQUEST);
            }

            // Check if token is expired
            $now = new DateTime();
            if ($confirmationToken->getExpiresAt() < $now) {
                $this->logger->info('Email confirmation failed: token expired', [
                    'token_id'   => $confirmationToken->getId(),
                    'user_id'    => $confirmationToken->getUserId(),
                    'expires_at' => $confirmationToken->getExpiresAt()->format('Y-m-d H:i:s'),
                ]);

                return $this->renderJson($response, [
                    'status'  => 'error',
                    'message' => 'This confirmation link has expired. Please request a new one.',
                ], StatusCode::BAD_REQUEST);
            }

            // Get the user
            $user = $this->userReader->getUserById(new UserId($confirmationToken->getUserId()));

            // Check if user is already active
            if ($user->getStatus() === UserStatus::ACTIVE) {
                $this->logger->info('Email confirmation: user already active', [
                    'user_id' => $user->getId(),
                ]);

                // Mark token as used
                $this->tokenWriter->markTokenAsUsed($confirmationToken->getId());

                // Auto-login the user
                $_SESSION['user'] = [
                    'id'        => $user->getId(),
                    'displayName'  => $user->getDisplayName(),
                    'email'     => $user->getEmail(),
                    'roles'     => $user->getRoles(),
                    'firstName' => $user->getFirstName(),
                    'lastName'  => $user->getLastName(),
                ];

                return $this->renderJson($response, [
                    'status'  => 'success',
                    'message' => 'Your account is already confirmed',
                    'user'    => [
                        'id'        => $user->getId(),
                        'displayName'  => $user->getDisplayName(),
                        'email'     => $user->getEmail(),
                        'firstName' => $user->getFirstName(),
                        'lastName'  => $user->getLastName(),
                    ],
                ]);
            }

            // Update user status to active
            $user->setStatus(UserStatus::ACTIVE);
            $this->userWriter->updateUser($user);

            // Mark token as used
            $this->tokenWriter->markTokenAsUsed($confirmationToken->getId());

            // Auto-login the user
            $_SESSION['user'] = [
                'id'        => $user->getId(),
                'displayName'  => $user->getDisplayName(),
                'email'     => $user->getEmail(),
                'roles'     => $user->getRoles(),
                'firstName' => $user->getFirstName(),
                'lastName'  => $user->getLastName(),
            ];

            $this->logger->info('Email confirmation successful', [
                'user_id'  => $user->getId(),
                'email'    => $user->getEmail(),
                'token_id' => $confirmationToken->getId(),
            ]);

            return $this->renderJson($response, [
                'status'  => 'success',
                'message' => 'Your email has been confirmed successfully!',
                'user'    => [
                    'id'        => $user->getId(),
                    'displayName'  => $user->getDisplayName(),
                    'email'     => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName'  => $user->getLastName(),
                ],
            ]);
        } catch (Exception $e) {
            $this->logger->error('Email confirmation failed', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderJson($response, [
                'status'  => 'error',
                'message' => 'Email confirmation failed. Please try again later.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
