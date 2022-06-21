<?php
namespace Abolfazlrastegar\LaravelSms\Drivers;

interface Message
{
    public function sendMessages ($mobile, $message, $params);
    public function sendMessageGroup($mobile = array(), $message = array(), $params = array());
    public function sendVerifyCode($mobile, $template, $params);
}
