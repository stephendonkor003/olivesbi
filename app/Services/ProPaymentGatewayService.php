<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ProIntegration;
use App\Models\ProPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ProPaymentGatewayService
{
    public function createCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        return match ($integration->provider) {
            'paystack' => $this->createPaystackCheckout($paymentRequest, $integration),
            'flutterwave' => $this->createFlutterwaveCheckout($paymentRequest, $integration),
            'stripe' => $this->createStripeCheckout($paymentRequest, $integration),
            'hubtel' => $this->createHubtelCheckout($paymentRequest, $integration),
            'expresspay' => $this->createExpressPayCheckout($paymentRequest, $integration),
            'ghipss' => $this->createConfiguredCheckout($paymentRequest, $integration),
            default => throw new RuntimeException('Unsupported payment provider.'),
        };
    }

    public function verify(ProPaymentRequest $paymentRequest, Request $request): array
    {
        $integration = $paymentRequest->integration;

        if (! $integration) {
            throw new RuntimeException('Payment integration not found.');
        }

        $result = match ($integration->provider) {
            'paystack' => $this->verifyPaystack($paymentRequest, $integration, $request),
            'flutterwave' => $this->verifyFlutterwave($paymentRequest, $integration, $request),
            'stripe' => $this->verifyStripe($paymentRequest, $integration, $request),
            'hubtel' => $this->verifyHubtelCallback($paymentRequest, $request),
            default => [
                'paid' => false,
                'message' => 'This provider requires webhook/manual settlement confirmation.',
                'raw' => $request->all(),
            ],
        };

        if ($result['paid'] ?? false) {
            $this->markPaid($paymentRequest, $result);
        }

        return $result;
    }

    private function createPaystackCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $secret = $this->requiredCredential($integration, 'secret_key');
        $reference = $paymentRequest->payment_reference ?: $this->reference('PSK', $paymentRequest);

        $response = Http::withToken($secret)
            ->baseUrl($integration->credential('base_url', 'https://api.paystack.co'))
            ->throw()
            ->post('transaction/initialize', [
                'email' => $paymentRequest->payer_email ?: $this->fallbackEmail($paymentRequest),
                'amount' => $this->minorAmount($paymentRequest->amount),
                'reference' => $reference,
                'currency' => strtoupper($paymentRequest->currency_code),
                'callback_url' => route('public.pro-payment-requests.callback', $paymentRequest),
                'metadata' => $this->metadata($paymentRequest),
            ]);

        return [
            'reference' => $reference,
            'payment_url' => $response->json('data.authorization_url'),
            'raw_response' => $response->json(),
        ];
    }

    private function createFlutterwaveCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $secret = $this->requiredCredential($integration, 'secret_key');
        $reference = $paymentRequest->payment_reference ?: $this->reference('FLW', $paymentRequest);

        $response = Http::withToken($secret)
            ->baseUrl($integration->credential('base_url', 'https://api.flutterwave.com/v3'))
            ->throw()
            ->post('payments', [
                'tx_ref' => $reference,
                'amount' => (string) $paymentRequest->amount,
                'currency' => strtoupper($paymentRequest->currency_code),
                'redirect_url' => route('public.pro-payment-requests.callback', $paymentRequest),
                'customer' => [
                    'email' => $paymentRequest->payer_email ?: $this->fallbackEmail($paymentRequest),
                    'phonenumber' => $paymentRequest->payer_phone,
                    'name' => $paymentRequest->payer_name ?: 'Rate payer',
                ],
                'customizations' => [
                    'title' => 'Olives BI Payment',
                    'description' => Str::limit($paymentRequest->description ?: 'Olives BI payment request', 120, ''),
                ],
                'meta' => $this->metadata($paymentRequest),
            ]);

        return [
            'reference' => $reference,
            'payment_url' => $response->json('data.link'),
            'raw_response' => $response->json(),
        ];
    }

    private function createStripeCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $secret = $this->requiredCredential($integration, 'secret_key');
        $reference = $paymentRequest->payment_reference ?: $this->reference('STR', $paymentRequest);

        $response = Http::withToken($secret)
            ->asForm()
            ->throw()
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => route('public.pro-payment-requests.callback', $paymentRequest).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('public.pro-payment-requests.show', $paymentRequest),
                'customer_email' => $paymentRequest->payer_email ?: $this->fallbackEmail($paymentRequest),
                'client_reference_id' => $reference,
                'line_items[0][price_data][currency]' => strtolower($paymentRequest->currency_code),
                'line_items[0][price_data][unit_amount]' => $this->minorAmount($paymentRequest->amount),
                'line_items[0][price_data][product_data][name]' => Str::limit($paymentRequest->description ?: 'Olives BI payment request', 120, ''),
                'line_items[0][quantity]' => 1,
                'metadata[pro_payment_request_uuid]' => $paymentRequest->uuid,
                'metadata[payment_reference]' => $reference,
            ]);

        return [
            'reference' => $reference,
            'payment_url' => $response->json('url'),
            'raw_response' => $response->json(),
        ];
    }

    private function createHubtelCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $clientId = $this->requiredCredential($integration, 'client_id');
        $clientSecret = $this->requiredCredential($integration, 'client_secret');
        $reference = $paymentRequest->payment_reference ?: $this->reference('HUB', $paymentRequest);
        $baseUrl = $integration->credential('base_url', 'https://payproxyapi.hubtel.com');

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->baseUrl($baseUrl)
            ->throw()
            ->post($integration->credential('checkout_path', 'items/initiate'), [
                'totalAmount' => (float) $paymentRequest->amount,
                'description' => $paymentRequest->description ?: 'Olives BI payment request',
                'callbackUrl' => route('public.pro-payment-requests.callback', $paymentRequest),
                'returnUrl' => route('public.pro-payment-requests.callback', $paymentRequest),
                'merchantAccountNumber' => $integration->credential('merchant_account_number'),
                'cancellationUrl' => route('public.pro-payment-requests.show', $paymentRequest),
                'clientReference' => $reference,
            ]);

        return [
            'reference' => $reference,
            'payment_url' => $response->json('data.checkoutUrl')
                ?? $response->json('data.paylinkUrl')
                ?? $response->json('checkoutUrl')
                ?? $response->json('paylinkUrl'),
            'raw_response' => $response->json(),
        ];
    }

    private function createExpressPayCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $merchantId = $this->requiredCredential($integration, 'merchant_id');
        $apiKey = $this->requiredCredential($integration, 'api_key');
        $reference = $paymentRequest->payment_reference ?: $this->reference('EXP', $paymentRequest);
        $baseUrl = $integration->credential('base_url', 'https://expresspaygh.com/api/direct');

        $response = Http::asForm()
            ->throw()
            ->post($baseUrl.'/submit.php', [
                'merchant-id' => $merchantId,
                'api-key' => $apiKey,
                'firstname' => $paymentRequest->payer_name ?: 'Olives',
                'lastname' => 'Payer',
                'email' => $paymentRequest->payer_email ?: $this->fallbackEmail($paymentRequest),
                'phonenumber' => $paymentRequest->payer_phone,
                'amount' => (float) $paymentRequest->amount,
                'currency' => strtoupper($paymentRequest->currency_code),
                'order-id' => $reference,
                'redirect-url' => route('public.pro-payment-requests.callback', $paymentRequest),
                'post-url' => route('public.pro-payment-requests.callback', $paymentRequest),
                'order-desc' => $paymentRequest->description ?: 'Olives BI payment request',
            ]);

        $raw = $response->json() ?: ['body' => $response->body()];
        $token = data_get($raw, 'token') ?: data_get($raw, 'data.token');

        return [
            'reference' => $reference,
            'payment_url' => $token ? $baseUrl.'/checkout.php?token='.$token : route('public.pro-payment-requests.show', $paymentRequest),
            'raw_response' => $raw,
        ];
    }

    private function createConfiguredCheckout(ProPaymentRequest $paymentRequest, ProIntegration $integration): array
    {
        $reference = $paymentRequest->payment_reference ?: $this->reference('GHP', $paymentRequest);
        $configuredUrl = $integration->credential('checkout_url') ?: $integration->credential('payment_url');

        return [
            'reference' => $reference,
            'payment_url' => $configuredUrl ?: route('public.pro-payment-requests.show', $paymentRequest),
            'raw_response' => [
                'message' => 'GhIPSS/GhQR settlement uses the configured provider URL or merchant instructions.',
                'merchant_id' => $integration->credential('merchant_id'),
            ],
        ];
    }

    private function verifyPaystack(ProPaymentRequest $paymentRequest, ProIntegration $integration, Request $request): array
    {
        $reference = $request->query('reference', $paymentRequest->payment_reference);
        $response = Http::withToken($this->requiredCredential($integration, 'secret_key'))
            ->baseUrl($integration->credential('base_url', 'https://api.paystack.co'))
            ->throw()
            ->get("transaction/verify/{$reference}");

        $paid = $response->json('data.status') === 'success';

        return [
            'paid' => $paid,
            'reference' => $reference,
            'channel' => $response->json('data.channel', 'paystack'),
            'raw' => $response->json(),
        ];
    }

    private function verifyFlutterwave(ProPaymentRequest $paymentRequest, ProIntegration $integration, Request $request): array
    {
        $transactionId = $request->query('transaction_id');

        if (! $transactionId) {
            return ['paid' => false, 'message' => 'Missing Flutterwave transaction id.', 'raw' => $request->all()];
        }

        $response = Http::withToken($this->requiredCredential($integration, 'secret_key'))
            ->baseUrl($integration->credential('base_url', 'https://api.flutterwave.com/v3'))
            ->throw()
            ->get("transactions/{$transactionId}/verify");

        $paid = $response->json('status') === 'success'
            && $response->json('data.status') === 'successful';

        return [
            'paid' => $paid,
            'reference' => $paymentRequest->payment_reference,
            'channel' => 'flutterwave',
            'raw' => $response->json(),
        ];
    }

    private function verifyStripe(ProPaymentRequest $paymentRequest, ProIntegration $integration, Request $request): array
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return ['paid' => false, 'message' => 'Missing Stripe session id.', 'raw' => $request->all()];
        }

        $response = Http::withToken($this->requiredCredential($integration, 'secret_key'))
            ->throw()
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        $paid = $response->json('payment_status') === 'paid';

        return [
            'paid' => $paid,
            'reference' => $paymentRequest->payment_reference ?: $response->json('client_reference_id'),
            'channel' => 'stripe',
            'raw' => $response->json(),
        ];
    }

    private function verifyHubtelCallback(ProPaymentRequest $paymentRequest, Request $request): array
    {
        $status = strtolower((string) ($request->input('status') ?: $request->input('ResponseCode')));
        $paid = in_array($status, ['success', 'paid', '0000', '0'], true);

        return [
            'paid' => $paid,
            'reference' => $paymentRequest->payment_reference,
            'channel' => 'hubtel',
            'raw' => $request->all(),
        ];
    }

    private function markPaid(ProPaymentRequest $paymentRequest, array $result): void
    {
        if ($paymentRequest->isPaid()) {
            return;
        }

        $paymentRequest->update([
            'status' => 'paid',
            'paid_at' => now(),
            'raw_response' => array_merge($paymentRequest->raw_response ?: [], ['verification' => $result['raw'] ?? $result]),
        ]);

        if ($paymentRequest->bill) {
            Payment::firstOrCreate(
                ['req_transaction_id' => $result['reference'] ?? $paymentRequest->payment_reference],
                [
                    'bills_id' => $paymentRequest->bill->bills_id,
                    'assembly_code' => $paymentRequest->assembly_code,
                    'amount' => $paymentRequest->amount,
                    'payment_mode' => $result['channel'] ?? $paymentRequest->provider,
                    'payment_source' => $paymentRequest->provider,
                    'transaction_status' => 'Success',
                    'transaction_id' => $result['reference'] ?? $paymentRequest->payment_reference,
                    'phone' => $paymentRequest->payer_phone,
                    'created_by' => optional($paymentRequest->requestedByAgent)->id ?? optional($paymentRequest->proUser)->id,
                    'authorization_url' => $paymentRequest->payment_url,
                ]
            );
        }
    }

    private function requiredCredential(ProIntegration $integration, string $key): string
    {
        $value = $integration->credential($key);

        if (blank($value)) {
            throw new RuntimeException("Missing {$integration->provider} credential: {$key}.");
        }

        return (string) $value;
    }

    private function fallbackEmail(ProPaymentRequest $paymentRequest): string
    {
        $seed = $paymentRequest->payer_phone ?: $paymentRequest->uuid;

        return Str::slug($seed ?: Str::random(8)).'@olivesbi.local';
    }

    private function minorAmount(mixed $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function reference(string $prefix, ProPaymentRequest $paymentRequest): string
    {
        return $prefix.'-'.$paymentRequest->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5));
    }

    private function metadata(ProPaymentRequest $paymentRequest): array
    {
        return [
            'pro_payment_request_uuid' => $paymentRequest->uuid,
            'bill_uuid' => $paymentRequest->bill_uuid,
            'waste_service_plan_uuid' => $paymentRequest->waste_service_plan_uuid,
            'assembly_code' => $paymentRequest->assembly_code,
            'purpose' => $paymentRequest->purpose,
        ];
    }
}
