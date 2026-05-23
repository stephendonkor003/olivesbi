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
                            You are Adding A New Division Record to your
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
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('divisions.store') }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="division_code">Division Code</label>
                                <input type="text" class="form-control @error('division_code') is-invalid @enderror"
                                    id="division_code" placeholder="Enter division code" name="division_code" required>

                                @error('division_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="division_name">Division Name</label>
                                <input type="text" class="form-control @error('division_name') is-invalid @enderror"
                                    id="division_name" placeholder="Enter division name" name="division_name" required>

                                @error('division_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code">Assembly Code</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" disabled>
                                        <option value="{{ Auth::user()->assembly_code }}" selected>
                                            {{ $assemblies->first()->name ?? 'N/A' }} ({{ Auth::user()->assembly_code }})
                                        </option>
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code"
                                        name="assembly_code" required>
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}">{{ $assembly->name }}
                                                ({{ $assembly->assembly_code }})
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

                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option disabled selected>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">In Active</option>
                                </select>

                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
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
@endsection
