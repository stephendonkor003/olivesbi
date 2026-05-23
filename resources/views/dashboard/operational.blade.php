@extends('layout.base')

@section('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.css" rel="stylesheet">

    <style>
        .equal-height-card {
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 1rem;
            transition: box-shadow 0.3s ease;
        }

        .equal-height-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('page-content')
    <div class="container-fluid">
        @php
            $level = \Auth::user()->access_level;
        @endphp

        @can('dashboards.operational')
            @switch($level)
                @case('customer')
                    @include('dashboard.includes.customer')
                    @break

                @case('GOG_Administrator')
                    @include('dashboard.includes.gra')
                    @break

                @default
                    @include('dashboard.includes.main')
                    @include('dashboard.includes.analytics')
            @endswitch
        @else
            @include('dashboard.includes.no-permission')
        @endcan


    </div>
@endsection


@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->

    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>

    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>

    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <!-- Auto Complete JS -->
    <script src="{{ asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Date & Time Picker JS -->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Sales Dashboard -->
    <script src="{{ asset('assets/js/sales-dashboard.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.js') }}"></script>
    <!-- Crypto Dashboard -->
    <script src="{{ asset('assets/js/crypto-dashboard.js') }}"></script>
    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <!-- Auto Complete JS -->
    <script src="{{ asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>


    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Swiper JS -->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Echarts JS -->
    <script src="{{ asset('assets/libs/echarts/echarts.min.js') }}"></script>

    <!-- Crypto Dashboard -->
    <script src="{{ asset('assets/js/crypto-dashboard.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>


    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.js') }}"></script>
    <!-- Swiper JS -->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Internal Swiper JS -->
    <script src="{{ asset('assets/js/swiper.js') }}"></script>

    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>

    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Fullcalendar JS -->
    <script src="{{ asset('assets/libs/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar.js?t=1234') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.js"></script>

    <script>
        const messages = [
            "Did You Know Paying your property rates helps drive National Development!",
            "Your contributions build better communities and infrastructure.",
            "Together, we create opportunities through your property rate payments.",
            "Support local growth by fulfilling your property rate obligations.",
            "Be a part of progress—your property rates make a difference!"
        ];

        let currentMessageIndex = 0;
        let currentCharIndex = 0;
        const messageElement = document.getElementById('message');

        function typeMessage() {
            if (currentCharIndex < messages[currentMessageIndex].length) {
                messageElement.textContent += messages[currentMessageIndex].charAt(currentCharIndex);
                currentCharIndex++;
                setTimeout(typeMessage, 100); // Adjust typing speed here
            } else {
                setTimeout(eraseMessage, 2000); // Pause before erasing
            }
        }

        function eraseMessage() {
            if (currentCharIndex > 0) {
                messageElement.textContent = messages[currentMessageIndex].substring(0, currentCharIndex - 1);
                currentCharIndex--;
                setTimeout(eraseMessage, 50); // Adjust erasing speed here
            } else {
                currentMessageIndex = (currentMessageIndex + 1) % messages.length;
                setTimeout(typeMessage, 1000); // Pause before typing the next message
            }
        }

        // Start typing the first message
        typeMessage();
    </script>

    <script>
        const ctx = document.getElementById('billPaymentChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        var chartData2 = @json($chartData2);

        var weeklyPerformance = function() {
            var optionsArea = {
                series: [{
                    name: "Payments",
                    data: chartData2
                }],
                chart: {
                    height: 250,
                    type: 'area',
                    group: 'social',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false,
                    tooltipHoverFormatter: function(val, opts) {
                        return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
                    },
                    markers: {
                        fillColors: ['var(--primary)'],
                        width: 3,
                        height: 16,
                        strokeWidth: 0,
                        radius: 16
                    }
                },
                markers: {
                    size: [8, ],
                    strokeWidth: [4, ],
                    strokeColors: ['#fff'],
                    border: 4,
                    radius: 4,
                    colors: ['var(--primary)'],
                    hover: {
                        size: 10,
                    }
                },
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    labels: {
                        style: {
                            colors: '#3E4954',
                            fontSize: '14px',
                            fontFamily: 'Poppins',
                            fontWeight: 100,
                        },
                    },
                    axisBorder: {
                        show: false,
                    }
                },
                yaxis: {
                    labels: {
                        show: true,
                        align: 'right',
                        minWidth: 15,
                        offsetX: -16,
                        style: {
                            colors: '#666666',
                            fontSize: '14px',
                            fontFamily: 'Poppins',
                            fontWeight: 100,
                        },
                    },
                },
                fill: {
                    colors: ['#fff', 'var(--primary)'],
                    type: 'gradient',
                    opacity: 1,
                    gradient: {
                        shade: 'light',
                        shadeIntensity: 1,
                        colorStops: [
                            [{
                                    offset: 0,
                                    color: 'var(--primary)',
                                    opacity: 0.4
                                },
                                {
                                    offset: 0.6,
                                    color: 'var(--primary)',
                                    opacity: 0.25
                                },
                                {
                                    offset: 100,
                                    color: 'var(--primary)',
                                    opacity: 0
                                }
                            ],
                            [{
                                    offset: 0,
                                    color: 'var(--primary)',
                                    opacity: .4
                                },
                                {
                                    offset: 50,
                                    color: 'var(--primary)',
                                    opacity: 0.25
                                },
                                {
                                    offset: 100,
                                    color: '#fff',
                                    opacity: 0
                                }
                            ]
                        ]

                    },
                },
                colors: ['var(--primary)', 'var(--primary)'],
                stroke: {
                    curve: "straight",
                    width: 3,
                },
                grid: {
                    borderColor: '#e1dede',
                    strokeDashArray: 8,
                    xaxis: {
                        lines: {
                            show: true,
                            opacity: 0.5,
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            opacity: 0.5,
                        }
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    },
                },
                responsive: [{
                    breakpoint: 1602,
                    options: {
                        markers: {
                            size: [6, 6, 4],
                            hover: {
                                size: 7,
                            }
                        },
                        chart: {
                            height: 230,
                        },
                    },

                }]
            };

            var chartArea = new ApexCharts(document.querySelector("#activity1"), optionsArea);
            chartArea.render();
        };

        weeklyPerformance();
    </script>

    <script>
        var chartData3 = @json($chartData3);

        var monthlyPerformance = function() {
            var optionsArea = {
                series: [{
                    name: "Payments",
                    data: chartData3
                }],
                chart: {
                    height: 250,
                    type: 'area',
                    group: 'social',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false,
                    tooltipHoverFormatter: function(val, opts) {
                        return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
                    },
                    markers: {
                        fillColors: ['var(--primary)'],
                        width: 3,
                        height: 16,
                        strokeWidth: 0,
                        radius: 16
                    }
                },
                markers: {
                    size: [8],
                    strokeWidth: [4],
                    strokeColors: ['#fff'],
                    border: 4,
                    radius: 4,
                    colors: ['var(--primary)'],
                    hover: {
                        size: 10,
                    }
                },
                xaxis: {
                    categories: [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ],
                    labels: {
                        style: {
                            colors: '#3E4954',
                            fontSize: '14px',
                            fontFamily: 'Poppins',
                            fontWeight: 100,
                        },
                    },
                    axisBorder: {
                        show: false,
                    }
                },
                yaxis: {
                    labels: {
                        show: true,
                        align: 'right',
                        minWidth: 15,
                        offsetX: -16,
                        style: {
                            colors: '#666666',
                            fontSize: '14px',
                            fontFamily: 'Poppins',
                            fontWeight: 100,
                        },
                    },
                },
                fill: {
                    colors: ['#fff', 'var(--primary)'],
                    type: 'gradient',
                    opacity: 1,
                    gradient: {
                        shade: 'light',
                        shadeIntensity: 1,
                        colorStops: [
                            [{
                                    offset: 0,
                                    color: 'var(--primary)',
                                    opacity: 0.4
                                },
                                {
                                    offset: 0.6,
                                    color: 'var(--primary)',
                                    opacity: 0.25
                                },
                                {
                                    offset: 100,
                                    color: 'var(--primary)',
                                    opacity: 0
                                }
                            ],
                            [{
                                    offset: 0,
                                    color: 'var(--primary)',
                                    opacity: 0.4
                                },
                                {
                                    offset: 50,
                                    color: 'var(--primary)',
                                    opacity: 0.25
                                },
                                {
                                    offset: 100,
                                    color: '#fff',
                                    opacity: 0
                                }
                            ]
                        ]
                    },
                },
                colors: ['var(--primary)', 'var(--primary)'],
                stroke: {
                    curve: "straight",
                    width: 3,
                },
                grid: {
                    borderColor: '#e1dede',
                    strokeDashArray: 8,
                    xaxis: {
                        lines: {
                            show: true,
                            opacity: 0.5,
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            opacity: 0.5,
                        }
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    },
                },
                responsive: [{
                    breakpoint: 1602,
                    options: {
                        markers: {
                            size: [6, 6, 4],
                            hover: {
                                size: 7,
                            }
                        },
                        chart: {
                            height: 230,
                        },
                    },
                }]
            };

            var chartArea = new ApexCharts(document.querySelector("#activity2"), optionsArea);
            chartArea.render();
        };

        monthlyPerformance();
    </script>

    <script>
        $(document).ready(function() {
            @foreach ($total['regions'] ?? [] as $region)
                $('#file-export-{{ $region->id }}').DataTable({
                    'dom': 'ZBfrltip',
                    buttons: [

                        {
                            extend: 'excel',
                            text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0'
                        }
                    ],

                    searching: true,
                    pageLength: 12,
                    select: false,
                    lengthChange: false,
                    language: {
                        paginate: {
                            next: '<i class="fa-solid fa-angle-right"></i>',
                            previous: '<i class="fa-solid fa-angle-left"></i>'
                        },
                        'search': ' <i class="fa-solid fa-magnifying-glass"></i>',
                        searchPlaceholder: "Search..."

                    },
                });
            @endforeach
        });
    </script>

    <script>
        $(document).ready(function() {
            console.log("Document Ready. Fetching data...");
            fetchRegionalGraphData();
        });

        function fetchRegionalGraphData() {
            $.ajax({
                url: "{{ route('fetch.regional.graph.data') }}",
                method: "GET",
                dataType: "json",
                beforeSend: function() {
                    console.log("Fetching data...");
                    $("#chart-loader").show();
                    $("#column-basic2").hide();
                },
                success: function(response) {
                    console.log("Fetched Data:", response);

                    if (!Array.isArray(response) || response.length === 0) {
                        console.error("Invalid or empty data");
                        return;
                    }

                    var regionNames = response.map(data => data.region_name || "Unknown");
                    var paymentGraphData = response.map(data => Number(data.total_payments_region) || 0);
                    var billGraphData = response.map(data => Number(data.total_bills_region) || 0);
                    var arrearsGraphData = response.map(data => Number(data.total_arrears_region) || 0);

                    console.log("Regions:", regionNames);
                    console.log("Payments:", paymentGraphData);
                    console.log("Bills:", billGraphData);
                    console.log("Arrears:", arrearsGraphData);

                    if (regionNames.length === 0) {
                        console.error("No regions found.");
                        return;
                    }

                    setTimeout(function() {
                        console.log("Rendering chart...");
                        renderRegionalChart(regionNames, paymentGraphData, billGraphData,
                            arrearsGraphData);
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    $("#column-basic2").html('<div class="alert alert-danger">Failed to load data.</div>');
                },
                complete: function() {
                    $("#chart-loader").hide();
                    $("#f_load").hide();
                    $("#column-basic2").show();
                }
            });
        }

        var chart = null;

        function renderRegionalChart(regionNames, paymentData, billData, arrearsData) {
            console.log("Rendering chart...");
            console.log("Container:", document.querySelector("#column-basic2"));

            if (!document.querySelector("#column-basic2")) {
                console.error("Chart container not found.");
                return;
            }

            var options = {
                series: [{
                        name: 'Payment',
                        data: paymentData
                    },
                    {
                        name: 'Bill',
                        data: billData
                    },
                    {
                        name: 'Arrears',
                        data: arrearsData
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 320
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%',
                        endingShape: 'rounded'
                    }
                },
                grid: {
                    borderColor: '#f2f5f7'
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
                    categories: regionNames
                },
                yaxis: {
                    title: {
                        text: 'GHS (thousands)'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "GHS " + val + " thousands";
                        }
                    }
                }
            };

            console.log("Chart options:", options);
            chart = new ApexCharts(document.querySelector("#column-basic2"), options);
            chart.render();
        }
    </script>

    <script>
        const dashChartData = @json($total['dashChartData'] ?? []);

        const bills = dashChartData.map(item => item.bills);
        const payments = dashChartData.map(item => item.payments);
        const arrears = dashChartData.map(item => item.arrears);

        var options = {
            series: [{
                    name: 'Bill',
                    data: bills,
                    type: 'bar',
                },
                {
                    name: 'Payment',
                    data: payments,
                    type: 'bar',
                },
                {
                    name: 'Arrears',
                    data: arrears,
                    type: 'area',
                }
            ],
            chart: {
                height: 352,
                type: 'line',
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: "40%",
                }
            },
            grid: {
                borderColor: "#f1f1f1",
                strokeDashArray: 2,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                opacity: 1,
            },
            legend: {
                position: 'top',
                fontSize: '14px',
                fontWeight: 500,
                fontFamily: 'Poppins, sans-serif',
            },
            colors: [
                "rgb(52, 152, 219)",
                "rgb(255, 183, 72)",
                "rgb(53, 189, 170)"
            ],
            stroke: {
                width: [0, 2.5, 2.5],
                curve: 'smooth',
            },
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            tooltip: {
                shared: true,
            }
        };

        var chart = new ApexCharts(document.querySelector("#sales-statistics1"), options);
        chart.render();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                // events: [{
                //         title: 'Meeting with Client',
                //         start: '2025-01-15',
                //         end: '2025-01-16'
                //     },
                //     {
                //         title: 'Project Deadline',
                //         start: '2025-01-18'
                //     },
                //     {
                //         title: 'Team Outing',
                //         start: '2025-01-22'
                //     }
                // ],
                nowIndicator: true,
                selectable: false,
                editable: false,
                initialDate: new Date().toISOString().split('T')[0],
            });

            calendar.render();
        });
    </script>

    <script>
        const finalCut = @json($total['yearlyPayments'] ?? []);

        console.log(finalCut);

        var options1 = {
            chart: {
                height: 225,
                type: 'radialBar',
                responsive: 'true',
                offsetX: 0,
                offsetY: -10,
                zoom: {
                    enabled: false
                }
            },
            grid: {
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                },
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    track: {
                        strokeWidth: "80%",
                    },
                    hollow: {
                        size: "55%"
                    },
                    dataLabels: {
                        name: {
                            fontSize: '15px',
                            color: undefined,
                            offsetY: 20,
                            fontWeight: [400]
                        },
                        value: {
                            offsetY: -20,
                            fontSize: '22px',
                            color: undefined,
                            fontWeight: [600],
                            formatter: function(val) {
                                return val;
                            }
                        }
                    }
                }
            },
            colors: ["var(--primary-color)"],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    type: "horizontal",
                    gradientToColors: ["rgb(53, 189, 170)"],
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            stroke: {
                dashArray: 4
            },
            labels: ['Revenue'],
            series: [finalCut],
        };
        var options1 = new ApexCharts(document.querySelector("#revenue-statistics"), options1);
        options1.render();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rows = document.querySelectorAll(".row.mb-4");

            rows.forEach((row) => {
                let maxHeight = 0;
                const cards = row.querySelectorAll(".card");

                // Find the tallest card
                cards.forEach((card) => {
                    maxHeight = Math.max(maxHeight, card.offsetHeight);
                });

                // Apply the tallest height to all cards in the row
                cards.forEach((card) => {
                    card.style.height = maxHeight + "px";
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            fetchRegionalDonutData();
        });

        function fetchRegionalDonutData() {
            $("#donut-loader").show();

            $.ajax({
                url: "{{ route('fetch.regional.donut.data') }}",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    var regionNames = [];
                    var totalBills = [];
                    var totalPayments = [];
                    var totalArrears = [];
                    var totalProperties = [];

                    response.forEach(function(data) {
                        regionNames.push(data.region_name);
                        totalBills.push(parseFloat(data.total_bills_region));
                        totalPayments.push(parseFloat(data.total_payments_region));
                        totalArrears.push(parseFloat(data.total_arrears_region));
                        totalProperties.push(parseFloat(data.total_properties_region));
                    });

                    renderRegionalDonutChart(totalBills, totalPayments, totalArrears, totalProperties);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $("#donut-regional").html('<div class="alert alert-danger">Failed to load data.</div>');
                },
                complete: function() {
                    $("#donut-loader").hide();
                    $("#s_load").hide();
                }
            });
        }

        function renderRegionalDonutChart(totalBills, totalPayments, totalArrears, totalProperties) {
            var totalBillsSum = totalBills.reduce((acc, val) => acc + val, 0);
            var totalPaymentsSum = totalPayments.reduce((acc, val) => acc + val, 0);
            var totalArrearsSum = totalArrears.reduce((acc, val) => acc + val, 0);
            var totalPropertiesSum = totalProperties.reduce((acc, val) => acc + val, 0);

            var options = {
                series: [
                    totalBillsSum,
                    totalPaymentsSum,
                    totalArrearsSum,
                    totalPropertiesSum
                ],
                chart: {
                    height: 300,
                    type: "donut",
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270,
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(value, opts) {
                        return `${opts.w.globals.labels[opts.seriesIndex]}: ${value.toFixed(2)} %`;
                    },
                },
                fill: {
                    type: "gradient",
                },
                colors: ["#8b7eff", "#35bdaa", "#ffb748", "#49b6f5"],
                title: {
                    text: "Regional Revenue Distribution",
                    align: "left",
                    style: {
                        fontSize: "13px",
                        fontWeight: "bold",
                        color: "#8c9097",
                    },
                },
                legend: {
                    position: "bottom",
                    formatter: function(seriesName, opts) {
                        return `${seriesName}: ${opts.w.globals.series[opts.seriesIndex].toFixed(2)} %`;
                    },
                },
                labels: ["Total Bills", "Total Payments", "Total Arrears", "Total Properties"],
            };

            var chart = new ApexCharts(document.querySelector("#donut-regional"), options);
            chart.render();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn-group .btn');

            const urlParams = new URLSearchParams(window.location.search);
            const displayValue = urlParams.get('display');

            if (displayValue) {
                buttons.forEach(button => {
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-primary-light');

                    if (button.href.includes(`display=${displayValue}`)) {
                        button.classList.add('btn-primary');
                        button.classList.remove('btn-primary-light');
                    }
                });
            } else {
                buttons[0].classList.add('btn-primary');
                buttons[0].classList.remove('btn-primary-light');
            }

            buttons.forEach(button => {
                button.addEventListener('click', function(event) {
                    buttons.forEach(btn => {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-primary-light');
                    });

                    this.classList.add('btn-primary');
                    this.classList.remove('btn-primary-light');
                });
            });
        });
    </script>

    @if (\Auth::user()->access_level == 'customer')
        <script>
            // Customer doughnut
            var totalBills = @json($customerData['totalBills']);
            var totalPayments = @json($customerData['totalPayments']);
            var totalArrears = @json($customerData['totalArrears2']);

            totalBills = [parseFloat(totalBills) || 0];
            totalPayments = [parseFloat(totalPayments) || 0];
            totalArrears = [parseFloat(totalArrears) || 0];

            // Calculate sums
            var totalBillsSum = totalBills.reduce((acc, val) => acc + val, 0);
            var totalPaymentsSum = totalPayments.reduce((acc, val) => acc + val, 0);
            var totalArrearsSum = totalArrears.reduce((acc, val) => acc + val, 0);

            // Options for ApexCharts Donut
            var options = {
                series: [
                    totalBillsSum,
                    totalPaymentsSum,
                    totalArrearsSum
                ],
                chart: {
                    height: 300,
                    type: "donut",
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270,
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(value, opts) {
                        return `${opts.w.globals.labels[opts.seriesIndex]}: ${value.toFixed(2)} %`;
                    },
                },
                fill: {
                    type: "gradient",
                },
                colors: ["#8b7eff", "#35bdaa", "#ffb748"],
                title: {
                    text: "Rate payer Financial Overview",
                    align: "left",
                    style: {
                        fontSize: "13px",
                        fontWeight: "bold",
                        color: "#8c9097",
                    },
                },
                legend: {
                    position: "bottom",
                    formatter: function(seriesName, opts) {
                        return `${seriesName}: ${opts.w.globals.series[opts.seriesIndex].toFixed(2)} %`;
                    },
                },
                labels: ["Total Bills", "Total Payments", "Total Arrears"],
            };

            // Render the Donut Chart
            var chart = new ApexCharts(document.querySelector("#donut-gradient"), options);
            chart.render();
        </script>
    @endif

    <script>
        $(document).ready(function() {
            fetchRegionalData();
        });

        function fetchRegionalData() {
            $.ajax({
                url: "{{ route('fetch.regional.data') }}",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    renderRegionalData(response.data);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $("#regional-data-container").html(
                        '<div class="alert alert-danger">Error: ' + error + '</div>'
                    );
                }
            });
        }

        // ✅ GHS formatter
        function formatGHS(value) {
            value = parseFloat(value || 0);

            if (value >= 1_000_000) {
                return `GHS ${ (value / 1_000_000).toFixed(2) }M`;
            } else if (value >= 1_000) {
                return `GHS ${ (value / 1_000).toFixed(2) }K`;
            } else {
                return `GHS ${ value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }`;
            }
        }

        function renderRegionalData(data) {
            // Helper to format currency with GHS, K, M
            function formatGHS(value) {
                value = parseFloat(value || 0);
                if (value >= 1_000_000) {
                    return `GHS ${ (value / 1_000_000).toFixed(2) }M`;
                } else if (value >= 1_000) {
                    return `GHS ${ (value / 1_000).toFixed(2) }K`;
                } else {
                    return `GHS ${ value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }`;
                }
            }

            // Helper to format plain numbers with commas
            function formatNumber(value) {
                value = parseFloat(value || 0);
                return value.toLocaleString(undefined, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            let html = '';
            if (data.length === 0) {
                html = '<div class="alert alert-warning">No data available.</div>';
            } else {
                data.forEach((region, index) => {
                    if (index % 4 === 0) {
                        html += '<div class="row mb-4">';
                    }

                    html += `
                <div class="col-xl-3">
                    <div class="card custom-card shadow-lg border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h5 class="fw-bold text-primary d-flex align-items-center gap-2 mb-0" style="font-size: 14px; word-break: break-word;">
                                        <i class="ri-map-pin-line fs-10"></i>
                                        <span class="text-truncate d-inline-block" style="max-width: 180px;">${region.region_name}</span>
                                    </h5>
                                </div>

                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-muted">Properties:</span>
                                    <span class="fw-bold text-dark text-end" style="min-width: 80px; word-break: break-word;">
                                        ${formatNumber(region.total_properties_region)}
                                    </span>
                                </div>

                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-muted">Payments:</span>
                                    <span class="fw-bold text-success text-end" style="min-width: 80px; word-break: break-word;">
                                        ${formatGHS(region.total_payments_region)}
                                    </span>
                                </div>

                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-muted">Outstanding:</span>
                                    <span class="fw-bold text-danger text-end" style="min-width: 80px; word-break: break-word;">
                                        ${formatGHS(region.total_arrears_region)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    if ((index + 1) % 4 === 0) {
                        html += '</div>';
                    }
                });
            }

            $("#regional-data-container").html(html);
        }
    </script>
@endsection
