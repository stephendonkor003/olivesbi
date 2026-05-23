<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\ImportPaymentInvoicesRequest;
use App\Imports\PaymentInvoiceImport;
use App\Jobs\Payment\SendPaymentSMS;
use App\Models\InvoicePayment;
use App\Models\PaymentTemp;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentTempController extends Controller
{
    //
    public function index()
    {
        $payments = PaymentTemp::get();

        return view('payments.index', compact('payments'));
    }

    public function show($id)
    {
        // Retrieve payment record using $id
        $payment = PaymentTemp::findOrFail($id);

        // Redirect to payment form view with the payment record
        return view('payments.paymentblade', compact('payment'));
    }

    public function showReceipt($id)
    {
        // Retrieve the payment record using $id
        $payment = PaymentTemp::findOrFail($id);

        // Pass the data to the Blade template
        return view('payments.receipt', [
            'account' => 'ABN083780339',  // Example data
            'year' => '2024',
            'suburb' => $payment->Suburb,
            'type' => 'Residential Private',  // Example data
            'class' => 'First Class Residential',  // Example data
            'accountNumber' => $payment->Account,
            'ratePerUnit' => '0.00135',  // Example data
            'rateableValue' => $payment->RateableV,
            'currentRate' => $payment->CurrentRate,
            'arrears' => $payment->Arrears,
            'totalBalance' => $payment->Balance,
        ]);
    }

    public function import()
    {
        return view('payments.import');
    }

    public function importData(ImportPaymentInvoicesRequest $request)
    {
        try {
            $data = $request->validated();

            $import = (new PaymentInvoiceImport);
            $import->import($request->file('file'));

            return redirect()->route('payments.index')->with('status', 'Payment invoice imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function processPayment(Request $request, $id)
    {
        // Retrieve payment record using $id
        $payment = PaymentTemp::findOrFail($id);

        // Validate the request data
        $request->validate([
            'amount_paid' => 'required|numeric',
            'paid_by' => 'required|string|max:255',
            'paid_by_phone' => 'required|string|max:15',
            'payment_method' => 'required|string|max:255',
            'payment_option' => 'required|string|max:255',
        ]);

        $paymentData = [
            'bills_id' => $payment->bills_id,
            'amount_paid' => $request->input('amount_paid'),
            'payment_method' => $request->input('payment_method'),
            'payment_phone_no' => $request->input('paid_by_phone'),
            'payment_network' => $request->input('payment_option'),
            'status' => $request->input('payment_method') == 'momo' ? 'Pending' : 'Success',
            'created_by' => $request->user()->id ?? null,
        ];

        // Check if the payment method is Hubtel
        if ($request->input('payment_method') == 'momo') {
            $client = new \GuzzleHttp\Client;

            $apiKey = '$2a$10$YKxaeD8IwH1SoCa0VrCHwuKXAELu8h2HFd8AsyPBs4k1YBovs2UhS';
            $apiId = '1647';

            $response = $client->post('https://api.reddeonline.com/v1/receive', [
                'headers' => [
                    'ApiKey' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache',
                ],
                'json' => [
                    'amount' => $request->input('amount_paid'),
                    'appid' => $apiId,
                    'clientreference' => '123456',
                    'clienttransid' => 'test2025donlinetest',
                    'description' => 'Registration Payment',
                    'nickname' => 'Registration Payment',
                    'paymentoption' => $request->input('payment_option'),
                    'walletnumber' => $request->input('paid_by_phone'),
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            if ($body['status'] === 'OK') {

                $data = [
                    'amount_paid' => $request->input('amount_paid'),
                    'paid_by' => $request->input('paid_by'),
                    'payment_method' => $request->input('payment_method'),
                    'transaction_id' => $body['transactionid'],
                    'reason' => $body['reason'],
                ];

                // Update the payment
                $payment->update($data);
                $paymentData['transaction_id'] = $body['transactionid'];

                $invoicePayment = InvoicePayment::create($paymentData);

                // Redirect the user to the checkout page
                return redirect()->route('payments.index')->with(
                    'status',
                    'Payment successfully processed. Kindly '.$data['reason']
                );
            } else {
                // Handle payment initiation failure
                return redirect()->route('payments.index')->with('error', 'Failed to initiate payment. Please try again.');
            }
        } else {
            // Handle other payment methods
            $payment->update([
                'amount_paid' => $request->input('amount_paid'),
                'paid_by' => $request->input('paid_by'),
                'payment_method' => $request->input('payment_method'),
                'status' => 'Paid',
            ]);

            $invoicePayment = InvoicePayment::create($paymentData);

            return redirect()->route('payments.index')->with('success', 'Payment successfully processed.');
        }
    }

    public function fetchPayment($id)
    {
        $payments = InvoicePayment::where('bills_id', $id)->get();
        $total = InvoicePayment::where('bills_id', $id)->sum('amount_paid');

        return view('payments.customer_payment')->with([
            'payments' => $payments,
            'total' => $total,
        ]);
    }

    public function invoicePayment()
    {
        $payments = InvoicePayment::with(['invoice', 'createdBy'])->get();
        $total = InvoicePayment::sum('amount_paid');

        return view('payments.invoice_payment')->with([
            'payments' => $payments,
            'total' => $total,
        ]);
    }

    // Callback for payment status
    public function paymentCallback(Request $request)
    {
        try {
            $data = $request->all();
            $payment = InvoicePayment::where('transaction_id', $data['transactionid'])->first();
            $invoiceBalance = 0;

            if (! empty($payment)) {
                $invoice = PaymentTemp::where('bills_id', $payment->bills_id)->first();
                $invoiceBalance = $invoice->Balance - $payment->amount_paid;

                if ($data['status'] === 'PAID') {
                    // Update payment status
                    $payment->update([
                        'status' => 'Success',
                    ]);

                    // Update invoice details
                    $invoice->update([
                        'Balance' => $invoiceBalance,
                        'status' => 'Success',
                    ]);

                    // Payment SMS
                    dispatch(new SendPaymentSMS($payment));
                } else {
                    // Update payment status
                    $payment->update([
                        'status' => 'Failed',
                    ]);

                    // Update invoice details
                    $invoice->update([
                        'status' => 'Success',
                    ]);
                }

                return response()->json([
                    'message' => 'Payment details updated successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Payment details not found',
                ], 422);
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    // Success and cancellation routes
    public function paymentSuccess()
    {
        return redirect()->route('payments.index')->with('success', 'Payment successful.');
    }

    public function paymentCancel()
    {
        return redirect()->route('payments.index')->with('error', 'Payment cancelled.');
    }

    public function edit($id)
    {
        $payment = PaymentTemp::findOrFail($id);

        return view('payments.edit', compact('payment'));
    }

    // Add update method to handle the actual update of the payment record
    public function update(Request $request, $id)
    {
        $payment = PaymentTemp::findOrFail($id);

        $request->validate([
            // Define your validation rules for updating here
        ]);

        $payment->update([
            // Update the payment record based on the form inputs
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment details updated successfully.');
    }
}
