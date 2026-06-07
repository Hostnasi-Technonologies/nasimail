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
        $this->app->make('mail.manager')->extend('nasimail', static function (array $config) {
            $transportClass = __NAMESPACE__ . '\\Mail\\NasiMailTransport';
            return new $transportClass($config);
        });

        $this->publishes([
            __DIR__ . '/../config/nasimail-client.php' => config_path('nasimail-client.php'),
        ], 'nasimail-client-config');
    }
}
