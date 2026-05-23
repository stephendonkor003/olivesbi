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
                                <i class="ri-wallet-3-line me-2"></i> Bill Adjustment Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are viewing bill adjustment record in your
                                central database repository.
                            </p>
                        </div>

                        @can('bills.create')
                            <a href="{{ route('bill-adjustments.index') }}" class="btn btn-sm btn-primary">
                                <i class="ri-add-line me-1"></i> Back
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('bill-adjustments.update', $billAdjustment) }}">
                        @csrf

                        <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                        <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Revenue Stream Type</label>
                            <input type="text" class="form-control @error('revenue_stream_type_id') is-invalid @enderror"
                                name="revenue_stream_type_id" placeholder="Revenue Stream Type"
                                value="{{ $billAdjustment->revenueStreamType->name ?? '' }}" readonly>

                            @error('revenue_stream_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="is_rated" class="form-label">Discount Year</label>
                            <input type="text" class="form-control @error('discount_year') is-invalid @enderror"
                                name="discount_year" placeholder="Assembly Code"
                                value="{{ $billAdjustment->discount_year }}" readonly>

                            @error('discount_year')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input1">
                            <label for="assembly" class="form-label">Discount Amount</label>
                            <input type="text" class="form-control @error('discount_amount') is-invalid @enderror"
                                name="discount_amount" placeholder="Discount Amount (Fixed Value Discount)"
                                value="{{ $billAdjustment->discount_amount }}" readonly>

                            @error('discount_amount')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input1">
                            <label for="assembly" class="form-label">Discount Type</label>
                            <input type="text" class="form-control @error('discount_type') is-invalid @enderror"
                                name="discount_type" placeholder="Discount Type (Fixed Value Discount)"
                                value="{{ $billAdjustment->discount_type }}" readonly>

                            @error('discount_type')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="assembly" class="form-label">Assembly</label>
                            <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                name="assembly_code" placeholder="Assembly Code"
                                value="{{ $billAdjustment->assembly->name ?? '' }}" readonly>

                            @error('assembly_code')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="division_id" class="form-label">Division</label>
                            <input type="text" class="form-control @error('division_id') is-invalid @enderror"
                                name="division_id" placeholder="Division ID"
                                value="{{ $billAdjustment->division->name ?? '' }}" readonly>

                            @error('division_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="block_id" class="form-label">Block</label>
                            <input type="text" class="form-control @error('block_id') is-invalid @enderror"
                                name="block_id" placeholder="Assembly Code"
                                value="{{ $billAdjustment->block->name ?? '' }}" readonly>

                            @error('block_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input5">
                            <label for="erms_id" class="form-label">ERMS ID</label>
                            <input type="text" class="form-control @error('erms_id') is-invalid @enderror"
                                name="erms_id" placeholder="ERMS ID" value="{{ $billAdjustment->erms_id }}" readonly>

                            @error('erms_id')
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
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=' . time()) }}"></script>
@endsection
