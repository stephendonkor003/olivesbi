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
                        <i class="ri-file-chart-line me-2"></i> Payments Analysis Overview
                    </h4>


                    <p class="mb-0 text-muted fs-14">
                        Gain valuable insights through charts on Revenue Trends, Top Performing Assemblies by Cash Flow
                        (GHS), Outstanding Payments, Revenue by Location, Regional Arrears, and Collection Efficiency.
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
                        <form method="GET" action="{{ route('dashboard.paymentAnalytic') }}" class="mb-3">
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
                                        @foreach ($total['regions'] as $region)
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
        <div class="col-xxl-12 col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Country Statistics
                    </div>
                    <a href="javascript:void(0);"
                        class="btn btn-light btn-wave btn-sm text-muted waves-effect waves-light">Export</a>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled country-stats-list">
                        <li>
                            <div class="d-flex align-items-center gap-3">
                                <div class="lh-1">
                                    <span class="avatar avatar-md bg-light p-2">
                                        <img src="{{ asset('assets/images/flags/india_flag.jpg') }}" alt=""
                                            class="rounded-circle">
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="d-flex mb-2 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold d-block">Overall Yearly Momo
                                                Payment</span>
                                        </div>
                                        <div class="fw-medium"><span class="text-danger me-1"><i
                                                    class="ti ti-arrow-down align-middle"></i></span><span>GHS
                                                {{ $total['yearlyMomoPayments'] }}</span>
                                            (65%)</div>
                                    </div>
                                    <div class="progress progress-xs progress-animate" role="progressbar" aria-valuenow="65"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center gap-3">
                                <div class="lh-1">
                                    <span class="avatar avatar-md bg-light p-2">
                                        <img src="{{ asset('assets/images/flags/russia_flag.jpg') }}" alt=""
                                            class="rounded-circle">
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="d-flex mb-2 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold d-block">Total Yearly
                                                Payments</span>
                                        </div>
                                        <div class="fw-medium"><span class="text-success me-1"><i
                                                    class="ti ti-arrow-up align-middle"></i></span><span>GHS
                                                {{ $total['yearlyPayments'] }}</span>
                                            (55%)</div>
                                    </div>
                                    <div class="progress progress-xs progress-animate" role="progressbar" aria-valuenow="55"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-secondary" style="width: 55%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center gap-3">
                                <div class="lh-1">
                                    <span class="avatar avatar-md bg-light p-2">
                                        <img src="{{ asset('assets/images/flags/canada_flag.jpg') }}" alt=""
                                            class="rounded-circle">
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="d-flex mb-2 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold d-block">Total
                                                Receivables</span>
                                        </div>
                                        <div class="fw-medium"><span class="text-danger me-1"><i
                                                    class="ti ti-arrow-down align-middle"></i></span><span>>GHS
                                                {{ $total['yearlyReceivables'] }}</span>
                                            (69%)</div>
                                    </div>
                                    <div class="progress progress-xs progress-animate" role="progressbar"
                                        aria-valuenow="69" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success" style="width: 69%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Revenue Trends Chart</div>
                </div>
                <div class="card-body">
                    <div id="column-basic"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Top Performing Assemblies (GHS)</div>
                </div>
                <div class="card-body">
                    <div id="column-datalabels"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Outstanding Payments</div>
                </div>
                <div class="card-body">
                    <div id="column-stacked"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Revenue by Location Chart</div>
                </div>
                <div class="card-body">
                    <div id="column-range"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Regional Arrears Chart</div>
                </div>
                <div class="card-body">
                    <div id="chart-arrears"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Collection Efficiency Chart</div>
                </div>
                <div class="card-body">
                    <div id="collection-chart"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        //Revenue trends chart
        var paymentData = @json($total['paymentData']);
        var billData = @json($total['billData']);
        var arrearsData = @json($total['arrearsData']);

        var options = {
            series: [{
                name: 'Payment',
                data: paymentData
            }, {
                name: 'Bill',
                data: billData
            }, {
                name: 'Arrears',
                data: arrearsData
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
            colors: ["#8b7eff", "#35bdaa", "#ffb748"],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
                    text: 'GHS (thousands)',
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
                        return "GHS " + val + " thousands"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-basic"), options);
        chart.render();
    </script>

    <script>
        //Top Performing Assembly graph
        var categories = @json($total['categories']);
        var data = @json($total['graphData']);

        var options = {
            series: [{
                name: 'Assembly',
                data: data
            }],
            chart: {
                height: 320,
                type: 'bar',
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#8c9097"]
                }
            },
            colors: ["#8b7eff"],
            xaxis: {
                categories: categories,
                position: 'top',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
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
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: true,
                    formatter: function(val) {
                        return val;
                    },
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-yaxis-label',
                    },
                },
                title: {
                    text: 'Payment (GHS)',
                    style: {
                        color: '#8c9097',
                        fontSize: '12px',
                        fontWeight: 600,
                    }
                }
            },
            title: {
                text: 'Monthly Payments in Ghana, 2025',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#444'
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#column-datalabels"), options);
        chart.render();
    </script>

    <script>
        //Outstanding payments graph
        var chartData11 = @json($total['chartData']);

        var options = {
            series: chartData11,
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            colors: ["#8b7eff", "#35bdaa", "#ffb748", "#e6533c", "#1e90ff"],
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            xaxis: {
                type: 'category',
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            yaxis: {
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-yaxis-label',
                    },
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#column-stacked"), options);
        chart.render();
    </script>

    <script>
        // Revenue by location chart
        var divisionPaymentData = @json($total['divisionPaymentData']);

        var seriesData = [];
        var divisions = [...new Set(divisionPaymentData.map(item => item.division_name))];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        divisions.forEach(function(division) {
            var divisionData = months.map(function(month) {
                var payment = divisionPaymentData.find(function(item) {
                    return item.division_name === division && item.month === (months.indexOf(
                        month) + 1);
                });

                return payment ? payment.total_payments : 0;
            });

            seriesData.push({
                name: division,
                data: divisionData
            });
        });

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            colors: ["#8b7eff", "#35bdaa", "#ffb748", "#e6533c", "#1e90ff", "#ff6347", "#ff1493", "#32cd32", "#f0e68c",
                "#800080", "#ff4500", "#708090"
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            xaxis: {
                type: 'category',
                categories: months,
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
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            yaxis: {
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-yaxis-label',
                    },
                },
                title: {
                    text: 'Payments (GHS)',
                    style: {
                        color: '#8c9097',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-range"), options);
        chart.render();
    </script>

    <script>
        // Regional arrears chart
        var regionArrearsData = @json($total['regionArrearsData']);

        var seriesData = [];
        var regions = [...new Set(regionArrearsData.map(item => item.region_name))];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        regions.forEach(function(region) {
            var regionData = months.map(function(month) {
                var arrearsData = regionArrearsData.find(function(item) {
                    return item.region_name === region && parseInt(item.month) === (months.indexOf(
                        month) + 1);
                });

                return arrearsData ? arrearsData.arrears : 0;
            });

            seriesData.push({
                name: region,
                data: regionData
            });
        });

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            colors: [
                "#e74c3c", "#3498db", "#2ecc71", "#f1c40f", "#9b59b6", "#1abc9c", "#e67e22", "#34495e",
                "#d35400", "#7f8c8d", "#8e44ad", "#c0392b", "#16a085", "#f39c12", "#2980b9", "#8e44ad"
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            xaxis: {
                type: 'category',
                categories: months,
                labels: {
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                    },
                }
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            yaxis: {
                labels: {
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                    },
                },
                title: {
                    text: 'Arrears (GHS)',
                    style: {
                        color: '#8c9097',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-arrears"), options);
        chart.render();
    </script>

    <script>
        //Collection efficient chart
        var regionPaymentData2 = @json($total['regionPaymentData2']);

        var seriesData = [];
        var regions = [...new Set(regionPaymentData2.map(item => item.region_name))];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var paymentModes = ['momo', 'cash'];

        regions.forEach(function(region) {
            var totalRegionData = months.map(function(month) {
                var totalPaymentsForMonth = paymentModes.reduce(function(total, paymentMode) {
                    var paymentData = regionPaymentData2.find(function(item) {
                        return item.region_name === region && item.payment_mode ===
                            paymentMode && parseInt(item.month) === (months.indexOf(month) +
                                1);
                    });

                    return total + (paymentData ? paymentData.total_payments : 0);
                }, 0);

                return totalPaymentsForMonth;
            });

            seriesData.push({
                name: region,
                data: totalRegionData,
                type: 'bar',
            });
        });

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            colors: [
                "#e74c3c", "#3498db", "#2ecc71", "#f1c40f", "#9b59b6", "#1abc9c", "#e67e22", "#34495e",
                "#d35400", "#7f8c8d", "#8e44ad", "#c0392b", "#16a085", "#f39c12", "#2980b9", "#8e44ad"
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            xaxis: {
                type: 'category',
                categories: months,
                labels: {
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                    },
                }
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            yaxis: {
                labels: {
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                    },
                },
                title: {
                    text: 'Total Payments (GHS)',
                    style: {
                        color: '#8c9097',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#collection-chart"), options);
        chart.render();
    </script>
@endsection
