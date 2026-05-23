@extends('layout.base')

@section('page-styles')
    <!-- Include any additional styles needed for the payment form -->
@endsection
@php
    $amount = $bill->amount ?? 0;
    $arrears = $bill->arrears ?? 0;
    $amountDue = $amount + $arrears;
@endphp

@section('page-content')
    <div class="row">
        <div class="container-fluid mh-auto">
            <div class="card">
                <div class="card-header flex-wrap d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">Submit Payment Record</h4>
                    </div>

                    <div>
                        <a href="{{ route('dashboard.operational') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="assembly_code" value="{{ $bill->assembly_code }}">

                        <div class="row">
                            <div class="form-group col-md-6 mb-4">
                                <label for="bills_id">Bill No.:</label>
                                <input type="text" class="form-control" id="bills_id" name="bills_id"
                                    value="{{ $bill->bills_id ?? '' }}" readonly>
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="paid_by">Amount: (<span style="color:green;font-weight:bold">Current Bill: GHS
                                        {{ $amountDue ?? 0 }}</span>)</label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="payment_mode">Select Payment Mode:</label>
                                <select class="form-control @error('payment_mode') is-invalid @enderror" id="payment_mode"
                                    name="payment_mode">
                                    <option disabled selected>Select Payment Mode</option>
                                    {{-- <option value="cash">Cash</option> --}}
                                    <option value="momo">Momo</option>
                                </select>

                                @error('payment_mode')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 phone">
                                <label for="phone">Payer Phone:</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 network">
                                <label for="network">Select Payment Network:</label>
                                <select class="form-control @error('network') is-invalid @enderror" id="network"
                                    name="network">
                                    <option disabled selected>Select Payment Network</option>
                                    <option value="MTN">MTN</option>
                                    <option value="AIRTELTIGO">AIRTEL TIGO</option>
                                    <option value="VODAFONE">VODAFONE</option>
                                </select>

                                @error('network')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- @if ($payment->reason != null)
                                <label style="color:red;font-size:16px">{{ $payment->reason }}</label>
                            @endif --}}

                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>
@endsection
