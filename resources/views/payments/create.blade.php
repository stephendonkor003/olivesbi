@extends('layout.base')

@section('page-styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include any additional styles needed for the payment form -->
@endsection
@php
    $amount = $bill->amount ?? 0;
    $arrears = $bill->arrears ?? 0;
    $amountDue = $amount + $arrears;
@endphp

@section('page-content')
    <div class="card">
        <!-- HEADER SECTION -->
        <div class="card-body border-bottom pb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="ri-wallet-3-line me-2"></i> Back Office Payment Inititation
                    </h4>

                    <p class="mb-0 text-muted fs-14">
                        You are Initiating Payment on A generated bills record in your
                        central database repository.
                    </p>
                </div>

                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-primary">
                    <i class="ri-arrow-go-back-line"></i> Back
                </a>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="container-fluid mh-auto">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="assembly_code" value="{{ $bill->assembly_code }}">

                        <div class="row">
                            <div class="form-group col-md-6 mb-4">
                                <label for="bills_id" class="form-label">Bill No.:</label>
                                <input type="text" class="form-control" id="bills_id" name="bills_id"
                                    value="{{ $bill->bills_id ?? '' }}" readonly>
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="paid_by" class="form-label">Amount: (<span
                                        style="color:green;font-weight:bold">Current Bill: GHS
                                        {{ $amountDue ?? 0 }}</span>)</label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" placeholder="Bill amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="payment_mode" class="form-label">Select Payment Mode:</label>
                                <select class="form-control @error('payment_mode') is-invalid @enderror" id="payment_mode"
                                    name="payment_mode">
                                    <option disabled selected>Select Payment Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="momo">Momo</option>
                                </select>

                                @error('payment_mode')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 phone">
                                <label for="phone" class="form-label">Payer Phone:</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" placeholder="Rate payer phone number">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 network">
                                <label for="network" class="form-label">Select Payment Network:</label>
                                <select class="form-control @error('network') is-invalid @enderror" id="network"
                                    name="network">
                                    <option disabled selected>Select Payment Network</option>
                                    <option value="MTN">MTN</option>
                                    <option value="TGO">AIRTEL TIGO</option>
                                    <option value="VDF">VODAFONE</option>
                                </select>

                                @error('network')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 receipt">
                                <label for="receipt" class="form-label">Upload Receipt:</label>
                                <input type="file" class="form-control @error('receipt') is-invalid @enderror"
                                    id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf">

                                @error('receipt')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
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
