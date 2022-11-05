<?php

namespace SergiX44\Nutgram\Telegram\Attributes;

class ChatType extends BaseAttribute
{
    public const SENDER = 'sender';
    public const PRIVATE = 'private';
    public const GROUP = 'group';
    public const SUPERGROUP = 'supergroup';
    public const CHANNEL = 'channel';
}
