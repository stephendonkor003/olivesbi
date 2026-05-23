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
                        <a href="{{ route('businesses.index') }}" class="btn btn-sm btn-primary">
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
                            action="{{ route('businesses.update', $business) }}">
                            @csrf

                            <input type="hidden" name="business_class_url" url="{{ route('ajax.business_class') }}">

                            <div class="col-sm-6 mb-3">
                                <label for="business_type" class="form-label">Business Type</label>
                                <input type="text" class="form-control" name="businessType->name??''"
                                    placeholder="Business Name" value="{{ $business->businessType->name ?? '' }}" readonly>

                                @error('business_type')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_class" class="form-label">Business Class</label>
                                <input type="text" class="form-control" name="businessType->name??''"
                                    placeholder="Business Name" value="{{ $business->businessClass->name ?? '' }}" readonly>
                            </div>

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
                                    value="{{ $business->street_name }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digital_address" class="form-label">Digital Address</label>
                                <input type="text" class="form-control" name="digital_address"
                                    placeholder="Digital Address" value="{{ $business->digital_address }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control" name="house_number" placeholder="House Number"
                                    value="{{ $business->house_number }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_phone" class="form-label">Business Phone</label>
                                <input type="text" class="form-control" name="business_phone"
                                    placeholder="Business Phone" value="{{ $business->business_phone }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="permit_number" class="form-label">Permit Number</label>
                                <input type="text" class="form-control" name="permit_number" placeholder="Permit Number"
                                    value="{{ $business->permit_number }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_validation_code" class="form-label">Business Validation Code</label>
                                <input type="text" class="form-control" name="business_validation_code"
                                    placeholder="Business Validation Code"
                                    value="{{ $business->business_validation_code }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="registration_number" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" name="registration_number"
                                    placeholder="Registration Number" value="{{ $business->registration_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <input type="text" class="form-control" name="business_address"
                                    placeholder="Business Address" value="{{ $business->business_address }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_contact" class="form-label">Business Contact</label>
                                <input type="text" class="form-control" name="business_contact"
                                    placeholder="Business Contact" value="{{ $business->business_contact }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="nature_of_business" class="form-label">Nature of Business</label>
                                <input type="text" class="form-control" name="nature_of_business"
                                    placeholder="Nature of Business" value="{{ $business->nature_of_business }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="tax_identification_number" class="form-label">Tax Identification
                                    Number</label>
                                <input type="text" class="form-control" name="tax_identification_number"
                                    placeholder="Tax Identification Number"
                                    value="{{ $business->tax_identification_number }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="establishment_date" class="form-label">Establishment Date</label>
                                <input type="date" class="form-control" name="establishment_date"
                                    placeholder="Establishment Date" value="{{ $business->establishment_date }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="citizen_account_number" class="form-label">Citizen Account Number</label>
                                <input type="text" class="form-control" name="citizen_account_number"
                                    placeholder="citizen account number" value="{{ $business->customer->first_name }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="bus_account_number" class="form-label">Business Account Number</label>
                                <input type="text" class="form-control" name="bus_account_number"
                                    placeholder="Business Account Number" value="{{ $business->bus_account_number }}"
                                    readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly</label>
                                <input type="text" class="form-control" name="assembly_code" placeholder="Assembly"
                                    value="{{ $business->assembly->name ?? '' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="division_id" class="form-label">Business Division</label>
                                <input type="text" class="form-control" name="division_id" placeholder="Division"
                                    value="{{ $business->division->division_name ?? '' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="block_id" class="form-label">Business Block</label>
                                <input type="text" class="form-control" name="block_id" placeholder="Block"
                                    value="{{ $business->block->block_name ?? '' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="zone_id" class="form-label">Business Zone</label>
                                <input type="text" class="form-control" name="zone_id" placeholder="Zone"
                                    value="{{ $business->zone->name ?? '' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="property_use_id" class="form-label">Property Use</label>
                                <input type="text" class="form-control" name="property_use_id"
                                    placeholder="Property Use" value="{{ $business->propertyUse->name ?? '' }}" readonly>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="status_of_business" class="form-label">Status of Business</label>
                                <input type="text" class="form-control" name="status_of_business"
                                    placeholder="Business Account Number" value="{{ $business->status_of_business }}"
                                    readonly>

                                @error('status_of_business')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
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
