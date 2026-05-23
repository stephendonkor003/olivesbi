@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-wallet-3-line me-2"></i> Bills Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing all generated bills record in your
                            central database repository.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card rounded-md overflow-hidden p-2">
                    <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                        <form method="GET" action="{{ route('bills.index') }}" class="mb-3">
                            <div class="row align-items-end g-3">
                                <!-- Bill Duration (From Date) -->
                                <div class="col-md-3">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>
                                <!-- Bill Duration (End Date) -->
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                                <!-- Bill Type -->

                                <!-- Arrears -->
                                <div class="col-md-3">
                                    <label for="arrears" class="form-label">Arrears</label>
                                    <select name="arrears" id="arrears" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('arrears') == '1' ? 'selected' : '' }}>
                                            With
                                            Arrears</option>
                                        <option value="0" {{ request('arrears') == '0' ? 'selected' : '' }}>
                                            Without
                                            Arrears</option>
                                    </select>
                                </div>
                                <!-- Apply Filters Button -->
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                {{-- <div class="tab-content" id="pills-tabContent"> --}}
                <div class="card">

                    <div class="card-body px-0">
                        <div class="table-responsive active-projects user-tbl  dt-filter">
                            <table id="file-export" class="table table-bordered text-nowrap w-100"
                                   data-ajax="{{ route('bills.index', $request->query()) }}" data-server-side="true"
                                   data-processing="true">
                                <thead>
                                <tr>
                                    <th data-orderable="false" data-searchable="false" data-name="DT_RowIndex"
                                        data-data="DT_RowIndex">S/N</th>
                                    <th  data-orderable="false" data-name="bills_id" data-data="bills_id">Bill No</th>
                                    <th  data-name="name" data-data="name" data-orderable="false" data-searchable="false">Name</th>
                                    <th  data-name="phone_number" data-data="phone_number" data-orderable="false" data-searchable="false">Phone Number</th>
                                    <th  data-name="division" data-data="division" data-orderable="false" data-searchable="false">Division</th>
                                    <th  data-name="block" data-data="block" data-orderable="false" data-searchable="false">Block</th>
                                    <th  data-name="billing_date" data-data="billing_date" data-searchable="false">Bill Date</th>
                                    <th  data-name="bills_year" data-data="bills_year" data-searchable="false">Bill Year</th>
                                    {{-- <th  data-name="erms_id" data-data="erms_id">Bill Type</th> --}}
                                    <th  data-name="arrears" data-data="arrears" data-orderable="false" data-searchable="false">Arrears</th>
                                    <th  data-name="amount" data-data="amount" data-orderable="false" data-searchable="false">Current Amount</th>
                                    <th  data-name="amount_due" data-data="amount_due" data-orderable="false" data-searchable="false">Amount Due</th>
                                    <th  data-name="discount_amount" data-data="discount_amount" data-orderable="false" data-searchable="false">Discount</th>
                                    {{-- <th  data-name="erms_id" data-data="erms_id">Date Created</th> --}}
                                    <th class="no-export" data-orderable="false" data-searchable="false" data-name="action"
                                        data-data="action">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="7"></th>
                                    <th>Total (GHS)</th>
                                    <th>{{ $total['totalArrears'] }}</th>
                                    <th>{{ $total['totalAmount'] }}</th>
                                    <th>{{ $total['totalDue'] }}</th>
                                    <th>{{ $total['totalDiscount'] }}</th>
                                    <th colspan="1"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
@endsection
