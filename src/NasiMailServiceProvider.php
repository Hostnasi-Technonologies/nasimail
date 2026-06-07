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

        $transportClass = self::resolveTransportClass();

        if ($transportClass !== null) {
            $this->app->afterResolving('mail.manager', static function ($manager) use ($transportClass): void {
                $manager->extend('nasimail', static function (array $config) use ($transportClass) {
                    return new $transportClass($config);
                });
            });
        }
    }

    public function boot(): void
    {
        $this->registerDefaultMailerConfig();

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

    protected function registerDefaultMailerConfig(): void
    {
        $existing = (array) $this->app['config']->get('mail.mailers.nasimail', []);

        $defaults = [
            'transport' => 'nasimail',
            'base_url' => env('NASIMAIL_BASE_URL', ''),
            'secret_key' => env('NASIMAIL_SECRET_KEY', ''),
            'timeout' => (int) env('NASIMAIL_TIMEOUT', 10),
        ];

        $this->app['config']->set('mail.mailers.nasimail', array_replace($defaults, $existing));
    }
}
