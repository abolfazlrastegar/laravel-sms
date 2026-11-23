<?php

namespace Tests;

use Illuminate\\Support\\Facades\\Http;
use Abolfazlrastegar\\LaravelSms\\Facades\\Sms;
use Abolfazlrastegar\\LaravelSms\\Exceptions\\UnsupportedFeatureException;
use InvalidArgumentException;

class SmsDriverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'sms.drivers.Kavenegar.key' => 'test-key',
            'sms.drivers.Smsir.key' => 'test-key',
            'sms.drivers.Melipayamak.username' => 'user',
            'sms.drivers.Melipayamak.password' => 'pass',
            'sms.drivers.Melipayamak.from' => '1000',
        ]);
    }

    public function test_kavenegar_verify_uses_encoded_query_parameters()
    {
        Http::fake([
            'api.kavenegar.com/*' => Http::response(['return' => ['status' => 200]], 200),
        ]);

        $result = Sms::make('kavenegar')
            ->mobile('09123456789')
            ->template('verify')
            ->params(['token' => '123', 'token2' => '456'])
            ->sendVerifyCode();

        $this->assertSame(200, $result['return']['status']);
        Http::assertSent(function ($request) {
            return strpos($request->url(), 'api.kavenegar.com') !== false
                && $request['token'] === '123'
                && $request['token2'] === '456';
        });
    }

    public function test_smsir_sends_correct_associative_headers()
    {
        Http::fake([
            'api.sms.ir/*' => Http::response(['status' => 1, 'data' => ['messageId' => 1]], 200),
        ]);

        Sms::make('smsir')
            ->mobile('09123456789')
            ->template(123)
            ->params([['name' => 'code', 'value' => '1234']])
            ->sendVerifyCode();

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-API-KEY', 'test-key')
                && $request['mobile'] === '09123456789';
        });
    }

    public function test_invalid_mobile_is_rejected_before_http_request()
    {
        $this->expectException(InvalidArgumentException::class);

        Sms::make('kavenegar')
            ->mobile('not-a-number')
            ->template('verify')
            ->params(['token' => '123'])
            ->sendVerifyCode();
    }

    public function test_voice_call_is_rejected_for_unsupported_driver()
    {
        $this->expectException(UnsupportedFeatureException::class);

        Sms::make('smsir')
            ->mobile('09123456789')
            ->message('Hello')
            ->voiceCall();
    }

    public function test_kavenegar_http_errors_are_not_returned_as_success()
    {
        Http::fake([
            'api.kavenegar.com/*' => Http::response(['error' => 'bad request'], 422),
        ]);

        $this->expectException(\\Abolfazlrastegar\\LaravelSms\\Exceptions\\SmsException::class);

        Sms::make('kavenegar')
            ->mobile('09123456789')
            ->template('verify')
            ->params(['token' => '123'])
            ->sendVerifyCode();
    }
}
