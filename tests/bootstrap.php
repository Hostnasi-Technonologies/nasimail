<?php

declare(strict_types=1);

namespace {
    require __DIR__ . '/../vendor/autoload.php';
}

namespace NasiMail\Laravel {
    use Tests\Support\TestConfig;

    function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return null;
        }

        return TestConfig::get($key, $default);
    }
}
