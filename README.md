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
KAVENEGAR_API_KEY=
SMSIR_API_KEY=
MELIPAYAMAK_USERNAME=
MELIPAYAMAK_PASSWORD=
MELIPAYAMAK_FROM=
```

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
- No automatic retries are performed. Retrying a send after a timeout can produce duplicate SMS messages.
- A successful API response means the provider accepted the request; it does not prove delivery to the handset.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Tests use Laravel's HTTP fake and never send real SMS messages.
