@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    @php
        $name = '';

        $firstname = $customerSupport->citizen->first_name ?? '';
        $lastname = $customerSupport->citizen->last_name ?? '';
        $name = $firstname . ' ' . $lastname;
    @endphp

    <div class="container-fluid">
        <div class="card">
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-customer-service-2-line"></i>Support Section
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            Easily manage Supports Records in one section.
                        </p>
                    </div>

                    <a href="{{ route('customer-supports.index') }}" class="btn btn-primary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('customer-supports.update', $customerSupport) }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror"
                                    id="description" placeholder="Enter division code" name="description"
                                    value="{{ $customerSupport->description }}" readonly>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="Pending"
                                        {{ old('status', $customerSupport->status) == 'Pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="InProgress"
                                        {{ old('status', $customerSupport->status) == 'InProgress' ? 'selected' : '' }}>In
                                        Progress
                                    </option>
                                    <option value="Resolved"
                                        {{ old('status', $customerSupport->status) == 'Resolved' ? 'selected' : '' }}>
                                        Resolved
                                    </option>
                                    <option value="Closed"
                                        {{ old('status', $customerSupport->status) == 'Closed' ? 'selected' : '' }}>Closed
                                    </option>
                                </select>

                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="response" class="form-label">Response</label>
                                <textarea class="form-control @error('response') is-invalid @enderror" id="response" name="response"
                                    placeholder="Enter Response"></textarea>

                                @error('response')
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
