<?php

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Enums\ChatMemberStatus;
use SergiX44\Nutgram\Telegram\Enums\UpdateType;

it('can create my chat member status', function () {
    Nutgram::fake()
        ->hearUpdateType(UpdateType::MY_CHAT_MEMBER, [
            'chat' => ['id' => 321],
            'from' => ['id' => 321],
            'new_chat_member' => ['status' => ChatMemberStatus::MEMBER->value],
        ])
        ->reply()
        ->assertNoReply();
});
