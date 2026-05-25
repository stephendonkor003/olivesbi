<?php

namespace App\Http\Controllers;

use App\Models\ProPaymentRequest;
use App\Services\ProPaymentGatewayService;
use Illuminate\Http\Request;

class PublicProPaymentRequestController extends Controller
{
    public function show(ProPaymentRequest $paymentRequest)
    {
        $paymentRequest->load(['assembly', 'bill.property', 'bill.business', 'integration']);

        return view('payments.pro-payment-request', [
            'paymentRequest' => $paymentRequest,
            'verification' => null,
        ]);
    }

    public function callback(Request $request, ProPaymentRequest $paymentRequest, ProPaymentGatewayService $gateway)
    {
        $paymentRequest->load(['assembly', 'bill.property', 'bill.business', 'integration', 'proUser', 'requestedByAgent']);

        $verification = $gateway->verify($paymentRequest, $request);

        $paymentRequest->refresh()->load(['assembly', 'bill.property', 'bill.business', 'integration']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => (bool) ($verification['paid'] ?? false),
                'status' => $paymentRequest->status,
                'message' => $verification['message'] ?? null,
            ]);
        }

        return view('payments.pro-payment-request', [
            'paymentRequest' => $paymentRequest,
            'verification' => $verification,
        ]);
    }
}
