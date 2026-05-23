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
                            You are Editing A Business Record from your
                            central database repository.
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
                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">

                            <div class="col-sm-6 mb-3">
                                <label for="business_type" class="form-label">Business Type</label>
                                <select class="form-control @error('business_type') is-invalid @enderror" id="business_type"
                                    name="business_type" required>
                                    <option disabled selected>Select Business Type</option>
                                    @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->id }}"
                                            {{ old('business_type', $business->business_type) == $businessType->id ? 'selected' : '' }}>
                                            {{ $businessType->name }}</option>
                                    @endforeach
                                </select>

                                @error('business_type')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_class" class="form-label">Business Class</label>
                                <select class="form-control @error('business_class') is-invalid @enderror"
                                    id="business_class" name="business_class" required>
                                    <option disabled selected>Select Business Class</option>
                                    @foreach ($businessClass as $businessClass)
                                        <option value="{{ $businessClass->id }}"
                                            {{ old('business_class', $business->business_class) == $businessClass->id ? 'selected' : '' }}>
                                            {{ $businessClass->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name" placeholder="Business Name"
                                    value="{{ $business->business_name }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="Location"
                                    value="{{ $business->location }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                    value="{{ $business->email }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" name="street_name" placeholder="Street Name"
                                    value="{{ $business->street_name }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digital_address" class="form-label">Digital Address</label>
                                <input type="text" class="form-control" name="digital_address"
                                    placeholder="Digital Address" value="{{ $business->digital_address }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control" name="house_number" placeholder="House Number"
                                    value="{{ $business->house_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_phone" class="form-label">Business Phone</label>
                                <input type="text" class="form-control" name="business_phone"
                                    placeholder="Business Phone" value="{{ $business->business_phone }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="permit_number" class="form-label">Permit Number</label>
                                <input type="text" class="form-control" name="permit_number"
                                    placeholder="Permit Number" value="{{ $business->permit_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_validation_code" class="form-label">Business Validation Code</label>
                                <input type="text" class="form-control" name="business_validation_code"
                                    placeholder="Business Validation Code"
                                    value="{{ $business->business_validation_code }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="registration_number" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" name="registration_number"
                                    placeholder="Registration Number" value="{{ $business->registration_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <input type="text" class="form-control" name="business_address"
                                    placeholder="Business Address" value="{{ $business->business_address }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_contact" class="form-label">Business Contact</label>
                                <input type="text" class="form-control" name="business_contact"
                                    placeholder="Business Contact" value="{{ $business->business_contact }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="nature_of_business" class="form-label">Nature of Business</label>
                                <input type="text" class="form-control" name="nature_of_business"
                                    placeholder="Nature of Business" value="{{ $business->nature_of_business }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="tax_identification_number" class="form-label">Tax Identification
                                    Number</label>
                                <input type="text" class="form-control" name="tax_identification_number"
                                    placeholder="Tax Identification Number"
                                    value="{{ $business->tax_identification_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="establishment_date" class="form-label">Establishment Date</label>
                                <input type="date" class="form-control" name="establishment_date"
                                    placeholder="Establishment Date" value="{{ $business->establishment_date }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="citizen_account_number" class="form-label">Citizen Account Number</label>
                                <select class="form-control" id="citizen_account_number" name="citizen_account_number">
                                    <option disabled selected>Select Citizen Account Number</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('citizen_account_number', $business->citizen_account_number) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} - {{ $customer->nia_number }} -
                                            {{ $customer->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="bus_account_number" class="form-label">Business Account Number</label>
                                <input type="text" class="form-control" name="bus_account_number"
                                    placeholder="Business Account Number" value="{{ $business->bus_account_number }}">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Select An Assembly Where Business is
                                        Located</label>
                                    <select class="form-control @error('assembly_code') is-invalid @enderror"
                                        id="assembly" name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code', $business->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                                {{ $assembly->name }}
                                                Assembly
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="division_id" class="form-label">Business Division</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled selected>Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id', $business->division_id) == $division->id ? 'selected' : '' }}>
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
                                <label for="block_id" class="form-label">Business Block</label>
                                <select class="form-control @error('block_id') is-invalid @enderror" id="block_id"
                                    name="block_id" required>
                                    <option disabled selected>Select Block</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}"
                                            {{ old('block_id', $business->block_id) == $block->id ? 'selected' : '' }}>
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
                                <label for="zone_id" class="form-label">Business Zone</label>
                                <select class="form-control @error('zone_id') is-invalid @enderror" id="zone_id"
                                    name="zone_id" required>
                                    <option disabled selected>Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $business->zone_id) == $zone->id ? 'selected' : '' }}>
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
                                    @foreach ($propertyUses as $propertyUse)
                                        <option value="{{ $propertyUse->id }}"
                                            {{ old('property_use_id', $business->property_use_id) == $propertyUse->id ? 'selected' : '' }}>
                                            {{ $propertyUse->name }}</option>
                                    @endforeach
                                </select>

                                @error('property_use_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="status_of_business" class="form-label">Status of Business</label>
                                <select class="form-control @error('status_of_business') is-invalid @enderror"
                                    id="status_of_business" name="status_of_business" required>
                                    <option disabled selected>Select Status of business</option>
                                    <option value="Active"
                                        {{ old('status_of_business', $business->status_of_business) == 'Active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="Inactive"
                                        {{ old('status_of_business', $business->status_of_business) == 'InActive' ? 'selected' : '' }}>
                                        In
                                        Active</option>
                                </select>

                                @error('status_of_business')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Update</button>
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
    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>
@endsection
