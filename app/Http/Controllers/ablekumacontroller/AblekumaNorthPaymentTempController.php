<?php

namespace App\Http\Controllers\ablekumacontroller;

use App\Http\Controllers\Controller;
use App\Models\Models\AblekumaNorthPaymentTemp;
use Illuminate\Http\Request;

class AblekumaNorthPaymentTempController extends Controller
{
    //
    // Display a listing of the resource.
    public function index()
    {
        $payments = AblekumaNorthPaymentTemp::all();

        return view('ablekuma.index', compact('payments'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('ablekuma.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'SN' => 'required',
            'Account' => 'required',
            'Address' => 'required',
            'OwnerName' => 'required',
            'Suburb' => 'required',
            'RateableV' => 'required',
            'Zone' => 'required',
            'Use_' => 'required',
            'Rate' => 'required',
            'CurrentRate' => 'required',
            'BasicRate' => 'required',
            'Arrears' => 'required',
            'Balance' => 'required',
            'amount_paid' => 'required',
            'paid_by' => 'required',
            'payment_method' => 'required',
        ]);

        AblekumaNorthPaymentTemp::create($request->all());

        return redirect()->route('ablekuma.index')
            ->with('success', 'Payment created successfully.');
    }

    // Display the specified resource.
    public function show(AblekumaNorthPaymentTemp $payment)
    {
        return view('ablekuma.show', compact('payment'));
    }

    // Show the form for editing the specified resource.
    public function edit(AblekumaNorthPaymentTemp $payment)
    {
        return view('ablekuma.edit', compact('payment'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, AblekumaNorthPaymentTemp $payment)
    {
        $request->validate([
            'SN' => 'required',
            'Account' => 'required',
            'Address' => 'required',
            'OwnerName' => 'required',
            'Suburb' => 'required',
            'RateableV' => 'required',
            'Zone' => 'required',
            'Use_' => 'required',
            'Rate' => 'required',
            'CurrentRate' => 'required',
            'BasicRate' => 'required',
            'Arrears' => 'required',
            'Balance' => 'required',
            'amount_paid' => 'required',
            'paid_by' => 'required',
            'payment_method' => 'required',
        ]);

        $payment->update($request->all());

        return redirect()->route('ablekuma.index')
            ->with('success', 'Payment updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(AblekumaNorthPaymentTemp $payment)
    {
        $payment->delete();

        return redirect()->route('ablekuma.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function collectPayment($id)
    {
        // Retrieve the payment record by ID from the database
        $payment = AblekumaNorthPaymentTemp::findOrFail($id);

        // Pass $payment to the view
        return view('ablekuma.collect-payment', compact('payment'));
    }

    public function processPayment(Request $request, AblekumaNorthPaymentTemp $ablekuma_north_payment)
    {
        // Validate the payment form data
        $request->validate([
            'amount' => 'required|numeric',
            'payment_method' => 'required',
            'card_number' => 'required',
            'expiry_date' => 'required|date_format:m/y',
            'cvv' => 'required|digits:3',
        ]);

        // Process the payment here
        // This might involve integrating with Hubtel or another payment gateway

        // For demonstration, let's assume the payment is successful
        // You would typically call the payment gateway API and handle the response accordingly

        // Redirect back to the payments index with success message
        return redirect()->route('ablekuma-north-payments.index')
            ->with('success', 'Payment successfully processed.');
    }
}
