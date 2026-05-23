@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-building-line me-2"></i> Property Management
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            You are Viewing A Property Record from your central database repository.
                        </p>
                    </div>
                    @can('properties.create')
                        <a href="{{ route('new-properties.index') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card h-auto">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#my-posts" data-bs-toggle="tab" class="nav-link active show">
                                            Owner Information
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#about-me" data-bs-toggle="tab" class="nav-link">
                                            Bills History
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile-settings" data-bs-toggle="tab" class="nav-link">
                                            Payments History
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#property" data-bs-toggle="tab" class="nav-link">
                                            Property Information
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- Owner Information Tab -->
                                    <div id="my-posts" class="tab-pane fade active show">
                                        <div class="my-post-content pt-3">
                                            <div class="post-input">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="first_name" class="form-label">First Name</label>
                                                            <input type="text" class="form-control" id="first_name"
                                                                name="first_name"
                                                                value="{{ $property->customer?->fname ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="last_name" class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" id="last_name"
                                                                name="last_name"
                                                                value="{{ $property->customer?->lname ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="other_name" class="form-label">Other Name</label>
                                                            <input type="text" class="form-control" id="other_name"
                                                                name="other_name"
                                                                value="{{ $property->customer?->mname ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="telephone_number" class="form-label">Phone
                                                                Number</label>
                                                            <input type="text" class="form-control" id="telephone_number"
                                                                name="telephone_number"
                                                                value="{{ $property->customer?->phone_no_primary ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="gender" class="form-label">Gender</label>
                                                            <input type="text" class="form-control" id="gender"
                                                                name="gender"
                                                                value="{{ $property->customer?->gender === 'M' ? 'Male' : 'Female' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="date_of_birth" class="form-label">Email
                                                                Address</label>
                                                            <input type="text" class="form-control" id="date_of_birth"
                                                                name="date_of_birth"
                                                                value="{{ $property->customer?->email ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="marital_status" class="form-label">TIN</label>
                                                            <input type="text" class="form-control"
                                                                id="marital_status" name="marital_status"
                                                                value="{{ $property->customer?->tin_no ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="nia_number" class="form-label">NIA</label>
                                                            <input type="text" class="form-control" id="nia_number"
                                                                name="nia_number"
                                                                value="{{ $property->customer?->nia_number ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="account_number" class="form-label">Account
                                                                Number</label>
                                                            <input type="text" class="form-control"
                                                                id="account_number" name="account_number"
                                                                value="{{ $property->customer?->erms_id ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="customer_type" class="form-label">Customer
                                                                Type</label>
                                                            <input type="text" class="form-control" id="customer_type"
                                                                name="customer_type"
                                                                value="{{ $property->customer?->customerType?->name ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bills History Tab -->
                                    <div id="about-me" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
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
                                                                        $bill->property->customer->fname ?? 'N/A';
                                                                    $lastname =
                                                                        $bill->property->customer->lname ?? 'N/A';
                                                                    $billType = 'Property Bill';
                                                                } else {
                                                                    $firstname =
                                                                        $bill->business->customer->fname ?? 'N/A';
                                                                    $lastname =
                                                                        $bill->business->customer->lname ?? 'N/A';
                                                                    $billType = 'Business Bill';
                                                                }
                                                                $name = trim($firstname . ' ' . $lastname) ?: 'N/A';
                                                            @endphp
                                                            <tr class="btn-reveal-trigger">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $bill->bills_id ?? 'N/A' }}</td>
                                                                <td>{{ $name }}</td>
                                                                <td>{{ $bill->billing_date ?? 'N/A' }}</td>
                                                                <td>{{ $bill->bills_year ?? 'N/A' }}</td>
                                                                <td>{{ $billType ?: 'N/A' }}</td>
                                                                <td>
                                                                    {{ isset($bill->arrears) ? number_format($bill->arrears, 2) : 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    {{ isset($bill->amount) ? number_format($bill->amount, 2) : 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    {{ isset($bill->amount) && isset($bill->arrears) ? number_format($bill->amount + $bill->arrears, 2) : 'N/A' }}
                                                                </td>
                                                                <td>{{ $bill->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $bill->created_at ?? 'N/A' }}</td>
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
                                                                                href="{{ route('bills.show', $bill) }}"
                                                                                target="_blank">
                                                                                Print Bill
                                                                            </a>
                                                                            <a class="dropdown-item"
                                                                                href="{{ route('payments.create', $bill) }}">
                                                                                Make Payment
                                                                            </a>
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
                                                            <th>{{ $customerData['totalArrears'] ?? 'N/A' }}
                                                            </th>
                                                            <th>{{ $customerData['totalAmount'] ?? 'N/A' }}
                                                            </th>
                                                            <th>{{ $customerData['totalDue'] ?? 'N/A' }}</th>
                                                            <th colspan="3"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Payment History Tab -->
                                    <div id="profile-settings" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
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
                                                                        $payment->bill->property->customer->fname ??
                                                                        'N/A';
                                                                    $lastname =
                                                                        $payment->bill->property->customer->lname ??
                                                                        'N/A';
                                                                    $name = trim($firstname . ' ' . $lastname) ?: 'N/A';
                                                                } elseif (
                                                                    $payment->bill->business &&
                                                                    $payment->bill->business->customer
                                                                ) {
                                                                    $firstname =
                                                                        $payment->bill->business->customer->fname ??
                                                                        'N/A';
                                                                    $lastname =
                                                                        $payment->bill->business->customer->lname ??
                                                                        'N/A';
                                                                    $name = trim($firstname . ' ' . $lastname) ?: 'N/A';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $payment->bills_id ?? 'N/A' }}</td>
                                                                <td>{{ $name }}</td>
                                                                <td>
                                                                    {{ isset($payment->amount) ? number_format($payment->amount, 2) : 'N/A' }}
                                                                </td>
                                                                <td>{{ $payment->payment_mode ?? 'N/A' }}</td>
                                                                <td>{{ $payment->transaction_status ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $payment->assembly->name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $payment->createdBy->name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $payment->created_at ?? 'N/A' }}</td>
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
                                                                                target="_blank">
                                                                                View Receipt
                                                                            </a>
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
                                                            <th>{{ $customerData['paymentTotal'] ?? 'N/A' }}
                                                            </th>
                                                            <th colspan="6"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Property Information Tab -->
                                    <div id="property" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="table-responsive active-projects">
                                                <form class="row g-3 needs-validation" method="POST"
                                                    action="{{ route('properties.update', $property) }}">
                                                    @csrf
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="hseno" class="form-label">House
                                                                No.</label>
                                                            <input type="text"
                                                                class="form-control @error('hseno') is-invalid @enderror"
                                                                id="hseno" name="hseno" placeholder="Entity Type"
                                                                value="{{ $property->hseno ?? 'N/A' }}" readonly>
                                                            @error('hseno')
                                                                <span class="invalid-feedback" role="alert">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="digital_address" class="form-label">Digital
                                                                Address</label>
                                                            <input type="text"
                                                                class="form-control @error('digital_address') is-invalid @enderror"
                                                                id="digital_address" name="digital_address"
                                                                placeholder="Digital Address"
                                                                value="{{ $property->digitaladdress ?? 'N/A' }}" readonly>
                                                            @error('digital_address')
                                                                <span class="invalid-feedback" role="alert">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="location" class="form-label">Location</label>
                                                            <input type="text"
                                                                class="form-control @error('location') is-invalid @enderror"
                                                                id="location" name="location" placeholder="Location"
                                                                value="{{ $property->location ?? 'N/A' }}" readonly>
                                                            @error('location')
                                                                <span class="invalid-feedback" role="alert">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="street_name" class="form-label">Street
                                                                Name</label>
                                                            <input type="text" class="form-control" id="street_name"
                                                                name="street_name" placeholder="Street Name"
                                                                value="{{ $property->streetname ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="rated" class="form-label">Rated</label>
                                                            <input type="text" class="form-control" id="rated"
                                                                name="rated" placeholder="Rated"
                                                                value="{{ $property->is_rated == '1' ? 'Yes' : 'No' }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="customer_name" class="form-label">Associate
                                                            Owner</label>
                                                        <input type="text" class="form-control" id="customer_name"
                                                            name="customer_name" placeholder="Owner Name"
                                                            value="{{ ($property->customer->fname ?? 'N/A') . ' ' . ($property->customer->lname ?? 'N/A') }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="ratable_value" class="form-label">Ratable
                                                                Value</label>
                                                            <input type="text" class="form-control" id="ratable_value"
                                                                name="ratable_value" placeholder="Ratable Value"
                                                                value="{{ $property->rateable_value ?? 'N/A' }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="longitude" class="form-label">Longitude</label>
                                                            <input type="text" class="form-control" id="longitude"
                                                                name="longitude" placeholder="Longitude"
                                                                value="{{ $property->polygon->cntr_x_coord ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="latitude" class="form-label">Latitude</label>
                                                            <input type="text" class="form-control" id="latitude"
                                                                name="latitude" placeholder="Latitude"
                                                                value="{{ $property->polygon->cntr_y_coord ?? 'N/A' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <!-- Assembly Field -->
                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="assembly" class="form-label">Assembly
                                                                Property</label>
                                                            <input type="text" class="form-control" id="assembly_code"
                                                                name="assembly_code" placeholder="Assembly"
                                                                value="{{ $property->assembly->name ?? 'N/A' }}" readonly>
                                                            @error('assembly_code')
                                                                <span class="invalid-feedback" role="alert">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Property Tab -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
@endsection
