<?php

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\MissingMethodMenu;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\ValidButtonNoCallbackMenu;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\ValidButtonNoDataMenu;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\ValidNoEndMenu;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\ValidReopenMenu;
use SergiX44\Nutgram\Tests\Feature\Conversations\InlineMenu\ValidWithFallbackMenu;

test('valid inline menu + no end', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidNoEndMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyMessage([
            'text' => 'Choose a color:',
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('Red', callback_data: 'red'))
                ->addRow(InlineKeyboardButton::make('Green', callback_data: 'green'))
                ->addRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow'))
        ])
        ->hearCallbackQueryData('red')
        ->reply()
        ->assertReplyText('Choosen: red!')
        ->assertReply('answerCallbackQuery', [
            'show_alert' => true,
            'text' => 'Alert!',
        ], 1)
        ->hearText('start')
        ->reply();
});

test('valid inline menu + no end + no data', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidButtonNoDataMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyMessage([
            'text' => 'Choose a color:',
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('Red', callback_data: 'Red'))
        ])
        ->hearCallbackQueryData('Red')
        ->reply()
        ->assertReplyText('Choosen: Red!')
        ->assertReply('answerCallbackQuery', index: 1);
});

test('valid inline menu + no end + no callback data', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidButtonNoCallbackMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyText('Choose a color:');
});

test('valid inline menu + reopen', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidReopenMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyMessage([
            'text' => 'Choose a color:',
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('Red', callback_data: 'red'))
                ->addRow(InlineKeyboardButton::make('Green', callback_data: 'green'))
                ->addRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow'))
        ])
        ->hearCallbackQueryData('red')
        ->reply()
        ->assertCalled('deleteMessage')
        ->assertReplyText('Choosen: red!', index: 1)
        ->assertReply('answerCallbackQuery', index: 2);
});

test('valid inline menu + orNext + click button', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidWithFallbackMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyMessage([
            'text' => 'Choose a color:',
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('Red', callback_data: 'red'))
                ->addRow(InlineKeyboardButton::make('Green', callback_data: 'green'))
                ->addRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow'))
        ])
        ->hearCallbackQueryData('red')
        ->reply()
        ->assertReplyText('Choosen: red!')
        ->assertReplyText('Bye!', 1);
});

test('valid inline menu + orNext + no button click', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(ValidWithFallbackMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply()
        ->assertReplyMessage([
            'text' => 'Choose a color:',
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('Red', callback_data: 'red'))
                ->addRow(InlineKeyboardButton::make('Green', callback_data: 'green'))
                ->addRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow'))
        ])
        ->hearText('wow')
        ->reply()
        ->assertReplyText('Bye!');
});

test('missing callback method', function () {
    $bot = Nutgram::fake();
    $bot->onMessage(MissingMethodMenu::class);

    $bot
        ->willStartConversation()
        ->hearText('start')
        ->reply();
})->throws(InvalidArgumentException::class, 'The method handleMissing does not exists.');