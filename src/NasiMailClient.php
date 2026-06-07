<?php

declare(strict_types=1);

namespace NasiMail\Laravel;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;

class NasiMailClient
{
    /**
     * Send a message using the configured driver.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|Response
     */
    public function send(array $payload, ?string $idempotencyKey = null): array|Response
    {
        $driver = (string) config('nasimail-client.driver', 'api');

        return match ($driver) {
            'api' => $this->sendViaApi($payload, $idempotencyKey),
            'mail' => $this->sendViaMail($payload),
            default => throw new InvalidArgumentException("Unsupported NasiMail driver [{$driver}]."),
        };
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function sendViaApi(array $payload, ?string $idempotencyKey = null): Response
    {
        $baseUrl = (string) config('nasimail-client.api.base_url');
        $secretKey = (string) config('nasimail-client.api.secret_key');
        $timeout = (int) config('nasimail-client.api.timeout', 10);
        $retryTimes = (int) config('nasimail-client.api.retry_times', 3);
        $retrySleepMs = (int) config('nasimail-client.api.retry_sleep_ms', 400);

        if ($secretKey === '') {
            throw new InvalidArgumentException('NASIMAIL_SECRET_KEY is required for api driver.');
        }

        $key = $idempotencyKey ?: (string) Str::uuid();

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->asJson()
            ->withToken($secretKey)
            ->withHeaders([
                'Idempotency-Key' => $key,
            ])
            ->retry($retryTimes, $retrySleepMs, static function ($exception): bool {
                $status = optional($exception->response)->status();
                return $status === 429 || ($status >= 500 && $status <= 599);
            })
            ->timeout($timeout)
            ->post('/api/v1/messages', $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    protected function sendViaMail(array $payload): array
    {
        $to = (array) ($payload['to'] ?? []);
        $subject = (string) ($payload['subject'] ?? '');
        $textBody = (string) ($payload['text'] ?? '');
        $htmlBody = (string) ($payload['html'] ?? '');

        if ($to === []) {
            throw new InvalidArgumentException('The payload requires at least one recipient in [to].');
        }

        if ($subject === '') {
            throw new InvalidArgumentException('The payload requires [subject].');
        }

        if ($textBody === '' && $htmlBody === '') {
            throw new InvalidArgumentException('The payload requires [text] or [html].');
        }

        $fromAddress = (string) ($payload['from'] ?? config('mail.from.address'));
        $fromName = (string) ($payload['from_name'] ?? config('mail.from.name'));
        $cc = (array) ($payload['cc'] ?? []);
        $bcc = (array) ($payload['bcc'] ?? []);
        $replyTo = (array) ($payload['reply_to'] ?? []);

        $body = $textBody !== '' ? $textBody : strip_tags($htmlBody);
        $mailer = config('nasimail-client.mail.mailer');
        $mail = $mailer ? Mail::mailer((string) $mailer) : Mail::mailer();

        $mail->raw($body, static function ($message) use ($to, $subject, $fromAddress, $fromName, $cc, $bcc, $replyTo): void {
            $message->to($to);
            $message->subject($subject);

            if ($fromAddress !== '') {
                $message->from($fromAddress, $fromName !== '' ? $fromName : null);
            }

            if ($cc !== []) {
                $message->cc($cc);
            }

            if ($bcc !== []) {
                $message->bcc($bcc);
            }

            if ($replyTo !== []) {
                $message->replyTo($replyTo);
            }
        });

        return [
            'success' => true,
            'message' => 'Message handed to Laravel mail driver.',
            'data' => [
                'driver' => 'mail',
                'mailer' => $mailer ?: config('mail.default'),
                'status' => 'accepted',
            ],
            'errors' => null,
        ];
    }
}
