<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Enum\Message\MessageFolderId;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a message
 *
 * @see \Ubix\Tests\Model\MessageTest PHPUnit test case
 */
final class Message extends Model
{
    public const SENDER_ID_SYSTEM = 0;

    public const SENDER_NAME_SYSTEM = 'System';

    public const SENDER_TYPES_VALID = [
        'model',
        'user',
        'system',
    ];

    public const RECIPIENT_TYPES_VALID = [
        'model',
        'user',
    ];

    /**
     * Constructor
     *
     * @param ?int               $id                 The message's ID
     * @param ?string            $senderType         The sender's type
     * @param ?int               $senderId           The sender's ID
     * @param ?string            $senderName         The sender's name
     * @param ?int               $senderMessageId    The sender's message ID
     * @param ?string            $senderIp           The sender's IP
     * @param ?string            $originalSenderType The original sender's type
     * @param ?int               $originalSenderId   The original sender's ID
     * @param ?int               $originalMessageId  The original message's ID
     * @param ?int               $recipientId        The recipient's ID
     * @param ?string            $recipientUserList  The list of recipients (isers)
     * @param ?string            $recipientModelList The list of recipients (models)
     * @param ?MessageFolderId   $folderId           The folder's ID
     * @param ?int               $hasAttachments     Whether or not the message has attachments
     * @param ?int               $isRead             Whether or not the message has been read
     * @param ?string            $subject            The message's subject
     * @param ?string            $body               The message's body
     * @param ?DateTimeInterface $dateCreated        Timestamp of the message
     * @param ?string            $status             The message's status
     */
    public function __construct(
        private ?int $id = null,
        private ?string $senderType = null,
        private ?int $senderId = null,
        private ?string $senderName = null,
        private ?int $senderMessageId = null,
        private ?string $senderIp = null,
        private ?string $originalSenderType = null,
        private ?int $originalSenderId = null,
        private ?int $originalMessageId = null,
        private ?int $recipientId = null,
        private ?string $recipientUserList = null,
        private ?string $recipientModelList = null,
        private ?MessageFolderId $folderId = null,
        private ?int $hasAttachments = null,
        private ?int $isRead = null,
        private ?string $subject = null,
        private ?string $body = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?string $status = null,
    ) {
    }

    /**
     * Get the value of id
     *
     * @return ?int The value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id The value for id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of senderType
     *
     * @return ?string The value of senderType
     */
    public function getSenderType(): ?string
    {
        return $this->senderType;
    }

    /**
     * Set the value of senderType
     *
     * @param ?string $senderType The value for senderType
     *
     * @return void
     */
    public function setSenderType(?string $senderType): void
    {
        $this->senderType = $senderType;
    }

    /**
     * Get the value of senderId
     *
     * @return ?int The value of senderId
     */
    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    /**
     * Set the value of senderId
     *
     * @param ?int $senderId The value for senderId
     *
     * @return void
     */
    public function setSenderId(?int $senderId): void
    {
        $this->senderId = $senderId;
    }

    /**
     * Get the value of senderName
     *
     * @return ?string The value of senderName
     */
    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    /**
     * Set the value of senderName
     *
     * @param ?string $senderName The value for senderName
     *
     * @return void
     */
    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * Get the value of senderMessageId
     *
     * @return ?int The value of senderMessageId
     */
    public function getSenderMessageId(): ?int
    {
        return $this->senderMessageId;
    }

    /**
     * Set the value of senderMessageId
     *
     * @param ?int $senderMessageId The value for senderMessageId
     *
     * @return void
     */
    public function setSenderMessageId(?int $senderMessageId): void
    {
        $this->senderMessageId = $senderMessageId;
    }

    /**
     * Get the value of senderIp
     *
     * @return ?string The value of senderIp
     */
    public function getSenderIp(): ?string
    {
        return $this->senderIp;
    }

    /**
     * Set the value of senderIp
     *
     * @param ?string $senderIp The value for senderIp
     *
     * @return void
     */
    public function setSenderIp(?string $senderIp): void
    {
        $this->senderIp = $senderIp;
    }

    /**
     * Get the value of originalSenderType
     *
     * @return ?string The value of originalSenderType
     */
    public function getOriginalSenderType(): ?string
    {
        return $this->originalSenderType;
    }

