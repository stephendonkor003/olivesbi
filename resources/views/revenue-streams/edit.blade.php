@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    @php use Illuminate\Support\Str; @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="card">

                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-settings-5-line me-2"></i> Revenue Stream Setup - Configurations
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                Manage and create your property revenue stream here.
                            </p>
                        </div>
                        @can('customer-types.create')
                            <a href="{{ route('revenue-streams.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card">


                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('revenue-streams.update', $revenueStream) }}">
                        @csrf

                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="assembly" class="form-label">Assembly</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly" disabled>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" selected>
                                                {{ $assembly->name }} Assembly
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                            name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code', $revenueStream->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
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
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="is_rated" class="form-label">Assessed Property?</label>
                            <select name="is_rated" class="form-control @error('is_rated') is-invalid @enderror">
                                <option disabled selected>Select Assessed Property?</option>
                                <option value="Y"
                                    {{ old('is_rated', $revenueStream->is_rated) == 'Y' ? 'selected' : '' }}>
                                    Yes
                                </option>
                                <option value="N"
                                    {{ old('is_rated', $revenueStream->is_rated) == 'N' ? 'selected' : '' }}>No</option>
                            </select>

                            @error('is_rated')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input1">
                            <label for="assembly" class="form-label">Zone Type</label>
                            <select class="form-control @error('zone_type_id') is-invalid @enderror" id="zone_type_id"
                                name="zone_type_id" data-assembly-dependent="true">
                                <option disabled selected>Select Zone Type</option>
                                @foreach ($zoneTypes as $zoneType)
                                    <option value="{{ $zoneType->id }}" data-assembly-code="{{ $zoneType->assembly_code }}"
                                        {{ old('zone_type_id', $revenueStream->zone_type_id) == $zoneType->id ? 'selected' : '' }}>
                                        {{ $zoneType->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('zone_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input2">
                            <label for="assembly" class="form-label">Property Classes</label>
                            <select class="form-control @error('property_classes_id') is-invalid @enderror"
                                id="property_classes_id" name="property_classes_id" data-assembly-dependent="true">
                                <option disabled selected>Select Property Classes</option>
                                @foreach ($propertyClasses as $propertyClass)
                                    <option value="{{ $propertyClass->id }}" data-assembly-code="{{ $propertyClass->assembly_code }}"
                                        {{ old('property_classes_id', $revenueStream->property_classes_id) == $propertyClass->id ? 'selected' : '' }}>
                                        {{ $propertyClass->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('property_classes_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input3">
                            <label for="rate_impost" class="form-label">Rate Impost</label>
                            <input type="text" class="form-control @error('rate_impost') is-invalid @enderror"
                                name="rate_impost" placeholder="Rate Impost" value="{{ old('rate_impost',$revenueStream->rate_impost) }}">

                            @error('rate_impost')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input4">
                            <label for="min_amount" class="form-label">Min Amount</label>
                            <input type="text" class="form-control @error('min_amount') is-invalid @enderror"
                                name="min_amount" placeholder="Min Amount" value="{{ old('min_amount',$revenueStream->min_amount) }}">

                            @error('min_amount')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input5">
                            <label for="flat_rate" class="form-label">Flat Rate</label>
                            <input type="text" class="form-control @error('flat_rate') is-invalid @enderror"
                                name="flat_rate" placeholder="Flat Rate" value="{{ old('flat_rate', $revenueStream->flat_rate) }}">

                            @error('flat_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input6">
                            <label for="assembly" class="form-label">Property Type</label>
                            <select class="form-control @error('property_type_id') is-invalid @enderror"
                                id="property_type_id" name="property_type_id" data-assembly-dependent="true">
                                <option disabled selected>Select Property Type</option>
                                @foreach ($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->id }}" data-assembly-code="{{ $propertyType->assembly_code }}"
                                        {{ old('property_type_id', $revenueStream->property_type_id) == $propertyType->id ? 'selected' : '' }}>
                                        {{ $propertyType->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('property_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input7">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                name="description" placeholder="Description" value="{{ old('description',$revenueStream->description) }}">

                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input8">
                            <label for="basic_rate" class="form-label">Basic Rate</label>
                            <input type="text" class="form-control @error('basic_rate') is-invalid @enderror"
                                name="basic_rate" placeholder="Basic Rate" value="{{ old('basic_rate',$revenueStream->basic_rate) }}">

                            @error('basic_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/revenue-stream.js?v1=' . time()) }}"></script>
@endsection
