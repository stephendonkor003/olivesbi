@include('dashboard.includes.analytics')

<div class="col-xxl-12">
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Revenue Statistics
                    </div>

                </div>
                <div class="card-body text-center p-0">
                    <div id="revenue-statistics1"></div>
                    <div class="revenue-statistics">
                        <div id="revenue-statistics"></div>
                        <div class="chart-circle-value"></div>
                    </div>

                    <div class="row justify-content-center mt-4 p-3 gx-xl-1 gx-xxl-3">
                        <div class="col col-xl-4 border-end border-inline-end-dashed">
                            <span class="d-block text-muted mb-1 fs-16">Assembly Fund - (70%)</span>
                            <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">GHS
                                {{ $total['assemblycut'] }}
                                <i class="ti ti-arrow-up text-success"></i></span>
                        </div>
                        <div class="col col-xl-4 border-end border-inline-end-dashed">
                            <span class="d-block text-muted mb-1 fs-16">GOG Fund - (15%)</span>
                            <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">GHS
                                {{ $total['gogcut'] }}
                                <i class="ti ti-arrow-down text-danger"></i></span>
                        </div>
                        <div class="col col-xl-4">
                            <span class="d-block text-muted mb-1 fs-16">Melchia Fund - (15%)</span>
                            <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">GHS
                                {{ $total['level10cut'] }}<i class="ti ti-arrow-up text-success"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card custom-card income-card">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center flex-wrap gap-2 lh-1 p-3">
                        <div class="circle-content">
                            <div id="income"></div>
                            <i class='bx bx-wallet fs-5 text-success'></i>
                        </div>
                        <div class="d-flex flex-column flex-fill">
                            <span class="fw-semibold h6 mb-2">GHS {{ $total['totalBusinessBill'] }}</span>
                            <p class="fs-13 mb-0">Total Revenue Generated For BoP</p>
                        </div>
                        <div class="text-end">
                            <span>This Month -AS AT
                                {{ \Carbon\Carbon::now()->format('l, F j, Y h:i A') }}</span>
                        </div>
                    </div>
                    <div id="income-chart"></div>
                </div>
            </div>
            <div class="card custom-card expense-card">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center flex-wrap gap-2 lh-1 p-3">
                        <div class="circle-content">
                            <div id="expense"></div>
                            <i class='bx bx-dollar-circle fs-5 text-secondary'></i>
                        </div>
                        <div class="d-flex flex-column flex-fill">
                            <span class="fw-semibold h6 mb-2">GHS {{ $total['totalPropertyBill'] }}</span>
                            <p class="fs-13 mb-0">Total Revenue Generated For Property Rate </p>
                        </div>
                        <div class="text-end">
                            <span>This Month AS AT-
                                {{ \Carbon\Carbon::now()->format('l, F j, Y h:i A') }}</span>
                        </div>
                    </div>
                    <div id="expenditure-chart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-8">
        <div class="card custom-card h-100">
            <div class="card-header">
                <div class="card-title">Regional Revenue Trends Chart</div>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div id="chart-loader" class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p id="f_load">Loading data...</p>
                </div>

                <div id="column-basic2"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card custom-card h-100">
            <div class="card-header">
                <div class="card-title">Regional Overview Chart</div>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div id="donut-loader" class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p id="s_load">Loading data...</p>
                </div>

                <div id="donut-regional"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary">
                                    <i class="ti ti-shopping-bag fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block"> Total Properties</span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    {{ $total['totalProperties'] }}</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary">
                                    <i class="ti ti-currency-dollar fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">Valued </span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    0.00%</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-success shadow shadow-success">
                                    <i class="ti ti-box fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">Un-Valued </span>
                                </div>
                                <span class="d-block mb-2"></span>
                                <h4 class="fw-semibold mb-3 lh-1">0.00%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary">
                                    <i class="ti ti-shopping-bag fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block"> Total Businessess</span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    {{ $total['totalBusinesses'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary">
                                    <i class="ti ti-currency-dollar fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">Validated </span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    0.00%</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-success shadow shadow-success">
                                    <i class="ti ti-box fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">Un-Validated </span>
                                </div>
                                <span class="d-block mb-2"></span>
                                <h4 class="fw-semibold mb-3 lh-1">0.00%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary">
                                    <i class="ti ti-shopping-bag fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block"> Total Assemblies</span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    {{ $total['totalAssembly'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary">
                                    <i class="ti ti-currency-dollar fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">Active </span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">
                                    0.00%</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="avatar avatar-md avatar-rounded bg-success shadow shadow-success">
                                    <i class="ti ti-box fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-block">In-Active </span>
                                </div>
                                <span class="d-block mb-2"></span>
                                <h4 class="fw-semibold mb-3 lh-1">0.00%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-12">
        <div class="card-header d-flex justify-content-end flex-wrap mt-0 mb-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <a type="button" href="{{ route('dashboard.operational', ['display' => 'daily']) }}"
                    class="btn btn-primary btn-sm btn-wave">Daily</a>
                <a type="button" href="{{ route('dashboard.operational', ['display' => 'weekly']) }}"
                    class="btn btn-primary-light btn-sm btn-wave">Weekly</a>
                <a type="button" href="{{ route('dashboard.operational', ['display' => 'monthly']) }}"
                    class="btn btn-primary-light btn-sm btn-wave">Monthly</a>
                <a type="button" href="{{ route('dashboard.operational', ['display' => 'yearly']) }}"
                    class="btn btn-primary-light btn-sm btn-wave">Yearly</a>
            </div>
        </div>
    </div>

    <div class="col-xxl-12">
        <div class="row g-3">
            <!-- Total Payments -->
            <div class="col-xl-3">
                <div class="card custom-card shadow-sm bg-gradient-light rounded-3 equal-height-card">
                    <div class="card-body position-relative">
                        <div class="d-flex flex-column">
                            <span class="fs-6 fw-bold text-uppercase mb-3" style="color: #4e73df;">
                                Payments Made
                            </span>
                            <h5 class="fw-normal lh-1 mb-0 text-dark">GHS {{ $total['dashTotalPayments'] }}</h5>
                        </div>
                        <div class="horizontal-line"
                            style="position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background-color: #4e73df;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Bill Amount -->
            <div class="col-xl-3">
                <div class="card custom-card shadow-sm bg-gradient-light rounded-3 equal-height-card">
                    <div class="card-body position-relative">
                        <div class="d-flex flex-column">
                            <span class="fs-6 fw-bold text-uppercase mb-3" style="color: #1cc88a;">
                                Bill Amount
                            </span>
                            <h5 class="fw-normal lh-1 mb-0 text-dark">GHS {{ $total['dashTotalBills'] }}</h5>
                        </div>
                        <div class="horizontal-line"
                            style="position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background-color: #1cc88a;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Outstanding Payments -->
            <div class="col-xl-3">
                <div class="card custom-card shadow-sm bg-gradient-light rounded-3 equal-height-card">
                    <div class="card-body position-relative">
                        <div class="d-flex flex-column">
                            <span class="fs-6 fw-bold text-uppercase mb-3" style="color: #dc1212;">
                                Outstanding Payments
                            </span>
                            <h5 class="fw-normal lh-1 mb-0 text-dark">GHS {{ $total['dashTotalOutstanding'] }}</h5>
                        </div>
                        <div class="horizontal-line"
                            style="position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background-color: #dc1212;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Bill Count -->
            <div class="col-xl-3">
                <div class="card custom-card shadow-sm bg-gradient-light rounded-3 equal-height-card">
                    <div class="card-body position-relative">
                        <div class="d-flex flex-column">
                            <span class="fs-6 fw-bold text-uppercase mb-3" style="color: #36b9cc;">
                                Bill Count
                            </span>
                            <h5 class="fw-normal lh-1 mb-0 text-dark">{{ $total['dashTotalBillCount'] }}</h5>
                        </div>
                        <div class="horizontal-line"
                            style="position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background-color: #36b9cc;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-12">
                <div id="regional-data-container">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="row">
    <div class="col-xxl-12">
        <div id="regional-data-container">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p>Loading data...</p>
            </div>
        </div>
    </div>
</div> --}}
