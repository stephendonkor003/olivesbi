@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fileinput.min.css') }}">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Import Block Buildings</h4>
                        </div>

                        <div>
                            <a href="{{ route('properties.downloadTemplate') }}"
                                class="btn btn-success btn-sm ms-2">Download Blocks Template</a>

                            <a href="{{ route('buildings.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                        </div>
                    </div>


                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('buildings.store') }}"
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

                            <div class="col-md-12 mb-3">
                                <label for="first_name" class="mb-3">Upload Blocks Building Excel File</label>
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
