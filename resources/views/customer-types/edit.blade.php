@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <!-- Header Section -->
            <div class="container-fluid mh-auto">
                <div class="card">
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-user-settings-line me-2"></i> Rate Payer Type Management
                                </h4>
                                <p class="mb-0 text-muted fs-14">
                                    You are Viewing all Rate Payer Types / Categories from your central database repository.
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


                <!-- Form Section -->

                <div class="card">
                    </br>
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('customer-types.update', $customerType) }}">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Customer Type Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter customer type name" value="{{ $customerType->name }}"
                                required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-control @error('status') is-invalid @enderror" name="status"
                                required>
                                <option value="Active"
                                    {{ old('status', $customerType->status) == 'Active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="InActive"
                                    {{ old('status', $customerType->status) == 'InActive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-12">
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
