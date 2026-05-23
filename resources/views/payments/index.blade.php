@extends('layout.base')

@section('page-styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="row">
            <div class="col-xl-12 active-p">
                <div class="card">
                    <!-- HEADER SECTION -->
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-wallet-3-line me-2"></i> Payments Management
                                </h4>

                                <p class="mb-0 text-muted fs-14">
                                    You are Viewing All Payment on A generated bills record in your
                                    central database repository.
                                </p>
                            </div>

                            {{-- <a href="{{ route('bills.fetchBill') }}" class="btn btn-sm btn-primary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a> --}}
                        </div>
                    </div>
                </div>

                <div class="card custom-card rounded-md overflow-hidden p-2">
                    <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                        <div class="row">
                            <div class="col-xxl-12">
                                <form action="{{ route('payments.index') }}" method="GET">
                                    <div class="row align-items-end g-3">

                                        <!-- Payment Mode -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="payment_mode">Payment Mode</label>
                                                <select name="payment_mode" id="payment_mode" class="form-select">
                                                    <option value="">Select Payment Mode</option>
                                                    @foreach($paymentModes as $mode)
                                                        <option value="{{$mode}}"
                                                            {{ request('payment_mode') == $mode ? 'selected' : '' }}>{{\Illuminate\Support\Str::title($mode)}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-select">
                                                    <option value="">Select Status</option>
                                                    @foreach($statuses as $status)
                                                        <option value="{{$status}}"
                                                            {{ request('status') == $status ? 'selected' : '' }}>{{\Illuminate\Support\Str::title($status)}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- From Date -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="from_date">From Date</label>
                                                <input type="date" name="from_date" id="from_date"
                                                    value="{{ request('from_date') }}" class="form-control">
                                            </div>
                                        </div>

                                        <!-- To Date -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="to_date">To Date</label>
                                                <input type="date" name="to_date" id="to_date"
                                                    value="{{ request('to_date') }}" class="form-control">
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary"
                                                style="width: 100px;">Filter</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Payment Data Cards Section -->
                <!-- Pending Payments Card -->
                <div class="row">
                    <div class="col-xl-4">
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
                                            <h4 class="fw-semibold mb-2">GHS {{ $totalPendingPayments }}</h4>
                                            <div class="text-muted mb-0">Total Pending Payments</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Successful Payments Card -->
                    <div class="col-xl-4">
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
                                            <h4 class="fw-semibold mb-2">GHS {{ $totalSuccessfulPayments }}</h4>
                                            <div class="text-muted mb-0">Total Successful Payments</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Payments Card -->
                    <div class="col-xl-4">
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
                                            <h4 class="fw-semibold mb-2">GHS {{ $totalFailedPayments }}</h4>
                                            <div class="text-muted mb-0">Total Failed Payments</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Data Table Section -->
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

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i
                                    class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table id="file-export" class="table table-bordered text-nowrap w-100"
                                   data-ajax="{{ route('payments.index', request()->query()) }}" data-server-side="true"
                                   data-processing="true">
                                <thead>
                                    <tr>
                                        <th data-orderable="false" data-searchable="false" data-name="DT_RowIndex" data-data="DT_RowIndex">SN</th>
                                        <th data-orderable="false" data-name="bills_id" data-data="bills_id">Bills ID</th>
                                        <th data-name="name" data-data="name" data-orderable="false" data-searchable="false">Name</th>
                                        <th data-name="amount" data-data="amount" data-orderable="false" data-searchable="false">Amount</th>
                                        <th data-name="payment_mode" data-data="payment_mode" data-orderable="false" data-searchable="false">Payment Mode</th>
                                        <th data-name="transaction_status" data-data="transaction_status" data-orderable="false" data-searchable="false">Status</th>
                                        <th data-name="assembly.name" data-data="assembly.name" data-orderable="false" data-searchable="false">Assembly</th>
                                        <th data-name="created_by.name" data-data="created_by.name" data-orderable="false" data-searchable="false">Paid By</th>
                                        <th data-name="created_at" data-data="created_at" data-orderable="false" data-searchable="false">Payment Date</th>
                                        <th class="no-export" data-orderable="false" data-searchable="false" data-name="action"
                                            data-data="action">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th colspan="2"></th>
                                    <th>Total (GHS)</th>
                                    <th>{{ $total }}</th>
                                    <th colspan="6"></th>
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

@endsection
