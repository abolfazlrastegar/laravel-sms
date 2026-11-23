<?php

namespace Abolfazlrastegar\\LaravelSms\\Drivers;

use Abolfazlrastegar\\LaravelSms\\Exceptions\\SmsException;
use Illuminate\\Support\\Facades\\Http;
use Illuminate\\Http\\Client\\ConnectionException;

class Melipayamak implements Message
{
    private function credentials()
    {
        $username = config('sms.drivers.Melipayamak.username');
        $password = config('sms.drivers.Melipayamak.password');
        if (!$username || !$password) {
            throw new SmsException('Melipayamak username and password must be configured.');
        }

        return [$username, $password];
    }

    private function request($endpoint, array $payload)
    {
        list($username, $password) = $this->credentials();

        try {
            $response = Http::timeout((int) config('sms.timeout', 15))
                ->acceptJson()
                ->asForm()
                ->post('https://rest.payamak-panel.com/api/SendSMS/' . $endpoint, array_merge([
                    'username' => $username,
                    'password' => $password,
                ], $payload));

            $response->throw();
            $data = $response->json();
            return $data === null ? $response->body() : $data;
        } catch (ConnectionException $exception) {
            throw new SmsException('Could not connect to Melipayamak: ' . $exception->getMessage(), 0, $exception);
        } catch (\\Illuminate\\Http\\Client\\RequestException $exception) {
            throw new SmsException('Melipayamak API request failed: ' . $exception->getMessage(), (int) $exception->response->status(), $exception);
        }
    }

    public function sendMessages($mobile, $message, $params = [])
    {
        $from = isset($params['from']) ? $params['from'] : config('sms.drivers.Melipayamak.from');
        if (!$from) {
            throw new \\InvalidArgumentException('Melipayamak sender number (from) is required.');
        }

        return $this->request('SendSMS', [
            'to' => is_array($mobile) ? implode(',', $mobile) : $mobile,
            'from' => $from,
            'text' => $message,
            'isFlash' => isset($params['isFlash']) ? $params['isFlash'] : false,
        ]);
    }

    public function sendMessageGroup($mobile = [], $message = [], $params = [])
    {
        if (count($mobile) !== count($message)) {
            throw new \\InvalidArgumentException('Recipients and messages must have equal lengths.');
        }
        if (count(array_unique($message)) === 1) {
            return $this->sendMessages($mobile, reset($message), $params);
        }

        throw new \\InvalidArgumentException('Melipayamak REST endpoint does not support per-recipient messages in this adapter.');
    }

    public function sendVerifyCode($mobile, $template, $params = [])
    {
        if (!isset($params['text']) || !is_string($params['text'])) {
            throw new \\InvalidArgumentException('Melipayamak shared-service SMS requires params[text] and a body ID as template.');
        }

        return $this->request('BaseServiceNumber', [
            'text' => $params['text'],
            'to' => $mobile,
            'bodyId' => $template,
        ]);
    }
}
