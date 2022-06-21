<?php

namespace Abolfazlrastegar\LaravelSms\Drivers;

use Illuminate\Support\Facades\Http;

class Melipayamak implements Message
{
    public function sendMessages ($mobile, $message, $params)
    {
       //
    }

    public function sendMessageGroup($mobile = array(), $message = array(), $params = array())
    {
        // TODO: Implement groupSend() method.
    }

    public function sendVerifyCode($mobile, $template, $params)
    {
        // TODO: Implement sendVerifyCode() method.
    }
}
