# Laravel SMS

A Laravel package with a common API for Kavenegar, SMS.ir and Melipayamak.

## Requirements

- PHP 7.4+ (the host application's PHP version must also satisfy the selected Laravel release)
- Laravel 8, 9, 10, 11, 12 and 13

Laravel's minimum PHP versions vary by major release: Laravel 8 supports PHP 7.4; Laravel 9 requires PHP 8.0.2+; Laravel 10 requires PHP 8.1+; Laravel 11 and 12 require PHP 8.2+; Laravel 13 requires PHP 8.3+.

## Installation

```bash
composer require abolfazlrastegar/laravel-sms
php artisan vendor:publish --provider="Abolfazlrastegar\\LaravelSms\\Providers\\SmsServiceProvider" --tag=config
php artisan vendor:publish --provider="Abolfazlrastegar\\LaravelSms\\Providers\\SmsServiceProvider" --tag=migrations
php artisan migrate
```

## Configuration

Set credentials in `.env`; never commit provider keys.

```dotenv
SMS_DRIVER=Kavenegar
SMS_MODE=sync
SMS_TIMEOUT=15
SMS_LOG_ENABLED=true
SMS_QUEUE=sms
SMS_QUEUE_TRIES=3
SMS_QUEUE_TIMEOUT=60
KAVENEGAR_API_KEY=
SMSIR_API_KEY=
MELIPAYAMAK_USERNAME=
MELIPAYAMAK_PASSWORD=
MELIPAYAMAK_FROM=
```

Sending mode is controlled explicitly with `SMS_MODE`. The default is `sync`, so installing the package does not change the application's existing behavior. No application-level Job class is required.

- `SMS_MODE=sync`: sends immediately and returns the provider response.
- `SMS_MODE=queue`: dispatches the package's internal `SendSmsJob` using Laravel's `queue.default` connection (for example `database` or `redis`).
- The queue name defaults to `sms` and can be changed with `SMS_QUEUE`.
- Attempts and timeout are configurable with `SMS_QUEUE_TRIES` and `SMS_QUEUE_TIMEOUT`.

### Delivery logs

The package can persist send lifecycle records in the `sms_logs` table for both sync and queue modes. Logging is enabled by default and can be disabled with `SMS_LOG_ENABLED=false`. Publish the package migration and run `php artisan migrate` to create the table. If the migration has not been run, logging safely becomes a no-op and does not interrupt SMS sending.

The log stores the driver, operation, masked recipient number(s), lifecycle status, provider message ID/response code when available, attempt count, duration, and error message. Message bodies, templates, parameters, and provider credentials are never intentionally stored. Keep in mind that exception text comes from HTTP/provider libraries; do not expose raw log records publicly.

Statuses include `queued`, `processing`, `retrying`, `sent`, and `failed`. The `metadata` JSON column is reserved for safe, non-sensitive additional context.

Configure the queue connection in the host Laravel application as usual and run its queue worker, for example:

```bash
php artisan queue:work --queue=sms
```

For the database queue driver, the host application must also have Laravel's standard queue table migration installed. The package does not create or modify Laravel's queue tables.

After changing environment values in production, clear/rebuild Laravel's config cache.

## Usage

```php
use Abolfazlrastegar\\LaravelSms\\Facades\\Sms;

Sms::make('kavenegar')
    ->mobile('09123456789')
    ->template('verify')
    ->params(['token' => '123456'])
    ->sendVerifyCode();
```

Send a regular SMS:

```php
Sms::make('smsir')
    ->mobile(['09123456789', '09123456780'])
    ->message('Your message')
    ->params(['lineNumber' => '3000XXXX'])
    ->sendMessages();
```

Group send requires matching recipient/message counts. Kavenegar additionally requires a sender array with the same number of entries.

```php
Sms::make('kavenegar')
    ->mobile(['09123456789', '09123456780'])
    ->message(['Message one', 'Message two'])
    ->params(['sender' => ['1000', '1000']])
    ->sendMessageGroup();
```

Voice calls are supported by Kavenegar only. Unsupported driver capabilities throw `UnsupportedFeatureException`.

## Driver notes

- Kavenegar uses its GET API with query parameters; Laravel encodes values safely.
- SMS.ir uses the `X-API-KEY` header and JSON request bodies.
- Melipayamak uses its REST SendSMS and BaseServiceNumber endpoints. For verification, pass the text template in `params(['text' => '...'])` and the provider body ID as `template()`.
- Queue workers retry failed jobs according to the configured attempt count. Be aware that retrying after a provider timeout can produce duplicate SMS messages.
- A successful API response means the provider accepted the request; it does not prove delivery to the handset.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Tests use Laravel's HTTP fake and never send real SMS messages.
