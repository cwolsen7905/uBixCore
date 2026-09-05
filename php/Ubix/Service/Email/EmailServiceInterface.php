<?php

declare(strict_types=1);

namespace Ubix\Service\Email;

use Ubix\DataTransferObject\EmailAccount;

/**
 * Interface for a service to manage emails
 */
interface EmailServiceInterface
{
    /**
     * Send an email
     *
     * @param EmailAccount                $from              The email account to send the email from
     * @param EmailAccount[]|EmailAccount $to                The email account(s) to send the email to
     * @param string                      $subject           The email subject line
     * @param ?string                     $textBody          The plaintext email body (optional) (default: null)
     * @param ?string                     $htmlBody          The HTML email body (optional) (default: null)
     * @param ?EmailAccount               $replyTo           The email account to reply to (optional) (default: null)
     * @param array<string, string>       $additionalHeaders Additional email headers (optional) (default: [])
     *
     * @return void
     */
    public function send(
        EmailAccount $from,
        array|EmailAccount $to,
        string $subject,
        ?string $textBody = null,
        ?string $htmlBody = null,
        ?EmailAccount $replyTo = null,
        array $additionalHeaders = [],
    ): void;
}
