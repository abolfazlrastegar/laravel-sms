<?php

namespace Abolfazlrastegar\\LaravelSms\\Providers;

use Abolfazlrastegar\\LaravelSms\\Sms;
use Illuminate\\Support\\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sms.php', 'sms');
        $this->app->singleton('sms', function () {
            return new Sms();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../database/migrations/2026_11_23_000000_create_sms_logs_table.php' => database_path('migrations/2026_11_23_000000_create_sms_logs_table.php'),
        ], 'migrations');
    }
}
