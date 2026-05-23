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
                            <i class="ri-settings-5-line me-2"></i> MMDA Setup - Configurations
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            Edit your MMDA here.
                        </p>
                    </div>
                    @can('assemblies.create')
                        <div>
                            <a href="{{ route('mmdas.index') }}" class="btn btn-primary btn-sm ms-2">
                                <i class="ri-arrow-go-back-line"></i> Back
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('mmdas.update', $mmda) }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="region_id" class="form-label">Region</label>
                                <select class="form-control @error('region_id') is-invalid @enderror" id="region_id"
                                    name="region_id" required>
                                    <option disabled selected>Select Region</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ old('region_id', $mmda->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}</option>
                                    @endforeach
                                </select>

                                @error('region_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="region_code" class="form-label">Region Code</label>
                                <input type="text" class="form-control @error('region_code') is-invalid @enderror"
                                    id="region_code" placeholder="Enter region code" name="region_code"
                                    value="{{ $mmda->region_code }}" required>

                                @error('region_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    id="assembly_code" placeholder="Enter assembly code" name="assembly_code"
                                    value="{{ $mmda->assembly_code }}" required>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_name" class="form-label">Assembly Name</label>
                                <input type="text" class="form-control @error('assembly_name') is-invalid @enderror"
                                    id="assembly_name" placeholder="Enter assembly name" name="assembly_name"
                                    value="{{ $mmda->assembly_name }}" required>

                                @error('assembly_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_id" class="form-label">Assembly ID</label>
                                <input type="text" class="form-control @error('assembly_id') is-invalid @enderror"
                                    id="assembly_id" placeholder="Enter division name" name="assembly_id"
                                    value="{{ $mmda->assembly_id }}" required>

                                @error('assembly_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_category" class="form-label">Assembly Category</label>
                                <select class="form-control @error('assembly_category') is-invalid @enderror"
                                    id="assembly_category" name="assembly_category" required>
                                    <option disabled selected>Select Assembly Category</option>
                                    <option value="Municipal"
                                        {{ old('assembly_category', $mmda->assembly_category) == 'Municipal' ? 'selected' : '' }}>
                                        Municipal
                                    </option>
                                    <option value="Metropolitan"
                                        {{ old('assembly_category', $mmda->assembly_category) == 'Metropolitan' ? 'selected' : '' }}>
                                        Metropolitan</option>
                                    <option value="District"
                                        {{ old('assembly_category', $mmda->assembly_category) == 'District' ? 'selected' : '' }}>
                                        District</option>
                                </select>

                                @error('assembly_category')
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
    </div>
@endsection

@section('page-scripts')
@endsection
