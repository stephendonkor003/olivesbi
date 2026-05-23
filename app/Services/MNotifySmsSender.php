<?php

namespace App\Services;

use AlhajiAki\Sms\Exception\FailedSmsException;
use AlhajiAki\Sms\Senders\SenderInterface;
use AlhajiAki\Sms\SentMessage;
use AlhajiAki\Sms\TextMessage;
use Illuminate\Support\Facades\Http;

class MNotifySmsSender implements SenderInterface
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
            'sender' => $message->getFrom() ?: $this->config['sender_id'],
            'message' => $message->getMessage(),
            'recipient' => $recipients,
            'is_schedule' => 'false',
            'schedule_date' => '',
        ];

        $response = Http::baseUrl('https://api.mnotify.com/api/')
            ->asJson()
            ->withHeader('api-key', $this->config['api_key'])
            ->post("sms/quick?key={$this->config['api_key']}", $payload);

        if ($response->failed() || $response->json('status') !== 'success') {
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
        return 'mnotify';
    }
}
