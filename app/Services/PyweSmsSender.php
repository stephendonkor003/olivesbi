<?php

namespace App\Services;

use AlhajiAki\Sms\Exception\FailedSmsException;
use AlhajiAki\Sms\Senders\SenderInterface;
use AlhajiAki\Sms\SentMessage;
use AlhajiAki\Sms\TextMessage;
use Illuminate\Support\Facades\Http;

class PyweSmsSender implements SenderInterface
{
    /**
     * Create a new Twilio sender instance.
     */
    public function __construct(protected array $config) {}

    /**
     * {@inheritdoc}
     */
    public function send(TextMessage $message): ?SentMessage
    {
        $recipients = is_string($message->getTo()) ? [$message->getTo()] : $message->getTo();

        $payload = [
            'api_key_public' => $this->config['public_key'],
            'api_key_secret' => $this->config['secret_key'],
            'message' => $message->getMessage(),
            'recipients' => $recipients,
            'sender_id' => $message->getFrom() ?: $this->config['sender_id'],
            'scheduled' => false,
            'time_scheduled' => null,
        ];

        $response = Http::baseUrl('https://pushr.pywe.org/api/client')
            ->asJson()
            ->post('sms/send-sms', $payload);

        if ($response->failed() || $response->json('status') !== 'sent') {
            throw new FailedSmsException(
                $response->json('message', 'SMS request failed!'), // @phpstan-ignore-line
                [
                    'message' => $message->toArray(),
                    'payload' => $payload,
                    'response' => $response->json(),
                ]
            );
        }

        return (new SentMessage($message))->appendDebug($response->body());
    }

    /**
     * Get the string representation of the sender.
     */
    public function __toString(): string
    {
        return 'pywe';
    }
}
