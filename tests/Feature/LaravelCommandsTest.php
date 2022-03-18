<?php

use Mockery\MockInterface;
use SergiX44\Nutgram\Laravel\Commands\HookInfoCommand;
use SergiX44\Nutgram\Laravel\Commands\HookRemoveCommand;
use SergiX44\Nutgram\Laravel\Commands\HookSetCommand;
use SergiX44\Nutgram\Laravel\Commands\ListCommand;
use SergiX44\Nutgram\Laravel\Commands\RegisterCommandsCommand;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Common\WebhookInfo;

test('nutgram:register-commands registers the bot commands', function () {
    $bot = Nutgram::fake();

    $bot->onCommand('start', static function () {
    })->description('start command');

    $bot->onCommand('help', static function () {
    })->description('help command');

    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $mock->shouldReceive('registerMyCommands')->andReturn(0);
    });

    $this->artisan(RegisterCommandsCommand::class)
        ->expectsOutput('Bot commands set.')
        ->assertExitCode(0);
});

test('nutgram:hook:info prints the webhook info', function () {
    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $webhookInfo = Nutgram::fake()->getContainer()->get(WebhookInfo::class);
        $webhookInfo->url = '';
        $webhookInfo->has_custom_certificate = false;
        $webhookInfo->pending_update_count = 0;
        $webhookInfo->ip_address = null;
        $webhookInfo->last_error_date = null;
        $webhookInfo->last_error_message = null;
        $webhookInfo->max_connections = null;
        $webhookInfo->allowed_updates = null;

        $mock->shouldReceive('getWebhookInfo')->andReturn($webhookInfo);
    });

    $this->artisan(HookInfoCommand::class)
        ->expectsTable(['Info', 'Value'], [
            ['url', ''],
            ['has_custom_certificate', 'false'],
            ['pending_update_count', 0],
            ['ip_address', null],
            ['last_error_date', null],
            ['last_error_message', null],
            ['max_connections', null],
            ['allowed_updates', ''],
        ])
        ->assertExitCode(0);
});

test('nutgram:hook:info prints the webhook info with error', function () {
    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $webhookInfo = Nutgram::fake()->getContainer()->get(WebhookInfo::class);
        $webhookInfo->url = '';
        $webhookInfo->has_custom_certificate = false;
        $webhookInfo->pending_update_count = 1;
        $webhookInfo->ip_address = '1.2.3.4';
        $webhookInfo->last_error_date = 1647554568;
        $webhookInfo->last_error_message = 'foobar';
        $webhookInfo->max_connections = 50;
        $webhookInfo->allowed_updates = null;

        $mock->shouldReceive('getWebhookInfo')->andReturn($webhookInfo);
    });

    $this->artisan(HookInfoCommand::class)
        ->expectsTable(['Info', 'Value'], [
            ['url', ''],
            ['has_custom_certificate', 'false'],
            ['pending_update_count', 1],
            ['ip_address', '1.2.3.4'],
            ['last_error_date', '2022-03-17 22:02:48 UTC'],
            ['last_error_message', 'foobar'],
            ['max_connections', 50],
            ['allowed_updates', ''],
        ])
        ->assertExitCode(0);
});

test('nutgram:hook:remove removes the bot webhook', function () {
    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $mock->shouldReceive('deleteWebhook')->with([
            'drop_pending_updates' => false,
        ])->andReturn(0);
    });

    $this->artisan(HookRemoveCommand::class, ['--drop-pending-updates' => false])
        ->expectsOutput('Bot webhook removed.')
        ->assertExitCode(0);
});

test('nutgram:hook:remove removes the bot webhook and the pending updates', function () {
    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $mock->shouldReceive('deleteWebhook')->with([
            'drop_pending_updates' => true,
        ])->andReturn(0);
    });

    $this->artisan(HookRemoveCommand::class, ['--drop-pending-updates' => true])
        ->expectsOutput('Pending updates dropped.')
        ->expectsOutput('Bot webhook removed.')
        ->assertExitCode(0);
});

test('nutgram:hook:set sets the bot webhook', function () {
    $this->mock(Nutgram::class, function (MockInterface $mock) {
        $mock->shouldReceive('setWebhook')->with('https://foo.bar/hook', [
            'max_connections' => 50,
        ])->andReturn(0);
    });

    $this->artisan(HookSetCommand::class, ['url' => 'https://foo.bar/hook'])
        ->expectsOutput('Bot webhook set with url: https://foo.bar/hook')
        ->assertExitCode(0);
});

test('nutgram:list with no handlers registered', function () {
    $this->swap(Nutgram::class, Nutgram::fake());

    $this
        ->artisan(ListCommand::class)
        ->expectsOutput('No handlers have been registered.')
        ->assertExitCode(0);
});

test('nutgram:list with handler registered', function () {
    $bot = Nutgram::fake();
    $bot->onCommand('start', static function () {
    });

    $this->swap(Nutgram::class, $bot);

    $this
        ->artisan(ListCommand::class)
        ->doesntExpectOutput('No handlers have been registered.')
        ->assertExitCode(0);
});
