# Laravel SMS

A Laravel package with a common API for Kavenegar, SMS.ir and Melipayamak.

## Requirements

- PHP 7.4+
- Laravel 8–12 (subject to the PHP version required by each Laravel release)

## Installation

```bash
composer require abolfazlrastegar/laravel-sms
php artisan vendor:publish --provider="Abolfazlrastegar\\LaravelSms\\Providers\\SmsServiceProvider" --tag=config
```

## Configuration

Set credentials in `.env`; never commit provider keys.

```dotenv
SMS_DRIVER=Kavenegar
SMS_TIMEOUT=15
SMS_QUEUE=sms
SMS_QUEUE_TRIES=3
SMS_QUEUE_TIMEOUT=60
KAVENEGAR_API_KEY=
SMSIR_API_KEY=
MELIPAYAMAK_USERNAME=
MELIPAYAMAK_PASSWORD=
MELIPAYAMAK_FROM=
```

The package queues SMS operations automatically using Laravel's configured `queue.default` connection. No application-level Job class is required.

- `sync`: sends immediately and returns the provider response, preserving synchronous behavior.
- `database`, `redis`, or another Laravel queue connection: dispatches the package's internal `SendSmsJob`.
- The queue name defaults to `sms` and can be changed with `SMS_QUEUE`.
- Attempts and timeout are configurable with `SMS_QUEUE_TRIES` and `SMS_QUEUE_TIMEOUT`.

Configure the queue connection in the host Laravel application as usual and run its queue worker, for example:

```bash
php artisan queue:work --queue=sms
```

For the database driver, the host application must have Laravel's queue table migration installed. The package intentionally does not create or modify application database tables.

After changing environment values in production, clear/rebuild Laravel's config cache.

## Usage

The fluent API remains compatible with the original package:

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
