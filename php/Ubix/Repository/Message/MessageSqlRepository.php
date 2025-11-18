<?php

declare(strict_types=1);

namespace Ubix\Repository\Message;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Message;
use Ubix\Repository\Message\MessageWriterInterface as MessageWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for writing message data
 *
 * @see \Ubix\Tests\Repository\Message\MessageSqlRepositoryTest PHPUnit test case
 */
final class MessageSqlRepository implements MessageWriter
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function save(Message $message): void
    {
        if ($message->getId() !== null && $message->getId() > 0) { // If there is a valid ID then update the database
            $sql = 'UPDATE ' . $this->getTableName($message) . ' SET
					sender_type          = :senderType, 
					sender_id            = :senderId,
					sender_name          = :senderName,
					sender_ip            = :senderIp,
					original_sender_id   = :originalSenderId,
					original_sender_type = :originalSenderType,
					recipient_user_list  = :recipientUserList,
					recipient_model_list = :recipientModelList,
					folder_id            = :folderId, 
					recipient_id         = :recipientId,
					is_read              = :isRead,
					subject              = :subject,
					body                 = :body,
					status               = :status,
					sender_message_id    = :senderMessageId,
					original_message_id  = :originalMessageId,
					date_created         = :dateCreated
                    WHERE id = :id';

            $this->sqlService->query($sql, [
                'body'               => $message->getBody(),
                'dateCreated'        => $message->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'folderId'           => $message->getFolderId()?->value,
                'id'                 => $message->getId(),
                'isRead'             => $message->getIsRead(),
                'originalMessageId'  => $message->getOriginalMessageId(),
                'originalSenderId'   => $message->getOriginalSenderId(),
                'originalSenderType' => $message->getOriginalSenderType(),
                'recipientId'        => $message->getRecipientId(),
                'recipientModelList' => $message->getRecipientModelList(),
                'recipientUserList'  => $message->getRecipientUserList(),
                'senderId'           => $message->getSenderId(),
                'senderIp'           => $message->getSenderIp(),
                'senderMessageId'    => $message->getSenderMessageId(),
                'senderName'         => $message->getSenderName(),
                'senderType'         => $message->getSenderType(),
                'status'             => $message->getStatus(),
                'subject'            => $message->getSubject(),
            ]);
        } else { // There is no valid ID so insert into the database
            $sql = 'INSERT INTO ' . $this->getTableName($message) . ' SET
					sender_type          = :senderType,
					sender_id            = :senderId,
					sender_name          = :senderName,
					sender_ip            = :senderIp,
					original_sender_id   = :originalSenderId,
					original_sender_type = :originalSenderType,
					recipient_user_list  = :recipientUserList,
					recipient_model_list = :recipientModelList,
					folder_id            = :folderId, 
					recipient_id         = :recipientId,
					is_read              = :isRead,
					subject              = :subject,
					body                 = :body,
					date_created         = :dateCreated,
					status               = "pending"';

            $this->sqlService->query($sql, [
                'body'               => $message->getBody(),
                'dateCreated'        => $message->getDateCreated()?->format(DateTime::ISO8601_EXPANDED),
                'folderId'           => $message->getFolderId()?->value,
                'isRead'             => $message->getIsRead(),
                'originalSenderId'   => $message->getOriginalSenderId(),
                'originalSenderType' => $message->getOriginalSenderType(),
                'recipientId'        => $message->getRecipientId(),
                'recipientModelList' => $message->getRecipientModelList(),
                'recipientUserList'  => $message->getRecipientUserList(),
                'senderId'           => $message->getSenderId(),
                'senderIp'           => $message->getSenderIp(),
                'senderName'         => $message->getSenderName(),
                'senderType'         => $message->getSenderType(),
                'subject'            => $message->getSubject(),
            ]);

            //
            //  Now that we have inserted the row, we are able to update relevant columns to the new message ID and change the status to "sent" then save those updates too
            //
            $message->setId((int)$this->sqlService->lastInsertId());
            $message->setSenderMessageId($message->getId());
            $message->setOriginalMessageId($message->getId());
            $message->setStatus('sent');

            $this->save($message);
        }
    }

    /**
     * Get the database table for a message
     *
     * @param Message $message The message whose database table needs to be determined
     *
     * @throws LogicException If the message is missing a recipient ID
     *
     * @return string The database table name
     */
    private function getTableName(Message $message): string
    {
        //
        //  Add the database name
        //
        $tableName = 'MESSAGING.';

        //
        //  Add the table name
        //
        if ($message->getRecipientUserList() !== null && $message->getRecipientUserList() !== '') {
            $tableName .= 'User_Messages_';
        } else {
            $tableName .= 'Model_Messages_';
        }

        //
        //  Add the sharding suffix which is the second last digit of the receipient ID (or last digit if it's a single digit number)
        //
        $receipientId = $message->getRecipientId();
        if ($receipientId === null) {
            throw new LogicException('Message is missing a recipient ID.', ExceptionCode::MESSAGE_IS_MISSING_RECIPIENT_ID->value);
        }

        $receipientId = abs($receipientId); // Use abs to avoid any unexpected results from negative recipient ID's (even though recipient ID's should never be negative)
        return $tableName . ($receipientId < 10 ? $receipientId : intdiv($receipientId, 10) % 10);
    }
}
