@extends('layout.base')

@section('page-styles')
    <!-- Include any additional styles needed for the payment form -->
@endsection
@php
    $name = '';
    if ($payment->bill->property && $payment->bill->property->customer) {
        $firstname = $payment->bill->property->customer->first_name ?? '';
        $lastname = $payment->bill->property->customer->last_name ?? '';
        $name = $firstname . ' ' . $lastname;
    } elseif ($payment->bill->business && $payment->bill->business->customer) {
        $firstname = $payment->bill->business->customer->first_name ?? '';
        $lastname = $payment->bill->business->customer->last_name ?? '';
        $name = $firstname . ' ' . $lastname;
    }
@endphp

@section('page-content')
    <div class="row">
        <div class="container-fluid mh-auto">
            <div class="card">

                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-user-settings-line me-2"></i> Rate Payer - Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are Viewing Rate Payer Payment History Record from your
                                central database repository.
                            </p>
                        </div>

                        <a href="{{ route('dashboard.mypaymenthistory') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">

                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="form-group col-md-6 mb-4">
                                <label for="bills_id" class="form-label">Bill No.:</label>
                                <input type="text" class="form-control" id="bills_id" name="bills_id"
                                    value="{{ $payment->bills_id ?? '' }}" readonly>
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="bills_id" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="bills_id" name="bills_id"
                                    value="{{ $name ?? '' }}" readonly>
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="paid_by" class="form-label">Amount:</label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" value="{{ $payment->amount }}" readonly>

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4">
                                <label for="payment_mode" class="form-label">Payment Mode:</label>
                                <input type="text" class="form-control @error('payment_mode') is-invalid @enderror"
                                    id="payment_mode" name="payment_mode" value="{{ $payment->payment_mode }}" readonly>

                                @error('payment_mode')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 phone">
                                <label for="phone" class="form-label">Payer Phone:</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ $payment->phone ?? 'N/A' }}" readonly>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 network">
                                <label for="network" class="form-label">Payment Network:</label>
                                <input type="text" class="form-control @error('network') is-invalid @enderror"
                                    id="network" name="network" value="{{ $payment->network ?? 'N/A' }}" readonly>

                                @error('network')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-4 network">
                                <label for="network" class="form-label">Transaction ID:</label>
                                <input type="text" class="form-control @error('network') is-invalid @enderror"
                                    id="network" name="network"
                                    value="{{ !empty($payment->transaction_id) ? $payment->transaction_id : 'N/A' }}"
                                    readonly>

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
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/663e4f9b9a809f19fb2fa32d/1hthme206';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>

    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>
@endsection
