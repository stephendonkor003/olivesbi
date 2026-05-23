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
                            You About to Add New Business Record from your
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
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('businesses.store') }}">
                            @csrf

                            <input type="hidden" name="business_class_url" url="{{ route('ajax.business_class') }}">
                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">

                            <div class="alert alert-warning alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                    stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    class="me-2">
                                    <polyline points="9 11 12 14 22 4"></polyline>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                <strong>INDIVIDUAL/ORGANIZATION INFORMATION</strong>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Select Entity Type</label>
                                <select id="entity_type" name="entity_type"
                                    class="form-control @error('entity_type') is-invalid @enderror">
                                    <option value="individual">Individual</option>
                                    <option value="organization">Organization</option>
                                </select>

                                @error('entity_type')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Individual entity type -->
                            <div id="individual">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                            name="firstname" placeholder="First Name">

                                        @error('firstname')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                            name="lastname" placeholder="Last Name">

                                        @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="middlename" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control @error('middlename') is-invalid @enderror"
                                            name="middlename" placeholder="Middle Name">

                                        @error('middlename')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                            <option disabled selected>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>

                                        @error('gender')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="tin_number" class="form-label">Tin Number</label>
                                        <input type="text" class="form-control @error('tin_number') is-invalid @enderror"
                                            name="tin_number" placeholder="Tin Number">

                                        @error('tin_number')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email_i" placeholder="Email">

                                        @error('email_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="primary_phone" class="form-label">Primary Phone Number</label>
                                        <input type="text"
                                            class="form-control @error('primary_phone') is-invalid @enderror"
                                            name="primary_phone_i" placeholder="Primary Phone Number">

                                        @error('primary_phone_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="secondary_phone" class="form-label">Secondary Phone Number</label>
                                        <input type="text"
                                            class="form-control @error('secondary_phone') is-invalid @enderror"
                                            name="secondary_phone_i" placeholder="Secondary Phone Number">

                                        @error('secondary_phone_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="house_number" class="form-label">House Number</label>
                                        <input type="text"
                                            class="form-control @error('house_number') is-invalid @enderror"
                                            name="house_number_i" placeholder="House Number">

                                        @error('house_number_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="digital_address" class="form-label">Digital Address</label>
                                        <input type="text"
                                            class="form-control @error('digital_address') is-invalid @enderror"
                                            name="digital_address_i" placeholder="Digital Address">

                                        @error('digital_address_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="residential_address" class="form-label">Residential Address</label>
                                        <input type="text"
                                            class="form-control @error('residential_address') is-invalid @enderror"
                                            name="residential_address_i" placeholder="Residential Address">

                                        @error('residential_address_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="postal_address" class="form-label">Postal Address</label>
                                        <input type="text"
                                            class="form-control @error('postal_address') is-invalid @enderror"
                                            name="postal_address_i" placeholder="Postal Address">

                                        @error('postal_address_i')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Organization entity type -->
                            <div id="organization" style="display: none">
                                <div id="organization_repeater" class="organization">
                                    <div class="items" data-repeater-list="organization_data">
                                        <div data-repeater-item>
                                            <div class="item-content">
                                                <div class="row">
                                                    <div class="col-sm-6 mb-3">
                                                        <label for="organization_name" class="form-label">Organization
                                                            Name</label>
                                                        <input type="text"
                                                            class="form-control @error('organization_name') is-invalid @enderror"
                                                            name="organization_name" placeholder="Organization Name">

                                                        @error('organization_name')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email_o" placeholder="Email">

                                                        @error('email_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="primary_phone" class="form-label">Primary Phone
                                                            Number</label>
                                                        <input type="text"
                                                            class="form-control @error('primary_phone') is-invalid @enderror"
                                                            name="primary_phone_o" placeholder="Primary Phone Number">

                                                        @error('primary_phone_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="secondary_phone" class="form-label">Secondary Phone
                                                            Number</label>
                                                        <input type="text"
                                                            class="form-control @error('secondary_phone') is-invalid @enderror"
                                                            name="secondary_phone_o" placeholder="Secondary Phone Number">

                                                        @error('secondary_phone_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="house_number" class="form-label">House Number</label>
                                                        <input type="text"
                                                            class="form-control @error('house_number') is-invalid @enderror"
                                                            name="house_number_o" placeholder="House Number">

                                                        @error('house_number_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="digital_address" class="form-label">Digital
                                                            Address</label>
                                                        <input type="text"
                                                            class="form-control @error('digital_address') is-invalid @enderror"
                                                            name="digital_address_o" placeholder="Digital Address">

                                                        @error('digital_address_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="residential_address" class="form-label">Residential
                                                            Address</label>
                                                        <input type="text"
                                                            class="form-control @error('residential_address') is-invalid @enderror"
                                                            name="residential_address_o"
                                                            placeholder="Residential Address">

                                                        @error('residential_address_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6 mb-3">
                                                        <label for="postal_address" class="form-label">Postal
                                                            Address</label>
                                                        <input type="text"
                                                            class="form-control @error('postal_address') is-invalid @enderror"
                                                            name="postal_address_o" placeholder="Postal Address">

                                                        @error('postal_address_o')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <input data-repeater-delete style="color: rgb(255, 255, 255);"
                                                class="btn btn-danger btn-sm" type="button" value="Delete" />
                                            <hr style="color: rgb(143, 135, 135);">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-right">
                                            <input data-repeater-create type="button"
                                                class="btn btn-success btn-sm mb-4 float-right btn-add"
                                                value="Add New Organization Information" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                    stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    class="me-2">
                                    <polyline points="9 11 12 14 22 4"></polyline>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                <strong>BUSINESS INFORMATION</strong>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_type" class="form-label">Business Type</label>
                                <select class="form-control @error('business_type') is-invalid @enderror"
                                    id="business_type" name="business_type" required>
                                    <option disabled selected>Select Business Type</option>
                                    @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->id }}">{{ $businessType->name }}</option>
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
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name"
                                    placeholder="Business Name">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="Location">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" name="street_name" placeholder="Street Name">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="digital_address" class="form-label">Digital Address</label>
                                <input type="text" class="form-control" name="digital_address"
                                    placeholder="Digital Address">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control" name="house_number"
                                    placeholder="House Number">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_phone" class="form-label">Business Phone</label>
                                <input type="text" class="form-control" name="business_phone"
                                    placeholder="Business Phone">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="permit_number" class="form-label">Permit Number</label>
                                <input type="text" class="form-control" name="permit_number"
                                    placeholder="Permit Number">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_validation_code" class="form-label">Business Validation Code</label>
                                <input type="text" class="form-control" name="business_validation_code"
                                    placeholder="Business Validation Code">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="registration_number" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" name="registration_number"
                                    placeholder="Registration Number">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <input type="text" class="form-control" name="business_address"
                                    placeholder="Business Address">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="business_contact" class="form-label">Business Contact</label>
                                <input type="text" class="form-control" name="business_contact"
                                    placeholder="Business Contact">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="nature_of_business" class="form-label">Nature of Business</label>
                                <input type="text" class="form-control" name="nature_of_business"
                                    placeholder="Nature of Business">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="tax_identification_number" class="form-label">Tax Identification
                                    Number</label>
                                <input type="text" class="form-control" name="tax_identification_number"
                                    placeholder="Tax Identification Number">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="establishment_date" class="form-label">Establishment Date</label>
                                <input type="date" class="form-control" name="establishment_date"
                                    placeholder="Establishment Date">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="citizen_account_number" class="form-label">Citizen Account Number</label>
                                <select class="form-control" id="citizen_account_number" name="citizen_account_number">
                                    <option disabled selected>Select Citizen Account Number</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->first_name }} - {{ $customer->nia_number }} -
                                            {{ $customer->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="bus_account_number" class="form-label">Business Account Number</label>
                                <input type="text" class="form-control" name="bus_account_number"
                                    placeholder="Business Account Number">
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="assembly" class="form-label">Select An Assembly Where Business is
                                        Located</label>
                                    <select class="form-control @error('assembly_code') is-invalid @enderror"
                                        id="assembly" name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}">{{ $assembly->name }}
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
                                <label for="division_id" class="form-label">Property Division</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled selected>Select Division</option>
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
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
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

                            <div class="col-sm-6 mb-3">
                                <label for="status_of_business" class="form-label">Status of Business</label>
                                <select class="form-control @error('status_of_business') is-invalid @enderror"
                                    id="status_of_business" name="status_of_business" required>
                                    <option disabled selected>Select Status of business</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">In Active</option>
                                </select>

                                @error('status_of_business')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Save</button>
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
