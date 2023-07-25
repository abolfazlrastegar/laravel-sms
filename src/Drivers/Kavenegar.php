<?php

namespace Abolfazlrastegar\LaravelSms\Drivers;

use Illuminate\Support\Facades\Http;

class Kavenegar implements Message
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
        $receptor = $mobile;
        if (is_array($mobile))
        {
            $receptor = implode(',', $mobile);
        }
        $date = $params['date'] ?? null;
        $sender = $params['sender'] ?? null;
        $type = $params['type'] ?? null;
        $localid = $params['localid'] ?? null;

      return  $this->requestHttp('https://api.kavenegar.com/v1/'. $this->setKey() .'/sms/send.json?receptor=' . $receptor . '&sender='. $sender . '&date='. $date . '&type='. $type . '&localid=' . $localid . '&message=' . $message);
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
        $date =  $params['date'] ?? null;
        $type =  $params['type'] ?? null;
        $localmessageids = $params['localid'] ?? null;

        return  $this->requestHttp('https://api.kavenegar.com/v1/'. $this->setKey() .'/sms/sendarray.json?receptor=' . $mobile . '&sender='. $params['sender'] . '&date='. $date . '&type='. $type . '&localmessageids=' . $localmessageids . '&message=' . $message);
    }


    /**
     * Send message verify code to user
     * @param $mobile
     * @param $template
     * @param $params
     * @return mixed
     */
    public function sendVerifyCode($mobile, $template, $params)
    {
        $token = '&token=' . $params['token'];
        if (count($params) > 1) {
            if (count($params) > 2)
            {
                $token = '&token=' . $params['token'] . '&token2=' . $params['token2'] . '&token3=' . $params['token3'];
            }else
            {
                $token = '&token=' . $params['token'] . '&token2=' . $params['token2'];
            }
        }
        $type = $params['type'] ?? 'sms';

        return $this->requestHttp('https://api.kavenegar.com/v1/' . $this->setKey() . '/verify/lookup.json?receptor=' . $mobile . $token . '&type=' . $type . '&template=' . $template);
    }

    /**
     * Send message call for one user or users
     * @param $mobile
     * @param $message
     * @param $params
     * @return mixed
     */
    public function voiceCall ($mobile, $message, $params) {
        $receptor = $mobile;
        if (is_array($mobile))
        {
            $receptor = implode(',', $mobile);
        }
        $date = $params['date'] ?? null;
        $localid = $params['localid'] ?? null;

        return $this->requestHttp('https://api.kavenegar.com/v1/' . $this->setKey() .'/call/maketts.json?receptor=' . $receptor. '&date='. $date . '&localid='. $localid .'&message=' . $message);
    }

    /**
     * @return string[]
     */
    private function setHeaders ()
    {
        return [
            'Accept: application/json',
            'charset: utf-8',
            'Content-Type: application/json',
        ];
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function setKey ()
    {
        return config('sms.drivers.Kavenegar.key');
    }

    /**
     * @param $url
     * @return mixed
     *
     */
    private function requestHttp ($url)
    {
        $result = Http::withHeaders($this->setHeaders())->post($url);
        return json_decode($result->getBody()->getContents(), true);
    }

}
