@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fileinput.min.css') }}">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-settings-5-line me-2"></i> Revenue Stream Import
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            Import revenue stream records to your central database repository.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('revenue-streams.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-arrow-go-back-line"></i> Back
                        </a>
                        <a href="{{ route('revenue-streams.downloadTemplate') }}" class="btn btn-sm btn-success">
                            <i class="ri-file-download-line me-1"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('revenue-streams.importData') }}"
                            enctype="multipart/form-data">
                            @csrf

                            @if (session()->has('status'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                        stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                        class="me-2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    <strong>{{ session('status') }}</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <span><i class="fa-solid fa-xmark"></i></span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger mt-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li><strong>{{ $error }}</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(empty(Auth::user()->assembly_code))
                                <div class="col-md-12 mb-3">
                                    <label for="assembly_code" class="form-label">Select Assembly <span class="text-danger">*</span></label>
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code"
                                            name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" @selected(old('assembly_code') == $assembly->assembly_code)>
                                                {{ $assembly->name }} Assembly
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                    <small class="text-muted d-block mt-1">Select the assembly you want to import revenue streams for</small>
                                </div>
                            @else
                                <div class="col-md-12 mb-3">
                                    <label for="assembly" class="form-label">Assembly</label>
                                    <select class="form-control" id="assembly" disabled>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" selected>
                                                {{ $assembly->name }} Assembly
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">Revenue streams will be imported to your assembly</small>
                                </div>
                            @endif

                            <!-- Import Instructions -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info border-0">
                                    <h6 class="alert-heading mb-2">
                                        <i class="ri-information-line me-1"></i> Excel File Format Instructions
                                    </h6>
                                    <p class="mb-2">Your Excel file must contain the following columns in order:</p>
                                    <ol class="mb-2 ps-3">
                                        <li><strong>Assessed Property</strong> - Required. Must be either Y/N or YES/NO</li>
                                        <li><strong>Zone Type</strong> - Required. Name of the zone/area type (e.g., "Residential", "Commercial") (will be created if doesn't exist)</li>
                                        <li><strong>Property Class</strong> - Optional. Property classification (will be created if doesn't exist)</li>
                                        <li><strong>Property Type</strong> - Optional. Type of property (will be created if doesn't exist)</li>
                                        <li><strong>Rate Impost</strong> - Optional. Numeric value for rate impost</li>
                                        <li><strong>Min Amount</strong> - Optional. Numeric value for minimum amount</li>
                                        <li><strong>Flat Rate</strong> - Optional. Numeric value for flat rate</li>
                                        <li><strong>Basic Rate</strong> - Required. Numeric value for basic rate</li>
                                        <li><strong>Description</strong> - Optional. Additional description or notes</li>
                                    </ol>
                                    <p class="mb-0">
                                        <i class="ri-lightbulb-line me-1 text-warning"></i>
                                        <strong>Tip:</strong> Download the template above to get started with the correct format.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="first_name" class="mb-3">Upload Revenue Stream Excel File <span class="text-danger">*</span></label>
                                <input class="file" id="file" name="file" type="file" data-show-upload="false"
                                    data-theme="fa" data-max-file-count="" data-max-total-file-count=""
                                    data-initial-preview-as-data="true" , data-initial-preview=""
                                    data-initial-preview-config="" data-required="false" data-overwrite-initial="false"
                                    data-max-file-size="15000" data-browse-label="Browse"
                                    data-browse-icon="<i class='fa fa-folder-open'></i>" />

                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Import Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/lightgallery-all.min.js') }}"></script>
@endsection
