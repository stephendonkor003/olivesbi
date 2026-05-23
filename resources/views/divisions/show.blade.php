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
                            <i class="ri-community-line me-2"></i> Division Settings
                        </h4>




                        <p class="mb-0 text-muted fs-14">
                            You are Viewing A Division Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('divisions.create')
                        <a href="{{ route('divisions.index') }}" class="btn btn-sm btn-primary">
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
                            action="{{ route('divisions.update', $division) }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="division_code">Division Code</label>
                                <input type="text" class="form-control @error('division_code') is-invalid @enderror"
                                    id="division_code" placeholder="Enter division code" name="division_code"
                                    value="{{ $division->division_code }}" readonly>

                                @error('division_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="division_name">Division Name</label>
                                <input type="text" class="form-control @error('division_name') is-invalid @enderror"
                                    id="division_name" placeholder="Enter division name" name="division_name"
                                    value="{{ $division->division_name }}" readonly>

                                @error('division_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    id="assembly_code" placeholder="Enter division name" name="assembly_code"
                                    value="{{ $division->assembly->name ?? '' }} ({{ $division->assembly_code }})" readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <input type="text" class="form-control @error('status') is-invalid @enderror"
                                    id="status" placeholder="Enter division name" name="status"
                                    value="{{ $division->status }}" readonly>

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
@endsection
