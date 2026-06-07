<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use NasiMail\Laravel\NasiMailClient;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestConfig;

final class NasiMailClientTest extends TestCase
{
    public function test_it_throws_for_unsupported_driver(): void
    {
        TestConfig::set([
            'nasimail-client.driver' => 'queue',
        ]);

        $client = new NasiMailClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported NasiMail driver [queue].');

        $client->send(['subject' => 'Hello']);
    }

    public function test_mail_driver_requires_at_least_one_recipient(): void
    {
        TestConfig::set([
            'nasimail-client.driver' => 'mail',
        ]);

        $client = new NasiMailClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The payload requires at least one recipient in [to].');

        $client->send([
            'subject' => 'Hello',
            'text' => 'Body',
        ]);
    }

    public function test_mail_driver_requires_subject(): void
    {
        TestConfig::set([
            'nasimail-client.driver' => 'mail',
        ]);

        $client = new NasiMailClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The payload requires [subject].');

        $client->send([
            'to' => ['user@example.com'],
            'text' => 'Body',
        ]);
    }

    public function test_mail_driver_requires_text_or_html(): void
    {
        TestConfig::set([
            'nasimail-client.driver' => 'mail',
        ]);

        $client = new NasiMailClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The payload requires [text] or [html].');

        $client->send([
            'to' => ['user@example.com'],
            'subject' => 'Hello',
        ]);
    }
}
