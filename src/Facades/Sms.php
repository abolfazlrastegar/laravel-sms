<?php
namespace Abolfazlrastegar\LaravelSms\Facades;

use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}
