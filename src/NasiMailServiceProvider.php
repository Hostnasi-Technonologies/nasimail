<?php

declare(strict_types=1);

namespace NasiMail\Laravel;

use Illuminate\Support\ServiceProvider;

class NasiMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nasimail-client.php', 'nasimail-client');

        $this->app->singleton(NasiMailClient::class, static function (): NasiMailClient {
            return new NasiMailClient();
        });
    }

    public function boot(): void
    {
        $transportClass = self::resolveTransportClass();

        if ($transportClass !== null) {
            $this->app->make('mail.manager')->extend('nasimail', static function (array $config) use ($transportClass) {
                return new $transportClass($config);
            });
        }

        $this->publishes([
            __DIR__ . '/../config/nasimail-client.php' => $this->app->configPath('nasimail-client.php'),
        ], 'nasimail-client-config');
    }

    protected static function resolveTransportClass(): ?string
    {
        if (class_exists(\Symfony\Component\Mailer\Transport\AbstractTransport::class)) {
            return __NAMESPACE__ . '\\Mail\\NasiMailTransport';
        }

        return null;
    }
}
