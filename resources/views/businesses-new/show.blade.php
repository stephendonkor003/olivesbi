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

                            <div class="col-sm-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    placeholder="Longitude" value="{{ $business->polygon->cntr_x_coord ?? 'N/A' }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    placeholder="Latitude" value="{{ $business->polygon->cntr_y_coord ?? 'N/A' }}"
                                    readonly>
                            </div>
                        </form>
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
