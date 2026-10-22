<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Jobs\Payment\SendPaymentSMS;
use App\Models\AuditTrail;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Support\PaymentAmounts;
use App\Support\PaymentRecordAccess;
use App\Support\PaystackPaymentSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('payments.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Payment::orderBy('created_at', 'DESC')
            ->when(!empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(!empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            });

        $payments = $query->clone()
            ->when($request->status, function ($query) use ($request) {
                $query->where('transaction_status', $request->status);
            })
            ->when($request->payment_mode, function ($query) use ($request) {
                $query->where('payment_mode', $request->payment_mode);
            })
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
            });

        if ($request->expectsJson()) {
            return datatables()->eloquent($payments->with(['bill', 'assembly', 'createdBy']))
                ->addIndexColumn()
                ->addColumn('action', function (Payment $payment) {
                    return '
                        <div class="dropdown">
                            <div class="btn-link" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item"
                                    href="' . route('payments.show', $payment) . '">View
                                    Payment</a>
                                <a class="dropdown-item"
                                    href="' . route('payments.receipt', $payment) . '"
                                    target="_blank">View Receipt</a>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('name', function (Payment $payment) {
                    $propertyCustomer = $payment->bill?->property?->customer;
                    $businessCustomer = $payment->bill?->business?->customer;

                    if (!$propertyCustomer && !$businessCustomer) {
                        return 'N/A';
                    }
                    if ($propertyCustomer) {
                        $firstname = $propertyCustomer->fname ?? '';
                        $lastname = $propertyCustomer->lname ?? '';
                    } else {
                        $firstname = $businessCustomer->fname ?? '';
                        $lastname = $businessCustomer->lname ?? '';
                    }

                    return $firstname . ' ' . $lastname;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $total = number_format($payments->clone()->sum('amount'), 2);
        $totalSuccessfulPayments = number_format($payments->clone()->where('transaction_status', 'success')->sum('amount'), 2);
        $totalPendingPayments = number_format($payments->clone()->where('transaction_status', 'Pending')->sum('amount'), 2);
        $totalFailedPayments = number_format($payments->clone()->where('transaction_status', 'Failed')->sum('amount'), 2);

        $statuses = $query->clone()->selectRaw('distinct(transaction_status)')->pluck('transaction_status')->filter()->toArray();
        $paymentModes = $query->clone()->selectRaw('distinct(payment_mode)')->pluck('payment_mode')->filter()->toArray();

        $data = [
            'totalSuccessfulPayments' => $totalSuccessfulPayments,
            'totalPendingPayments' => $totalPendingPayments,
            'totalFailedPayments' => $totalFailedPayments,
            'total' => $total,
            'statuses' => $statuses,
            'paymentModes' => $paymentModes
        ];

        return view('payments.index', $data);
    }

    public function create(Bill $bill)
    {
        if (!auth()->user()->can('payments.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.create', compact('bill'));
    }

    public function customerCreate(Bill $bill)
    {
        return view('payments.customer-create', compact('bill'));
    }

    public function store(CreatePaymentRequest $request)
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
            $image_name = $image_name . '.' . $extension;
            $receiptDestinationPath = storage_path('app/public/images/receipt');
            $image->move($receiptDestinationPath, $image_name);
        }

        $paymentData = [
            'bills_id' => $bill->bills_id,
            'amount' => PaymentAmounts::decimal($request->input('amount')),
            'payment_mode' => $request->input('payment_mode'),
            'payment_source' => 'Paystack',
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
            $reference = $payment->transaction_id;

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

            $paystackEmail = $request->user()->email ?: "{$request->user()->phone}@ermsplus.africa";

            $paystackData = [
                'email' => $paystackEmail,
                'amount' => PaymentAmounts::minor($payment->amount),
                'reference' => $reference,
                'callback_url' => route('payments.verify'),
                'currency' => 'GHS',
            ];

            $response = Http::withToken(config('services.paystack.secret_key'))
                ->post('https://api.paystack.co/transaction/initialize', $paystackData);

            if ($response->successful() && $response->json('status') === true
                && $response->json('data.reference') === $reference
                && is_string($response->json('data.authorization_url'))) {
                $authUrl = $response->json('data.authorization_url');
                $payment->update(['authorization_url' => $authUrl]);

                return redirect($authUrl);
            }

            return back()->with('error', 'Payment initialization failed. Try again.');
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

            if ($request->user()->access_level !== 'customer') {
                return redirect()->route('payments.index')->with('status', 'Payment processed successfully');
            } else {
                return redirect()->route('dashboard.operational')->with('status', 'Payment processed successfully');
            }
        }
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function receipt(Payment $payment)
    {
        return view('payments.receipt', compact('payment'));
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function makePayment(Request $request)
    {
        $request->validate(['payment_id' => ['required', 'integer', 'exists:payments,id']]);
        $payment = DB::transaction(function () use ($request) {
            $payment = Payment::whereKey($request->input('payment_id'))->lockForUpdate()->firstOrFail();
            $bill = $payment->bill;
            abort_unless($bill && PaymentRecordAccess::scopedStaff($request->user(), $bill)
                && (string) $payment->assembly_code === (string) $bill->assembly_code, 403);
            abort_unless($payment->payment_mode === 'cash', 422, 'Online payments require provider verification');
            try {
                PaymentAmounts::minor($payment->amount);
            } catch (\InvalidArgumentException) {
                abort(422, 'Invalid payment amount');
            }
            if (strtolower((string) $payment->transaction_status) === 'success') {
                return null;
            }
            abort_unless(in_array(strtolower((string) $payment->transaction_status), ['pending', 'failed'], true), 422);
            $payment->update(['transaction_status' => 'Success']);
            return $payment;
        });
        if ($payment) {
            dispatch(new SendPaymentSMS($payment));
        }
        session()->flash('status', 'Payment made successfully');
        return response()->json(['message' => 'Payment made successfully']);
    }

    public function verify(Request $request)
    {
        $reference = $request->query('reference');
        if (! is_string($reference) || $reference === '') {
            return redirect()->route('payments.index')->with('error', 'Missing transaction reference.');
        }
        $key = config('services.paystack.secret_key');
        abort_unless(is_string($key) && trim($key) !== '', 503, 'Payment provider unavailable');
        $response = Http::withToken($key)->timeout(15)->connectTimeout(5)
            ->get('https://api.paystack.co/transaction/verify/'.rawurlencode($reference));
        $payment = $response->successful() && $response->json('status') === true
            ? PaystackPaymentSettlement::settle($reference, $response->json('data')) : null;
        if (! $payment) {
            return redirect()->route('payments.index')->with('error', 'Payment verification did not match.');
        }
        return redirect()->route('payments.index')->with('status', 'Payment verified successfully.');
    }

    public function publicVerify(Request $request)
    {
        $reference = $request->query('reference');
        abort_unless(is_string($reference) && $reference !== '', 404);
        $key = config('services.paystack.secret_key');
        abort_unless(is_string($key) && trim($key) !== '', 503, 'Payment provider unavailable');
        $response = Http::withToken($key)->timeout(15)->connectTimeout(5)
            ->baseUrl(config('services.paystack.url'))
            ->get('transaction/verify/'.rawurlencode($reference));
        $payment = $response->successful() && $response->json('status') === true
            ? PaystackPaymentSettlement::settle($reference, $response->json('data')) : null;
        abort_unless($payment, 404);
        return redirect()->route('admin.public.bills.show', $payment->bill)->with('status', 'Payment completed successfully.');
    }
}
