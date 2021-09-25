<?php

namespace SergiX44\Nutgram\Conversations;

use InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardMarkup;

abstract class InlineMenu extends Conversation
{
    protected ?int $messageId = null;

    protected ?int $chatId = null;

    protected string $text;

    protected InlineKeyboardMarkup $buttons;

    protected array $callbacks = [];

    protected ?string $fallbackStep;

    public function __construct()
    {
        $this->buttons = InlineKeyboardMarkup::make();
    }

    /**
     * @param  string  $text
     * @return InlineMenu
     */
    public function menuText(string $text): InlineMenu
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param  InlineKeyboardButton  $buttons
     * @return InlineMenu
     */
    public function addButtonRow(...$buttons): InlineMenu
    {
        foreach ($buttons as $button) {
            [$callbackData, $method] = explode($button->callback_data, '@');

            if (!method_exists($this, $method)) {
                throw new InvalidArgumentException("The method $method does not exists.");
            }

            $this->callbacks[$callbackData] = $method;
        }

        $this->buttons->addRow(...$buttons);
        return $this;
    }

    /**
     * @param  string|null  $fallbackStep
     * @return InlineMenu
     */
    public function fallbackStep(?string $fallbackStep): InlineMenu
    {
        $this->fallbackStep = $fallbackStep;
        return $this;
    }

    public function handleStep()
    {
        if ($this->bot->isCallbackQuery()) {
            $this->bot->answerCallbackQuery();

            $data = $this->bot->callbackQuery()?->data;
            if (isset($this->callbacks[$data])) {
                $this->step = $this->callbacks[$data];
            } elseif ($this->fallbackStep !== null) {
                $this->step = $this->fallbackStep;
            }
        }

        return $this($this->bot);
    }

    public function showMenu(bool $forceSend = false, bool $noHandlers = false, bool $noMiddlewares = false)
    {
        if ($forceSend || !$this->messageId || !$this->chatId) {
            $message = $this->bot->sendMessage($this->text, [
                'reply_markup' => $this->buttons,
            ]);
        } else {
            $message = $this->bot->editMessageText($this->text, [
                'reply_markup' => $this->buttons,
            ]);
        }

        $this->messageId = $message->message_id;
        $this->chatId = $message->chat?->id;

        $this->setSkipHandlers($noHandlers)
            ->setSkipMiddlewares($noMiddlewares)
            ->next('handleStep');
    }

    protected function closing(Nutgram $bot)
    {
        if ($this->messageId && $this->chatId) {
            $this->bot->deleteMessage($this->chatId, $this->messageId);
        }
    }
}
