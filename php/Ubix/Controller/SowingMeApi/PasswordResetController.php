<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Exception\DtoException;
use Ubix\Payload\Request\PasswordResetConfirmPayload;
use Ubix\Payload\Request\PasswordResetRequestPayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\EmailService;
use Ubix\Service\JsonService;
use Ubix\Service\PasswordResetTokenService;
use Ubix\Service\UserService;

/**
 * Password reset request + confirm endpoints (authentication TDS §4)
 *
 * @see \Ubix\Tests\Controller\SowingMeApi\PasswordResetControllerTest PHPUnit test case
 */
final class PasswordResetController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger                    $logger       The Monolog logger
     * @param TemplateRenderer          $view         The template renderer
     * @param JsonService               $jsonService  The JSON service
     * @param UserService               $userService  The user service
     * @param PasswordResetTokenService $tokenService The reset token service
     * @param EmailService              $emailService The email service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected UserService $userService,
        protected PasswordResetTokenService $tokenService,
        protected EmailService $emailService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * Issue a reset token and email the link — the response is identical
     * whether or not the email exists (FR-30, no user enumeration)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function request(Request $request, Response $response): Response
    {
        try {
            $payload = PasswordResetRequestPayload::getRequest($request);
            assert($payload instanceof PasswordResetRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        try {
            $user   = $this->userService->getUserByEmail($payload->email);
            $userId = $user->getId();
            assert($userId !== null);

            $rawToken = $this->tokenService->issueToken($userId);
            $resetUrl = (getenv('APP_URL') ?: 'http://127.0.0.1:5173') . '/reset-password?token=' . $rawToken;

            $this->emailService->sendPasswordReset(
                $payload->email->value,
                $user->getFirstName() ?? '',
                $resetUrl,
            );
        } catch (Exception $e) {
            // Unknown email: fall through to the identical response (FR-30)
            $this->logger->info('Password reset requested for unknown email', [
                'email' => $payload->email->value,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->renderJson($response, [
            'message' => 'If that email is registered, a reset link is on its way',
            'status'  => 'success',
        ]);
    }

    /**
     * Consume a valid token and set the new password (FR-32/33/34)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function confirm(Request $request, Response $response): Response
    {
        try {
            $payload = PasswordResetConfirmPayload::getRequest($request);
            assert($payload instanceof PasswordResetConfirmPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        if ($payload->password->value !== $payload->confirmPassword->value) {
            return $this->renderJson($response, [
                'message' => 'Passwords do not match',
                'status'  => 'error',
            ], StatusCode::BAD_REQUEST);
        }

        $token = $this->tokenService->getValidToken($payload->token);
        if ($token === null) {
            return $this->renderJson($response, [
                'message' => 'This reset link is invalid or has expired — please request a new one',
                'status'  => 'error',
            ], StatusCode::BAD_REQUEST);
        }

        $tokenUserId = $token->getUserId();
        assert($tokenUserId !== null);

        $this->userService->changePassword($tokenUserId, $payload->password->value);
        $this->tokenService->consumeToken($token);

        $this->logger->info('Password reset completed', ['user_id' => $tokenUserId]);

        // Deliberately NOT logging the user in (FR-34)
        return $this->renderJson($response, [
            'message' => 'Password updated — you can now log in',
            'status'  => 'success',
        ]);
    }
}
