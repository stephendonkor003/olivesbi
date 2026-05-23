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
                            You are about to generate bulk BOP bills record into your
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
                            <form method="POST" action="{{ route('bills.bus.store') }}">
                                @csrf

                                <input type="hidden" name="preview_url" url="{{ route('bills.bus.preview') }}">

                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label for="assembly_code" class="form-label">Assembly Code</label>
                                        @if(!empty(Auth::user()->assembly_code))
                                            <select class="form-control @error('assembly_code') is-invalid @enderror"
                                                id="assembly_code" disabled>
                                                <option value="{{ Auth::user()->assembly_code }}" selected>
                                                    {{ $assemblies->first()->name ?? 'N/A' }}
                                                </option>
                                            </select>
                                            <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                            <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                        @else
                                            <select class="form-control @error('assembly_code') is-invalid @enderror"
                                                id="assembly_code" name="assembly_code" required>
                                                <option disabled selected>Select Assembly</option>
                                                @foreach ($assemblies as $assembly)
                                                    <option value="{{ $assembly->assembly_code }}">{{ $assembly->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif

                                        @error('assembly_code')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
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
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" id="preview-bills-bus" class="btn btn-secondary">Preview
                                        Bill</button>
                                    <button type="submit" class="btn btn-success">Generate Bill</button>
                                </div>
                            </form>
                        </div>

                        <div id="bill-preview-section" class="mt-4 d-none">
                            <h5>Bill Preview</h5>
                            <div class="table-responsive">
                                <table id="file-export" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Business No</th>
                                            <th>Owner Name</th>
                                            <th>Rate Impost</th>
                                            <th>Flat Rate</th>
                                            <th>Discount</th>
                                            <th>Min Amount</th>
                                            <th>Final Bill</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-tbody">
                                    </tbody>
                                </table>
                            </div>
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
