<?php

namespace App\Actions\Payment;

use App\Support\PaymentAmounts;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MakePayment
{
    public function handlePaymentPaystackWebhook(Request $request)
    {
        $payload = $request->all();
        $event = $payload['event'] ?? null;

        Log::info('Paystack Webhook Payload:', $request->all());

        return;

        $data = $payload['data'];
        $reference = $data['reference'];
        $reference = $payload['data']['reference'];
        $amountPaid = (int) ($data['amount'] / 100);

        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ((int) $payment->amount !== $amountPaid) {
            // Optional: log tampering
            return response()->json(['status' => 'amount_mismatch'], 400);
        }

        // Update payment status if not already marked as success
        if ($payment->status !== 'Success') {
            $payment->update([
                'transaction_status' => 'Success',
                'payment_method' => $data['channel'] ?? 'paystack',
                'paid_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    public static function acceptPayment(User $user, $amount, $reference, $phone, $network)
    {
        $paystackEmail = $user->email ?: "{$phone}@ermsplus.africa";

        $payload = [
            'amount' => PaymentAmounts::minor($amount),
            'email' => $paystackEmail,
            'currency' => 'GHS',
            'reference' => $reference,
            'mobile_money' => [
                'provider' => $network,
                'phone' => $phone,
            ],
        ];

        try {
            $response = Http::withToken(config('services.paystack.secret_key'))
                ->throw()
                ->post('https://api.paystack.co/charge', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
            ];
        } catch (\Exception $e) {
            report($e);

            return [
                'status' => false,
            ];
        }
    }

    // Paystack callback for transaction verification
    public function verify(Request $request)
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('payments.index')->with('error', 'Missing transaction reference.');
        }

        // Call Paystack API
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->failed()) {
            return redirect()->route('payments.index')->with('error', 'Unable to verify payment.');
        }

        $data = $response->json('data');

        if ($data['status'] !== 'success') {
            return redirect()->route('payments.index')->with('error', 'Payment not successful.');
        }

        // Verify amount and update DB
        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('payments.index')->with('error', 'Payment record not found.');
        }

        if ((int) $payment->amount !== (int) ($data['amount'] / 100)) {
            return redirect()->route('payments.index')->with('error', 'Amount mismatch detected.');
        }

        $payment->update([
            'transaction_status' => 'Success',
            'payment_mode' => $data['channel'] ?? 'paystack',
        ]);

        \Log::info('Paystack verification route hit', ['reference' => $request->reference]);

        return redirect()->route('payments.index', $payment->id)->with('success', 'Payment verified successfully.');
    }

    public function webhook(Request $request)
    {
        $data = $request->all();

        if (! isset($data['data']['reference'])) {
            return response('No reference', 400);
        }

        $reference = $data['data']['reference'];
        $status = $data['event'] === 'charge.success' ? 'success' : 'failed';

        $payment = Payment::where('transaction_id', $reference)->first();

        if ($payment) {
            $payment->update(['transaction_status' => $status]);
        }

        return response('Webhook received', 200);
    }

    public static function checkPaymentStatus()
    {
        $transaction_id = 'your_transaction_id';
        $client_id = 'your_client_id';
        $client_secret = 'your_client_secret';

        $url = "https://api.theteller.net/v1.1/transaction/$transaction_id/status";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic '.base64_encode("$client_id:$client_secret"),
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($result && $result['status'] == 'approved') {
            echo 'MoMo Payment successful';
        } else {
            echo 'MoMo Payment failed';
        }
    }
}
