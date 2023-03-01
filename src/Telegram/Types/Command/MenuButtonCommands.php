<?php

namespace SergiX44\Nutgram\Telegram\Types\Command;

use SergiX44\Nutgram\Telegram\Enums\MenuButtonType;

/**
 * Represents a menu button, which opens the bot's list of commands.
 * @see https://core.telegram.org/bots/api#menubuttoncommands
 */
class MenuButtonCommands extends MenuButton
{
    /**
     * Type of the button, must be commands
     */
    public string $type = 'commands';

    public function getType(): string
    {
        return MenuButtonType::COMMANDS;
    }
}
