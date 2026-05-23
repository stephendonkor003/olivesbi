<?php

namespace App\Http\Controllers\Donation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index()
    {
        return view('donation.index', [
            'paystackPublicKey'    => config('services.paystack.public_key'),
            'flutterwavePublicKey' => config('services.flutterwave.public_key'),
            'stripePublicKey'      => config('services.stripe.key'),
            'currency'             => config('services.donation.currency', 'USD'),
        ]);
    }

    // ── PAYSTACK ──────────────────────────────────────────────────────────────

    public function verifyPaystack(Request $request)
    {
        $reference = $request->reference ?? $request->trxref;

        if (! $reference) {
            return redirect()->route('donation.index')->with('error', 'No payment reference found.');
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get(config('services.paystack.url') . "/transaction/verify/{$reference}");

        $data    = $response->json('data');
        $success = $response->successful() && ($data['status'] ?? '') === 'success';

        return view('donation.success', [
            'gateway'   => 'Paystack',
            'success'   => $success,
            'reference' => $reference,
            'amount'    => $success ? number_format(($data['amount'] ?? 0) / 100, 2) : null,
            'currency'  => $success ? ($data['currency'] ?? '') : null,
        ]);
    }

    // ── FLUTTERWAVE ───────────────────────────────────────────────────────────

    public function verifyFlutterwave(Request $request)
    {
        if (($request->status ?? '') !== 'successful') {
            return view('donation.success', [
                'gateway' => 'Flutterwave',
                'success' => false,
            ]);
        }

        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->get(config('services.flutterwave.url') . "/transactions/{$request->transaction_id}/verify");

        $data    = $response->json('data');
        $success = $response->successful() && ($data['status'] ?? '') === 'successful';

        return view('donation.success', [
            'gateway'   => 'Flutterwave',
            'success'   => $success,
            'reference' => $request->tx_ref,
            'amount'    => $success ? number_format($data['amount'] ?? 0, 2) : null,
            'currency'  => $success ? ($data['currency'] ?? '') : null,
        ]);
    }

    // ── STRIPE ────────────────────────────────────────────────────────────────

    public function initializeStripe(Request $request)
    {
        $request->validate([
            'email'  => 'required|email',
            'amount' => 'required|numeric|min:1',
        ]);

        $currency = strtolower(config('services.donation.currency', 'usd'));

        $response = Http::withToken(config('services.stripe.secret'))
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode'                                              => 'payment',
                'customer_email'                                    => $request->email,
                'line_items[0][quantity]'                           => 1,
                'line_items[0][price_data][currency]'               => $currency,
                'line_items[0][price_data][unit_amount]'            => (int) ($request->amount * 100),
                'line_items[0][price_data][product_data][name]'     => 'Olives BI Environmental Donation',
                'line_items[0][price_data][product_data][description]' => 'Protecting forests, wetlands, rivers and land.',
                'success_url' => route('donation.verify.stripe') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('donation.index'),
                'metadata[donor_name]' => $request->name ?? 'Anonymous',
            ]);

        if ($response->successful()) {
            return redirect($response->json('url'));
        }

        return back()->with('error', 'Could not connect to Stripe: ' . $response->json('error.message'));
    }

    public function verifyStripe(Request $request)
    {
        $sessionId = $request->session_id;

        if (! $sessionId) {
            return redirect()->route('donation.index')->with('error', 'No session ID found.');
        }

        $response = Http::withToken(config('services.stripe.secret'))
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        $data    = $response->json();
        $success = $response->successful() && ($data['payment_status'] ?? '') === 'paid';

        return view('donation.success', [
            'gateway'   => 'Stripe',
            'success'   => $success,
            'reference' => $sessionId,
            'amount'    => $success ? number_format(($data['amount_total'] ?? 0) / 100, 2) : null,
            'currency'  => $success ? strtoupper($data['currency'] ?? '') : null,
        ]);
    }
}
