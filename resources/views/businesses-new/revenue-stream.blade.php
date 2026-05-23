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
                            <i class="ri-building-line me-2"></i> Business Management
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            You are Viewing A Business Record from your central database repository.
                        </p>
                    </div>

                    @can('businesses.create')
                        <a href="{{ route('new-businesses.index') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('new-businesses.update', $business) }}">
                            @csrf

                            <input type="hidden" name="business_class_url" url="{{ route('ajax.business_class') }}">

                            <div class="col-sm-6 mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name" placeholder="Business Name"
                                    value="{{ $business->business_name }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="Location"
                                    value="{{ $business->location }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                    value="{{ $business->email }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" name="street_name" placeholder="Street Name"
                                    value="{{ $business->street_name ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digital_address" class="form-label">Digital Address</label>
                                <input type="text" class="form-control" name="digitaladdress"
                                    placeholder="Digital Address" value="{{ $business->digitaladdress }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control" name="hseno" placeholder="House Number"
                                    value="{{ $business->hseno }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_phone" class="form-label">Business Phone</label>
                                <input type="text" class="form-control" name="phone_no" placeholder="Business Phone"
                                    value="{{ $business->phone_no }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="citizen_account_number" class="form-label">Citizen Account Number</label>
                                <input type="text" class="form-control" name="citizen_account_number"
                                    placeholder="citizen account number" value="{{ $business->owner_id }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="bus_account_number" class="form-label">Business Account Number</label>
                                <input type="text" class="form-control" name="bus_account_number"
                                    placeholder="Business Account Number" value="{{ $business->erms_id }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly</label>
                                <input type="text" class="form-control" name="assembly_code" placeholder="Assembly"
                                    value="{{ $business->assembly->name ?? 'N/A' }}" readonly>
                            </div>
                        </form>

                        <div class="alert alert-primary alert-dismissible fade show">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                class="me-2">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <strong>CURRENT REVENUE STREAM / FEE FIXING ASSIGNED</strong>
                        </div>

                        <div class="table-responsive" id="search-results">
                            <table id="file-export" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Revenue Stream Type</th>
                                        <th>Main Item</th>
                                        <th>Sub Item</th>
                                        <th>Item Description</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Flat Rate</th>
                                        <th>Min Amount</th>
                                        <th>Created By</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revenueStreams as $index => $revenueStream)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $revenueStream->revenueStreamType->name ?? '' }}</td>
                                            <td>{{ $revenueStream->mainItem->name ?? 'N/A' }}</td>
                                            <td>{{ $revenueStream->subItem->name ?? 'N/A' }}</td>
                                            <td>{{ $revenueStream->itemDescription->name ?? '' }}</td>
                                            <td>{{ $revenueStream->category->name ?? '' }}</td>
                                            <td>{{ $revenueStream->duration ?? '' }}</td>
                                            <td>{{ $revenueStream->flat_rate ?? '0.00' }}</td>
                                            <td>{{ $revenueStream->min_amount ?? '0.00' }}</td>
                                            <td>{{ $revenueStream->createdBy->name ?? '' }}</td>
                                            <td>{{ $revenueStream->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-danger alert-dismissible fade show" style="margin-top:20px">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                class="me-2">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <strong>ASSIGN NEW REVENUE STREAM / FEE FIXING HERE - SEARCH FOR AVAILABLE REVENUE STREAM / FEE
                                FIXING HERE</strong>
                        </div>

                        <form class="row g-3 needs-validation" method="GET"
                            action="{{ route('businesses.assignRevenueStream', $business) }}#search-results">
                            @csrf

                            <div class="col-sm-3 mb-2" style="display: none">
                                <label for="revenue_stream_type_id" class="form-label">Revenue Stream Type</label>
                                <select class="form-control @error('revenue_stream_type_id') is-invalid @enderror"
                                    id="revenue_stream_type_id" name="revenue_stream_type_id">
                                    <option disabled {{ request('revenue_stream_type_id') ? '' : 'selected' }}>Select
                                        Revenue Stream Type</option>
                                    @foreach ($revenueStreamTypes as $revenueStreamType)
                                        <option value="{{ $revenueStreamType->id }}"
                                            {{ request('revenue_stream_type_id') == $revenueStreamType->id ? 'selected' : '' }}>
                                            {{ $revenueStreamType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('revenue_stream_type_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="is_rated" class="form-label">Item Description</label>
                                <select class="form-control @error('description_id') is-invalid @enderror"
                                    id="description_id" name="description_id">
                                    <option disabled {{ request('description_id') ? '' : 'selected' }}>Select Description
                                    </option>
                                    @foreach ($itemDescriptions as $description)
                                        <option value="{{ $description->id }}"
                                            {{ request('description_id') == $description->id ? 'selected' : '' }}>
                                            {{ $description->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('is_rated')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 mb-2" id="input1">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    <option disabled {{ request('category_id') ? '' : 'selected' }}>Select Category
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary search" style="width: 200px;">
                                    Search
                                </button>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Stream Name</th>
                                    <th>Main Item</th>
                                    <th>Sub Item</th>
                                    <th>Item Description</th>
                                    <th>Category</th>
                                    <th>Duration</th>
                                    <th>Flate Rate</th>
                                    <th>Min Rate</th>
                                    <th>Assembly</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fetchedRevenueStreams as $index => $stream)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stream->revenueStreamType->name ?? '-' }}</td>
                                        <td>{{ $stream->mainItem->name ?? '-' }}</td>
                                        <td>{{ $stream->subItem->name ?? '-' }}</td>
                                        <td>{{ $stream->itemDescription->name ?? 'N/A' }}</td>
                                        <td>{{ $stream->category->name ?? '-' }}</td>
                                        <td>{{ $stream->duration ?? '-' }}</td>
                                        <td>{{ $stream->flat_rate ?? '0.00' }}</td>
                                        <td>{{ $stream->min_amount ?? '0.00' }}</td>
                                        <td>{{ $stream->assembly->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('businesses.assignRevenue', ['business_id' => $business->id, 'revenue_stream_id' => $stream->id]) }}"
                                                class="btn btn-success btn-sm ms-2">Assign</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">No revenue streams
                                            found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/jquery.repeater.js') }}"></script>
    <script src="{{ asset('assets/js/general/business.js') }}"></script>
@endsection
