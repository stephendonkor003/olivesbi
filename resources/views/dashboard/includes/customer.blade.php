@if (session()->has('status'))
    <div class="alert alert-success alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
            stroke-linecap="round" stroke-linejoin="round" class="me-2">
            <polyline points="9 11 12 14 22 4"></polyline>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
        <strong>{{ session('status') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i
                    class="fa-solid fa-xmark"></i></span>
        </button>
    </div>
@endif


<div class="row">
    <div class="col-xl-3">
        <a href="{{ route('dashboard.mybills') }}">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                    <div class="d-flex gap-2 align-items-center ps-2">
                        <div class="align-self-start">
                            <div class="fw-medium mb-2"> Current Bill</div>
                            <h4 class="fw-semibold mb-0 lh-1">GHS {{ $customerData['totalArrearsP'] }}</h4>
                        </div>
                        <div class="ms-auto text-end align-self-end">
                            <div class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary mb-2">
                                <!-- SVG Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    class="svg-icon-med text-fixed-white" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M136,108A52,52,0,1,1,84,56,52,52,0,0,1,136,108Z" opacity="0.2"></path>
                                    <path
                                        d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Last Payment -->
    <div class="col-xl-3">
        <a href="{{ route('dashboard.mypaymenthistory') }}">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-secondary bg-opacity-10 rounded-2 ps-4 medical-cards secondary">
                    <div class="d-flex gap-2 align-items-center ps-2">
                        <div class="align-self-start">
                            <div class="fw-medium mb-2">Last Payment</div>
                            <h4 class="fw-semibold mb-0 lh-1">GHS {{ $customerData['yearlyPaymentsP'] }}</h4>
                        </div>
                        <div class="ms-auto text-end align-self-end">
                            <div class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary mb-2">
                                <!-- SVG Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    class="svg-icon-med text-fixed-white" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160a32,32,0,1,1-32-32A32,32,0,0,1,240,160Z" opacity="0.2"></path>
                                    <path
                                        d="M220,160a12,12,0,1,1-12-12A12,12,0,0,1,220,160Zm-4.55,39.29A48.08,48.08,0,0,1,168,240H144a48.05,48.05,0,0,1-48-48V151.49A64,64,0,0,1,40,88V40a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56V88a48,48,0,0,0,48.64,48c26.11-.34,47.36-22.25,47.36-48.83V48H136a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8V87.17c0,32.84-24.53,60.29-56,64.31V192a32,32,0,0,0,32,32h24a32.06,32.06,0,0,0,31.22-25,40,40,0,1,1,16.23.27ZM232,160a24,24,0,1,0-24,24A24,24,0,0,0,232,160Z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Arrears -->
    <div class="col-xl-3">
        <a href="{{ route('dashboard.mypaymenthistory') }}">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-danger bg-opacity-10 rounded-2 ps-4 medical-cards danger">
                    <div class="d-flex gap-2 align-items-center ps-2">
                        <div class="align-self-start">
                            <div class="fw-medium mb-2"> Balance</div>
                            <h4 class="fw-semibold mb-0 lh-1">GHS {{ $customerData['totalArrearsP'] }}</h4>
                        </div>
                        <div class="ms-auto text-end align-self-end">
                            <div class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary mb-2">
                                <!-- SVG Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    class="svg-icon-med text-fixed-white" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160a32,32,0,1,1-32-32A32,32,0,0,1,240,160Z" opacity="0.2"></path>
                                    <path
                                        d="M220,160a12,12,0,1,1-12-12A12,12,0,0,1,220,160Zm-4.55,39.29A48.08,48.08,0,0,1,168,240H144a48.05,48.05,0,0,1-48-48V151.49A64,64,0,0,1,40,88V40a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56V88a48,48,0,0,0,48.64,48c26.11-.34,47.36-22.25,47.36-48.83V48H136a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8V87.17c0,32.84-24.53,60.29-56,64.31V192a32,32,0,0,0,32,32h24a32.06,32.06,0,0,0,31.22-25,40,40,0,1,1,16.23.27ZM232,160a24,24,0,1,0-24,24A24,24,0,0,0,232,160Z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: All Properties -->
    <div class="col-xl-3">
        <a href="{{ route('dashboard.myproperties') }}">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-success bg-opacity-10 rounded-2 ps-4 medical-cards success">
                    <div class="d-flex gap-2 align-items-center ps-2">
                        <div class="align-self-start">
                            <div class="fw-medium mb-2">All Properties</div>
                            <h4 class="fw-semibold mb-0 lh-1">{{ $customerData['propertyCount'] }}</h4>
                        </div>
                        <div class="ms-auto text-end align-self-end">
                            <div class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary mb-2">
                                <!-- SVG Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    class="svg-icon-med text-fixed-white" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M240,160a32,32,0,1,1-32-32A32,32,0,0,1,240,160Z" opacity="0.2"></path>
                                    <path
                                        d="M220,160a12,12,0,1,1-12-12A12,12,0,0,1,220,160Zm-4.55,39.29A48.08,48.08,0,0,1,168,240H144a48.05,48.05,0,0,1-48-48V151.49A64,64,0,0,1,40,88V40a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56V88a48,48,0,0,0,48.64,48c26.11-.34,47.36-22.25,47.36-48.83V48H136a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8V87.17c0,32.84-24.53,60.29-56,64.31V192a32,32,0,0,0,32,32h24a32.06,32.06,0,0,0,31.22-25,40,40,0,1,1,16.23.27ZM232,160a24,24,0,1,0-24,24A24,24,0,0,0,232,160Z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-8 col-xl-8 col-lg-8">
        <div class="card custom-card overflow-hidden nft-main-card h-100">
            <div class="card-body">
                <div class="row gap-3 gap-sm-0 mx-0 py-3 rounded-3">
                    <div class="col-xxl-8 col-xl-6 col-lg-8 col-12">
                        <div class="p-2">
                            @php
                                $hour = date('H');
                                if ($hour < 12) {
                                    $greeting = 'Good Morning';
                                } elseif ($hour < 18) {
                                    $greeting = 'Good Afternoon';
                                } else {
                                    $greeting = 'Good Evening';
                                }
                            @endphp
                            <h4 class="fw-semibold mb-3 op-9 text-fixed-white"> {{ $greeting }}
                                {{ Auth::user()->name }} ! &#128075;</h4>

                            <p class="mb-4 text-fixed-white op-7 fs-16">
                                Empowering you to manage your contributions with ease, transparency, and
                                confidence.
                                Together, we’re building stronger communities!
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('citizens.linkProperty') }}" class="btn btn-sm btn-success"><i
                                        class="fe fe-plus"></i>Link Property </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-6 col-lg-4 my-auto text-end">
                        <div class="featured-nft text-end">
                            <img src="{{ asset('assets/images/gh.png') }}" alt=""
                                class="img-fluid nft-cardimg rounded-3"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>
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


    <div class="col-xl-12">
        <div class="card">

            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-user-settings-line me-2"></i> Rate Payer - Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Viewing Rate Payer Recent Bills Record from your
                            central database repository.
                        </p>
                    </div>

                    {{-- <a href="{{ route('dashboard.myproperties') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a> --}}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body px-0">
                <div class="table-responsive active-projects user-tbl dt-filter">
                    <table id="file-export" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Bill No</th>
                                <th>Name</th>
                                <th>Arrears</th>
                                <th>Current Amount</th>
                                <th>Amount Due</th>
                                <th>Created By</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customerData['bills'] as $index => $bill)
                                @php
                                    $billType = '';

                                    if ($bill->property_id !== null) {
                                        $firstname = $bill->property->customer->first_name ?? '';
                                        $lastname = $bill->property->customer->last_name ?? '';
                                        $billType = 'Property Bill';
                                    } else {
                                        $firstname = $bill->business->customer->first_name ?? '';
                                        $lastname = $bill->business->customer->last_name ?? '';
                                        $billType = 'Business Bill';
                                    }
                                    $name = $firstname . ' ' . $lastname;
                                @endphp

                                <tr class="btn-reveal-trigger">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->bills_id }}</td>
                                    <td>{{ $name ?? '' }}</td>
                                    <td>{{ number_format($bill->arrears, 2) }}</td>
                                    <td>{{ number_format($bill->amount, 2) }}</td>
                                    <td>{{ number_format($bill->amount + $bill->arrears, 2) }}</td>
                                    <td>{{ $bill->createdBy->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->created_at }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        href="{{ route('citizens.viewBill', $bill) }}">View Bill</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('payments.customerCreate', $bill) }}">Make
                                                        Payment</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"></th>
                                <th>Total (GHS)</th>
                                <th>{{ $customerData['totalArrears'] }}</th>
                                <th>{{ $customerData['totalAmount'] }}</th>
                                <th>{{ $customerData['totalDue'] }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/663e4f9b9a809f19fb2fa32d/1hthme206';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>

<script>
    function updateTime() {
        const currentDate = new Date();
        const time = currentDate.toLocaleTimeString('en-US', {
            hour12: true
        });
        document.getElementById('currentTime').innerText = time;
    }

    // Initial call to display the current time
    updateTime();

    // Update the time every second
    setInterval(updateTime, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Data from the controller
        const data = @json($customerData);

        // Labels and values
        const labels = [
            "Total Properties",
            "Total Businesses",
            "Total Bills (Properties)",
            "Total Bills (Businesses)",
            "Total Arrears (Properties)",
            "Total Arrears (Businesses)",
            "Total Expected Payments (Properties)",
            "Total Expected Payments (Businesses)",
            "Yearly Payments (Properties)",
            "Yearly Payments (Businesses)",
            "Total Due"
        ];

        const values = [
            data.totalProperties || 0,
            data.totalBusinesses || 0,
            data.totalBillsProperties || 0,
            data.totalBillsBusinesses || 0,
            data.totalArrearsProperties || 0,
            data.totalArrearsBusinesses || 0,
            data.totalExpectedPaymentsProperties || 0,
            data.totalExpectedPaymentsBusinesses || 0,
            data.yearlyPaymentsProperties || 0,
            data.yearlyPaymentsBusinesses || 0,
            data.totalDue || 0
        ];

        const colors = [
            "#FF9999", "#66B3FF", "#99FF99", "#FFCC99", "#C0C0C0",
            "#FFB6C1", "#ADD8E6", "#90EE90", "#FFDAC1", "#FFD700", "#FFA07A"
        ];

        // Render Chart.js
        const ctx = document.getElementById('summaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Customer Data Summary',
                    data: values,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Categories'
                        },
                        ticks: {
                            autoSkip: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Values'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

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

@if (session('status_1'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Success!",
                text: "{{ session('status_1') }}",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    let urlParams = new URLSearchParams(window.location.search);
                    let paymentId = urlParams.get("payment_id");

                    if (!paymentId) {
                        console.error("Payment ID not found in URL");
                        return;
                    }

                    // Make an AJAX request to execute the controller
                    fetch("{{ route('payments.makePayment') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                payment_id: paymentId
                            }) // Pass payment_id
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);

                            // Reload the page to display the session message
                            window.location.reload();
                        })
                        .catch(error => console.error("Error:", error));
                }
            });
        });
    </script>
@endif
