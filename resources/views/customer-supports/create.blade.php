@extends('layout.base')

@section('page-content')
    <div class="container-fluid">
        <div class="card">

            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-user-settings-line me-2"></i> Rate Payer Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing the Record of -{{ $citizen->account_number }} from your
                            central database repository.
                        </p>

                    </div>
                    @can('customers.create')
                        <a href="{{ route('citizens.index') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i>
                            Back to Rate Payer Management
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card h-auto">
                    <div class="card-body">
                        <div class="profile-tab">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="#my-posts" data-bs-toggle="tab"
                                            class="nav-link active show">Personal Information</a>
                                    </li>
                                    <li class="nav-item"><a href="#about-me" data-bs-toggle="tab" class="nav-link">Bills
                                            History
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="#profile-settings" data-bs-toggle="tab"
                                            class="nav-link">Payments History</a>
                                    </li>
                                    <li class="nav-item"><a href="#property" data-bs-toggle="tab" class="nav-link">Property
                                            Information</a>
                                    </li>
                                    <li class="nav-item"><a href="#business" data-bs-toggle="tab" class="nav-link">Business
                                            Information</a>
                                    </li>
                                </ul>
                                <div class="tab-content">

                                    <div id="my-posts" class="tab-pane fade active show">
                                        <div class="my-post-content pt-3">
                                            <div class="post-input">

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="first_name" class="form-label">First Name</label>
                                                            <input type="text" class="form-control" id="first_name"
                                                                name="first_name" value="{{ $citizen->first_name }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="last_name" class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" id="last_name"
                                                                name="last_name" value="{{ $citizen->last_name }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="other_name" class="form-label">Other Name</label>
                                                            <input type="text" class="form-control" id="other_name"
                                                                name="other_name"
                                                                value="{{ $citizen->other_name ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="gender" class="form-label">Gender</label>
                                                            <input type="text" class="form-control" id="gender"
                                                                name="gender" value="{{ $citizen->gender }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="date_of_birth" class="form-label">Date of
                                                                Birth</label>
                                                            <input type="text" class="form-control" id="date_of_birth"
                                                                name="date_of_birth" value="{{ $citizen->date_of_birth }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="marital_status" class="form-label">Marital
                                                                Status</label>
                                                            <input type="text" class="form-control" id="marital_status"
                                                                name="marital_status" value="{{ $citizen->marital_status }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="nia_number" class="form-label">NIA Number</label>
                                                            <input type="text" class="form-control" id="nia_number"
                                                                name="nia_number"
                                                                value="{{ $citizen->nia_number ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="account_number" class="form-label">Account
                                                                Number</label>
                                                            <input type="text" class="form-control"
                                                                id="account_number" name="account_number"
                                                                value="{{ $citizen->account_number }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="account_number" class="form-label">Customer
                                                                Type</label>
                                                            <input type="text" class="form-control" id="customer_type"
                                                                name="customer_type"
                                                                value="{{ $citizen->customerType->name ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="about-me" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
                                                {{-- <div class="card-header">
                                                    <div class="card-title">Bill Generation History</div>
                                                </div> --}}
                                                <table id="file-export" class="table table-bordered text-nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Bill No</th>
                                                            <th>Name</th>
                                                            <th>Bill Date</th>
                                                            <th>Bill Year</th>
                                                            <th>Bill Type</th>
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
                                                                    $firstname =
                                                                        $bill->property->customer->first_name ?? '';
                                                                    $lastname =
                                                                        $bill->property->customer->last_name ?? '';
                                                                    $billType = 'Property Bill';
                                                                } else {
                                                                    $firstname =
                                                                        $bill->business->customer->first_name ?? '';
                                                                    $lastname =
                                                                        $bill->business->customer->last_name ?? '';
                                                                    $billType = 'Business Bill';
                                                                }
                                                                $name = $firstname . ' ' . $lastname;
                                                            @endphp

                                                            <tr class="btn-reveal-trigger">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $bill->bills_id }}</td>
                                                                <td>{{ $name ?? '' }}</td>
                                                                <td>{{ $bill->billing_date }}</td>
                                                                <td>{{ $bill->bills_year }}</td>
                                                                @if ($bill->property_id !== null)
                                                                    <td>
                                                                        <span class="badge light badge-success">
                                                                            {{ $billType }}
                                                                        </span>
                                                                    </td>
                                                                @else
                                                                    <td>
                                                                        <span class="badge light badge-warning">
                                                                            {{ $billType }}
                                                                        </span>
                                                                    </td>
                                                                @endif
                                                                <td>{{ number_format($bill->arrears, 2) }}</td>
                                                                <td>{{ number_format($bill->amount, 2) }}</td>
                                                                <td>{{ number_format($bill->amount + $bill->arrears, 2) }}
                                                                </td>
                                                                <td>{{ $bill->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $bill->created_at }}</td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <div class="btn-link" data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                                <path
                                                                                    d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                                <path
                                                                                    d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <div class="py-2">
                                                                                <a class="dropdown-item"
                                                                                    href=" {{ route('bills.show', $bill) }}"
                                                                                    target="_blank">Print Bill
                                                                                </a>
                                                                                <a class="dropdown-item"
                                                                                    href=" {{ route('payments.create', $bill) }}">Make
                                                                                    Payment
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="5"></th>
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

                                    <div id="profile-settings" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
                                                {{-- <div class="card-header">
                                                    <div class="card-title">Payment History</div>
                                                </div> --}}
                                                <table id="file-export2" class="table table-bordered text-nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Bills ID</th>
                                                            <th>Name</th>
                                                            <th>Amount</th>
                                                            <th>Payment Mode</th>
                                                            <th>Status</th>
                                                            <th>Assembly</th>
                                                            <th>Paid By</th>
                                                            <th>Payment Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($customerData['payments'] as $index => $payment)
                                                            @php
                                                                $name = '';
                                                                if (
                                                                    $payment->bill->property &&
                                                                    $payment->bill->property->customer
                                                                ) {
                                                                    $firstname =
                                                                        $payment->bill->property->customer
                                                                            ->first_name ?? '';
                                                                    $lastname =
                                                                        $payment->bill->property->customer->last_name ??
                                                                        '';
                                                                    $name = $firstname . ' ' . $lastname;
                                                                } elseif (
                                                                    $payment->bill->business &&
                                                                    $payment->bill->business->customer
                                                                ) {
                                                                    $firstname =
                                                                        $payment->bill->business->customer
                                                                            ->first_name ?? '';
                                                                    $lastname =
                                                                        $payment->bill->business->customer->last_name ??
                                                                        '';
                                                                    $name = $firstname . ' ' . $lastname;
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $payment->bills_id }}</td>
                                                                <td>{{ $name }}</td>
                                                                <td>{{ number_format($payment->amount, 2) }}</td>
                                                                <td>{{ $payment->payment_mode }}</td>
                                                                <td>{{ $payment->transaction_status }}</td>
                                                                <td>{{ $payment->assembly->name ?? 'N/A' }}</td>
                                                                <td>{{ $payment->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $payment->created_at }}</td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <div class="btn-link" data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                                <path
                                                                                    d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                                <path
                                                                                    d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                                    stroke="#737B8B" stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <a class="dropdown-item"
                                                                                href="{{ route('payments.receipt', $payment) }}"
                                                                                target="_blank">View
                                                                                Receipt</a>
                                                                            {{-- <a class="dropdown-item"
                                                                                    href="{{ route('payments.edit', $payment) }}">Amend
                                                                                    Bill</a> --}}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <th colspan="2"></th>
                                                        <th>Total (GHS)</th>
                                                        <th>{{ $customerData['paymentTotal'] }}</th>
                                                        <th colspan="6"></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="property" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
                                                {{-- <div class="card-header">
                                                    <div class="card-title">Property Information</div>
                                                </div> --}}
                                                <table id="file-export3" class="table table-bordered text-nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Entity Type</th>
                                                            <th>Category</th>
                                                            <th>Digital Address</th>
                                                            <th>Location</th>
                                                            <th>Street Name</th>
                                                            <th>Rated</th>
                                                            <th>Validated</th>
                                                            <th>Property Number</th>
                                                            <th>Owner Account</th>
                                                            <th>Owner Name</th>
                                                            <th>Ratable Value</th>
                                                            <th>Assembly</th>
                                                            <th>Division</th>
                                                            <th>Block</th>
                                                            <th>Zone</th>
                                                            <th>Property Use</th>
                                                            <th>Date Created</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($customerData['properties'] as $index => $property)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $property->entityType->name ?? '' }}</td>
                                                                <td>{{ $property->entityType->category ?? '' }}</td>
                                                                <td>{{ $property->digital_address }}</td>
                                                                <td>{{ $property->location }}</td>
                                                                <td>{{ $property->street_name }}</td>
                                                                <td>{{ $property->rated }}</td>
                                                                <td>{{ $property->validated }}</td>
                                                                <td>{{ $property->property_number }}</td>
                                                                <td>{{ $property->customer->account_number ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $property->customer->first_name ?? '' }}
                                                                    {{ $property->customer->last_name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ number_format($property->ratable_value, 2) }}
                                                                </td>
                                                                <td>{{ $property->assembly->name ?? 'N/A' }}</td>
                                                                <td>{{ $property->division->division_name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $property->block->block_name ?? 'N/A' }}</td>
                                                                <td>{{ $property->zone->name ?? 'N/A' }}</td>
                                                                <td>{{ $property->propertyUse->name ?? 'N/A' }}</td>
                                                                <td>{{ $property->created_at }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="10"></th>
                                                            <th>Total (GHS)</th>
                                                            <th>{{ $customerData['total'] }}</th>
                                                            <th colspan="6"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="business" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
                                                {{-- <div class="card-header">
                                                    <div class="card-title">Business Information</div>
                                                </div> --}}
                                                <table id="file-export4" class="table table-bordered text-nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Business Name</th>
                                                            <th>Business Type</th>
                                                            <th>Business Class</th>
                                                            <th>Location</th>
                                                            <th>Email</th>
                                                            <th>Phone</th>
                                                            <th>Business Owner</th>
                                                            <th>Assembly</th>
                                                            <th>Division</th>
                                                            <th>Block</th>
                                                            <th>Zone</th>
                                                            <th>Property Use</th>
                                                            <th>Created By</th>
                                                            <th>Date Created</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($customerData['businesses'] as $index => $business)
                                                            @php
                                                                $firstname = $business->customer->first_name ?? '';
                                                                $lastname = $business->customer->last_name ?? '';
                                                                $fullname = $firstname . ' ' . $lastname;
                                                            @endphp

                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $business->business_name }}</td>
                                                                <td>{{ $business->businessType->name ?? '' }}</td>
                                                                <td>{{ $business->businessClass->name ?? '' }}</td>
                                                                <td>{{ $business->location }}</td>
                                                                <td>{{ $business->email }}</td>
                                                                <td>{{ $business->business_phone }}</td>
                                                                <td>{{ $fullname ?? '' }}</td>
                                                                <td>{{ $business->assembly->name ?? 'N/A' }}</td>
                                                                <td>{{ $business->division->division_name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $business->block->block_name ?? 'N/A' }}</td>
                                                                <td>{{ $business->zone->name ?? 'N/A' }}</td>
                                                                <td>{{ $business->propertyUse->name ?? 'N/A' }}</td>
                                                                <td>{{ $business->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $business->created_at }}</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
