<?php

declare(strict_types=1);

namespace Ubix\Service\Email;

use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\EmailAccount;
use Ubix\Enum\Email\EmailContentType;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Service\Email\EmailServiceInterface as EmailService;

/**
 * Service to manage emails using PHP's mail() function
 *
 * @see \Ubix\Tests\Service\Email\PhpMailFunctionEmailServiceTest PHPUnit test case
 */
final class PhpMailFunctionEmailService implements EmailService // NOT_IMPLEMENTED: The legacy system has a concept of "priority" that is an integer (1 = wish, 2 = low, 3 = normal, 4 = high) with a default value of 3
{
    private const CHARSET_DEFAULT = 'iso-8859-1';  // This was copy/pasted from PHPCoreClasses/core/notifications/EmailAlert.cl on 2025-08-29

    private const CONTENT_TRANSFER_ENCODING_DEFAULT = '8bit'; // This was copy/pasted from PHPCoreClasses/core/notifications/EmailAlert.cl on 2025-08-29

    private const MIME_VERSION_DEFAULT = '1.0'; // This was copy/pasted from PHPCoreClasses/core/notifications/EmailAlert.cl on 2025-08-29

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException When an invalid parameter is passed in
     * @throws LogicException When an unimplemented feature is invoked
     */
    public function send(
        EmailAccount $from,
        array|EmailAccount $to,
        string $subject,
        ?string $textBody = null,
        ?string $htmlBody = null,
        ?EmailAccount $replyTo = null,
        array $additionalHeaders = [],
    ): void {
        //
        //  Validate parameters
        //
        if (!filter_var($from->emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The `' . $from->emailAddress . '` from email address is invalid', ExceptionCode::INVALID_FROM_EMAIL_ADDRESS->value);
        }

        if (!is_array($to)) {
            $to = [$to];
        }

        foreach ($to as $recipient) {
            if (!filter_var($recipient->emailAddress, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('The `' . $recipient->emailAddress . '` recipient email address is invalid', ExceptionCode::INVALID_TO_EMAIL_ADDRESS->value);
            }
        }

        if (trim($subject) === '') {
            throw new InvalidArgumentException('The email subject can not be blank', ExceptionCode::INVALID_EMAIL_SUBJECT->value);
        }

        $textBodyIsValid = $textBody !== null && trim($textBody) !== '';
        $htmlBodyIsValid = $htmlBody !== null && trim($htmlBody) !== '';

        if (!$textBodyIsValid && !$htmlBodyIsValid) {
            throw new InvalidArgumentException('You must include either a text or HTML body', ExceptionCode::INVALID_EMAIL_BODY->value);
        } elseif ($textBodyIsValid && $htmlBodyIsValid) {
            throw new InvalidArgumentException('You must include either a text or HTML body but not both', ExceptionCode::MULTIPLE_VALID_EMAIL_BODIES->value);
        }

        if ($replyTo !== null && !filter_var($replyTo->emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The `' . $replyTo->emailAddress . '` reply-to email address is invalid', ExceptionCode::INVALID_REPLY_TO_EMAIL_ADDRESS->value);
        }

        $recipients        = $this->getRfc5322FromDto($to);
        $message           = $textBodyIsValid ? $textBody : $htmlBody;
        $additionalHeaders = array_merge( // Merge default headers, then any additional headers that were passed in then finally a Reply-To header if a reply-to email account was passed in
            [
                'charset'                   => self::CHARSET_DEFAULT,
                'Content-Transfer-Encoding' => self::CONTENT_TRANSFER_ENCODING_DEFAULT,
                'Content-type'              => $textBodyIsValid ? EmailContentType::PLAIN_TEXT->value : EmailContentType::HTML->value,
                'From'                      => $this->getRfc5322FromDto($from),
                'MIME-Version'              => self::MIME_VERSION_DEFAULT,
            ],
            $additionalHeaders,
            $replyTo !== null ? ['Reply-To' => $this->getRfc5322FromDto($replyTo)] : [],
        );

        if (mail($recipients, $subject, $message, $additionalHeaders)) {
            // NOT_IMPLEMENTED: handle a successful send
            throw new LogicException('Email sent successfully - NOT_IMPLEMENTED');
        } else {
            // NOT_IMPLEMENTED: handle a failed send
            throw new LogicException('Email failed to send - NOT_IMPLEMENTED');
        }
    }

    /**
     * Get an RFC 5322 formatted string from an EmailAccount DTO('s)
     *
     * @param EmailAccount[]|EmailAccount $emailAccount The DTO or an array of DTO's
     *
     * @return string The RFC 5322 formatted string
     */
    private function getRfc5322FromDto(array|EmailAccount $emailAccount): string // NOT_IMPLEMENTED: should this go away and instead we change EmailAccount from a DTO to a model and do this inside the model?
    {
        return is_array($emailAccount) ? implode(', ', array_map([$this, 'getRfc5322FromDto'], $emailAccount)) : trim(($emailAccount->name ?? '') . ' <' . $emailAccount->emailAddress . '>');
    }
}
