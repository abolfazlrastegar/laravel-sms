<?php

namespace Abolfazlrastegar\LaravelSms\Jobs;

use Abolfazlrastegar\LaravelSms\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSmsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $tries;
    public $timeout;

    private $driver;
    private $operation;
    private $payload;

    public function __construct($driver, $operation, array $payload)
    {
        $this->driver = $driver;
        $this->operation = $operation;
        $this->payload = $payload;
        $this->tries = (int) config('sms.queue.tries', 3);
        $this->timeout = (int) config('sms.queue.timeout', 60);
    }

    public function handle()
    {
        return Sms::make($this->driver)->sendNow($this->operation, $this->payload);
    }
}
