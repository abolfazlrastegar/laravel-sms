<?php

namespace Abolfazlrastegar\LaravelSms\Providers;

use Abolfazlrastegar\LaravelSms\Sms;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('sms', function () {
            return new Sms();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/sms.php','sms'
        );
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/sms.php' => config_path('sms.php')], 'config');
    }
}
