@extends('layout.base')

@section('page-styles')
    <!-- Additional page styles -->
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="heading mb-0">Edit Payment</h4>
                <div class="d-flex align-items-center">
                    <ul class="nav nav-pills mix-chart-tab user-m-tabe" id="pills-tab" role="tablist">
                        <!-- Add any additional tabs if needed -->
                    </ul>
                    <a href="{{ route('ablekuma.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                </div>
            </div>

            <!-- Payment Edit Form -->
            <div class="col-xl-12 active-p">
                <div class="card">
                    <div class="card-body px-0">
                        <form action="{{ route('ablekuma.update', $payment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="sn" class="form-label">SN</label>
                                    <input type="text" class="form-control" id="sn" name="SN"
                                        value="{{ old('SN', $payment->SN) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="account" class="form-label">Account</label>
                                    <input type="text" class="form-control" id="account" name="Account"
                                        value="{{ old('Account', $payment->Account) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="Address"
                                        value="{{ old('Address', $payment->Address) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="ownerName" class="form-label">Owner Name</label>
                                    <input type="text" class="form-control" id="ownerName" name="OwnerName"
                                        value="{{ old('OwnerName', $payment->OwnerName) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="suburb" class="form-label">Suburb</label>
                                    <input type="text" class="form-control" id="suburb" name="Suburb"
                                        value="{{ old('Suburb', $payment->Suburb) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="rateableV" class="form-label">Rateable Value</label>
                                    <input type="text" class="form-control" id="rateableV" name="RateableV"
                                        value="{{ old('RateableV', $payment->RateableV) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="zone" class="form-label">Zone</label>
                                    <input type="text" class="form-control" id="zone" name="Zone"
                                        value="{{ old('Zone', $payment->Zone) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="use" class="form-label">Use</label>
                                    <input type="text" class="form-control" id="use" name="Use_"
                                        value="{{ old('Use_', $payment->Use_) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="rate" class="form-label">Rate</label>
                                    <input type="text" class="form-control" id="rate" name="Rate"
                                        value="{{ old('Rate', $payment->Rate) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="currentRate" class="form-label">Current Rate</label>
                                    <input type="text" class="form-control" id="currentRate" name="CurrentRate"
                                        value="{{ old('CurrentRate', $payment->CurrentRate) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="basicRate" class="form-label">Basic Rate</label>
                                    <input type="text" class="form-control" id="basicRate" name="BasicRate"
                                        value="{{ old('BasicRate', $payment->BasicRate) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="arrears" class="form-label">Arrears</label>
                                    <input type="text" class="form-control" id="arrears" name="Arrears"
                                        value="{{ old('Arrears', $payment->Arrears) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="balance" class="form-label">Balance</label>
                                    <input type="text" class="form-control" id="balance" name="Balance"
                                        value="{{ old('Balance', $payment->Balance) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="amount_paid" class="form-label">Amount Paid</label>
                                    <input type="text" class="form-control" id="amount_paid" name="amount_paid"
                                        value="{{ old('amount_paid', $payment->amount_paid) }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="paid_by" class="form-label">Paid By</label>
                                    <input type="text" class="form-control" id="paid_by" name="paid_by"
                                        value="{{ old('paid_by', $payment->paid_by) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <input type="text" class="form-control" id="payment_method" name="payment_method"
                                        value="{{ old('payment_method', $payment->payment_method) }}" required>
                                </div>
                                <div class="col-sm-8">
                                    <label for="note" class="form-label">Note</label>
                                    <input type="text" class="form-control" id="note" name="note"
                                        value="{{ old('note', $payment->note) }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update Payment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Payment Edit Form -->
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <!-- Ensure jQuery is loaded before other scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Additional scripts -->
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
@endsection
