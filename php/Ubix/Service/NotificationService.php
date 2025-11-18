<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Enum\Message\MessageFolderId;
use Ubix\Model\Message;
use Ubix\Repository\Message\MessageWriterInterface as MessageWriter;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;

/**
 * Service to handle notifications (including messages)
 *
 * @see \Ubix\Tests\Service\NotificationServiceTest PHPUnit test case
 */
final class NotificationService
{
    /**
     * Constructor
     *
     * @param Logger             $logger             Logger
     * @param MessageWriter      $messageWriter      Message writer
     * @param PerformerReader    $performerReader    Performer reader
     * @param PlatformUserReader $platformUserReader Platform user reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private MessageWriter $messageWriter,
        private PerformerReader $performerReader,
        private PlatformUserReader $platformUserReader,
    ) {
    }

    /**
     * Send a message
     *
     * @param string    $title         The message subject
     * @param string    $body          The message body
     * @param string    $recipientType The recipient account type
     * @param int|int[] $recipientIds  The recipient account ID (or an array of account ID's)
     * @param string    $senderIp      The sender's IP address
     * @param string    $senderType    The sender account type
     * @param int       $senderId      The sender account ID, (default: \Ubix\Model\Message::SENDER_ID_SYSTEM)
     *
     * @throws Exception If an account ID is not found
     * @throws InvalidArgumentException When an invalid parameter is passed
     * @throws LogicException If unimplemented features are used
     *
     * @return void
     */
    public function sendMessage(
        string $title,
        string $body,
        string $recipientType,
        int|array $recipientIds,
        string $senderIp,
        string $senderType,
        int $senderId = Message::SENDER_ID_SYSTEM,
    ): void {
        //
        //  Validate parameters
        //
        if (trim($title) === '') {
            throw new InvalidArgumentException('You must enter a title.', ExceptionCode::MISSING_TITLE_FOR_NOTIFICATION->value);
        }

        if (trim($body) === '') {
            throw new InvalidArgumentException('You must enter a message.', ExceptionCode::MISSING_MESSAGE_FOR_NOTIFICATION->value);
        }

        if (!in_array($recipientType, Message::RECIPIENT_TYPES_VALID, true)) {
            throw new InvalidArgumentException('You must use a valid recipient type.', ExceptionCode::INVALID_RECIPIENT_TYPE_FOR_NOTIFICATION->value);
        }

        if (is_array($recipientIds)) {
            foreach ($recipientIds as $recipientId) {
                if (!is_int($recipientId)) {
                    throw new InvalidArgumentException('You must use exclusively integers for recipient ID\'s.', ExceptionCode::INVALID_RECIPIENT_ID_FOR_NOTIFICATION->value);
                }
            }
        }

        if (!filter_var($senderIp, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('You must use a valid sender IP address.', ExceptionCode::INVALID_SENDER_IP_FOR_NOTIFICATION->value);
        }

        if (!in_array($senderType, Message::SENDER_TYPES_VALID, true)) {
            throw new InvalidArgumentException('You must use a valid sender type.', ExceptionCode::INVALID_SENDER_TYPE_FOR_NOTIFICATION->value);
        }

        if ($senderId !== Message::SENDER_ID_SYSTEM && $senderType === 'system') {
            throw new InvalidArgumentException('You must not include a sender ID for the `system` sender type.', ExceptionCode::INVALID_SENDER_ID_FOR_NOTIFICATION->value);
        }

        //
        //  If a single integer was passed as a recipient ID then convert to an array
        //
        if (!is_array($recipientIds)) {
            $recipientIds = [$recipientIds];
        }

        //
        //  Loop through the recipient(s) and send a message to each
        //
        foreach ($recipientIds as $recipientId) {
            $recipientUserList  = '';
            $recipientModelList = '';

            switch ($recipientType) {
                case 'user':
                    $platformUser = $this->platformUserReader->getById($recipientId);
                    if (count($platformUser) > 0) {
                        $recipientUserList = (string)$recipientId . ':' . $platformUser[0]->getScreenName();
                    } else {
                        throw new Exception('User not found with ID `' . $recipientId . '`', ExceptionCode::NO_MATCHES_FOR_RECIPIENT_PLATFORM_USER->value);
                    }
                    break;

                case 'model':
                    $performer = $this->performerReader->getById($recipientId);
                    if (count($performer) > 0) {
                        $recipientModelList = $recipientId . ':' . $performer[0]->getName();
                    } else {
                        throw new Exception('Model not found with ID `' . $recipientId . '`', ExceptionCode::NO_MATCHES_FOR_RECIPIENT_PERFORMER->value);
                    }
                    break;
            }

            //
            //  Create a new Message object and save it
            //
            $message = new Message(
                body:               $body,
                folderId:           MessageFolderId::INBOX,
                isRead:             0,
                originalSenderId:   $senderId,
                originalSenderType: $senderType,
                recipientId:        $recipientId,
                recipientModelList: $recipientModelList,
                recipientUserList:  $recipientUserList,
                senderId:           $senderId,
                senderIp:           $senderType === 'system' ? '' : $senderIp,
                senderName:         $senderType === 'system' ? Message::SENDER_NAME_SYSTEM : 'NOT_IMPLEMENTED',
                senderType:         $senderType,
                subject:            $title,
                dateCreated:        new DateTime(),
            );

            if ($senderType !== 'system') { // Not yet implemented
                throw new LogicException('MESSAGES HAVE ONLY BEEN CODED (SO FAR) TO SEND AS SYSTEM, MORE WORK IS NEEDED TO SUPPORT OTHER SENDER TYPES'); // NOT_IMPLEMENTED: needs to be done
            }

            $this->messageWriter->save($message);
        }
    }
}
