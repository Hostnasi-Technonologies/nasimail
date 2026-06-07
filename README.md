# NasiMail Laravel Client (Local Package)

This package provides a Laravel client with two delivery drivers:

- `api`: send messages to a NasiMail API instance via HTTP.
- `mail`: send using Laravel's mail transport (`MAIL_MAILER`, etc.).

## Docs

- Changelog: `CHANGELOG.md`
- Wiki: `WIKI.md`

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
