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
                            <i class="ri-map-pin-line me-2"></i> Zones Settings
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing A Zone Record from your
                            central database repository.
                        </p>
                    </div>
                    @can('zones.create')
                        <a href="{{ route('zones.index') }}" class="btn btn-sm btn-primary">
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
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('zones.update', $zone) }}">
                            @csrf

                            <div class="col-md-12 mb-3">
                                <label for="block_code" class="form-label">Zone Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Zone name" value="{{ $zone->name }}"
                                    readonly>

                                @error('name')
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
@endsection
