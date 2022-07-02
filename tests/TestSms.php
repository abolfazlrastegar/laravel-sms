<?php

namespace Tests;

use Abolfazlrastegar\LaravelSms\Sms;
use PHPUnit\Framework\TestCase;

class TestSms extends TestCase
{
    /** @test  */
    public function testSendVerifyCode ()
    {
        $result = Sms::make('Kavenegar')
            ->mobile('09105805777')
            ->template(548762)
            ->params(['token' => 122254])
            ->sendVerifyCode();

        $this->assertEquals($result, $result);
    }

    /** @test  */
    public function testSendMessageGroup ()
    {
        $result = Sms::make('Kavenegar')
         ->mobile(['09105805772', '09105805772', '09105805772'])
         ->message(['set message for send1', 'set message for send2', 'set message for send3'])
         ->params([
             'sender' => ['5455557', '987565423', '6322154'],
             'date' => 'اختیاری',
             'type' => 'اختیاری',
             'localmessageids' => 'اختیاری',
         ])
         ->sendMessageGroup();

        $this->assertEquals($result, $result);
    }

    /** @test  */
    public function testSendMessages ()
    {
        $result = Sms::make('Kavenegar')
            ->mobile(['09105805772', '09105805772', '09105805772'])
            ->message('set message for send')
            ->params([
                'sender' => '6322154',
                'date' => 'اختیاری',
                'type' => 'اختیاری',
                'localmessageids' => 'اختیاری',
            ])
            ->sendMessages();

        $this->assertEquals($result, $result);
    }

    /** @test  */
    public function testVoiceCall ()
    {
        $result = Sms::make('Kavenegar')
            ->message('سلام')
            ->mobile('09105805772')
            ->voiceCall();

        $this->assertEquals($result,  $result);
    }
}
