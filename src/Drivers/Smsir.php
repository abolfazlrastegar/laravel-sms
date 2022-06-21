<?php

namespace Abolfazlrastegar\LaravelSms\Drivers;

use Illuminate\Support\Facades\Http;

class Smsir implements Message
{
    /**
     * Send message one user or users
     * @param $mobile
     * @param $message
     * @param $params
     * @return mixed
     */
    public function sendMessages($mobile, $message, $params)
    {
        $data = [
            "lineNumber" => $params['lineNumber'],
            "messageText" => $message,
            "mobiles" => $mobile,
            "SendDateTime" => isset($params['SendDateTime']) ? $params['SendDateTime'] : null
        ];
        $result = Http::withHeaders($this->setHeaders())->post('https://api.sms.ir/v1/send/bulk', (object) $data);
        return json_decode($result->getBody()->getContents(), true);
    }


    /**
     * Send message group users
     * @param $mobile
     * @param $message
     * @param $params
     * @return mixed
     */
    public function sendMessageGroup($mobile = array(), $message = array(), $params = array())
    {
        $data = [
            "lineNumber" => $params['lineNumber'],
            "messageText" => $message,
            "mobiles" => $mobile,
            "SendDateTime" => isset($params['SendDateTime']) ? $params['SendDateTime'] : null
        ];
        $result = Http::withHeaders($this->setHeaders())->post('https://api.sms.ir/v1/send/likeToLike', (object) $data);
        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * Send message verify code to user
     * @param $mobile
     * @param $template
     * @param $params
     * @return mixed
     *
     */
    public function sendVerifyCode($mobile, $template, $params)
    {
        $data = [
            "mobile" => $mobile,
            "templateId" => $template,
            "parameters" => $params,
        ];
        $result = Http::withHeaders($this->setHeaders())->post('https://api.sms.ir/v1/send/verify', (object) $data);
        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * set headers request http
     * @return string[]
     */
    private function setHeaders ()
    {
        return [
            'Accept: text/plain',
            'charset: utf-8',
            'Content-Type: application/json',
            'X-API-KEY:' . config('sms.drivers.Smsir.key')
        ];
    }
}
