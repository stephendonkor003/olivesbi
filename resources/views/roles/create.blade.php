@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 active-p">
                <div class="card">
                    <!-- HEADER BLOCK -->
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-shield-user-line me-2"></i> Create New Role
                                </h4>
                                <p class="mb-0 text-muted fs-14">
                                    Define a new system role and assign access permissions accordingly.
                                </p>
                            </div>

                            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back to Roles
                            </a>
                        </div>
                    </div>
                    @if (session()->has('status'))
                        <div class="alert alert-error alert-dismissible fade show m-4">
                            <strong>{{ session('status') }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
                <div class="card">
                    <!-- FORM BODY -->
                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('roles.store') }}">
                            @csrf

                            <!-- Role Name -->
                            <div class="mb-4 col-md-12">
                                <label class="form-label">Role Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter Role Name" name="name" id="name" required>

                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Permissions -->
                            <div class="mb-4 col-md-12">
                                <label class="form-label">Permissions</label>
                                <select data-placeholder="Select Permissions" class="form-control js-example-basic-multiple @error('permissions') is-invalid @enderror" name="permissions[]" id="permissions" multiple>
                                    @foreach($permissions as $permission)
                                        <option value="{{$permission->name}}">@lang('role.' . $permission->name)</option>
                                    @endforeach
                                </select>

                                @error('permissions')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Save Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- card -->
        </div>
    </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{asset('assets/libs/select2/dist/js/select2.full.js')}}"></script>
    <script src="{{asset('assets/js/select2.js')}}"></script>
@endsection
