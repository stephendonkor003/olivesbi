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
                            <i class="ri-home-gear-line"></i> Use Type Settings
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Editing A Property Use Type Record from your
                            central database repository.
                        </p>
                    </div>
                    @can('property-uses.create')
                        <a href="{{ route('property-users.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-arrow-go-back-line"></i> Back
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
                            action="{{ route('property-users.update', $propertyUser) }}">
                            @csrf


                            <div class="col-md-6 mb-3">
                                <label for="block_code" class="form-label">Property Use Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Property use name"
                                    value="{{ $propertyUser->name }}">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="zone_id" class="form-label">Zone</label>
                                <select class="form-control @error('zone_id') is-invalid @enderror" id="zone_id"
                                    name="zone_id" required>
                                    <option disabled selected>Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $propertyUser->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}</option>
                                    @endforeach
                                </select>

                                @error('zone_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly_code" disabled>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" selected>
                                                {{ $assembly->name }} Assembly
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code"
                                        name="assembly_code" required>
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code', $zone->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                                {{ $assembly->name }}
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
