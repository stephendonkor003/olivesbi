<?php

namespace App\Actions\SMS;

use AlhajiAki\Sms\Sender;
use AlhajiAki\Sms\Sms;
use Illuminate\Validation\ValidationException;

class SendSensitiveSMS
{
    public static function ensureAvailable(): void
    {
        if (self::senders() === []) {
            self::unavailable();
        }
    }

    public static function sendSMS($phone, string $message): void
    {
        try {
            $phone = phone($phone, ['GH'])->formatE164();
        } catch (\Throwable) {
            self::unavailable();
        }

        foreach (self::senders() as $candidate) {
            try {
                $sender = new Sender($candidate['name'], Sms::createSender($candidate['config']), app('events'));
                $sender->alwaysFrom($candidate['from']);
                if ($sender->send($message, $phone) !== null) {
                    return;
                }
            } catch (\Throwable) {
                // Provider exceptions can contain the code, credentials and response.
                // Retry only another configured phone transport without reporting them.
            }
        }

        self::unavailable();
    }

    private static function senders(): array
    {
        $name = config('sms.default');
        $config = is_string($name) ? config('sms.senders.'.$name) : null;
        if (! is_array($config)) {
            return [];
        }
        $from = $config['from'] ?? config('sms.from');

        return self::resolve($name, is_string($from) ? $from : '', []);
    }

    private static function resolve(string $name, string $from, array $ancestors): array
    {
        if (count($ancestors) >= 16 || in_array($name, $ancestors, true)) {
            return [];
        }
        $config = config('sms.senders.'.$name);
        if (! is_array($config)) {
            return [];
        }
        $driver = $config['sender'] ?? null;
        if (in_array($driver, ['failover', 'roundrobin'], true)) {
            $resolved = [];
            foreach (is_array($config['senders'] ?? null) ? $config['senders'] : [] as $child) {
                if (is_string($child)) {
                    array_push($resolved, ...self::resolve($child, $from, [...$ancestors, $name]));
                }
            }

            return $resolved;
        }

        $required = match ($driver) {
            'mnotify', 'arkesel' => ['api_key'],
            'pywe' => ['public_key', 'secret_key'],
            'frog_sms' => ['username', 'password', 'service_type', 'message_type'],
            default => null,
        };
        if ($required === null || ($driver === 'arkesel' && ($config['sandbox'] ?? false))) {
            return [];
        }
        foreach ($required as $key) {
            if (! is_string($config[$key] ?? null) || trim($config[$key]) === '') {
                return [];
            }
        }
        $from = $from ?: ($driver === 'frog_sms' ? '' : ($config['sender_id'] ?? ''));
        if (! is_string($from) || trim($from) === '') {
            return [];
        }

        return [['name' => $name, 'config' => $config, 'from' => $from]];
    }

    private static function unavailable(): never
    {
        throw ValidationException::withMessages([
            'phone' => 'SMS verification is temporarily unavailable. Please try again later or contact support.',
        ]);
    }
}
