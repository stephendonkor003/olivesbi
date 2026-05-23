@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
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

        <div class="col-xl-12">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">

                    <div class="row">
                        <div class="col-xxl-12">
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
                                            <option value="1" {{ request('arrears') == '1' ? 'selected' : '' }}>With
                                                Arrears</option>
                                            <option value="0" {{ request('arrears') == '0' ? 'selected' : '' }}>Without
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


        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="card-header">
                    <div class="card-title">Bills Management / Property Bills</div>
                </div>
                <div class="d-flex align-items-center">
                    @can('bills.create')
                        <button class="btn btn-primary btn-wave">
                            <a href="{{ route('bills.create') }}" class="text-white text-decoration-none">
                                <i class="ri-share-forward-line me-1 rtl-icon-transform lh-1 d-inline-block"></i> Bulk Bill
                                Generation
                            </a>
                        </button>
                    @endcan

                    @can('bills.create')
                        ......
                        <button class="btn btn-primary btn-wave">
                            <a href="{{ route('bills.singleCreate') }}" class="text-white text-decoration-none">
                                <i class="ri-share-forward-line me-1 rtl-icon-transform lh-1 d-inline-block"></i> Single Bill
                                Generation
                            </a>
                        </button>
                    @endcan

                    {{-- @can('bills.create')
                        ...
                        <button class="btn btn-primary btn-wave">
                            <a href="{{ route('bills.divisionCreate') }}" class="text-white text-decoration-none">
                                <i class="ri-share-forward-line me-1 rtl-icon-transform lh-1 d-inline-block"></i> Division Bills
                            </a>
                        </button>
                    @endcan --}}

                    {{-- @can('bills.create')
                        ...
                        <button class="btn btn-primary btn-wave">
                            <a href="{{ route('bills.blockCreate') }}" class="text-white text-decoration-none">
                                <i class="ri-share-forward-line me-1 rtl-icon-transform lh-1 d-inline-block"></i> Block Bills
                            </a>
                        </button>
                    @endcan --}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex gap-2 justify-content-between">
                            <div class="d-flex flex-column justify-content-between gap-2">
                                <div id="crmchart01"></div>
                            </div>
                            <div class="text-end">
                                <div class="avatar avatar-md bg-primary bg-opacity-25 avatar-rounded mb-2">
                                    <div class="avatar avatar-sm bg-primary text-fixed-white avatar-rounded">
                                        <i class="ri-bar-chart-box-line fs-18"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="fw-semibold mb-2"> GHS {{ $total['totalExpectedPayments'] }} </h4>
                                    <div class="text-muted mb-0">Total Bills Payables </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex gap-2 justify-content-between">
                            <div class="d-flex flex-column justify-content-between gap-2">
                                <div id="crmchart02"></div>
                            </div>
                            <div class="text-end">
                                <div class="avatar avatar-md bg-secondary bg-opacity-25 avatar-rounded mb-2">
                                    <div class="avatar avatar-sm bg-secondary text-fixed-white avatar-rounded">
                                        <i class="ri-user-add-line fs-18"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="fw-semibold mb-2">GHS {{ $total['totalPayments'] }} </h4>
                                    <div class="text-muted mb-0"> Total Bills Paid </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex gap-2 justify-content-between">
                            <div class="d-flex flex-column justify-content-between gap-2">
                                <div id="crmchart03"></div>
                            </div>
                            <div class="text-end">
                                <div class="avatar avatar-md bg-success bg-opacity-25 avatar-rounded mb-2">
                                    <div class="avatar avatar-sm bg-success text-fixed-white avatar-rounded">
                                        <i class="ri-shake-hands-line fs-18"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="fw-semibold mb-2">GHS {{ $total['totalUnpaidBills'] }} </h4>
                                    <div class="text-muted mb-0"> Total Un-Paid Bills</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex gap-2 justify-content-between">
                            <div class="d-flex flex-column justify-content-between gap-2">
                                <div id="crmchart04"></div>
                            </div>
                            <div class="text-end">
                                <div class="avatar avatar-md bg-info bg-opacity-25 avatar-rounded mb-2">
                                    <div class="avatar avatar-sm bg-info text-fixed-white avatar-rounded">
                                        <i class="ri-hourglass-line fs-18"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="fw-semibold mb-2">{{ $total['paymentPercentage'] }}%</h4>
                                    <div class="text-muted mb-0"> Payment Percentage</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if (session()->has('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                        fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    <strong>{{ session('status') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i
                                class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
            @endif

            <div class="col-xl-12 active-p">
                <div class="tab-pane fade show active" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">

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
    </div>
    </div>
@endsection


@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
@endsection
