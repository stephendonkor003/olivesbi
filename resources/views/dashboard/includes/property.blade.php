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
                        <i class="ri-file-chart-line me-2"></i> Properties Analysis Overview
                    </h4>

                    <p class="mb-0 text-muted fs-14">
                        Get insights into Total Registered Rate Payers, Active vs. Inactive Rate Payers, Property
                        Registration Trends, and Top Contributing Properties.
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
                        <form method="GET" action="{{ route('dashboard.propertyAnalytic') }}" class="mb-3">
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

    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Total Registered Rate Payers Chart</div>
                </div>
                <div class="card-body">
                    <div id="column-stacked-full"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Active vs. Inactive Rate Payers</div>
                </div>
                <div class="card-body">
                    <div id="column-markers"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <div class="card-title">Properties Registration Trend</div>
                </div>
                <div class="card-body">
                    <div id="column-rotated-labels"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <div class="card-title">Top Contributing Properties</div>
                </div>
                <div class="card-body">
                    <div id="column-negative"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        //Total Registered Rate payers graph
        var maleMonthlyData = @json($total['maleMonthlyCount']);
        var femaleMonthlyData = @json($total['femaleMonthlyCount']);

        var options = {
            series: [{
                name: 'MALE',
                data: maleMonthlyData
            }, {
                name: 'FEMALE',
                data: femaleMonthlyData
            }],
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                stackType: '100%'
            },
            grid: {
                borderColor: '#f2f5f7',
            },
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
            colors: ["#8b7eff", "#35bdaa", "#ffb748"],
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
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-yaxis-label',
                    },
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'right',
                offsetX: 0,
                offsetY: 50
            },
        };
        var chart = new ApexCharts(document.querySelector("#column-stacked-full"), options);
        chart.render();
    </script>

    <script>
        var activeCitizens = @json($total['monthlyActiveCounts']);
        var inactiveCitizens = @json($total['monthlyInactiveCounts']);

        var options = {
            series: [{
                name: 'Active',
                data: [{
                        x: 'Jan',
                        y: activeCitizens[0],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[0],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Feb',
                        y: activeCitizens[1],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[1],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Mar',
                        y: activeCitizens[2],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[2],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Apr',
                        y: activeCitizens[3],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[3],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'May',
                        y: activeCitizens[4],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[4],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Jun',
                        y: activeCitizens[5],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[5],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Jul',
                        y: activeCitizens[6],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[6],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Aug',
                        y: activeCitizens[7],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[7],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Sep',
                        y: activeCitizens[8],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[8],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Oct',
                        y: activeCitizens[9],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[9],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Nov',
                        y: activeCitizens[10],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[10],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    },
                    {
                        x: 'Dec',
                        y: activeCitizens[11],
                        goals: [{
                            name: 'InActive',
                            value: inactiveCitizens[11],
                            strokeHeight: 5,
                            strokeColor: '#775DD0'
                        }]
                    }
                ]
            }],
            chart: {
                height: 320,
                type: 'bar'
            },
            plotOptions: {
                bar: {
                    columnWidth: '60%'
                }
            },
            colors: ['#35bdaa'],
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            legend: {
                show: true,
                showForSingleSeries: true,
                customLegendItems: ['Active', 'InActive'],
                markers: {
                    fillColors: ['#35bdaa', '#775DD0']
                }
            },
            xaxis: {
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
                labels: {
                    show: true,
                    style: {
                        colors: "#8c9097",
                        fontSize: '11px',
                        fontWeight: 600,
                        cssClass: 'apexcharts-xaxis-label',
                    },
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-markers"), options);
        chart.render();
    </script>

    <script>
        //Property and business registration chart
        var propertyData = @json($total['monthlyPropertyCounts']);
        var businessData = @json($total['monthlyBusinessCounts']);

        var options = {
            series: [{
                name: 'Property',
                data: propertyData
            }, {
                name: 'Business',
                data: businessData
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
                    text: 'Monthly (counts)',
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
                        return val
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-rotated-labels"), options);
        chart.render();
    </script>

    <script>
        var chartData12 = @json($total['chartData12']);

        var seriesData = [
            ...chartData12.properties.map(property => ({
                name: property.name,
                data: property.data,
                stack: 'Properties'
            })),
            ...chartData12.businesses.map(business => ({
                name: business.name,
                data: business.data,
                stack: 'Businesses'
            }))
        ];

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 400,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                },
                group: 'grouped-stacks'
            },
            grid: {
                borderColor: '#f2f5f7',
            },
            colors: [
                "#8b7eff", "#35bdaa", "#ffb748", "#e6533c", "#1e90ff", "#34a853", "#4285f4", "#fbbc05", "#ea4335",
                "#a142f4",
                "#ff9e67", "#00aaff", "#66bb6a", "#ff4081", "#ffa726", "#42a5f5", "#9c27b0", "#ab47bc", "#8d6e63",
                "#ff7043"
            ],
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
                },
            },
            legend: {
                position: 'top',
                offsetY: 10
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
            },
            tooltip: {
                shared: true,
                intersect: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#column-negative"), options);
        chart.render();
    </script>
@endsection
