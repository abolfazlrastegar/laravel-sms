<?php

namespace Tests;

use Abolfazlrastegar\\LaravelSms\\Support\\SmsLogger;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

class SmsLoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('driver');
            $table->string('operation');
            $table->string('recipient')->nullable();
            $table->string('status');
            $table->string('provider_message_id')->nullable();
            $table->string('response_code')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function test_logger_masks_recipients_and_stores_only_safe_fields()
    {
        $id = SmsLogger::create('kavenegar', 'sendMessages', [
            'mobile' => '09123456789',
            'message' => 'private message content',
            'params' => ['token' => 'secret'],
        ], 'processing');

        $this->assertNotNull($id);
        $row = DB::table('sms_logs')->find($id);

        $this->assertSame('*******6789', $row->recipient);
        $this->assertSame('processing', $row->status);
        $this->assertStringNotContainsString('private message', json_encode($row));
        $this->assertStringNotContainsString('secret', json_encode($row));
    }

    public function test_logger_can_be_disabled()
    {
        config(['sms.logging.enabled' => false]);

        $this->assertNull(SmsLogger::create('kavenegar', 'sendMessages', ['mobile' => '09123456789'], 'processing'));
    }
}
