<?php

declare(strict_types=1);

namespace NasiMail\Laravel\Mail;

use Illuminate\Support\Facades\Http;
use Throwable;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class NasiMailTransport extends AbstractTransport
{
    /** @var array<string, mixed> */
    protected array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();

        if (! $original instanceof Email) {
            throw new TransportException('NasiMail only supports Symfony Email messages.');
        }

        $baseUrl = (string) ($this->config['base_url'] ?? env('NASIMAIL_BASE_URL', ''));
        $secretKey = (string) ($this->config['secret_key'] ?? env('NASIMAIL_SECRET_KEY', ''));

        if ($baseUrl === '' || $secretKey === '') {
            throw new TransportException('NasiMail requires base_url and secret_key configuration.');
        }

        $payload = [
            'from' => $this->firstAddress($original->getFrom()),
            'from_name' => $this->firstName($original->getFrom()),
            'to' => $this->addressList($original->getTo()),
            'cc' => $this->addressList($original->getCc()),
            'bcc' => $this->addressList($original->getBcc()),
            'reply_to' => $this->addressList($original->getReplyTo()),
            'subject' => (string) $original->getSubject(),
            'text' => (string) ($original->getTextBody() ?? ''),
            'html' => (string) ($original->getHtmlBody() ?? ''),
        ];

        try {
            $response = Http::baseUrl($baseUrl)
                ->acceptJson()
                ->asJson()
                ->withToken($secretKey)
                ->withHeaders([
                    'Idempotency-Key' => (string) $message->getMessageId(),
                ])
                ->timeout((int) ($this->config['timeout'] ?? env('NASIMAIL_TIMEOUT', 10)))
                ->post('/api/v1/messages', $payload);
        } catch (Throwable $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            throw new TransportException(sprintf(
                'NasiMail API send failed with status %d: %s',
                $response->status(),
                (string) $response->body()
            ));
        }
    }

    public function __toString(): string
    {
        return 'nasimail';
    }

    /**
     * @param Address[] $addresses
     * @return array<int, string>
     */
    protected function addressList(array $addresses): array
    {
        return array_values(array_map(
            static fn (Address $address): string => $address->getAddress(),
            $addresses
        ));
    }

    /**
     * @param Address[] $addresses
     */
    protected function firstAddress(array $addresses): string
    {
        if ($addresses === []) {
            return '';
        }

        return $addresses[0]->getAddress();
    }

    /**
     * @param Address[] $addresses
     */
    protected function firstName(array $addresses): string
    {
        if ($addresses === []) {
            return '';
        }

        return $addresses[0]->getName();
    }
}
