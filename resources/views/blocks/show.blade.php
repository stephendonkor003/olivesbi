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
                            <i class="ri-building-4-line me-2"></i> Blocks Settings
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing A Block within Division Record from your
                            central database repository.
                        </p>
                    </div>
                    @can('blocks.create')
                        <a href="{{ route('blocks.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-arrow-go-back-line me-1"></i> Back
                        </a>
                    @endcan
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('blocks.update', $block) }}">
                            @csrf

                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">

                            <div class="col-md-6 mb-3">
                                <label for="block_code">Block Code</label>
                                <input type="text" class="form-control @error('block_code') is-invalid @enderror"
                                    id="block_code" name="block_code" placeholder="Enter block code"
                                    value="{{ $block->block_code }}" readonly>

                                @error('block_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="block_name">Block Name</label>
                                <input type="text" class="form-control @error('block_name') is-invalid @enderror"
                                    id="block_name" name="block_name" placeholder="Enter block name"
                                    value="{{ $block->block_name }}" readonly>

                                @error('block_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    id="assembly_code" placeholder="Enter division name" name="assembly_code"
                                    value="{{ $block->assembly->name ?? '' }} ({{ $block->assembly_code }})" readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="division_name">Division Name</label>
                                <input type="text" class="form-control @error('division_code') is-invalid @enderror"
                                    id="division_code" placeholder="Enter division code" name="division_code"
                                    value="{{ $block->division->division_name ?? '' }}" readonly>

                                @error('division_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <input type="text" class="form-control @error('status') is-invalid @enderror"
                                    id="status" placeholder="Enter status" name="status" value="{{ $block->status }}"
                                    readonly>

                                @error('status')
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
    <script src="{{ asset('assets/js/general/app.js') }}"></script>
@endsection
