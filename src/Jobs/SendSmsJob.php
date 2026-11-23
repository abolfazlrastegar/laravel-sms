<?php

namespace Abolfazlrastegar\LaravelSms\Jobs;

use Abolfazlrastegar\LaravelSms\Sms;
use Abolfazlrastegar\LaravelSms\Support\SmsLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class SendSmsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $tries;
    public $timeout;

    private $driver;
    private $operation;
    private $payload;
    private $logId;

    public function __construct($driver, $operation, array $payload, $logId = null)
    {
        $this->driver = $driver;
        $this->operation = $operation;
        $this->payload = $payload;
        $this->logId = $logId;
        $this->tries = (int) config('sms.queue.tries', 3);
        $this->timeout = (int) config('sms.queue.timeout', 60);
    }

    public function handle()
    {
        $startedAt = microtime(true);
        $attempts = method_exists($this, 'attempts') ? (int) $this->attempts() : 1;
        SmsLogger::update($this->logId, ['status' => 'processing', 'attempts' => $attempts]);

        try {
            $response = Sms::make($this->driver)->sendNow($this->operation, $this->payload);
            SmsLogger::update($this->logId, array_merge(
                ['status' => 'sent', 'attempts' => $attempts, 'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000)],
                SmsLogger::response($response)
            ));
            return $response;
        } catch (Throwable $exception) {
            SmsLogger::update($this->logId, [
                'status' => $attempts < $this->tries ? 'retrying' : 'failed',
                'attempts' => $attempts,
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
                'error_message' => substr($exception->getMessage(), 0, 60000),
            ]);
            throw $exception;
        }
    }

    public function failed($exception)
    {
        SmsLogger::update($this->logId, [
            'status' => 'failed',
            'error_message' => substr($exception->getMessage(), 0, 60000),
        ]);
    }
}
