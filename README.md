# NasiMail Laravel Client (Local Package)

[![Tests](https://github.com/Hostnasi-Technonologies/nasimail/actions/workflows/tests.yml/badge.svg)](https://github.com/Hostnasi-Technonologies/nasimail/actions/workflows/tests.yml)

This package provides a Laravel client with two delivery drivers:

- `api`: send messages to a NasiMail API instance via HTTP.
- `mail`: send using Laravel's mail transport (`MAIL_MAILER`, etc.).

Supported Laravel versions: 8.x to 12.x.

Note: the custom `MAIL_MAILER=nasimail` transport is available on Laravel 9+.
On Laravel 8, use the package `api` or `mail` driver via `NasiMailClient`.

## Docs

- Changelog: `CHANGELOG.md`
- Wiki: `WIKI.md`

## Testing

Run tests locally:

```bash
composer install
./vendor/bin/phpunit
```

CI validates Laravel 8 through 12 with a PHP/Laravel matrix:

- Laravel 8.83 on PHP 8.1
- Laravel 9.52 on PHP 8.1
- Laravel 10.48 on PHP 8.2
- Laravel 11.x on PHP 8.3
- Laravel 12.x on PHP 8.4

## Install in this monorepo

1. Add a path repository in your app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/nasimail",
      "options": { "symlink": true }
    }
  ]
}
```

2. Require the package:

```bash
composer require nasimail/laravel-client:*
```

3. Publish config:

```bash
php artisan vendor:publish --tag=nasimail-client-config
```

## Config

Set in `.env`:

```dotenv
NASIMAIL_DRIVER=api
NASIMAIL_BASE_URL=https://nasimail.hostnasi.com
NASIMAIL_SECRET_KEY=sk_live_xxx
```

For Laravel mail transport mode:

```dotenv
NASIMAIL_DRIVER=mail
# optional override, otherwise uses your default Laravel mailer
NASIMAIL_MAILER=log
```

For custom transport mode via Laravel Mail:

```dotenv
MAIL_MAILER=nasimail
NASIMAIL_BASE_URL=https://nasimail.hostnasi.com
NASIMAIL_SECRET_KEY=sk_live_xxx
```

## Usage

```php
use NasiMail\Laravel\NasiMailClient;

$response = app(NasiMailClient::class)->send([
    'from' => 'hello@example.com',
    'to' => ['user@example.com'],
    'subject' => 'Hello',
    'text' => 'Hi there',
]);
```
