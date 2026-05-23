@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">

                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-user-settings-line me-2"></i> Revenue Stream Type Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are About to Edit A Revenue Stream Type Record from your
                                central database repository.
                            </p>

                        </div>
                            <a href="{{ route('revenue-stream-types.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>
                    </div>
                </div>
            </div>

            <div class="card">


                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('revenue-stream-types.update', $revenueStreamType) }}">
                        @csrf

                        <div class="col-md-12 mb-3">
                            <label for="block_code" class="form-label">Revenue Stream Type Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter revenue stream type name"
                                value="{{ old('name', $revenueStreamType->name) }}">

                            @error('name')
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
@endsection

@section('page-scripts')
@endsection
