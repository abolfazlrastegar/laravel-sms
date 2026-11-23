<?php

namespace Tests;

use Abolfazlrastegar\LaravelSms\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class SmsQueueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sms.drivers.Kavenegar.key' => 'test-key',
            'queue.default' => 'database',
            'sms.queue.name' => 'outbound-sms',
        ]);
    }

    public function test_sms_is_dispatched_to_configured_laravel_queue_without_application_job()
    {
        Queue::fake();

        $result = \Abolfazlrastegar\LaravelSms\Facades\Sms::make('kavenegar')
            ->mobile('09123456789')
            ->template('verify')
            ->params(['token' => '123'])
            ->sendVerifyCode();

        $this->assertNotNull($result);

        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->queue === 'outbound-sms'
                && $job->connection === 'database';
        });
    }

    public function test_internal_job_executes_the_provider_operation()
    {
        Http::fake([
            'api.kavenegar.com/*' => Http::response(['return' => ['status' => 200]], 200),
        ]);

        $job = new SendSmsJob('kavenegar', 'sendVerifyCode', [
            'mobile' => '09123456789',
            'template' => 'verify',
            'params' => ['token' => '123'],
        ]);

        $result = $job->handle();

        $this->assertSame(200, $result['return']['status']);
        Http::assertSent(function ($request) {
            return strpos($request->url(), 'api.kavenegar.com') !== false;
        });
    }

    public function test_sync_connection_keeps_immediate_provider_response()
    {
        config(['queue.default' => 'sync']);
        Http::fake([
            'api.kavenegar.com/*' => Http::response(['return' => ['status' => 200]], 200),
        ]);

        $result = \Abolfazlrastegar\LaravelSms\Facades\Sms::make('kavenegar')
            ->mobile('09123456789')
            ->template('verify')
            ->params(['token' => '123'])
            ->sendVerifyCode();

        $this->assertSame(200, $result['return']['status']);
    }
}
