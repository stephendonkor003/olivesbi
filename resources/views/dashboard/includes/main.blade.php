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
                            <h4 class="fw-semibold mb-2"> {{ $total['totalAssembly'] }}</h4>
                            <div class="text-muted mb-0"> Total Assemblies </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                            <h4 class="fw-semibold mb-2">GHS {{ $total['totalArrears'] }}</h4>
                            <div class="text-muted mb-0">Outstanding Balances</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                            <h4 class="fw-semibold mb-2">{{ $total['totalProperties'] }}</h4>
                            <div class="text-muted mb-0">Registered Properties </div>
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
                                    <span class="d-block"> Total Revenue Collected</span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">GHS {{ $total['yearlyPayments'] }}</h4>

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
                                    <span class="d-block">Total Revenue Growth</span>
                                </div>
                                <h4 class="fw-semibold mb-3 lh-1">{{ $total['newPercentage'] }}%</h4>

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
                                    <span class="d-block"> Outstanding Payments</span>
                                </div>
                                <span class="d-block mb-2"></span>
                                <h4 class="fw-semibold mb-3 lh-1">GHS {{ $total['totalBill'] }}</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-12">
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Payment Trends
            </div>
            <a href="{{ route('payments.index', ['display' => 'yearly']) }}" class="btn btn-primary btn-sm">View
                All Payments</a>

        </div>
        <div class="card-body">
            <div class="list-unstyled row mt-1">
                <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                    <div
                        class="border border-primary border-opacity-10 align-items-center gap-2 p-3 text-center rounded-3 pod-artist">
                        <div class="lh-1 mb-3 rounded-circle">
                            <span class="podcast-author rounded-circle d-inline-block">
                                <span class="avatar avatar-xxl p-1 bg-white avatar-rounded">
                                    <img src="{{ asset('assets/images/coin.jpg') }}" alt="">
                                </span>
                            </span>
                        </div>
                        <div>
                            <a href="javascript:void(0);" class="fw-semibold">Today's Payment</a>
                            <p class="text-muted mb-2">As At , {{ \Carbon\Carbon::now()->format('l, F j, Y ') }}
                            </p>
                            <span class="text-primary fw-semibold">GHS {{ $total['dailyPayments'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                    <div
                        class="border border-secondary border-opacity-10 align-items-center gap-2 p-3 text-center rounded-3 pod-artist">
                        <div class="lh-1 mb-3">
                            <span class="podcast-author secondary rounded-circle d-inline-block">
                                <span class="avatar avatar-xxl p-1 bg-white avatar-rounded">
                                    <img src="{{ asset('assets/images/coin.jpg') }}" alt="">
                                </span>
                            </span>
                        </div>
                        <div>
                            <a href="javascript:void(0);" class="fw-semibold">This Week's Payment </a>
                            <p class="text-muted mb-2">As At ,
                                {{ \Carbon\Carbon::now()->format('l, F j, Y ') }}
                            </p>
                            <span class="text-secondary fw-semibold">GHS {{ $total['weeklyPayments'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                    <div
                        class="border border-success border-opacity-10 align-items-center gap-2 p-3 text-center rounded-3 pod-artist">
                        <div class="lh-1 mb-3">
                            <span class="podcast-author success rounded-circle d-inline-block">
                                <span class="avatar avatar-xxl p-1 bg-white avatar-rounded">
                                    <img src="{{ asset('assets/images/coin.jpg') }}" alt="">
                                </span>
                            </span>
                        </div>
                        <div>
                            <a href="javascript:void(0);" class="fw-semibold">This Month's Payment </a>
                            <p class="text-muted mb-2">As At ,
                                {{ \Carbon\Carbon::now()->format('l, F j, Y ') }}
                            </p>
                            <span class="text-success fw-semibold">GHS {{ $total['monthlyPayments'] }} </span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div
                        class="border border-info border-opacity-10 align-items-center gap-2 p-3 text-center rounded-3 pod-artist">
                        <div class="lh-1 mb-3">
                            <span class="podcast-author info rounded-circle d-inline-block">
                                <span class="avatar avatar-xxl p-1 bg-white avatar-rounded">
                                    <img src="{{ asset('assets/images/coin.jpg') }}" alt="">
                                </span>
                            </span>
                        </div>
                        <div>
                            <a href="javascript:void(0);" class="fw-semibold">This Year's Payment</a>
                            <p class="text-muted mb-2">As At ,
                                {{ \Carbon\Carbon::now()->format('l, F j, Y ') }}
                            </p>
                            <span class="text-info fw-semibold">GHS {{ $total['yearlyPayments'] }} </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-12 col-xl-12 col-lg-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center justify-content-between bg-light p-2 px-3 rounded-3">
                        <h6 class="fw-semibold mb-0">Operational Information :</h6>
                    </div>
                    <div
                        class="row row-cols-md-4 row-cols-sm-3 row-cols-xxl-auto personal-favourite-contacts mb-0 gap-2 flex-wrap">
                        <div class="col flex-fill">
                            <div class="d-flex align-items-center bg-primary-transparent rounded-pill p-2 pe-5">
                                <div class="me-2">
                                    <span class="avatar avatar-md avatar-rounded bg-primary p-2">
                                        <i class="ri-macbook-line fs-18 lh-1"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold mb-0 text-default">Total Agents
                                    </div>
                                    <span class="text-muted">{{ $total['totalAssemblyAgents'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col flex-fill">
                            <div class="d-flex align-items-center bg-secondary-transparent rounded-pill p-2 pe-5">
                                <div class="me-2">
                                    <span class="avatar avatar-md avatar-rounded bg-secondary p-2">
                                        <i class="ri-briefcase-2-line fs-18 lh-1"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold mb-0 text-default"> Active Agents</div>
                                    <span class="text-muted">{{ $total['totalActiveAssemblyAgents'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col flex-fill">
                            <div class="d-flex align-items-center bg-success-transparent rounded-pill p-2 pe-5">
                                <div class="me-2">
                                    <span class="avatar avatar-md avatar-rounded bg-success p-2">
                                        <i class="ri-heart-pulse-line fs-18 lh-1"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold mb-0 text-default"> In-Active Agents</div>
                                    <span class="text-muted">{{ $total['totalInactiveAssemblyAgents'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col flex-fill">
                            <div class="d-flex align-items-center bg-info-transparent rounded-pill p-2 pe-5">
                                <div class="me-2">
                                    <span class="avatar avatar-md avatar-rounded bg-info p-2">
                                        <i class="ri-football-line fs-18 lh-1"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold mb-0 text-default">Total Businessess</div>
                                    <span class="text-muted">{{ $total['totalBusinesses'] }}</span>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col flex-fill">
                            <div class="d-flex align-items-center bg-pink-transparent rounded-pill p-2 pe-5">
                                <div class="me-2">
                                    <span class="avatar avatar-md avatar-rounded bg-warning p-2">
                                        <i class="ri-briefcase-2-line fs-18 lh-1"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold mb-0 text-default">Total Properties</div>
                                    <span class="text-muted">{{ $total['totalProperties'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3">
            <div class="card custom-card profit-analysis-card">
                <div class="card-body p-0">
                    <div class="p-4 pb-1">
                        <h4 class="mb-1 d-flex align-items-center fw-semibold flex-wrap">GHS
                            {{ $total['totalExpectedPayments'] }}<span class="text-success fw-medium fs-12 ms-2"><i
                                    class="ti ti-arrow-up align-middle me-1"></i> </span> </h4>
                        <span class="fs-14 d-block">Expected Revenue </span>
                    </div>
                    <div id="profit-analysis"></div>
                    <div id="profit-analysis1"></div>
                    <div id="profit-analysis2"></div>
                </div>
            </div>
            <div class="card custom-card" style="height: 238px">
                <div class="card-header">
                    <div class="card-title">
                        Bills And Payments

                    </div>
                </div>
                <div class="card-body">
                    <div class="progress-stacked progress-sm mb-4 mt-2 gap-1">
                        <div class="progress-bar rounded" role="progressbar" style="width: 45%" aria-valuenow="45"
                            aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-secondary rounded" role="progressbar" style="width: 25%"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-success rounded" role="progressbar" style="width: 30%"
                            aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <ul class="list-unstyled sales-traffic-list">
                        <li>
                            <div class="d-flex align-items-center flex-wrap justify-content-between">
                                <div class="fw-semibold"> Bills</div>
                                <div class="fw-semibold">GHS
                                    {{ $total['totalBill'] }}</div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center flex-wrap justify-content-between">
                                <div class="fw-semibold">Arrears</div>
                                <div class="fw-semibold">GHS
                                    {{ $total['totalArrears'] }} </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center flex-wrap justify-content-between">
                                <div class="fw-semibold">Revenue</div>
                                <div class="fw-semibold">GHS
                                    {{ $total['totalExpectedPayments'] }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Payment Statistics
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="sales-statistics1" class="p-3"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12">
        <div class="row">
            <!-- Revenue Statistics Card -->
{{--            <div class="col-xl-12">--}}
{{--                <div class="card custom-card overflow-hidden">--}}
{{--                    <div class="card-header justify-content-between">--}}
{{--                        <div class="card-title">--}}
{{--                            Revenue Statistics--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="card-body text-center p-0">--}}
{{--                        <div id="revenue-statistics1"></div>--}}

{{--                        <div class="revenue-statistics">--}}
{{--                            <div id="revenue-statistics"></div>--}}
{{--                            <div class="chart-circle-value"></div>--}}
{{--                        </div>--}}

{{--                        <div class="row justify-content-center mt-4 p-3 gx-xl-1 gx-xxl-3">--}}
{{--                            <!-- Assembly Fund -->--}}
{{--                            <div class="col col-xl-4 border-end border-inline-end-dashed">--}}
{{--                                <span class="d-block text-muted mb-1 fs-16">Assembly Fund - (70%)</span>--}}
{{--                                <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">--}}
{{--                                    GHS--}}
{{--                                    {{ is_numeric($total['assemblycut'] ?? null) ? number_format($total['assemblycut'], 2) : '0.00' }}--}}
{{--                                    <i class="ti ti-arrow-up text-success"></i>--}}
{{--                                </span>--}}
{{--                            </div>--}}

{{--                            <!-- GOG Fund -->--}}
{{--                            <div class="col col-xl-4 border-end border-inline-end-dashed">--}}
{{--                                <span class="d-block text-muted mb-1 fs-16">GOG Fund - (15%)</span>--}}
{{--                                <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">--}}
{{--                                    GHS--}}
{{--                                    {{ is_numeric($total['gogcut'] ?? null) ? number_format($total['gogcut'], 2) : '0.00' }}--}}
{{--                                    <i class="ti ti-arrow-down text-danger"></i>--}}
{{--                                </span>--}}
{{--                            </div>--}}

{{--                            <!-- Melchia Fund -->--}}
{{--                            <div class="col col-xl-4">--}}
{{--                                <span class="d-block text-muted mb-1 fs-16">Melchia Fund - (15%)</span>--}}
{{--                                <span class="fw-semibold mb-0 text-center" style="font-size: 20px !important;">--}}
{{--                                    GHS--}}
{{--                                    {{ is_numeric($total['level10cut'] ?? null) ? number_format($total['level10cut'], 2) : '0.00' }}--}}
{{--                                    <i class="ti ti-arrow-up text-success"></i>--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Revenue Cards -->
            <div class="col-xl-12">
                <!-- BoP Revenue -->
                <div class="card custom-card income-card">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center flex-wrap gap-2 lh-1 p-3">
                            <div class="circle-content">
                                <div id="income"></div>
                                <i class='bx bx-wallet fs-5 text-success'></i>
                            </div>
                            <div class="d-flex flex-column flex-fill">
                                <span class="fw-semibold h6 mb-2">
                                    GHS
                                    {{ is_numeric($total['totalBusinessBill'] ?? null) ? number_format($total['totalBusinessBill'], 2) : '0.00' }}
                                </span>
                                <p class="fs-13 mb-0">Total Revenue Generated For BoP</p>
                            </div>
                            <div class="text-end">
                                <span>This Month - AS AT {{ now()->format('l, F j, Y h:i A') }}</span>
                            </div>
                        </div>
                        <div id="income-chart"></div>
                    </div>
                </div>

                <!-- Property Rate Revenue -->
                <div class="card custom-card expense-card">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center flex-wrap gap-2 lh-1 p-3">
                            <div class="circle-content">
                                <div id="expense"></div>
                                <i class='bx bx-dollar-circle fs-5 text-secondary'></i>
                            </div>
                            <div class="d-flex flex-column flex-fill">
                                <span class="fw-semibold h6 mb-2">
                                    GHS
                                    {{ is_numeric($total['totalPropertyBill'] ?? null) ? number_format($total['totalPropertyBill'], 2) : '0.00' }}
                                </span>
                                <p class="fs-13 mb-0">Total Revenue Generated For Property Rate</p>
                            </div>
                            <div class="text-end">
                                <span>This Month - AS AT {{ now()->format('l, F j, Y h:i A') }}</span>
                            </div>
                        </div>
                        <div id="expenditure-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="col-xl-12 col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Weekly Payments Performance</div>
        </div>
        <div class="card-body py-0 custome-tooltip">
            <div id="activity1"></div>
        </div>
        <div class="card-footer border-0 pt-0">
            <a href="{{ route('payments.index') }}" class="btn btn-primary btn-block">Explore Details</a>
        </div>
    </div>
</div>

<div class="col-xl-12 col-md-12">
    <div class="card">

        <div class="card-header">
            <div class="card-title">Monthly Payments Performance</div>
        </div>
        <div class="card-body py-0 custome-tooltip">
            <div id="activity2"></div>
        </div>
        <div class="card-footer border-0 pt-0">
            <a href="{{ route('payments.index') }}" class="btn btn-primary btn-block">Explore Details</a>
        </div>
    </div>
</div>
