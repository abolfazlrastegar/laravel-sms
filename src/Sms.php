<?php

namespace Abolfazlrastegar\LaravelSms;

use Abolfazlrastegar\LaravelSms\Drivers\Kavenegar;
use Abolfazlrastegar\LaravelSms\Drivers\Melipayamak;
use Abolfazlrastegar\LaravelSms\Drivers\Smsir;
use Abolfazlrastegar\LaravelSms\Drivers\VoiceCall;
use Abolfazlrastegar\LaravelSms\Exception\createMessageErrorException;
use Abolfazlrastegar\LaravelSms\Exceptions\UnsupportedFeatureException;
use Abolfazlrastegar\LaravelSms\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Bus;
use InvalidArgumentException;

class Sms
{
    private $name_sms;
    private $mobile;
    private $template;
    private $message;
    private $params = [];

    private const DRIVERS = [
        'kavenegar' => Kavenegar::class,
        'smsir' => Smsir::class,
        'melipayamak' => Melipayamak::class,
    ];

    public function __construct($name_sms = '')
    {
        $this->name_sms = $name_sms;
    }

    public static function make($name_sms = '')
    {
        return new static($name_sms);
    }

    public function mobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function template($template)
    {
        $this->template = $template;
        return $this;
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    public function params($params)
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException('SMS params must be an array.');
        }

        $this->params = $params;
        return $this;
    }

    public function defaultSms()
    {
        $this->name_sms = config('sms.default');
        return $this;
    }

    public function sendVerifyCode()
    {
        $this->validateMobile(false);
        if (!is_string($this->template) && !is_numeric($this->template)) {
            throw new InvalidArgumentException('A valid SMS template is required.');
        }

        return $this->dispatchOrSend('sendVerifyCode', [
            'mobile' => $this->mobile,
            'template' => $this->template,
            'params' => $this->params,
        ]);
    }

    public function sendMessages()
    {
        $this->validateMobile(true);
        if (!is_string($this->message) || trim($this->message) === '') {
            throw new InvalidArgumentException('A non-empty SMS message is required.');
        }

        return $this->dispatchOrSend('sendMessages', [
            'mobile' => $this->mobile,
            'message' => $this->message,
            'params' => $this->params,
        ]);
    }

    public function sendMessageGroup()
    {
        if (!is_array($this->mobile) || count($this->mobile) === 0) {
            throw new InvalidArgumentException('Group SMS recipients must be a non-empty array.');
        }
        $this->validateMobile(true);

        if (!is_array($this->message) || count($this->message) === 0) {
            throw new InvalidArgumentException('Group SMS messages must be a non-empty array.');
        }
        foreach ($this->message as $message) {
            if (!is_string($message) || trim($message) === '') {
                throw new InvalidArgumentException('Every group SMS message must be a non-empty string.');
            }
        }

        if (count($this->mobile) !== count($this->message)) {
            throw new InvalidArgumentException('The number of recipients and messages must be equal.');
        }

        return $this->dispatchOrSend('sendMessageGroup', [
            'mobile' => $this->mobile,
            'message' => $this->message,
            'params' => $this->params,
        ]);
    }

    public function voiceCall()
    {
        $this->validateMobile(true);
        if (!is_string($this->message) || trim($this->message) === '') {
            throw new InvalidArgumentException('A non-empty voice message is required.');
        }

        return $this->dispatchOrSend('voiceCall', [
            'mobile' => $this->mobile,
            'message' => $this->message,
            'params' => $this->params,
        ]);
    }

    /**
     * Execute a queued operation. This method is used by the package's internal job.
     */
    public function sendNow($operation, array $payload)
    {
        $driver = $this->makeSms();

        if ($operation === 'voiceCall') {
            if (!$driver instanceof VoiceCall) {
                throw new UnsupportedFeatureException('The selected SMS driver does not support voice calls.');
            }

            return $driver->voiceCall($payload['mobile'], $payload['message'], $payload['params']);
        }

        if (!in_array($operation, ['sendVerifyCode', 'sendMessages', 'sendMessageGroup'], true)) {
            throw new InvalidArgumentException('Unsupported SMS operation.');
        }

        return call_user_func_array([$driver, $operation], array_values($payload));
    }

    private function dispatchOrSend($operation, array $payload)
    {
        $driver = $this->driverName();

        // Preserve the original provider response when Laravel is configured for sync.
        if (config('queue.default', 'sync') === 'sync') {
            return $this->sendNow($operation, $payload);
        }

        $job = new SendSmsJob($driver, $operation, $payload);
        $job->onConnection(config('queue.default'));
        $job->onQueue(config('sms.queue.name', 'sms'));

        return Bus::dispatch($job);
    }

    private function validateMobile($allowMultiple)
    {
        $mobiles = is_array($this->mobile) ? $this->mobile : [$this->mobile];
        if (!$allowMultiple && count($mobiles) !== 1) {
            throw new InvalidArgumentException('This operation accepts exactly one mobile number.');
        }
        if (count($mobiles) === 0) {
            throw new InvalidArgumentException('At least one mobile number is required.');
        }

        foreach ($mobiles as $mobile) {
            if (!is_string($mobile) || !preg_match('/^(?:\+?[1-9]\d{7,14}|0\d{9,10})$/', $mobile)) {
                throw new InvalidArgumentException('Invalid mobile number provided.');
            }
        }
    }

    private function driverName()
    {
        $name = strtolower(trim((string) ($this->name_sms ?: config('sms.default', ''))));
        if (!isset(self::DRIVERS[$name])) {
            throw createMessageErrorException::notClass($name, 'supported SMS drivers: ' . implode(', ', array_keys(self::DRIVERS)));
        }

        return $name;
    }

    private function makeSms()
    {
        $class = self::DRIVERS[$this->driverName()];
        return new $class();
    }
}
