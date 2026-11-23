<?php

namespace Abolfazlrastegar\\LaravelSms\\Drivers;

interface Message
{
    public function sendMessages($mobile, $message, $params = []);
    public function sendMessageGroup($mobile = [], $message = [], $params = []);
    public function sendVerifyCode($mobile, $template, $params = []);
}
