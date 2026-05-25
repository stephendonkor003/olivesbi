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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        do {
            $transactionID = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Payment::where('transaction_id', $transactionID)->exists());

        if ($request->hasFile('receipt')) {
            $image = $request->file('receipt');
            $image_name = Payment::generateImageFileName();
            $image_name = $image_name.'.'.$image->getClientOriginalExtension();
            $receiptDestinationPath = storage_path('app/public/images/receipt');
            $image->move($receiptDestinationPath, $image_name);
        }

        $paymentData = [
            'bills_id' => $request->input('bills_id'),
            'amount' => $request->input('amount'),
            'payment_mode' => $request->input('payment_mode'),
            'payment_source' => 'Mobile',
            'phone' => $request->input('phone'),
            'network' => $request->input('network'),
            'transaction_id' => $request->input('payment_mode') == 'momo' ? $transactionID : null,
            'transaction_status' => $request->input('payment_mode') == 'momo' ? 'Pending' : 'Success',
            'assembly_code' => $request->input('assembly_code'),
            'receipt' => $image_name ?? null,
            'created_by' => $request->user()->id ?? null,
        ];

        $payment = Payment::create($paymentData);

        if ($request->input('payment_mode') == 'momo') {
            $amount = $payment->amount;
            $reference = $payment->transaction_id;

            $response = MakePayment::acceptPayment($request->user(), $amount, $reference, $payment->phone, $payment->network);

            if ($response['status'] === true) {

                $data = [
                    'transaction_id' => $response['data']['reference'],
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
                $paymentData['send_otp'] = ($response['data']['status'] === 'send_otp');

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
        $payload = $request->all();
        $event = $payload['event'] ?? null;

        // Log the full payload
        Log::info('Paystack Webhook Payload:', $payload);

        if ($event === 'charge.success') {
            $data = $payload['data'];
            $reference = $data['reference'] ?? null;
            $status = $data['status'] ?? null;

            if ($reference) {
                $payment = Payment::where('transaction_id', $reference)->first();

                if ($payment) {
                    $payment->transaction_status = ($status === 'success') ? 'Success' : 'Failed';
                    $payment->save();

                    Log::info("Payment status updated for reference: {$reference} to status: {$payment->transaction_status}");
                } else {
                    Log::warning("Payment not found for reference: {$reference}");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        $payment = Payment::whereUuidOrId($request->input('payment_id'))->first();

        if (empty($payment)) {
            return response()->json([
                'message' => 'Payment details not found',
            ], 422);
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->post('https://api.paystack.co/charge/submit_otp', [
                'otp' => $request->input('otp'),
                'reference' => $payment->transaction_id,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            if ($data['status'] === true) {

                if (empty($response['data']['reference']) || empty($response['data']['display_text'])) {
                    return response()->json([
                        'message' => 'The otp is invalid or the otp has already been confirmed',
                    ], 422);
                }

                $data2 = [
                    'transaction_id' => $response['data']['reference'],
                    'prompt' => $response['data']['display_text'],
                ];

                $payment->update($data2);

                $paymentData = $payment->toArray();
                $paymentData['send_otp'] = ($response['data']['status'] === 'send_otp');

                return response()->json([
                    'message' => 'Payment processed successfully. '.$data2['prompt'],
                    'data' => $paymentData,
                ]);
            } else {
                return response()->json([
                    'message' => 'An error occured during otp confirmation',
                ], 422);
            }
        }
    }
}