    /**
     * Set the value of originalSenderType
     *
     * @param ?string $originalSenderType The value for originalSenderType
     *
     * @return void
     */
    public function setOriginalSenderType(?string $originalSenderType): void
    {
        $this->originalSenderType = $originalSenderType;
    }

    /**
     * Get the value of originalSenderId
     *
     * @return ?int The value of originalSenderId
     */
    public function getOriginalSenderId(): ?int
    {
        return $this->originalSenderId;
    }

    /**
     * Set the value of originalSenderId
     *
     * @param ?int $originalSenderId The value for originalSenderId
     *
     * @return void
     */
    public function setOriginalSenderId(?int $originalSenderId): void
    {
        $this->originalSenderId = $originalSenderId;
    }

    /**
     * Get the value of originalMessageId
     *
     * @return ?int The value of originalMessageId
     */
    public function getOriginalMessageId(): ?int
    {
        return $this->originalMessageId;
    }

    /**
     * Set the value of originalMessageId
     *
     * @param ?int $originalMessageId The value for originalMessageId
     *
     * @return void
     */
    public function setOriginalMessageId(?int $originalMessageId): void
    {
        $this->originalMessageId = $originalMessageId;
    }

    /**
     * Get the value of recipientId
     *
     * @return ?int The value of recipientId
     */
    public function getRecipientId(): ?int
    {
        return $this->recipientId;
    }

    /**
     * Set the value of recipientId
     *
     * @param ?int $recipientId The value for recipientId
     *
     * @return void
     */
    public function setRecipientId(?int $recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    /**
     * Get the value of recipientUserList
     *
     * @return ?string The value of recipientUserList
     */
    public function getRecipientUserList(): ?string
    {
        return $this->recipientUserList;
    }

    /**
     * Set the value of recipientUserList
     *
     * @param ?string $recipientUserList The value for recipientUserList
     *
     * @return void
     */
    public function setRecipientUserList(?string $recipientUserList): void
    {
        $this->recipientUserList = $recipientUserList;
    }

    /**
     * Get the value of recipientModelList
     *
     * @return ?string The value of recipientModelList
     */
    public function getRecipientModelList(): ?string
    {
        return $this->recipientModelList;
    }

    /**
     * Set the value of recipientModelList
     *
     * @param ?string $recipientModelList The value for recipientModelList
     *
     * @return void
     */
    public function setRecipientModelList(?string $recipientModelList): void
    {
        $this->recipientModelList = $recipientModelList;
    }

    /**
     * Get the value of folderId
     *
     * @return ?MessageFolderId The value of folderId
     */
    public function getFolderId(): ?MessageFolderId
    {
        return $this->folderId;
    }

    /**
     * Set the value of folderId
     *
     * @param ?MessageFolderId $folderId The value for folderId
     *
     * @return void
     */
    public function setFolderId(?MessageFolderId $folderId): void
    {
        $this->folderId = $folderId;
    }

    /**
     * Get the value of hasAttachments
     *
     * @return ?int The value of hasAttachments
     */
    public function getHasAttachments(): ?int
    {
        return $this->hasAttachments;
    }

    /**
     * Set the value of hasAttachments
     *
     * @param ?int $hasAttachments The value for hasAttachments
     *
     * @return void
     */
    public function setHasAttachments(?int $hasAttachments): void
    {
        $this->hasAttachments = $hasAttachments;
    }

    /**
     * Get the value of isRead
     *
     * @return ?int The value of isRead
     */
    public function getIsRead(): ?int
    {
        return $this->isRead;
    }

    /**
     * Set the value of isRead
     *
     * @param ?int $isRead The value for isRead
     *
     * @return void
     */
    public function setIsRead(?int $isRead): void
    {
        $this->isRead = $isRead;
    }

    /**
     * Get the value of subject
     *
     * @return ?string The value of subject
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param ?string $subject The value for subject
     *
     * @return void
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * Get the value of body
     *
     * @return ?string The value of body
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param ?string $body The value for body
     *
     * @return void
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * Get the value of dateCreated
     *
     * @return ?DateTimeInterface The value of dateCreated
     */
    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?DateTimeInterface $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get the value of status
     *
     * @return ?string The value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?string $status The value for status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}
