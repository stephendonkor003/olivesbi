@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">View Business Operating Permit Rate</h4>
                        </div>

                        <div>
                            <a href="{{ route('rates.bus.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('rates.bus.update', $rate) }}">
                            @csrf

                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">

                            <div class="col-sm-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                <input type="text" class="form-control @error('assemly_code') is-invalid @enderror"
                                    id="assemly_code" name="assemly_code" placeholder="Assemly_code"
                                    value="{{ $rate->assembly->name ?? '' }}" readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="zone_id" class="form-label">Zone</label>
                                <input type="text" class="form-control @error('zone_id') is-invalid @enderror"
                                    id="zone_id" name="zone_id" placeholder="Zone_id"
                                    value="{{ $rate->zone->name ?? '' }}" readonly>

                                @error('zone_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="property_use_id" class="form-label">Property use</label>
                                <input type="text" class="form-control @error('property_use_id') is-invalid @enderror"
                                    id="property_use_id" name="property_use_id" placeholder="Property_use_id"
                                    value="{{ $rate->propertyUse->name ?? '' }}" readonly>

                                @error('property_use_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="amount">Amount</label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" placeholder="Amount" value="{{ $rate->amount }}" readonly>

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                {{-- <button type="submit" class="btn btn-primary">Update</button> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>
@endsection
