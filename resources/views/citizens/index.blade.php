@extends('layout.base')

@section('page-styles')
@endsection


@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="card-body">
            <div class="col-md-12 active-p">
                <div class="card">

                    <!-- HEADER SECTION -->
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-user-settings-line me-2"></i> Rate Payer Management
                                </h4>
                                <p class="mb-0 text-muted fs-14">
                                    You are viewing all Rate Payers from your central database repository.
                                </p>
                            </div>

                            @can('customers.create')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('citizens.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-plus me-1"></i> Add new Rate Payer
                                    </a>
                                    <a href="{{ route('citizens.import') }}" class="btn btn-sm btn-success">
                                        <i class="fa fa-plus me-1"></i> Bulk Import of Rate Payers
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @php
                use App\Models\CustomerType;

                // Fetch customer types from the database
                $customerTypes = CustomerType::all();
                $countries = [
                    'Ghana',
                    'Nigeria',
                    'South Africa',
                    'Kenya',
                    'United States',
                    'United Kingdom',
                    'Canada',
                    'India',
                ];
            @endphp

            <div class="col-xl-12">
                <div class="card custom-card rounded-md overflow-hidden p-2">
                    <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                        <div class="d-flex gap-2 align-items-center ps-2">
                            <div class="align-self-start">
                                <h4 class="fw-semibold mb-0 lh-1">Apply Filters</h4>
                            </div>
                            <div class="ms-auto text-end align-self-end">
                                <div class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary mb-2">
                                    <!-- Optional SVG Icon here -->
                                </div>
                            </div>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('citizens.index') }}" class="mb-4">
                            <div class="row gy-4">
                                <!-- Country Filter -->
                                <div class="col-xl-3">
                                    <label for="country_of_citizenship" class="form-label d-block">Country</label>
                                    <select name="country_of_citizenship" id="country_of_citizenship" class="form-select">
                                        <option value="">All</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Kenya">Kenya</option>
                                    </select>
                                </div>

                                <!-- Customer Type Filter -->
                                <div class="col-xl-3">
                                    <label for="customer_type" class="form-label d-block">Customer Type</label>
                                    <select name="customer_type" id="customer_type" class="form-select">
                                        <option value="">All</option>
                                        @foreach (App\Models\CustomerType::all() as $customerType)
                                            <option value="{{ $customerType->name }}"
                                                {{ request('customer_type') == $customerType->name ? 'selected' : '' }}>
                                                {{ $customerType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="col-xl-3">
                                    <label for="status" class="form-label d-block">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>

                                <!-- Gender Filter -->
                                <div class="col-xl-3">
                                    <label for="gender" class="form-label d-block">Gender</label>
                                    <select name="gender" id="gender" class="form-select">
                                        <option value="">All</option>
                                        <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row gy-4">
                                <!-- From Date Filter -->
                                <div class="col-xl-3">
                                    <label for="from_date" class="form-label d-block">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>

                                <!-- To Date Filter -->
                                <div class="col-xl-3">
                                    <label for="to_date" class="form-label d-block">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        value="{{ request('to_date') }}">
                                </div>


                                <!-- Filter Button -->
                                <div class="col-xl-3">
                                    <label for="from_date" class="form-label d-block">.</label>
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>

                                <!-- Reset Filters Button -->
                                <div class="col-xl-3">
                                    <label for="from_date" class="form-label d-block">.</label>
                                    <a href="{{ route('citizens.index') }}" class="btn btn-secondary w-100">Reset
                                        Filters</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mb-4">
            <div class="col-xl-4">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <div id="bar-gender"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <div id="donut-gradient"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <div id="line-heatmap"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Row for Pie Chart Placeholders -->

            <div class="col-xl-12 active-p">
                @if (session()->has('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span><i class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body px-0">
                        <div class="table-responsive active-projects user-tbl dt-filter">
                            <table id="file-export" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Account #</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Phone </th>
                                        <th>Customer Type</th>
                                        <th>Status</th>
                                        {{-- <th>NIA#</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($citizens as $index => $citizen)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $citizen->account_number }}</td>
                                            <td>
                                                <a href="#">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img class="rounded-circle img-fluid"
                                                                src="{{ asset('assets/images/user.png') }}"
                                                                alt="" width="30">
                                                        </div>
                                                        {{ $citizen->first_name }}
                                                    </div>
                                                </a>
                                            </td>
                                            <td>{{ $citizen->last_name }}</td>
                                            <td>{{ $citizen->telephone_number }}</td>
                                            {{-- <td>{{ $citizen->country_of_citizenship }}</td> --}}
                                            <td>{{ $citizen->customerType->name ?? '' }}</td>
                                            <td>
                                                @if ($citizen->status === 'Active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">In-Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <div class="btn-link" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path
                                                                d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path
                                                                d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <div class="py-2">
                                                            <a class="dropdown-item"
                                                                href="{{ route('citizens.show', $citizen->id) }}">View
                                                                Rate Payer Record</a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('citizens.edit', $citizen->id) }}">Edit
                                                                Rate Payer Record</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>

    <script>
        // Pass the data from the controller to JavaScript
        var totals = @json($totals);

        // Gender Bar Chart
        var genderOptions = {
            series: [{
                name: 'Percentage',
                data: [totals.male_percentage, totals.female_percentage],
            }],
            chart: {
                type: 'bar',
                height: 290,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                },
            },
            colors: ['#8b7eff', '#35bdaa'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(2) + '%';
                },
            },
            xaxis: {
                categories: ['Male', 'Female'],
            },
            title: {
                text: 'Gender Distribution',
                align: 'center'
            },
        };

        var genderChart = new ApexCharts(document.querySelector("#bar-gender"), genderOptions);
        genderChart.render();

        // Customer Type Bar Chart
        // Prepare data dynamically from Laravel variables
        // Prepare data dynamically from Laravel variables
        var customerTypeData = Object.values(totals.customer_type_percentages); // Percentages
        var customerTypeLabels = Object.keys(totals.customer_type_counts); // Labels

        // Gradient Donut Chart Configuration
        var options = {
            series: customerTypeData, // Percentages
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
                enabled: true, // Enable data labels to show names
                formatter: function(value, opts) {
                    return `${opts.w.globals.labels[opts.seriesIndex]}: ${value.toFixed(2)}%`; // Show label and percentage
                },
            },
            fill: {
                type: "gradient",
            },
            colors: ["#8b7eff", "#35bdaa", "#ffb748", "#49b6f5", "#e6533c"], // Gradient colors
            title: {
                text: "Rate Payer Type ",
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
                    // Show the name and percentage for each item
                    return `${seriesName}: ${opts.w.globals.series[opts.seriesIndex].toFixed(2)}%`;
                },
            },
            labels: customerTypeLabels, // Labels
        };

        // Initialize the chart
        var chart = new ApexCharts(document.querySelector("#donut-gradient"), options);
        chart.render();
    </script>

    <script>
        // Prepare data dynamically
        var heatMapData = @json($heatMapData);

        console.log(heatMapData);

        // Process data into a chart-friendly format
        var seriesData = [];
        var categories = [...new Set(heatMapData.map(item => item.customer_type))]; // Unique customer types
        var genders = [...new Set(heatMapData.map(item => item.gender))]; // Unique genders
        var statuses = [...new Set(heatMapData.map(item => item.status))]; // Unique statuses

        // Organize data for the heat map
        genders.forEach(gender => {
            statuses.forEach(status => {
                let dataPoints = categories.map(category => {
                    let entry = heatMapData.find(item => item.customer_type === category && item
                        .gender === gender && item.status === status);
                    return entry ? entry.count : 0; // Default to 0 if no entry exists
                });

                seriesData.push({
                    name: `${gender} - ${status}`,
                    data: dataPoints,
                });
            });
        });

        console.log(seriesData);
        // Chart options
        var options = {
            chart: {
                type: 'line',
                height: 350,
            },
            series: seriesData, // Dynamic data
            xaxis: {
                categories: categories, // Customer types
            },
            yaxis: {
                title: {
                    text: 'Count',
                },
            },
            colors: ['#FF4560', '#00E396', '#008FFB', '#FEB019'], // Custom colors
            title: {
                text: ' ',
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: '#8c9097',
                },
            },
            markers: {
                size: 5,
            },
            dataLabels: {
                enabled: true,
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val + ' Rate Payer';
                    },
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#line-heatmap"), options);
        chart.render();
    </script>
@endsection
