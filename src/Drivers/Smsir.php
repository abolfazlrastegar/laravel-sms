<?php

namespace Abolfazlrastegar\\LaravelSms\\Drivers;

use Abolfazlrastegar\\LaravelSms\\Exceptions\\SmsException;
use Illuminate\\Support\\Facades\\Http;
use Illuminate\\Http\\Client\\ConnectionException;

class Smsir implements Message
{
    private function headers()
    {
        $key = config('sms.drivers.Smsir.key');
        if (!is_string($key) || trim($key) === '') {
            throw new SmsException('SMS.ir API key is not configured.');
        }

        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-API-KEY' => $key,
        ];
    }

    private function request($endpoint, array $payload)
    {
        try {
            $response = Http::timeout((int) config('sms.timeout', 15))
                ->withHeaders($this->headers())
                ->post('https://api.sms.ir/v1/' . $endpoint, $payload);

            $response->throw();
            $data = $response->json();
            if (!is_array($data)) {
                throw new SmsException('SMS.ir returned an invalid JSON response.');
            }

            return $data;
        } catch (ConnectionException $exception) {
            throw new SmsException('Could not connect to SMS.ir: ' . $exception->getMessage(), 0, $exception);
        } catch (\\Illuminate\\Http\\Client\\RequestException $exception) {
            throw new SmsException('SMS.ir API request failed: ' . $exception->getMessage(), (int) $exception->response->status(), $exception);
        }
    }

    private function payload($mobile, $message, $params)
    {
        if (!isset($params['lineNumber'])) {
            throw new \\InvalidArgumentException('SMS.ir lineNumber is required.');
        }

        return [
            'lineNumber' => $params['lineNumber'],
            'messageText' => $message,
            'mobiles' => $mobile,
            'SendDateTime' => isset($params['SendDateTime']) ? $params['SendDateTime'] : null,
        ];
    }

    public function sendMessages($mobile, $message, $params = [])
    {
        return $this->request('send/bulk', $this->payload($mobile, $message, $params));
    }

    public function sendMessageGroup($mobile = [], $message = [], $params = [])
    {
        if (count($mobile) !== count($message)) {
            throw new \\InvalidArgumentException('Recipients and messages must have equal lengths.');
        }

        return $this->request('send/likeToLike', $this->payload($mobile, $message, $params));
    }

    public function sendVerifyCode($mobile, $template, $params = [])
    {
        if (!is_array($params)) {
            throw new \\InvalidArgumentException('SMS.ir verify parameters must be an array.');
        }

        return $this->request('send/verify', [
            'mobile' => $mobile,
            'templateId' => $template,
            'parameters' => $params,
        ]);
    }
}
