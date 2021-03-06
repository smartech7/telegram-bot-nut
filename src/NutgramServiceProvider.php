<?php


namespace SergiX44\Nutgram;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\RunningMode\Webhook;

class NutgramServiceProvider extends ServiceProvider
{
    /**
     * Register the bot instance
     */
    public function register()
    {
        $this->app->singleton(Nutgram::class, function (Application $app) {
            $config = array_merge([
                'cache' => $app->make(Cache::class),
            ], config('nutgram.config'));

            $bot = new Nutgram(config('nutgram.token'), $config);

            if ($app->runningInConsole()) {
                $bot->setRunningMode(Polling::class);
            } else {
                $bot->setRunningMode(Webhook::class);
            }

            return $bot;
        });

        $this->app->alias(Nutgram::class, 'nutgram');

        $this->mergeConfigFrom(__DIR__.'/../laravel/config.php', 'nutgram');
    }

    /**
     * Load bot commands and callbacks
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../laravel/config.php' => config_path('nutgram.php'),
        ], 'nutgram');
    }
}
