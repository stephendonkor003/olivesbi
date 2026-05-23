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
                            <i class="ri-wallet-3-line me-2"></i> BOP Bills Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are about to generate divisional BOP bills record into your
                            central database repository.
                        </p>
                    </div>

                    <a href="{{ route('bills.bus.index') }}" class="btn btn-sm btn-primary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="POST" action="{{ route('bills.bus.divisionStore') }}">
                                @csrf

                                <div class="row">

                                    <div class="alert alert-warning alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                            class="me-2">
                                            <polyline points="9 11 12 14 22 4"></polyline>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                        </svg>
                                        <strong>TOTAL BUSINESSES AVAILABLE FOR BILLING: {{ $total }}</strong>
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label">Bill Year</label>
                                        <select class="form-control form-select @error('bills_year') is-invalid @enderror"
                                            name="bills_year">
                                            <option disabled selected>Select Bill Year</option>
                                            @for ($i = 2010; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>

                                        @error('bills_year')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="division_id" class="form-label">Business Division</label>
                                        <select class="form-control @error('division_id') is-invalid @enderror"
                                            id="division_id" name="division_id" required>
                                            <option disabled selected>Select Division</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->division_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('division_id')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">Generate Invoice</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/bill.js?v1=1234') }}"></script>
@endsection
