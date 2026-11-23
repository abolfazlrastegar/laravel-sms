<?php

namespace Abolfazlrastegar\LaravelSms\Drivers;

use Abolfazlrastegar\LaravelSms\Exceptions\SmsException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class Kavenegar implements Message, VoiceCall
{
    private function key()
    {
        $key = config('sms.drivers.Kavenegar.key');
        if (!is_string($key) || trim($key) === '') {
            throw new SmsException('Kavenegar API key is not configured.');
        }
        return $key;
    }

    private function request($endpoint, array $params)
    {
        try {
            $response = Http::timeout((int) config('sms.timeout', 15))
                ->acceptJson()
                ->get('https://api.kavenegar.com/v1/' . $this->key() . '/' . $endpoint . '.json', $params);

            $response->throw();
            $data = $response->json();
            if (!is_array($data)) {
                throw new SmsException('Kavenegar returned an invalid JSON response.');
            }

            return $data;
        } catch (ConnectionException $exception) {
            throw new SmsException('Could not connect to Kavenegar: ' . $exception->getMessage(), 0, $exception);
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            throw new SmsException('Kavenegar API request failed: ' . $exception->getMessage(), (int) $exception->response->status(), $exception);
        }
    }

    public function sendMessages($mobile, $message, $params = [])
    {
        $query = [
            'receptor' => is_array($mobile) ? implode(',', $mobile) : $mobile,
            'message' => $message,
        ];
        foreach (['sender', 'date', 'type', 'localid'] as $key) {
            if (isset($params[$key])) {
                $query[$key] = $params[$key];
            }
        }

        return $this->request('sms/send', $query);
    }

    public function sendMessageGroup($mobile = [], $message = [], $params = [])
    {
        if (!isset($params['sender']) || !is_array($params['sender'])) {
            throw new \InvalidArgumentException('Kavenegar group sending requires a sender array.');
        }
        if (count($mobile) !== count($message) || count($mobile) !== count($params['sender'])) {
            throw new \InvalidArgumentException('Recipients, messages and senders must have equal lengths.');
        }

        $query = [
            'receptor' => $mobile,
            'sender' => $params['sender'],
            'message' => $message,
        ];
        foreach (['date', 'type', 'localmessageids'] as $key) {
            if (isset($params[$key])) {
                $query[$key] = $params[$key];
            }
        }

        return $this->request('sms/sendarray', $query);
    }

    public function sendVerifyCode($mobile, $template, $params = [])
    {
        if (!isset($params['token']) || !is_scalar($params['token'])) {
            throw new \InvalidArgumentException('Kavenegar verify code requires a scalar token parameter.');
        }

        $query = [
            'receptor' => $mobile,
            'token' => $params['token'],
            'template' => $template,
            'type' => isset($params['type']) ? $params['type'] : 'sms',
        ];
        foreach (['token2', 'token3', 'token10', 'token20', 'tag'] as $key) {
            if (isset($params[$key])) {
                $query[$key] = $params[$key];
            }
        }

        return $this->request('verify/lookup', $query);
    }

    public function voiceCall($mobile, $message, $params = [])
    {
        $query = [
            'receptor' => is_array($mobile) ? implode(',', $mobile) : $mobile,
            'message' => $message,
        ];
        foreach (['date', 'localid', 'repeat', 'tag'] as $key) {
            if (isset($params[$key])) {
                $query[$key] = $params[$key];
            }
        }

        return $this->request('call/maketts', $query);
    }
}
