<?php


namespace SergiX44\Nutgram\Telegram\Types;

/**
 * This object represents a unique message identifier.
 * @see https://core.telegram.org/bots/api#messageid
 */
class MessageId
{
    /**
     * Unique message identifier
     * @var int
     */
    public int $message_id;
}
