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
                                <i class="ri-user-settings-line me-2"></i> Rate Payer Type Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are viewing A Selected Rate Payer Types / Category Record from your
                                central database repository.
                            </p>

                        </div>
                        @can('customer-types.create')
                            <a href="{{ route('customer-types.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>
                        @endcan


                    </div>
                </div>
            </div>

            <div class="card">
                </br>


                <form class="row g-3 needs-validation" method="POST"
                    action="{{ route('customer-types.update', $customerType) }}">
                    @csrf

                    <div class="col-md-6 mb-3">
                        <label for="block_code" class="form-label">Customer Type Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" placeholder="Enter customer type name" value="{{ $customerType->name }}"
                            readonly>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control @error('status') is-invalid @enderror" id="status"
                            name="status" placeholder="Enter status" value="{{ $customerType->status }}" readonly>

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
@endsection

@section('page-scripts')
@endsection
