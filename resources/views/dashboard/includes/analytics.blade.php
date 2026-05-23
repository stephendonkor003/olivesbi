<div class="row">
    <div class="col-xl-3">
        <div class="card custom-card rounded-md overflow-hidden p-2">
            <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                <div class="d-flex gap-2 align-items-center ps-2">
                    <div class="align-self-start">
                        <div class="fw-medium mb-2">Total Payments (Last Month) </div>
                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['lastMonthPayments'] }}</h4>
                    </div>
                    <div class="ms-auto text-end align-self-end">

                        <div>
                            <span class="text-danger fw-medium fs-13 mb-2">
                                GHS
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card custom-card rounded-md overflow-hidden p-2">
            <div class="card-body bg-secondary bg-opacity-10 rounded-2 ps-4 medical-cards secondary">
                <div class="d-flex gap-2 align-items-center ps-2">
                    <div class="align-self-start">
                        <div class="fw-medium mb-2">Total property tax (Last Month)</div>
                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['lastMonthPropertyBill'] }}</h4>
                    </div>
                    <div class="ms-auto text-end align-self-end">
                        <div>
                            <span class="text-success fw-medium fs-13 mb-2">
                                GHS
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card custom-card rounded-md overflow-hidden p-2">
            <div class="card-body bg-success bg-opacity-10 rounded-2 ps-4 medical-cards success">
                <div class="d-flex gap-2 align-items-center ps-2">
                    <div class="align-self-start">
                        <div class="fw-medium mb-2">Total BoP (Last Month)</div>
                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['lastMonthBusinessBill'] }}</h4>
                    </div>
                    <div class="ms-auto text-end align-self-end">
                        <div>
                            <span class="text-danger fw-medium fs-13 mb-2">
                                GHS
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card custom-card rounded-md overflow-hidden p-2">
            <div class="card-body bg-info bg-opacity-10 rounded-2 ps-4 medical-cards info">
                <div class="d-flex gap-2 align-items-center ps-2">
                    <div class="align-self-start">
                        <div class="fw-medium mb-2">Total market tolls (Last Month)</div>
                        <h4 class="fw-semibold mb-0 lh-1">0.00</h4>
                    </div>
                    <div class="ms-auto text-end align-self-end">
                        <div>
                            <span class="text-danger fw-medium fs-13 mb-2">
                                GHS
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-6 col-xl-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 flex-xxl-nowrap">
                    <div class="flex-fill d-flex gap-2 align-items-center">
                        <div class="flex-shrink-0 avatar avatar-sm bg-pink-transparent avatar-rounded">
                            <i class="ri-money-dollar-circle-line fs-5 lh-1"></i>
                        </div>
                        <p class="mb-0 fw-medium">Total Revenue Collected</p>
                    </div>
                    <div class="text-end ms-auto">
                        <h4 class="mb-0 fw-semibold">GHS {{ $total['dailyPayments'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap bg-pink-transparent justify-content-between">
                <div id="widget-chart-1"></div>
                <div class="text-end pe-3">
                    <span class="text-muted fw-medium fs-12">Daily</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-xl-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 flex-xxl-nowrap">
                    <div class="flex-fill d-flex gap-2 align-items-center">
                        <div class="flex-shrink-0 avatar avatar-sm bg-primary-transparent avatar-rounded">
                            <i class="ri-money-dollar-circle-line fs-5 lh-1"></i>
                        </div>
                        <p class="mb-0 fw-medium">Total Revenue Collected</p>
                    </div>
                    <div class="text-end ms-auto">
                        <h4 class="mb-0 fw-semibold">GHS {{ $total['weeklyPayments'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap bg-primary-transparent justify-content-between">
                <div id="widget-chart-2"></div>
                <div class="text-end pe-3">
                    <span class="text-muted fw-medium fs-12">Weekly</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-xl-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 flex-xxl-nowrap">
                    <div class="flex-fill d-flex gap-2 align-items-center">
                        <div class="flex-shrink-0 avatar avatar-sm bg-secondary-transparent avatar-rounded">
                            <i class="ri-money-dollar-circle-line fs-5 lh-1"></i>
                        </div>
                        <p class="mb-0 fw-medium">Total Revenue Collected </p>
                    </div>
                    <div class="text-end ms-auto">
                        <h4 class="mb-0 fw-semibold">GHS {{ $total['monthlyPayments'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap bg-secondary-transparent justify-content-between">
                <div id="widget-chart-3"></div>
                <div class="text-end pe-3">
                    <span class="text-muted fw-medium fs-12">Monthly</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-xl-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 flex-xxl-nowrap">
                    <div class="flex-fill d-flex gap-2 align-items-center">
                        <div class="flex-shrink-0 avatar avatar-sm bg-success-transparent avatar-rounded">
                            <i class="ri-money-dollar-circle-line fs-5 lh-1"></i>
                        </div>
                        <p class="mb-0 fw-medium">Total Revenue Collected </p>
                    </div>
                    <div class="text-end ms-auto">
                        <h4 class="mb-0 fw-semibold">GHS {{ $total['yearlyPayments'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap bg-success-transparent justify-content-between">
                <div id="widget-chart-4"></div>
                <div class="text-end pe-3">
                    <span class="text-muted fw-medium fs-12">Yearly</span>
                </div>
            </div>
        </div>
    </div>
</div>
