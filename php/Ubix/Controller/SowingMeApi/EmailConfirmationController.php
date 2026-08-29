<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\DataType\Int\UserId;
use Ubix\Enum\StatusCode;
use Ubix\Enum\User\UserStatus;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as EmailConfirmationTokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as EmailConfirmationTokenWriter;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;
use Ubix\Service\JsonService;

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
     * @param Logger                       $logger      The Monolog logger
     * @param TemplateRenderer             $view        The template renderer
     * @param JsonService                  $jsonService The JSON service
     * @param EmailConfirmationTokenReader $tokenReader The email confirmation token reader
     * @param EmailConfirmationTokenWriter $tokenWriter The email confirmation token writer
     * @param UserReader                   $userReader  The user reader
     * @param UserWriter                   $userWriter  The user writer
     *
     * @return void
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected EmailConfirmationTokenReader $tokenReader,
        protected EmailConfirmationTokenWriter $tokenWriter,
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
        $token       = $queryParams['token'] ?? null;

        if (!$token) {
            return $this->renderJson($response, [
                'message' => 'Confirmation token is required',
                'status'  => 'error',
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
                    'message' => 'Invalid confirmation token',
                    'status'  => 'error',
                ], StatusCode::BAD_REQUEST);
            }

            // Check if token is already used
            if ($confirmationToken->getUsedAt() !== null) {
                $this->logger->info('Email confirmation failed: token already used', [
                    'token_id' => $confirmationToken->getId(),
                    'user_id'  => $confirmationToken->getUserId(),
                ]);

                return $this->renderJson($response, [
                    'message' => 'This confirmation link has already been used',
                    'status'  => 'error',
                ], StatusCode::BAD_REQUEST);
            }

            // Check if token is expired
            $now = new DateTime();
            if ($confirmationToken->getExpiresAt() < $now) {
                $this->logger->info('Email confirmation failed: token expired', [
                    'expires_at' => $confirmationToken->getExpiresAt()?->format('Y-m-d H:i:s'),
                    'token_id'   => $confirmationToken->getId(),
                    'user_id'    => $confirmationToken->getUserId(),
                ]);

                return $this->renderJson($response, [
                    'message' => 'This confirmation link has expired. Please request a new one.',
                    'status'  => 'error',
                ], StatusCode::BAD_REQUEST);
            }

            // Get the user
            $tokenId     = $confirmationToken->getId();
            $tokenUserId = $confirmationToken->getUserId();
            assert($tokenId !== null && $tokenUserId !== null);

            $user = $this->userReader->getUserById(new UserId($tokenUserId));

            // Check if user is already active
            if ($user->getStatus() === UserStatus::ACTIVE) {
                $this->logger->info('Email confirmation: user already active', [
                    'user_id' => $user->getId(),
                ]);

                // Mark token as used
                $this->tokenWriter->markTokenAsUsed($tokenId);

                // Auto-login the user
                $_SESSION['user'] = [
                    'displayName' => $user->getDisplayName(),
                    'email'       => $user->getEmail(),
                    'firstName'   => $user->getFirstName(),
                    'id'          => $user->getId(),
                    'lastName'    => $user->getLastName(),
                    'roles'       => $user->getRoles(),
                ];

                return $this->renderJson($response, [
                    'message' => 'Your account is already confirmed',
                    'status'  => 'success',
                    'user'    => [
                        'displayName' => $user->getDisplayName(),
                        'email'       => $user->getEmail(),
                        'firstName'   => $user->getFirstName(),
                        'id'          => $user->getId(),
                        'lastName'    => $user->getLastName(),
                    ],
                ]);
            }

            // Update user status to active
            $user->setStatus(UserStatus::ACTIVE);
            $this->userWriter->updateUser($user);

            // Mark token as used
            $this->tokenWriter->markTokenAsUsed($tokenId);

            // Auto-login the user
            $_SESSION['user'] = [
                'displayName' => $user->getDisplayName(),
                'email'       => $user->getEmail(),
                'firstName'   => $user->getFirstName(),
                'id'          => $user->getId(),
                'lastName'    => $user->getLastName(),
                'roles'       => $user->getRoles(),
            ];

            $this->logger->info('Email confirmation successful', [
                'email'    => $user->getEmail(),
                'token_id' => $confirmationToken->getId(),
                'user_id'  => $user->getId(),
            ]);

            return $this->renderJson($response, [
                'message' => 'Your email has been confirmed successfully!',
                'status'  => 'success',
                'user'    => [
                    'displayName' => $user->getDisplayName(),
                    'email'       => $user->getEmail(),
                    'firstName'   => $user->getFirstName(),
                    'id'          => $user->getId(),
                    'lastName'    => $user->getLastName(),
                ],
            ]);
        } catch (Exception $e) {
            $this->logger->error('Email confirmation failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 10) . '...',
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderJson($response, [
                'message' => 'Email confirmation failed. Please try again later.',
                'status'  => 'error',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
