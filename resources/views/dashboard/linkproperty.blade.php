@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

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
                            You are Linking Property to your account - {{ $customers->first()?->account_number }} from your
                            central database repository.
                        </p>

                    </div>
                    <div>
                        <a href="{{ route('dashboard.operational') }}" class="btn btn-primary btn-sm ms-2">
                            <i class="ri-arrow-go-back-line"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card custom-card rounded-md overflow-hidden p-2">
                <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">

                    <div class="row">
                        <div class="col-xxl-12">
                            <form method="GET" action="{{ route('citizens.linkProperty') }}" class="mb-3">
                                <div class="row align-items-end g-3">

                                    <div class="col-md-6">
                                        <label for="property_number" class="form-label">Property Number</label>
                                        <input type="test" name="property_number" id="property_number"
                                            class="form-control" placeholder="Enter Your Property Number Here"
                                            value="{{ request('property_number') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <span class="btn btn-sm btn-primary w-100" data-bs-toggle="modal"
                                            data-bs-target="#addtask">Apply Filters From Map </span>
                                    </div>

                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-xxl-12">
                            @if (isset(request()->property_number) && $property?->ratable_value === null)
                                <label for="property_number" class="form-label" style="color:red">No record
                                    found for the property number or the property has already been linked</label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" action="{{ route('properties.ratePayer') }}" method="POST">
                            @csrf

                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">
                            <input type="hidden" name="property_num" value="{{ $property->property_number ?? '' }}">

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Entity Type</label>
                                <select class="form-control @error('entity_type') is-invalid @enderror" id="entity_type"
                                    name="entity_type">
                                    <option disabled selected>Select Entity Type</option>
                                    @foreach ($businessClassTypes as $businessClassType)
                                        <option value="{{ $businessClassType->id }}"
                                            {{ old('entity_type', $property?->entity_type) == $businessClassType->id ? 'selected' : '' }}>
                                            {{ $businessClassType->name }} -
                                            {{ $businessClassType->category }} - GHS
                                            {{ $businessClassType->rate }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('entity_type')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digital_address" class="form-label">Digital Address</label>
                                <input type="text" class="form-control @error('digital_address') is-invalid @enderror"
                                    id="digital_address" name="digital_address" placeholder="Digital address"
                                    value="{{ $property->digital_address ?? '' }}">

                                @error('digital_address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    id="location" name="location" placeholder="Location"
                                    value="{{ $property->location ?? '' }}">

                                @error('location')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" id="street_name" name="street_name"
                                    placeholder="Street name" value="{{ $property->street_name ?? '' }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rated" class="form-label">Rated</label>
                                    <select class="form-control" id="rated" name="rated">
                                        <option disabled selected>Select If Rated</option>
                                        <option value="Yes"
                                            {{ old('rated', $property?->rated) == 'Yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="No"
                                            {{ old('rated', $property?->rated) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="validated" class="form-label">Validated</label>
                                    <select class="form-control" id="validated" name="validated">
                                        <option disabled selected>Select If Validated</option>
                                        <option value="Yes"
                                            {{ old('validated', $property?->validated) == 'Yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="No"
                                            {{ old('validated', $property?->validated) == 'No' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Associate Owner</label>
                                <select class="form-control" id="customer_name" name="customer_name">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->first_name }} - {{ $customer->nia_number }} -
                                            {{ $customer->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="ratable_value" class="form-label">Ratable Value</label>
                                    <input type="text" class="form-control" id="ratable_value" name="ratable_value"
                                        placeholder="Ratable value" value="{{ $property->ratable_value ?? '' }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    placeholder="Longitude" value="{{ $property->longitude ?? '' }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    placeholder="Latitude" value="{{ $property->latitude ?? '' }}">
                            </div>

                            <!-- Add Assembly field here -->
                            <div class="col-sm-6 mb-3">
                                <label for="assembly" class="form-label">Select An Assembly Where Property is
                                    Located</label>
                                <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                    name="assembly_code">
                                    <option disabled selected>Select Assembly</option>
                                    @foreach ($assemblies as $assembly)
                                        <option value="{{ $assembly->assembly_code }}"
                                            {{ old('assembly_code', $property?->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                            {{ $assembly->name }} Assembly
                                        </option>
                                    @endforeach
                                </select>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="division_id" class="form-label">Property Division</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled selected>Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id', $property?->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }}</option>
                                    @endforeach
                                </select>

                                @error('division_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="block_id" class="form-label">Property Block</label>
                                <select class="form-control @error('block_id') is-invalid @enderror" id="block_id"
                                    name="block_id" required>
                                    <option disabled selected>Select Block</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}"
                                            {{ old('block_id', $property?->block_id) == $block->id ? 'selected' : '' }}>
                                            {{ $block->block_name }}</option>
                                    @endforeach
                                </select>

                                @error('block_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="zone_id" class="form-label">Property Zone</label>
                                <select class="form-control @error('zone_id') is-invalid @enderror" id="zone_id"
                                    name="zone_id" required>
                                    <option disabled selected>Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $property?->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}</option>
                                    @endforeach
                                </select>

                                @error('zone_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="property_use_id" class="form-label">Property use</label>
                                <select class="form-control @error('property_use_id') is-invalid @enderror"
                                    id="property_use_id" name="property_use_id" required>
                                    <option disabled selected>Select Property Use</option>
                                </select>

                                @error('property_use_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12 text-end">
                                    <button type="submit" class="btn btn-primary" style="width:200px">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Add "Add Me" Button Above the Table -->
    <div class="col-xl-12 mb-4">
        <div class="modal fade" id="addtask" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="mail-ComposeLabel">Link Your Property</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row gy-2">
                            <div id="propertyMap" style="height: 400px; width: 100%;"></div>

                            <div id="propertyDetails" class="p-3 border rounded" style="display: none;">
                                <h5 id="propertyTitle">Property Details</h5>
                                <div id="propertyInfo"></div>
                                <a href="#" id="applyFilterBtn" class="btn btn-primary mt-3">Apply Filter</a>
                                <input type="hidden" name="property_id" id="selectedPropertyId">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v=' . time()) }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addtask').addEventListener('shown.bs.modal', function() {
                initializeMap();
            });

            function initializeMap() {
                var map = L.map('propertyMap').setView([-1.286389, 36.817223],
                    12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                fetch('{{ route('properties.getAll') }}')
                    .then(response => response.json())
                    .then(properties => {
                        properties.forEach(property => {
                            var marker = L.marker([property.latitude, property.longitude])
                                .addTo(map)
                                .bindTooltip(property.digital_address);

                            marker.on('click', function() {
                                selectProperty(property);
                            });
                        });

                        if (properties.length > 0) {
                            var bounds = [];
                            properties.forEach(p => {
                                if (p.latitude && p.longitude) {
                                    bounds.push([p.latitude, p.longitude]);
                                }
                            });
                            if (bounds.length > 0) {
                                map.fitBounds(bounds);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching properties:', error);
                    });
            }

            function selectProperty(property) {
                document.getElementById('propertyDetails').style.display = 'block';

                document.getElementById('propertyTitle').textContent = property.title || 'Property Details';

                var infoHTML = `
                    <p><strong>Property Number:</strong> ${property.property_number || 'N/A'}</p>
                    <p><strong>Location:</strong> ${property.location || 'N/A'} ${property.location || ''}</p>
                    <p><strong>Street Name:</strong> ${property.street_name || 'N/A'}</p>
                    <p><strong>Ratable Value:</strong> ${property.ratable_value || 'N/A'}</p>`;

                document.getElementById('propertyInfo').innerHTML = infoHTML;

                document.getElementById('selectedPropertyId').value = property.id;

                var applyFilterBtn = document.getElementById('applyFilterBtn');
                var propertyNumber = property.property_number || '';

                applyFilterBtn.href = "{{ route('citizens.linkProperty') }}?property_number=" + propertyNumber;
            }
        });
    </script>
@endsection
