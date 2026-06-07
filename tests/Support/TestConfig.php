<?php

declare(strict_types=1);

namespace Tests\Support;

final class TestConfig
{
    /** @var array<string, mixed> */
    private static array $values = [];

    /** @param array<string, mixed> $values */
    public static function set(array $values): void
    {
        self::$values = $values;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$values[$key] ?? $default;
    }
}
