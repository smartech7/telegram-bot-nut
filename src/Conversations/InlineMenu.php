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

    protected string $orNext;

    public function __construct()
    {
        $this->buttons = InlineKeyboardMarkup::make();
    }

    /**
     * @param  string  $text
     * @return InlineMenu
     */
    public function menuText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearButtons(): self
    {
        $this->buttons = InlineKeyboardMarkup::make();
        return $this;
    }

    /**
     * @param  InlineKeyboardButton  $buttons
     * @return InlineMenu
     */
    public function addButtonRow(...$buttons): self
    {
        foreach ($buttons as $button) {

            if ($button->callback_data === null) {
                continue;
            }

            if (str_starts_with($button->callback_data, '@')) {
                $button->callback_data = $button->text.$button->callback_data;
            }

            [$callbackData, $method] = explode('@', $button->callback_data ?? $button->text);

            if (!method_exists($this, $method)) {
                throw new InvalidArgumentException("The method $method does not exists.");
            }

            $this->callbacks[$callbackData] = $method;
        }

        $this->buttons->addRow(...$buttons);
        return $this;
    }

    /**
     * @param  string|null  $orNext
     * @return InlineMenu
     */
    public function orNext(?string $orNext): self
    {
        $this->orNext = $orNext;
        return $this;
    }

    /**
     * @return mixed
     */
    public function handleStep(): mixed
    {
        if ($this->bot->isCallbackQuery()) {
            $this->bot->answerCallbackQuery();

            $data = $this->bot->callbackQuery()?->data;
            if (isset($this->callbacks[$data])) {
                $this->step = $this->callbacks[$data];
            } elseif (isset($this->orNext)) {
                $this->step = $this->orNext;
            }
        }

        return $this($this->bot);
    }

    /**
     * @param  bool  $forceSend
     * @param  array  $opt
     * @param  bool  $noHandlers
     * @param  bool  $noMiddlewares
     * @return mixed
     */
    public function showMenu(bool $forceSend = false, array $opt = [], bool $noHandlers = false, bool $noMiddlewares = false): mixed
    {
        if ($forceSend || !$this->messageId || !$this->chatId) {
            $message = $this->bot->sendMessage($this->text, array_merge([
                'reply_markup' => $this->buttons,
            ], $opt));
        } else {
            $message = $this->bot->editMessageText($this->text, array_merge([
                'reply_markup' => $this->buttons,
            ], $opt));
        }

        $this->messageId = $message->message_id;
        $this->chatId = $message->chat?->id;

        return $this->setSkipHandlers($noHandlers)
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
