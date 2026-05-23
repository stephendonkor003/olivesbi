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
                            <i class="ri-user-settings-line me-2"></i> Rate Payer - Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Viewing Rate Payer Property Record from your
                            central database repository.
                        </p>
                    </div>

                    <a href="{{ route('dashboard.myproperties') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
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
                                    <label for="entity_type" class="form-label">Entity Type</label>
                                    <input type="text" class="form-control @error('entity_type') is-invalid @enderror"
                                        id="entity_type" name="entity_type" placeholder="Digital address"
                                        value="{{ $property->entityType->name ?? '' }}" readonly>

                                    @error('entity_type')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="digital_address" class="form-label">Digital Address</label>
                                    <input type="text"
                                        class="form-control @error('digital_address') is-invalid @enderror"
                                        id="digital_address" name="digital_address" placeholder="Digital address"
                                        value="{{ $property->digital_address }}" readonly>

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
                                        value="{{ $property->location }}" readonly>

                                    @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="street_name" class="form-label">Street Name</label>
                                    <input type="text" class="form-control" id="street_name" name="street_name"
                                        placeholder="Street name" value="{{ $property->street_name }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rated" class="form-label">Rated</label>
                                    <input type="text" class="form-control" id="rated" name="rated"
                                        placeholder="Street name" value="{{ $property->rated }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="validated" class="form-label">Validated</label>
                                    <input type="text" class="form-control" id="validated" name="validated"
                                        placeholder="Street name" value="{{ $property->validated }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Associate Owner</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    placeholder="Street name"
                                    value="{{ $property->customer->first_name ?? '' }} {{ $property->customer->last_name ?? '' }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="ratable_value" class="form-label">Ratable Value</label>
                                    <input type="text" class="form-control" id="ratable_value" name="ratable_value"
                                        placeholder="Ratable value" value="{{ $property->ratable_value }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude"
                                        placeholder="Longitude" value="{{ $property->longitude }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->latitude }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Property Number</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->property_number }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Division</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->division->division_name ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Block</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->block->block_name ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Zone</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->zone->name ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Property Use</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->propertyUse->name ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Add Assembly field here -->
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Assembly Property</label>
                                    <input type="text" class="form-control" id="assembly_code" name="assembly_code"
                                        placeholder="Assembly_code" value="{{ $property->assembly->name ?? '' }}"
                                        readonly>

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
        </div>
    </div>
@endsection

@section('page-scripts')
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
@endsection
