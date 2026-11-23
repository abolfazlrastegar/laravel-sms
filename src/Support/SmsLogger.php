<?php

namespace Abolfazlrastegar\LaravelSms\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SmsLogger
{
    public static function create($driver, $operation, array $payload, $status)
    {
        if (!config('sms.logging.enabled', true)) {
            return null;
        }

        try {
            if (!Schema::hasTable('sms_logs')) {
                return null;
            }

            return DB::table('sms_logs')->insertGetId([
                'driver' => (string) $driver,
                'operation' => (string) $operation,
                'recipient' => self::maskRecipients(isset($payload['mobile']) ? $payload['mobile'] : null),
                'status' => $status,
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $exception) {
            return null;
        }
    }

    public static function update($id, array $values)
    {
        if (!$id || !config('sms.logging.enabled', true)) {
            return;
        }

        try {
            if (!Schema::hasTable('sms_logs')) {
                return;
            }

            $values['updated_at'] = now();
            DB::table('sms_logs')->where('id', $id)->update($values);
        } catch (Throwable $exception) {
            // Logging must never interrupt SMS delivery.
        }
    }

    public static function response($response)
    {
        $data = is_array($response) ? $response : [];
        $messageId = data_get($data, 'entries.0.messageid')
            ?: data_get($data, 'data.messageId')
            ?: data_get($data, 'messageId');

        $code = data_get($data, 'return.status')
            ?: data_get($data, 'status')
            ?: data_get($data, 'code');

        return [
            'provider_message_id' => $messageId ? (string) $messageId : null,
            'response_code' => $code !== null ? (string) $code : null,
        ];
    }

    public static function maskRecipients($recipients)
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];
        $masked = [];

        foreach ($recipients as $recipient) {
            if (!is_scalar($recipient)) {
                continue;
            }

            $value = (string) $recipient;
            $length = strlen($value);
            $masked[] = $length > 4
                ? str_repeat('*', max(0, $length - 4)) . substr($value, -4)
                : '****';
        }

        return $masked ? implode(',', $masked) : null;
    }
}
