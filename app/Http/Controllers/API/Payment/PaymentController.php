<?php

namespace App\Http\Controllers\API\Payment;

use App\Actions\Payment\MakePayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Payment\ConfirmPaymentRequest;
use App\Http\Requests\API\Payment\CreatePaymentRequest;
use App\Models\AuditTrail;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Support\PaymentAmounts;
use App\Support\PaymentRecordAccess;
use App\Support\PaystackPaymentSettlement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $data = Payment::orderBy('created_at', 'DESC')
            ->with(['assembly', 'bill'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return response()->json([
            'message' => 'Get all payments',
            'data' => $data,
        ]);
    }

    public function makePayment(CreatePaymentRequest $request)
    {
        $bill = PaymentRecordAccess::creationBill($request);
        if ($request->input('payment_mode') === 'momo') {
            abort_unless(is_string(config('services.paystack.secret_key')) && trim(config('services.paystack.secret_key')) !== '', 503, 'Payment provider unavailable');
        }
        do {
            $transactionID = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Payment::where('transaction_id', $transactionID)->exists());

        if ($request->hasFile('receipt')) {
            $image = $request->file('receipt');
            $image_name = Payment::generateImageFileName();
            $extension = $image->guessExtension();
            abort_unless(in_array($extension, ['jpg', 'jpeg', 'png', 'pdf'], true), 422, 'Unsupported receipt');
            $image_name = $image_name.'.'.$extension;
            $receiptDestinationPath = storage_path('app/public/images/receipt');
            $image->move($receiptDestinationPath, $image_name);
        }

        $paymentData = [
            'bills_id' => $bill->bills_id,
            'amount' => PaymentAmounts::decimal($request->input('amount')),
            'payment_mode' => $request->input('payment_mode'),
            'payment_source' => 'Mobile',
            'phone' => $request->input('phone'),
            'network' => $request->input('network'),
            'transaction_id' => $request->input('payment_mode') == 'momo' ? $transactionID : null,
            'transaction_status' => $request->input('payment_mode') == 'momo' ? 'Pending' : 'Success',
            'assembly_code' => $bill->assembly_code,
            'receipt' => $image_name ?? null,
            'created_by' => $request->user()->id ?? null,
        ];

        $payment = DB::transaction(function () use ($request, $paymentData) {
            $bill = PaymentRecordAccess::creationBill($request, true);
            $paymentData['assembly_code'] = $bill->assembly_code;
            return Payment::create($paymentData);
        });

        if ($request->input('payment_mode') == 'momo') {
            $amount = $payment->amount;
            $reference = $payment->transaction_id;

            $response = MakePayment::acceptPayment($request->user(), $amount, $reference, $payment->phone, $payment->network);

            if (($response['status'] ?? null) === true
                && ($response['data']['reference'] ?? null) === $reference) {

                $data = [
                    'prompt' => $response['data']['display_text'] ?? null,
                ];

                $payment->update($data);

                $serviceData = [
                    'user_id' => $request->user()->id,
                    'service_used' => 'Bill Payment',
                    'usage_date' => now(),
                    'service_channel' => 'Web Portal',
                    'status' => 'Completed',
                ];

                $auditTrailData = [
                    'user_id' => $request->user()->id,
                    'action_performed' => 'Bill Payment',
                    'action_date' => now(),
                    'ip_address' => $request->ip(),
                    'device_used' => request()->userAgent(),
                    'remarks' => 'Success',
                ];

                ServiceRequest::create($serviceData);
                AuditTrail::create($auditTrailData);

                $paymentData = $payment->toArray();
                $paymentData['send_otp'] = (($response['data']['status'] ?? null) === 'send_otp');

                return response()->json([
                    'message' => 'Payment processed successfully. '.$data['prompt'],
                    'data' => $paymentData,
                ]);
            } else {
                // Handle payment initiation failure
                return response()->json([
                    'message' => 'Failed to initiate payment. Please try again.',
                ]);
            }
        } else {
            // Cash payment

            $serviceData = [
                'user_id' => $request->user()->id,
                'service_used' => 'Bill Payment',
                'usage_date' => now(),
                'service_channel' => 'Web Portal',
                'status' => 'Completed',
            ];

            $auditTrailData = [
                'user_id' => $request->user()->id,
                'action_performed' => 'Bill Payment',
                'action_date' => now(),
                'ip_address' => $request->ip(),
                'device_used' => request()->userAgent(),
                'remarks' => 'Success',
            ];

            ServiceRequest::create($serviceData);
            AuditTrail::create($auditTrailData);

            return response()->json([
                'message' => 'Cash payment made successfully',
                'data' => $payment,
            ]);
        }
    }

    public function show($uuid)
    {
        $payment = Payment::whereUuidOrId($uuid)
            ->with(['assembly', 'bill'])
            ->first();

        if (empty($payment)) {
            return response()->json([
                'message' => 'Payment not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular payment',
            'data' => $payment,
        ]);
    }

    public function customerPayment($uuid)
    {
        $customer = Owner::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid);
            })
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Customer not found, enter customer account id',
            ], 422);
        }

        $payments = Payment::orderBy('created_at', 'DESC')
            ->with([
                'assembly',
                'bill.property.polygon.block.division',
                'bill.property.polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->whereHas('bill', function ($query) use ($customer) {
                $query->whereHas('property', function ($propertyQuery) use ($customer) {
                    $propertyQuery->where('owner_id', $customer->erms_id)
                        ->whereNotNull('property_id')
                        ->whereNull('business_id');
                })
                    ->orWhereHas('business', function ($businessQuery) use ($customer) {
                        $businessQuery->where('owner_id', $customer->erms_id)
                            ->whereNotNull('business_id')
                            ->whereNull('property_id');
                    });
            })
            ->get()
            ->map(function ($payment) {
                if (is_null($payment->bill->property_id)) {
                    $payment->bill_type = 'Business Bill';
                } elseif (is_null($payment->bill->business_id)) {
                    $payment->bill_type = 'Property Bill';
                } else {
                    $payment->bill_type = 'Unknown';
                }

                return $payment;
            });

        return response()->json([
            'message' => 'Get customer payments',
            'data' => $payments,
        ]);
    }

    public function paymentCallback(Request $request)
    {
        // Log the full request for debugging
        \Log::info('Payment Callback Received:', $request->all());

        $payment = Payment::where('transaction_id', $request->transactionid)->first();

        if (! $payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Update the payment record based on the status
        $payment->update([
            'status' => ucfirst($request->status),
        ]);
    }

    public function handlePaymentPaystackWebhook(Request $request)
    {
        $body = $request->getContent();
        abort_unless(PaystackPaymentSettlement::hasValidSignature($body, $request->header('x-paystack-signature')), 403);
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return response()->json(['message' => 'Invalid event'], 400);
        }
        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid event'], 400);
        }
        if (($payload['event'] ?? null) !== 'charge.success') {
            return response()->json(['status' => 'success']);
        }
        $data = $payload['data'] ?? null;
        $reference = is_array($data) ? ($data['reference'] ?? null) : null;
        if (! is_string($reference) || $reference === '') {
            return response()->json(['message' => 'Invalid payment evidence'], 422);
        }
        if (! Payment::where('transaction_id', $reference)->exists()) {
            // An authenticated event for another integration must not cause endless retries.
            return response()->json(['status' => 'success']);
        }
        // A valid signature authenticates the event, not its association to our bill.
        $payment = PaystackPaymentSettlement::settle($reference, $data);
        return $payment ? response()->json(['status' => 'success'])
            : response()->json(['message' => 'Payment evidence did not match'], 422);
    }

    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        $payment = Payment::whereUuidOrId($request->input('payment_id'))->first();
        abort_unless($payment, 422, 'Payment details not found');
        abort_unless(PaymentRecordAccess::mayConfirm($request->user(), $payment), 403);
        abort_unless($payment->payment_mode === 'momo' && strtolower((string) $payment->transaction_status) === 'pending'
            && is_string($payment->transaction_id) && $payment->transaction_id !== '', 422, 'Payment is not awaiting confirmation');
        $reference = $payment->transaction_id;
        $key = config('services.paystack.secret_key');
        abort_unless(is_string($key) && trim($key) !== '', 503, 'Payment provider unavailable');
        $response = Http::withToken($key)->timeout(15)->connectTimeout(5)
            ->post('https://api.paystack.co/charge/submit_otp', [
                'otp' => $request->input('otp'), 'reference' => $reference,
            ]);
        $data = $response->json();
        if (! $response->successful() || ! is_array($data) || ($data['status'] ?? null) !== true
            || ($data['data']['reference'] ?? null) !== $reference
            || ! is_string($data['data']['display_text'] ?? null) || $data['data']['display_text'] === '') {
            return response()->json(['message' => 'The otp is invalid or the otp has already been confirmed'], 422);
        }
        $payment = DB::transaction(function () use ($payment, $request, $reference, $data) {
            $current = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless(PaymentRecordAccess::mayConfirm($request->user(), $current), 403);
            abort_unless($current->payment_mode === 'momo' && strtolower((string) $current->transaction_status) === 'pending'
                && $current->transaction_id === $reference, 422, 'Payment no longer awaits confirmation');
            $current->update(['prompt' => $data['data']['display_text']]);
            return $current;
        });
        $paymentData = $payment->toArray();
        $paymentData['send_otp'] = ($data['data']['status'] ?? null) === 'send_otp';
        return response()->json(['message' => 'Payment processed successfully. '.$payment->prompt, 'data' => $paymentData]);
    }
}
