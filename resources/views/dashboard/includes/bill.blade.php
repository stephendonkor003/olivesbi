@extends('layout.base')

@section('page-styles')
@endsection


@section('page-content')
    <div class="card">
        <!-- HEADER SECTION -->
        <div class="card-body border-bottom pb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="ri-file-chart-line me-2"></i> Bills Analysis Overview
                    </h4>

                    <p class="mb-0 text-muted fs-14">
                        Get all your generated bills, region by region, across all 16 regions of Ghana.
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
                        <form method="GET" action="{{ route('dashboard.billAnalytic') }}" class="mb-3">
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="arrears" class="form-label">Region</label>
                                    <select name="region" id="region" class="form-select">
                                        <option value="">All</option>
                                        @foreach ($total['regionsDrop'] as $region)
                                            <option value="{{ $region->regional_code }}"
                                                {{ request('region') == $region->regional_code ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
        <div class="col-xxl-12">
            <div class="card custom-card">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 align-items-center mb-2 justify-content-between">
                        <p class="mb-0 fw-semibold">Revenue Overview</p>
                    </div>
                    <h3 class="d-inline-block me-2">{{ $total['completedPercentage'] }}%</h3><span
                        class="text-muted fs-12 align-middle">Generated Bills</span>
                    <div class="d-flex align-items-center mb-1 justify-content-between">
                        <div>Revenue Progress</div>
                    </div>
                    <div class="progress-stacked progress-sm mb-3 mt-2 gap-1">
                        <div class="progress-bar rounded" role="progressbar"
                            style="width: <?= $total['completedPercentage'] ?>%"
                            aria-valuenow="<?= $total['completedPercentage'] ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                        <div class="progress-bar bg-secondary rounded" role="progressbar"
                            style="width: <?= $total['upcomingPercentage'] ?>%"
                            aria-valuenow="<?= $total['upcomingPercentage'] ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                        <div class="progress-bar bg-success rounded" role="progressbar"
                            style="width: <?= $total['newPercentage'] ?>%" aria-valuenow="<?= $total['newPercentage'] ?>"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-start justify-content-between flex-wrap flex-xxl-nowrap">
                        <div class="text-center">
                            <div class="flex-shrink-0 avatar avatar-md bg-primary-transparent avatar-rounded mb-2">
                                <i class="ri-file-list-3-fill fs-15 lh-1"></i>
                            </div>
                            <div class="fw-semibold mb-1">
                                Generated Bills
                            </div>
                            <span class="text-muted fs-14 fw-medium">{{ $total['completedPercentage'] }}%</span>
                            <div class="fw-semibold mt-1">
                                {{ $total['totalCompletedBill'] }} Bills
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="flex-shrink-0 avatar avatar-md bg-secondary-transparent avatar-rounded mb-2">
                                <i class="ri-file-list-3-fill fs-15 lh-1"></i>
                            </div>
                            <div class="fw-semibold mb-1">
                                Arrears
                            </div>
                            <span class="text-muted fs-14 fw-medium">{{ $total['upcomingPercentage'] }}%</span>
                            <div class="fw-semibold mt-1">
                                {{ $total['totalUpcomingBill'] }} Arrears On Bills
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="flex-shrink-0 avatar avatar-md bg-success-transparent avatar-rounded mb-2">
                                <i class="ri-file-list-3-fill fs-15 lh-1"></i>
                            </div>
                            <div class="fw-semibold mb-1">
                                Payments
                            </div>
                            <span class="text-muted fs-14 fw-medium">{{ $total['newPercentage'] }}%</span>
                            <div class="fw-semibold mt-1">
                                {{ $total['totalNewBill'] }} Payments On Bills
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <div class="card-title">Regional Bill Overview</div>
                </div>
                <div class="card-body">
                    <div id="column-rotated-labels"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        var regions = @json($total['regionsDrop']);
        var totalBills = @json($total['totals']);
        var regionNames = regions.map(region => region.name);

        var options = {
            series: [{
                name: 'Total Bills',
                data: totalBills
            }],
            chart: {
                type: 'bar',
                height: 320
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '80%',
                    endingShape: 'rounded'
                },
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            dataLabels: {
                enabled: false
            },
            colors: ["#ff7f7f"],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: regionNames,
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            },
            yaxis: {
                title: {
                    text: 'Total Bills (GHS)',
                    style: {
                        color: "#8c9097",
                    }
                },
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "GHS " + val.toLocaleString();
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-rotated-labels"), options);
        chart.render();
    </script>
@endsection
