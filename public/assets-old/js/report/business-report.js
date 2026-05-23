$(document).ready(function () {

    $('.generate_report').on('click', function(event) {
        event.preventDefault();

        var from_date = $('input[name="from_date"]').val();
        var to_date = $('input[name="to_date"]').val();
        var assembly_code = $('select[name="assembly_code"]').val();
        var status = $('select[name="status"]').val();
        var report_type = $('select[name="report_type"]').val();


        if ($('select[name="report_type"]').val() == 1) {
            $('#summary').hide();
            $('#details').show();
            //$('#graph').hide();
            $('#example4').DataTable().destroy();
            load_data(from_date, to_date, assembly_code, report_type, status);
        } else {
            $('#summary').show();
            $('#details').hide();
            $('#graph').show();
            $('#example5').DataTable().destroy();
            $('#header').text(headerName);
            load_summary(from_date, to_date, level_type, status);
        }
    });

    $('select[name="report_type"]').change(function () {
        if ($(this).val() == 2) {
            $('#detail_field').slideUp(400);
            $('#summary_field').slideDown(400);
        } else {
            $('#detail_field').slideDown(400);
            $('#summary_field').slideUp(400);
        }
    });

    function load_data(from_date = '', to_date = '', assembly_code = '', report_type = '', status = '') {
        var url = $("input[name='business-report_url']").attr("url");

        try {
            $('#example4').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        assembly_code: assembly_code,
                        report_type: report_type,
                        status: status
                    },
                    error: function(xhr, status, errorThrown) {
                        console.error('Ajax error:', errorThrown);
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'business_name', name: 'business_name'},
                    {data: 'business_type', name: 'business_type'},
                    {data: 'business_class', name: 'business_class'},
                    {data: 'location', name: 'location'},
                    {data: 'email', name: 'email'},
                    {data: 'business_phone', name: 'business_phone'},
                    {data: 'business_owner', name: 'business_owner'},
                    {data: 'assembly', name: 'assembly'},
                    {data: 'division', name: 'division'},
                    {data: 'block', name: 'block'},
                    {data: 'zone', name: 'zone'},
                    {data: 'property_use', name: 'property_use'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'created_at', name: 'created_at'}
                ],
                language: {
                    paginate: {
                        next: '<i class="fa-solid fa-angle-right"></i>',
                        previous: '<i class="fa-solid fa-angle-left"></i>'
                    }
                },
                dom: 'lBfrtip',
                buttons: ['copyHtml5', 'excelHtml5', 'pdfHtml5', 'csvHtml5'],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    // Total over all pages
                    var quantity_total = api
                        .column(4)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update status DIV
                    $('#title').html('Total');
                    $('#quantity_total').html(format_money(quantity_total));
                }
            });
        } catch (err) {
            console.error('Error initializing DataTable:', err);
            alert('An error occurred while initializing the table. Please check the console for more information.');
        }
    }

    function load_summary(from_date = '', to_date = '', level_type = '', status = '') {
        var url = $("input[name='customer-report_url']").attr("url");

        try {
            $('#example5').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,

                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        level_type: level_type,
                        status: status
                    }
                },

               columns: [

                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},

                    {data: 'name', name: 'name'},

                    {data: 'total_customer', name: 'total_customer'},

                    {data: 'link', name: 'link'},
                ],
                language: {
                    paginate: {
                        next: '<i class="fa-solid fa-angle-right">',
                        previous: '<i class="fa-solid fa-angle-left">'
                    }
                },
                dom: 'lBfrtip',
			    buttons: [
				    'copyHtml5', 'excelHtml5', 'pdfHtml5', 'csvHtml5'
			    ],
                "footerCallback": function () {
                    var api = this.api();
                    
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                     // Total over all pages
                     var customer_total = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    // Update status DIV
                    $('#title').html('Total');
                    $('#customer_total').html(customer_total);
                }
            });
        } catch (err) {
            //alert(err);
        }
    }

    function loadPieChart(from_date = '', to_date = '', compost_type = '', company_id = '', site_id = '', status = '') {
        var url = $("input[name='compost-graph_url']").attr("url");
        var formData = new FormData();
        formData.append('from_date', from_date);
        formData.append('to_date', to_date);
        formData.append('compost_type', compost_type);
        formData.append('company_id', company_id);
        formData.append('site_id', site_id);
        formData.append('status', status);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Process the response data
                var labels = response.labels;
                var data = response.data;
                var color = [];

                var dynamicColors = function() {
                    var r = Math.floor(Math.random() * 255);
                    var g = Math.floor(Math.random() * 255);
                    var b = Math.floor(Math.random() * 255);
                    return "rgb(" + r + "," + g + "," + b + ")";
                };

                for (var i = 1; i <= data.length; i++) {
                    color.push(dynamicColors());
                }

                // Create the pie chart
                $("canvas#pie-chart").remove();
                $("div.pie-chart").append('<canvas id="pie-chart" width="400" height="400"></canvas>');
                var ctx = document.getElementById('pie-chart').getContext('2d');
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: color,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        title: {
                            display: true,
                            text: 'Compost'
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                //alert(error);
            }
        });
    }

    function loadBarChart(from_date = '', to_date = '', compost_type = '', company_id = '', site_id = '', status = '') {
        var url = $("input[name='compost-graph_url']").attr("url");
        var formData = new FormData();
        formData.append('from_date', from_date);
        formData.append('to_date', to_date);
        formData.append('compost_type', compost_type);
        formData.append('company_id', company_id);
        formData.append('site_id', site_id);
        formData.append('status', status);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Process the response data
                var labels = response.labels2;
                var data = response.data;
                var color = [];

                var dynamicColors = function() {
                    var r = Math.floor(Math.random() * 255);
                    var g = Math.floor(Math.random() * 255);
                    var b = Math.floor(Math.random() * 255);
                    return "rgb(" + r + "," + g + "," + b + ")";
                };

                for (var i = 1; i <= data.length; i++) {
                    color.push(dynamicColors());
                }
                
                // Create the bar chart
                $("canvas#bar-chart").remove();
                $("div.bar-chart").append('<canvas id="bar-chart" width="400" height="400"></canvas>');
                var ctx = document.getElementById('bar-chart').getContext('2d');
                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: color,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Compost'
                        },
                        scales: {
                            xAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Company'
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Quantity (Tonnes)'
                                }
                            }]
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                //alert(error);
            }
        });
    }

    function format_money(num) {
        var p = num.toFixed(2).split(".");
        return p[0].split("").reverse().reduce(function(acc, num, i, orig) {
            return num + (num != "-" && i && !(i % 3) ? "," : "") + acc;
        }, "") + "." + p[1];
    }

});