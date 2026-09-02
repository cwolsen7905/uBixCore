<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface as TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Service to handle email sending via Symfony Mailer
 *
 * @see \Ubix\Tests\Service\EmailServiceTest PHPUnit test case
 */
final class EmailService
{
    /**
     * Constructor
     *
     * @param Logger          $logger Logger
     * @param MailerInterface $mailer Symfony Mailer
     */
    public function __construct(
        private Logger $logger,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * Send a registration confirmation email with confirmation link
     *
     * @param string $toEmail         The recipient email address
     * @param string $firstName       The recipient's first name
     * @param string $lastName        The recipient's last name
     * @param string $confirmationUrl The confirmation URL with token
     *
     * @return bool True if email was sent successfully
     *
     * @throws Exception If email sending fails
     */
    public function sendRegistrationConfirmation(
        string $toEmail,
        string $firstName,
        string $lastName,
        string $confirmationUrl,
    ): bool {
        try {
            $email = (new Email())
                ->from('no-reply@ubixstudios.com')
                ->to($toEmail)
                ->subject('Welcome to SowingMe - Confirm Your Email')
                ->html($this->getRegistrationEmailHtml($firstName, $lastName, $confirmationUrl))
                ->text($this->getRegistrationEmailText($firstName, $lastName, $confirmationUrl));

            $this->mailer->send($email);

            $this->logger->info('Registration confirmation email sent', [
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'to'         => $toEmail,
            ]);

            return true;
        } catch (TransportException $e) {
            $this->logger->error('Failed to send registration confirmation email', [
                'error' => $e->getMessage(),
                'to'    => $toEmail,
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception('Failed to send confirmation email: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Send the password reset link
     *
     * @param string $toEmail   Recipient address
     * @param string $firstName Recipient first name (may be empty)
     * @param string $resetUrl  The single-use reset URL
     *
     * @return bool True when the message was handed to the mailer
     */
    public function sendPasswordReset(
        string $toEmail,
        string $firstName,
        string $resetUrl,
    ): bool {
        try {
            $greeting = $firstName !== '' ? 'Hi ' . $firstName : 'Hi';
            $email    = (new Email())
                ->from('no-reply@ubixstudios.com')
                ->to($toEmail)
                ->subject('SowingMe - Reset Your Password')
                ->html('<p>' . htmlspecialchars($greeting) . ',</p><p>We received a request to reset your password. This link is valid for one hour and can be used once:</p><p><a href="' . htmlspecialchars($resetUrl) . '">Reset your password</a></p><p>If you did not request this, you can ignore this email.</p>')
                ->text($greeting . ",\n\nWe received a request to reset your password. This link is valid for one hour and can be used once:\n\n" . $resetUrl . "\n\nIf you did not request this, you can ignore this email.\n");

            $this->mailer->send($email);
            $this->logger->info('Password reset email sent', ['to' => $toEmail]);

            return true;
        } catch (TransportException $e) {
            $this->logger->error('Password reset email failed', [
                'error' => $e->getMessage(),
                'to'    => $toEmail,
            ]);

            return false;
        }
    }

    /**
     * Get HTML content for registration email
     *
     * @param string $firstName       The recipient's first name
     * @param string $lastName        The recipient's last name
     * @param string $confirmationUrl The confirmation URL with token
     *
     * @return string The HTML email content
     */
    private function getRegistrationEmailHtml(string $firstName, string $lastName, string $confirmationUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background-color: #4A90E2;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .content {
                    background-color: #f9f9f9;
                    padding: 30px;
                    border: 1px solid #ddd;
                    border-radius: 0 0 5px 5px;
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background-color: #4A90E2;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Welcome to SowingMe!</h1>
            </div>
            <div class="content">
                <p>Hello {$firstName} {$lastName},</p>

                <p>Thank you for registering with SowingMe! We're excited to have you join our community.</p>

                <p>Please confirm your email address by clicking the button below:</p>

                <p style="text-align: center;">
                    <a href="{$confirmationUrl}" class="button">Confirm Your Email</a>
                </p>

                <p>Or copy and paste this link into your browser:</p>
                <p style="word-break: break-all; color: #4A90E2;">{$confirmationUrl}</p>

                <p>This link will expire in 24 hours for security reasons.</p>

                <p>If you did not create this account, please ignore this email.</p>

                <p>Best regards,<br>
                The SowingMe Team</p>
            </div>
            <div class="footer">
                <p>&copy; 2025 Ubix Studios. All rights reserved.</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get plain text content for registration email
     *
     * @param string $firstName       The recipient's first name
     * @param string $lastName        The recipient's last name
     * @param string $confirmationUrl The confirmation URL with token
     *
     * @return string The plain text email content
     */
    private function getRegistrationEmailText(string $firstName, string $lastName, string $confirmationUrl): string
    {
        return <<<TEXT
        Welcome to SowingMe!

        Hello {$firstName} {$lastName},

        Thank you for registering with SowingMe! We're excited to have you join our community.

        Please confirm your email address by clicking the link below:

        {$confirmationUrl}

        This link will expire in 24 hours for security reasons.

        If you did not create this account, please ignore this email.

        Best regards,
        The SowingMe Team

        ---
        © 2025 Ubix Studios. All rights reserved.
        TEXT;
    }
}
