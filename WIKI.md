# Package Wiki

## Overview

`nasimail/laravel-client` is a local Laravel package that supports two send modes:

- `api`: Sends messages to NasiMail over HTTP.
- `mail`: Sends messages through Laravel's native Mail system.

It also registers a custom mailer transport named `nasimail`, so you can use:

```dotenv
MAIL_MAILER=nasimail
```

## Folder Structure

- `src/NasiMailClient.php`: main client API.
- `src/NasiMailServiceProvider.php`: service provider and transport registration.
- `src/Mail/NasiMailTransport.php`: custom Symfony transport for Laravel mailer.
- `config/nasimail-client.php`: package config.

## Configuration

### API driver

```dotenv
NASIMAIL_DRIVER=api
NASIMAIL_BASE_URL=https://nasimail.test
NASIMAIL_SECRET_KEY=sk_live_xxx
NASIMAIL_TIMEOUT=10
NASIMAIL_RETRY_TIMES=3
NASIMAIL_RETRY_SLEEP_MS=400
```

### Laravel mail driver (uses `MAIL_MAILER`)

```dotenv
NASIMAIL_DRIVER=mail
MAIL_MAILER=log
```

Optional explicit mailer override for package mail mode:

```dotenv
NASIMAIL_MAILER=log
```

### Custom NasiMail transport

```dotenv
MAIL_MAILER=nasimail
NASIMAIL_BASE_URL=https://nasimail.test
NASIMAIL_SECRET_KEY=sk_live_xxx
```

## Usage Examples

### 1) Send via package API driver

```php
use NasiMail\Laravel\NasiMailClient;

$response = app(NasiMailClient::class)->send([
    'from' => 'hello@yourdomain.com',
    'to' => ['user@example.com'],
    'subject' => 'Welcome',
    'text' => 'Thanks for joining us.',
]);
```

### 2) Send via Laravel Mail + `nasimail`

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Hello from nasimail transport', function ($message) {
    $message->to('user@example.com')
        ->subject('NasiMail test');
});
```

## Testing and CI

### Local tests

```bash
composer install
./vendor/bin/phpunit
```

### GitHub Actions

- Workflow file: `.github/workflows/tests.yml`
- Triggers: push and pull request
- Matrix: PHP 8.2, 8.3, and 8.4
- Includes Composer cache to speed up dependency installation

## Behavior Notes

- `MAIL_MAILER=log` writes emails to logs and does not deliver externally.
- `MAIL_MAILER=nasimail` delivers through NasiMail API.
- For retry-safe API sends, use idempotency keys (the package auto-generates one when omitted).

## Troubleshooting

### Error: unsupported driver

Ensure `NASIMAIL_DRIVER` is either `api` or `mail`.

### Error: base_url and secret_key required

Set:

```dotenv
NASIMAIL_BASE_URL=...
NASIMAIL_SECRET_KEY=...
```

### Mail sends but no external delivery

If `MAIL_MAILER=log`, this is expected. Switch to `smtp` or `nasimail` for real delivery.
