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
                                Manage and create your BOP revenue stream here.
                            </p>
                        </div>
                        @can('customer-types.create')
                            <a href="{{ route('revenue-streams.indexBOP') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('revenue-streams.updateBOP', $revenueStream) }}">
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

                        <div class="col-md-6 mb-2" id="input3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                placeholder="Enter Code" value="{{ $revenueStream->code }}" readonly>

                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="main_item_id" class="form-label">Main Item</label>
                            <input type="text" class="form-control @error('main_item_id') is-invalid @enderror"
                                name="main_item_id" placeholder="Enter Code"
                                value="{{ $revenueStream->mainItem->name ?? '' }}" readonly>

                            @error('main_item_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="sub_item_id" class="form-label">Sub Item</label>
                            <input type="text" class="form-control @error('sub_item_id') is-invalid @enderror"
                                name="sub_item_id" placeholder="Enter Code"
                                value="{{ $revenueStream->subItem->name ?? '' }}" readonly>

                            @error('sub_item_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input6">
                            <label for="assembly" class="form-label">Item Description</label>
                            <input type="text" class="form-control @error('description_id') is-invalid @enderror"
                                name="description_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->itemDescription->name ?? 'N/A' }}" readonly>

                            @error('description_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input6">
                            <label for="assembly" class="form-label">Category</label>
                            <input type="text" class="form-control @error('category_id') is-invalid @enderror"
                                name="category_id" placeholder="Rate Impost"
                                value="{{ $revenueStream->category->name ?? 'N/A' }}" readonly>

                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input5">
                            <label for="flat_rate" class="form-label">Amount</label>
                            <input type="text" class="form-control @error('flat_rate') is-invalid @enderror"
                                name="flat_rate" placeholder="Flat Rate" value="{{ $revenueStream->flat_rate }}" readonly>

                            @error('flat_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input6">
                            <label for="assembly" class="form-label">Duration</label>
                            <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                name="duration" placeholder="Rate Impost" value="{{ $revenueStream->duration ?? 'N/A' }}"
                                readonly>

                            @error('duration')
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
                            {{-- <button type="submit" class="btn btn-primary">Update</button> --}}
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
