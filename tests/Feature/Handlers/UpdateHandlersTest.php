<?php

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\MessageType;

it('calls onMessage() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onMessage(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('message');

it('calls onMessageType() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onMessageType(MessageType::TEXT, function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('message');

it('calls onEditedMessage() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onEditedMessage(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('edited_message');

it('calls onChannelPost() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onChannelPost(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('channel_post');

it('calls onEditedChannelPost() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onEditedChannelPost(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('edited_channel_post');

it('calls onInlineQuery() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onInlineQuery(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('inline_query');

it('calls onChosenInlineResult() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onChosenInlineResult(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('chosen_inline_result');

it('calls onCallbackQuery() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onCallbackQuery(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('callback_query');

it('calls onCallbackQueryData() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onCallbackQueryData('thedata', function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('callback_query');

it('calls onShippingQuery() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onShippingQuery(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('shipping_query');

it('calls onPreCheckoutQuery() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onPreCheckoutQuery(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('pre_checkout_query');

it('calls onPreCheckoutQueryPayload() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onPreCheckoutQueryPayload('thedata', function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('pre_checkout_query');

it('calls onUpdatePoll() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onUpdatePoll(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('poll');

it('calls onPollAnswer() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onPollAnswer(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('poll_answer');

it('calls onMyChatMember() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onMyChatMember(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('my_chat_member');

it('calls onChatMember() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onChatMember(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('chat_member');

it('calls onChatJoinRequest() handler', function ($update) {
    $bot = Nutgram::fake($update);

    $bot->onChatJoinRequest(function (Nutgram $bot) {
        $bot->set('called', true);
    });

    $bot->run();

    expect($bot->get('called'))->toBeTrue();
})->with('chat_join_request');
