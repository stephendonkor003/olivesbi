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
                            You are Viewing Property Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('properties.create')
                        <a href="{{ route('new-properties.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-arrow-go-back-line me-1"></i> Back
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
                            action="{{ route('properties.update', $property) }}">
                            @csrf

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="entity_type" class="form-label">Property Number</label>
                                    <input type="text" class="form-control @error('entity_type') is-invalid @enderror"
                                        id="entity_type" name="entity_type" placeholder="Entity Type"
                                        value="{{ $property->erms_id ?? 'N/A' }}" readonly>
                                    @error('entity_type')
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
                                        id="digital_address" name="digital_address" placeholder="Digital Address"
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
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
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
                                    <input type="text" class="form-control" id="street_name" name="street_name"
                                        placeholder="Street Name" value="{{ $property->streetname ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rated" class="form-label">Rated</label>
                                    <input type="text" class="form-control" id="rated" name="rated"
                                        placeholder="Rated" value="{{ $property->is_rated == '1' ? 'Yes' : 'No' }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="customer_name" class="form-label">Associate
                                    Owner</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    placeholder="Owner Name"
                                    value="{{ ($property->customer->fname ?? 'N/A') . ' ' . ($property->customer->lname ?? 'N/A') }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="ratable_value" class="form-label">Ratable
                                        Value</label>
                                    <input type="text" class="form-control" id="ratable_value" name="ratable_value"
                                        placeholder="Ratable Value" value="{{ $property->rateable_value ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude"
                                        placeholder="Longitude" value="{{ $property->polygon->cntr_x_coord ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->polygon->cntr_y_coord ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Assembly Field -->
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Assembly
                                        Property</label>
                                    <input type="text" class="form-control" id="assembly_code" name="assembly_code"
                                        placeholder="Assembly" value="{{ $property->assembly->name ?? 'N/A' }}" readonly>
                                    @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
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
                                        <th>Zone Type</th>
                                        <th>Property Classes</th>
                                        <th>Rate Impost</th>
                                        <th>Flat Rate</th>
                                        <th>Min Amount</th>
                                        <th>Is Rated</th>
                                        <th>Created By</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revenueStreams as $index => $revenueStream)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $revenueStream->revenueStreamType->name ?? '' }}</td>
                                            <td>{{ $revenueStream->zoneType->name ?? '' }}</td>
                                            <td>{{ $revenueStream->propertyClasses->name ?? '' }}</td>
                                            <td>{{ $revenueStream->rate_impost ?? '0.00' }}</td>
                                            <td>{{ $revenueStream->flat_rate ?? '0.00' }}</td>
                                            <td>{{ $revenueStream->min_amount ?? '0.00' }}</td>
                                            <td>{{ $revenueStream->is_rated === 'Y' ? 'Assessed' : 'Unassessed' }}</td>
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
                            action="{{ route('properties.assignRevenueStream', $property) }}#search-results">
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
                                <label for="is_rated" class="form-label">Assessed Property?</label>
                                <select name="is_rated" class="form-control @error('is_rated') is-invalid @enderror">
                                    <option disabled {{ request('is_rated') ? '' : 'selected' }}>Select Assessed Property?
                                    </option>
                                    <option value="Y" {{ request('is_rated') == 'Y' ? 'selected' : '' }}>Yes</option>
                                    <option value="N" {{ request('is_rated') == 'N' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('is_rated')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 mb-2" id="input1">
                                <label for="zone_type_id" class="form-label">Zone Type</label>
                                <select class="form-control @error('zone_type_id') is-invalid @enderror" id="zone_type_id"
                                    name="zone_type_id">
                                    <option disabled {{ request('zone_type_id') ? '' : 'selected' }}>Select Zone Type
                                    </option>
                                    @foreach ($zoneTypes as $zoneType)
                                        <option value="{{ $zoneType->id }}"
                                            {{ request('zone_type_id') == $zoneType->id ? 'selected' : '' }}>
                                            {{ $zoneType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('zone_type_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 mb-2" id="input2">
                                <label for="property_classes_id" class="form-label">Property Classes</label>
                                <select class="form-control @error('property_classes_id') is-invalid @enderror"
                                    id="property_classes_id" name="property_classes_id">
                                    <option disabled {{ request('property_classes_id') ? '' : 'selected' }}>Select Property
                                        Classes</option>
                                    @foreach ($propertyClasses as $propertyClass)
                                        <option value="{{ $propertyClass->id }}"
                                            {{ request('property_classes_id') == $propertyClass->id ? 'selected' : '' }}>
                                            {{ $propertyClass->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_classes_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 mb-2" id="input6">
                                <label for="property_type_id" class="form-label">Property Type</label>
                                <select class="form-control @error('property_type_id') is-invalid @enderror"
                                    id="property_type_id" name="property_type_id">
                                    <option disabled {{ request('property_type_id') ? '' : 'selected' }}>Select Property
                                        Type</option>
                                    @foreach ($propertyTypes as $propertyType)
                                        <option value="{{ $propertyType->id }}"
                                            {{ request('property_type_id') == $propertyType->id ? 'selected' : '' }}>
                                            {{ $propertyType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_type_id')
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
                                    <th>Is Rated</th>
                                    <th>Zone Type</th>
                                    <th>Property Class</th>
                                    <th>Property Type</th>
                                    <th>Rate Impost</th>
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
                                        <td>{{ $stream->is_rated == 'Y' ? 'Yes' : 'No' }}</td>
                                        <td>{{ $stream->zoneType->name ?? '-' }}</td>
                                        <td>{{ $stream->propertyClasses->name ?? '-' }}</td>
                                        <td>{{ $stream->propertyType->name ?? '-' }}</td>
                                        <td>{{ $stream->rate_impost ?? '0.00' }}</td>
                                        <td>{{ $stream->flat_rate ?? '0.00' }}</td>
                                        <td>{{ $stream->min_amount ?? '0.00' }}</td>
                                        <td>{{ $stream->assembly->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('properties.assignRevenue', ['property_id' => $property->id, 'revenue_stream_id' => $stream->id]) }}"
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
    <script>
        $(document).ready(function() {

            $("select[name='is_rated']").on('change', function() {
                if ($(this).val() === 'Y') {
                    $("#input6").hide();
                } else if ($(this).val() === 'N') {
                    $("#input6").show();
                }
            });

            if (window.location.hash === '#search-results') {
                $('html, body').animate({
                    scrollTop: $('#search-results').offset().top
                }, 800);
            }
        });
    </script>
@endsection
