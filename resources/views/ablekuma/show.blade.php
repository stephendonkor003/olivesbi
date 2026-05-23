 @extends('layout.base')

 @section('page-styles')
 @endsection

 @section('page-content')
     <div class="container-fluid mh-auto">
         <div class="row">
             <div class="d-flex justify-content-between align-items-center mb-4">
                 <h4 class="heading mb-0">Ablekuma North Payments Management - 2024</h4>
                 <div class="d-flex align-items-center">
                     <ul class="nav nav-pills mix-chart-tab user-m-tabe" id="pills-tab" role="tablist">

                     </ul>
                     <a href="{{ route('ablekuma-north-payments.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>

                 </div>

             </div>

             <div class="col-xl-4 col-md-4">
                 <div class="card sale-card">
                     <div class="card-header pb-0 border-0 align-items-baseline">
                         <div>
                             <span>Variance of Arrears and Current Bill </span>
                             <h4>GHS <i class="fa-solid fa-arrow-trend-up ms-1"></i>
                             </h4>
                         </div>

                     </div>
                     <div class="card-body p-0 custome-tooltip">
                         <div id="totalSale"></div>
                     </div>
                     <div class="card-footer border-0">
                         <span class="tag bg-secondary">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                 <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                 <polyline points="17 6 23 6 23 12"></polyline>
                             </svg>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="col-xl-4 col-md-4">
                 <div class="card sale-card">
                     <div class="card-header pb-0 border-0 align-items-baseline">
                         <div>
                             <span>Total Current Bill</span>
                             <h4>GHS <i class="fa-solid fa-arrow-trend-down ms-1"></i>
                             </h4>
                         </div>

                     </div>
                     <div class="card-body p-0 custome-tooltip">
                         <div id="totalPurchase"></div>
                     </div>
                     <div class="card-footer border-0">
                         <span class="tag bg-secondary">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                 <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                 <polyline points="17 6 23 6 23 12"></polyline>
                             </svg>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="col-xl-4 col-md-4">
                 <div class="card sale-card">
                     <div class="card-header pb-0 border-0 align-items-baseline">
                         <div>
                             <span>Total Arrears
                             </span>
                             <h4>GHS <i class="fa-solid fa-arrow-trend-down ms-1"></i></h4>
                         </div>

                     </div>
                     <div class="card-body p-0 custome-tooltip">
                         <div id="activeCustomers"></div>
                     </div>
                     <div class="card-footer border-0">
                         <span class="tag bg-secondary">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                 <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                 <polyline points="17 6 23 6 23 12"></polyline>
                             </svg>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="col-xl-12 active-p">
                 <div class="tab-content" id="pills-tabContent">
                     <div class="tab-pane fade show active" id="pills-list" role="tabpanel"
                         aria-labelledby="pills-list-tab">

                         <div class="card">
                             <div class="card-body px-0">
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="sn" class="form-label">SN</label>
                                         <input type="text" class="form-control" id="sn"
                                             value="{{ $payment->sn }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="account" class="form-label">Account</label>
                                         <input type="text" class="form-control" id="account"
                                             value="{{ $payment->account }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="address" class="form-label">Address</label>
                                         <input type="text" class="form-control" id="address"
                                             value="{{ $payment->address }}" readonly>
                                     </div>
                                 </div>
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="ownerName" class="form-label">OwnerName</label>
                                         <input type="text" class="form-control" id="ownerName"
                                             value="{{ $payment->ownerName }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="suburb" class="form-label">Suburb</label>
                                         <input type="text" class="form-control" id="suburb"
                                             value="{{ $payment->suburb }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="rateableV" class="form-label">RateableV</label>
                                         <input type="text" class="form-control" id="rateableV"
                                             value="{{ $payment->rateableV }}" readonly>
                                     </div>
                                 </div>
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="zone" class="form-label">Zone</label>
                                         <input type="text" class="form-control" id="zone"
                                             value="{{ $payment->zone }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="use" class="form-label">Use</label>
                                         <input type="text" class="form-control" id="use"
                                             value="{{ $payment->use }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="rate" class="form-label">Rate</label>
                                         <input type="text" class="form-control" id="rate"
                                             value="{{ $payment->rate }}" readonly>
                                     </div>
                                 </div>
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="currentRate" class="form-label">CurrentRate</label>
                                         <input type="text" class="form-control" id="currentRate"
                                             value="{{ $payment->currentRate }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="basicRate" class="form-label">BasicRate</label>
                                         <input type="text" class="form-control" id="basicRate"
                                             value="{{ $payment->basicRate }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="arrears" class="form-label">Arrears</label>
                                         <input type="text" class="form-control" id="arrears"
                                             value="{{ $payment->arrears }}" readonly>
                                     </div>
                                 </div>
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="balance" class="form-label">Balance</label>
                                         <input type="text" class="form-control" id="balance"
                                             value="{{ $payment->balance }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="amount_paid" class="form-label">Amount Paid</label>
                                         <input type="text" class="form-control" id="amount_paid"
                                             value="{{ $payment->amount_paid }}" readonly>
                                     </div>
                                     <div class="col-sm-4">
                                         <label for="paid_by" class="form-label">Paid By</label>
                                         <input type="text" class="form-control" id="paid_by"
                                             value="{{ $payment->paid_by }}" readonly>
                                     </div>
                                 </div>
                                 <div class="row mb-3">
                                     <div class="col-sm-4">
                                         <label for="payment_method" class="form-label">Payment Method</label>
                                         <input type="text" class="form-control" id="payment_method"
                                             value="{{ $payment->payment_method }}" readonly>
                                     </div>
                                     <div class="col-sm-8">
                                         <label for="note" class="form-label">Note</label>
                                         <input type="text" class="form-control" id="note"
                                             value="{{ $payment->note }}" readonly>
                                     </div>
                                 </div>

                             </div>
                         </div>


                     </div>
                 </div>

             </div>
         </div>
     </div>
     </div>
 @endsection


 @section('page-scripts')
     <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->

     <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

     <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
     <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
     <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
     <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
 @endsection
