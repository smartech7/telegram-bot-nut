<?php

namespace SergiX44\Nutgram\Telegram\Types\Keyboard;

use JsonSerializable;

/**
 * This object represents one button of the reply keyboard.
 * For simple text buttons String can be used instead of this object to specify text of the button.
 * Optional fields are mutually exclusive.
 *
 * Note: request_contact and request_location options will only work in Telegram versions released after 9 April, 2016.
 * Older clients will ignore them.
 * @see https://core.telegram.org/bots/api#keyboardbutton
 */
class KeyboardButton implements JsonSerializable
{
    /**
     * Text of the button.
     * If none of the optional fields are used, it will be sent to the bot as a message when the button is pressed
     */
    public string $text;

    /**
     * Optional. If True, the user's phone number will be sent as a contact when the button is pressed.
     * Available in private chats only
     */
    public ?bool $request_contact = null;

    /**
     * Optional. If True, the user's current location will be sent when the button is pressed.
     * Available in private chats only
     */
    public ?bool $request_location = null;

    /**
     * Optional. If specified, the user will be asked to create a poll and send it to the bot when the button is
     * pressed. Available in private chats only
     */
    public ?KeyboardButtonPollType $request_poll = null;

    public function __construct(
        string $text,
        ?bool $request_contact = null,
        ?bool $request_location = null,
        ?KeyboardButtonPollType $request_poll = null,
    ) {
        $this->text = $text;
        $this->request_contact = $request_contact;
        $this->request_location = $request_location;
        $this->request_poll = $request_poll;
    }

    public static function make(
        string $text,
        ?bool $request_contact = null,
        ?bool $request_location = null,
        ?KeyboardButtonPollType $request_poll = null,
    ): self {
        return new self(
            $text,
            $request_contact,
            $request_location,
            $request_poll,
        );
    }

    public function jsonSerialize()
    {
        return array_filter([
            'text' => $this->text,
            'request_contact' => $this->request_contact,
            'request_location' => $this->request_location,
            'request_poll' => $this->request_poll,
        ]);
    }
}
