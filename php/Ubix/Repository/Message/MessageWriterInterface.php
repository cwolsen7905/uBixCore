<?php

declare(strict_types=1);

namespace Ubix\Repository\Message;

use Ubix\Model\Message;

/**
 * Interface for writing message data
 */
interface MessageWriterInterface
{
    /**
     * Save the message
     *
     * @param Message $message The message to be saved
     *
     * @return void
     */
    public function save(Message $message): void;
}
