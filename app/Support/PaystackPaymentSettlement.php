<?php

namespace App\Support;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaystackPaymentSettlement
{
    public static function hasValidSignature(string $body, mixed $signature): bool
    {
        $key = config('services.paystack.secret_key');

        return is_string($key) && trim($key) !== '' && is_string($signature)
            && preg_match('/\A[0-9a-f]{128}\z/D', $signature) === 1
            && hash_equals(hash_hmac('sha512', $body, $key), $signature);
    }

    /** A provider response is evidence only for this exact existing payment. */
    public static function settle(string $reference, mixed $data): ?Payment
    {
        if ($reference === '' || ! is_array($data) || ($data['reference'] ?? null) !== $reference
            || ($data['status'] ?? null) !== 'success' || ($data['currency'] ?? null) !== 'GHS'
            || ! is_int($data['amount'] ?? null) || $data['amount'] <= 0) {
            return null;
        }

        return DB::transaction(function () use ($reference, $data) {
            // Refuse ambiguous legacy references rather than crediting an arbitrary row.
            $payments = Payment::where('transaction_id', $reference)->lockForUpdate()->limit(2)->get();
            if ($payments->count() !== 1) {
                return null;
            }
            $payment = $payments->first();
            try {
                if (PaymentAmounts::minor($payment->amount) !== $data['amount']) {
                    return null;
                }
            } catch (InvalidArgumentException) {
                return null;
            }
            if (! $payment->bill || ! filled($payment->bill->assembly_code)
                || (string) $payment->assembly_code !== (string) $payment->bill->assembly_code) {
                return null;
            }
            if (strtolower((string) $payment->transaction_status) === 'success') {
                return $payment;
            }
            // Online evidence cannot settle a manual/cash record or an unknown state.
            if ($payment->payment_mode !== 'momo'
                || ! in_array(strtolower((string) $payment->transaction_status), ['pending', 'failed'], true)) {
                return null;
            }
            $payment->transaction_status = 'Success';
            if (isset($data['channel']) && is_string($data['channel']) && in_array($data['channel'], ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer', 'eft', 'capitec_pay', 'payattitude'], true)) {
                $payment->payment_mode = $data['channel'];
            }
            $payment->save();

            return $payment;
        });
    }
}
