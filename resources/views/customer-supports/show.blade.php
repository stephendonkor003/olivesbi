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
                                <label for="tax_payer" class="form-label">Tax Payer</label>
                                <input type="text" class="form-control @error('tax_payer') is-invalid @enderror"
                                    id="tax_payer" placeholder="Enter division name" name="tax_payer"
                                    value="{{ $name }}" readonly>

                                @error('tax_payer')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control @error('status') is-invalid @enderror"
                                    id="status" placeholder="Enter status" name="status"
                                    value="{{ $customerSupport->status }}" readonly>

                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_code" class="form-label">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    id="assembly_code" placeholder="Enter division name" name="assembly_code"
                                    value="{{ $customerSupport->assembly->name ?? '' }} ({{ $customerSupport->assembly_code }})"
                                    readonly>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="response" class="form-label">Response</label>
                                <textarea class="form-control @error('response') is-invalid @enderror" id="response" name="response"
                                    placeholder="Enter division name" readonly>{{ $customerSupport->response ?? 'N/A' }}</textarea>

                                @error('response')
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
