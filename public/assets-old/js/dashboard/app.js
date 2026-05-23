$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    activity;

    var activity = function() {
        $.ajax({
            url: '/get-weekly-payments', // Route to the controller method
            method: 'GET',
            success: function(data) {
                var optionsArea = {
                    series: [{
                        name: "Payments",
                        data: data
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
                            fillColors:['var(--primary)'],
                            width: 3,
                            height: 16,
                            strokeWidth: 0,
                            radius: 16
                        }
                    },
                    markers: {
                        size: [8,],
                        strokeWidth: [4,],
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
                        axisBorder:{
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
                                [
                                    {
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
                                [
                                    {
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
                        curve : "straight",
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

                var chartArea = new ApexCharts(document.querySelector("#payment_activity"), optionsArea);
                chartArea.render();
            }
        });
    };

});