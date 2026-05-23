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
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('revenue-streams.store') }}">
                        @csrf

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Revenue Stream Type</label>
                            <input type="text" class="form-control @error('revenue_stream_type_id') is-invalid @enderror"
                                name="revenue_stream_type_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->revenueStreamType->name ?? '' }}" readonly>

                            @error('revenue_stream_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="is_rated" class="form-label">Assessed Property?</label>
                            <input type="text" class="form-control @error('is_rated') is-invalid @enderror"
                                name="is_rated" placeholder="Rate Impost" value="{{ $revenueStream->is_rated }}" readonly>

                            @error('is_rated')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input1">
                            <label for="assembly" class="form-label">Zone Type</label>
                            <input type="text" class="form-control @error('zone_type_id') is-invalid @enderror"
                                name="zone_type_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->zoneType->name ?? '' }}" readonly>

                            @error('zone_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input2">
                            <label for="assembly" class="form-label">Property Classes</label>
                            <input type="text" class="form-control @error('property_classes_id') is-invalid @enderror"
                                name="property_classes_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->propertyClasses->name ?? '' }}" readonly>

                            @error('property_classes_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input3">
                            <label for="rate_impost" class="form-label">Rate Impost</label>
                            <input type="text" class="form-control @error('rate_impost') is-invalid @enderror"
                                name="rate_impost" placeholder="Rate Impost" value="{{ $revenueStream->rate_impost }}"
                                readonly>

                            @error('rate_impost')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input4">
                            <label for="min_amount" class="form-label">Min Amount</label>
                            <input type="text" class="form-control @error('min_amount') is-invalid @enderror"
                                name="min_amount" placeholder="Rate Impost" value="{{ $revenueStream->min_amount }}"
                                readonly>

                            @error('min_amount')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input5">
                            <label for="flat_rate" class="form-label">Flat Rate</label>
                            <input type="text" class="form-control @error('flat_rate') is-invalid @enderror"
                                name="flat_rate" placeholder="Flat Rate" value="{{ $revenueStream->flat_rate }}" readonly>

                            @error('flat_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input6">
                            <label for="assembly" class="form-label">Property Type</label>
                            <input type="text" class="form-control @error('property_type_id') is-invalid @enderror"
                                name="property_type_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->propertyType->name ?? '' }}" readonly>

                            @error('property_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input7">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                name="description" placeholder="Description" value="{{ $revenueStream->description }}"
                                readonly>

                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input8">
                            <label for="basic_rate" class="form-label">Basic Rate</label>
                            <input type="text" class="form-control @error('basic_rate') is-invalid @enderror"
                                name="basic_rate" placeholder="Basic Rate" value="{{ $revenueStream->basic_rate }}"
                                readonly>

                            @error('basic_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="assembly" class="form-label">Assembly</label>
                                <input type="text" class="form-control @error('assembly-code') is-invalid @enderror"
                                    name="assembly-code" placeholder="Rate Impost"
                                    value="{{ $revenueStream->assembly->name ?? '' }}" readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
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
