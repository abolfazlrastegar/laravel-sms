<?php

namespace Abolfazlrastegar\LaravelSms\Drivers;

interface VoiceCall
{
    public function voiceCall($mobile, $message, $params = []);
}
