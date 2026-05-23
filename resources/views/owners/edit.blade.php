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
                            <i class="ri-user-settings-line me-2"></i> Rate Payer - Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are About to Edit Rate Payer Record from your
                            central database repository.
                        </p>
                    </div>

                    <a href="{{ route('owners.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('owners.update', $owner) }}">
                            @csrf
                            @method('PUT')

                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                        stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                        class="me-2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    <strong>{{ session('error') }}</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span>
                                    </button>
                                </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    name="first_name" placeholder="First Name" value="{{ old('first_name', $owner->fname) }}">

                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    name="last_name" placeholder="Last Name" value="{{ old('last_name', $owner->lname) }}">

                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                    name="email" placeholder="Email Address" value="{{ old('email', $owner->email) }}">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option disabled @selected(old('gender', $owner->gender) == '')>Select Gender</option>
                                    <option value="M" @selected(old('gender', $owner->gender) == 'M')>Male</option>
                                    <option value="F" @selected(old('gender', $owner->gender) == 'F')>Female</option>
                                </select>

                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Phone No.</label>
                                <input type="text" class="form-control @error('phone_no_primary') is-invalid @enderror"
                                    name="phone_no_primary" placeholder="Phone Number"
                                    value="{{ old('phone_no_primary', $owner->phone_no_primary) }}">

                                @error('phone_no_primary')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tin_no" class="form-label">TIN No.</label>
                                <input type="text" class="form-control @error('tin_no') is-invalid @enderror"
                                    name="tin_no" placeholder="TIN No." value="{{ old('tin_no', $owner->tin_no) }}">

                                @error('tin_no')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="house_no" class="form-label">House No.</label>
                                <input type="text" class="form-control @error('house_no') is-invalid @enderror"
                                    name="house_no" placeholder="House Number" value="{{ old('house_no', $owner->house_no) }}">

                                @error('house_no')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="residential_address" class="form-label">Residential address</label>
                                <input type="text"
                                    class="form-control @error('residential_address') is-invalid @enderror"
                                    name="residential_address" placeholder="Residential Address"
                                    value="{{ old('residential_address', $owner->residential_address) }}">

                                @error('residential_address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="postal_address" class="form-label">Postal address</label>
                                <input type="text" class="form-control @error('postal_address') is-invalid @enderror"
                                    name="postal_address" placeholder="Postal Address"
                                    value="{{ old('postal_address', $owner->postal_address) }}">

                                @error('postal_address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="digital_address" class="form-label">Digital address</label>
                                <input type="text" class="form-control @error('digital_address') is-invalid @enderror"
                                    name="digital_address" placeholder="Digital Address"
                                    value="{{ old('digital_address', $owner->digital_address) }}">

                                @error('digital_address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="assembly" class="form-label">Assembly</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly" disabled>
                                        <option value="{{ Auth::user()->assembly_code }}" selected>
                                            {{ $assemblies->first()->name ?? 'N/A' }} Assembly
                                        </option>
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                        name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                @selected(old('assembly_code', $owner->assembly_code) == $assembly->assembly_code)>
                                                {{ $assembly->name }}
                                                Assembly
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
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
