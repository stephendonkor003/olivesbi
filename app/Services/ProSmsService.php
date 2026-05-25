<?php

namespace App\Services;

use App\Models\ProIntegration;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ProSmsService
{
    public function sendForPro(User $proUser, string $phone, string $message): bool
    {
        $integration = ProIntegration::sms()
            ->default()
            ->where('pro_user_uuid', $proUser->uuid)
            ->first();

        if (! $integration) {
            Log::warning('No active Olives Pro SMS integration configured.', [
                'pro_user_uuid' => $proUser->uuid,
                'phone' => $phone,
            ]);

            return false;
        }

        try {
            return match ($integration->provider) {
                'hubtel' => $this->sendHubtel($integration, $phone, $message),
                'mnotify' => $this->sendMnotify($integration, $phone, $message),
                'twilio' => $this->sendTwilio($integration, $phone, $message),
                default => throw new RuntimeException('Unsupported SMS provider.'),
            };
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function sendHubtel(ProIntegration $integration, string $phone, string $message): bool
    {
        $response = Http::baseUrl($integration->credential('base_url', 'https://devp-sms03726-api.hubtel.com/v1/messages'))
            ->throw()
            ->get($integration->credential('send_path', 'send'), [
                'clientid' => $this->requiredCredential($integration, 'client_id'),
                'clientsecret' => $this->requiredCredential($integration, 'client_secret'),
                'from' => $this->requiredCredential($integration, 'sender_id'),
                'to' => $this->formatPhone($phone),
                'content' => $message,
            ]);

        return $response->successful();
    }

    private function sendMnotify(ProIntegration $integration, string $phone, string $message): bool
    {
        $apiKey = $this->requiredCredential($integration, 'api_key');

        $response = Http::baseUrl($integration->credential('base_url', 'https://api.mnotify.com/api'))
            ->asJson()
            ->withHeader('api-key', $apiKey)
            ->throw()
            ->post('sms/quick?key='.$apiKey, [
                'sender' => $this->requiredCredential($integration, 'sender_id'),
                'message' => $message,
                'recipient' => [$this->formatPhone($phone)],
                'is_schedule' => 'false',
                'schedule_date' => '',
            ]);

        return $response->successful();
    }

    private function sendTwilio(ProIntegration $integration, string $phone, string $message): bool
    {
        $sid = $this->requiredCredential($integration, 'account_sid');
        $payload = [
            'To' => $this->formatPhone($phone),
            'Body' => $message,
        ];

        if (filled($integration->credential('messaging_service_sid'))) {
            $payload['MessagingServiceSid'] = $integration->credential('messaging_service_sid');
        } else {
            $payload['From'] = $this->requiredCredential($integration, 'from_number');
        }

        $response = Http::withBasicAuth($sid, $this->requiredCredential($integration, 'auth_token'))
            ->asForm()
            ->throw()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", $payload);

        return $response->successful();
    }

    private function requiredCredential(ProIntegration $integration, string $key): string
    {
        $value = $integration->credential($key);

        if (blank($value)) {
            throw new RuntimeException("Missing {$integration->provider} SMS credential: {$key}.");
        }

        return (string) $value;
    }

    private function formatPhone(string $phone): string
    {
        try {
            return phone($phone)->formatE164();
        } catch (Throwable) {
            try {
                return phone($phone, ['GH'])->formatE164();
            } catch (Throwable) {
                return $phone;
            }
        }
    }
}
