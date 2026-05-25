@extends('layout.base')

@section('page-styles')
    <style>
        /* Adjust the profile card size */
        .profile-card {
            width: 50%;
            height: auto;
            margin: 20px auto;
            background-color: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
        }

        .main-profile-cover {
            background-color: #d3d3d3;
            padding: 40px 20px;
            border-radius: 15px 15px 0 0;
        }

        .text-fixed-white {
            color: white;
        }

        .bg-black-transparent {
            background: rgba(72, 125, 110, 0.5);
        }

        .profile-info {
            margin-top: -20px;
        }

        .user-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .user-info {
            padding-left: 15px;
            text-align: left;
        }

        .user-info p {
            margin-bottom: 5px;
        }

        .user-stats .stat-box {
            text-align: center;
            padding: 10px;
        }

        .user-stats .stat-box i {
            font-size: 25px;
            padding: 8px;
            background-color: #f0f1f6;
            border-radius: 50%;
        }

        .user-stats .stat-box p {
            margin-bottom: 0;
        }

        /* Styling for the inner profile card */
        .inner-profile-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            border-radius: 10px 10px 0 0;
            padding: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .user-stats {
            margin-top: 10px;
        }
    </style>
@endsection

@section('page-content')
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-shield-user-line me-2"></i> User Management
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            Hey there, you are viewing the profile for <strong>{{ $user->name }}</strong>
                        </p>
                    </div>

                    @can('users.create')
                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                            <i class="ri-arrow-go-back-line"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="assembly_url" url="{{ route('assembly.fetch') }}">

                        <!-- First Row -->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Enter full name" name="name" value="{{ old('name', $user->name) }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Enter email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Enter password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Second Row -->
                        <div class="mb-4 col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Password confirmation">
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Enter phone number" name="phone" value="{{ old('phone', $user->phone) }}"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male"
                                    {{ old('gender', $user->gender ?? '') == 'Male' ? 'selected' : '' }}>
                                    Male</option>
                                <option value="Female"
                                    {{ old('gender', $user->gender ?? '') == 'Female' ? 'selected' : '' }}>
                                    Female</option>
                            </select>

                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Access Level</label>
                            <select class="form-control @error('access_level') is-invalid @enderror" name="access_level"
                                required>
                                <option value="">Select Access Level</option>
                                @if (\Illuminate\Support\Str::contains(\Auth::user()->access_level, 'Assembly'))
                                    <option value="Assembly_Administrator"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Administrator' ? 'selected' : '' }}>
                                        Assembly Administrator
                                    </option>
                                    <option value="Assembly_Supervisor"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Supervisor' ? 'selected' : '' }}>
                                        Assembly Supervisor
                                    </option>
                                    <option value="Assembly_Agent"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Agent' ? 'selected' : '' }}>
                                        Assembly Agent
                                    </option>
                                    <option value="customer"
                                        {{ old('access_level', $user->access_level) == 'customer' ? 'selected' : '' }}>
                                        Customer
                                    </option>
                                @else
                                    <option value="Super_User"
                                        {{ old('access_level', $user->access_level) == 'Super_User' ? 'selected' : '' }}>
                                        Super User
                                    </option>
                                    <option value="Assembly_Administrator"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Administrator' ? 'selected' : '' }}>
                                        Assembly Administrator
                                    </option>
                                    <option value="Assembly_Supervisor"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Supervisor' ? 'selected' : '' }}>
                                        Assembly Supervisor
                                    </option>
                                    <option value="Assembly_Agent"
                                        {{ old('access_level', $user->access_level) == 'Assembly_Agent' ? 'selected' : '' }}>
                                        Assembly Agent
                                    </option>
                                    <option value="GOG_Administrator"
                                        {{ old('access_level', $user->access_level) == 'GOG_Administrator' ? 'selected' : '' }}>
                                        GRA Administrator
                                    </option>
                                    <option value="customer"
                                        {{ old('access_level', $user->access_level) == 'customer' ? 'selected' : '' }}>
                                        Customer
                                    </option>
                                @endif
                            </select>

                            @error('access_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Third Row -->
                        <div class="mb-3 col-md-4">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role">
                                <option value="">Select User Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ in_array($role->id, $userRole) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Region</label>
                            @if(!empty(Auth::user()->regional_code))
                                <select class="form-control @error('regional_code') is-invalid @enderror" disabled>
                                    <option value="{{ Auth::user()->regional_code }}" selected>
                                        {{ $regions->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="regional_code" value="{{ Auth::user()->regional_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your region</small>
                            @elseif(!empty($derivedRegionalCode))
                                <select class="form-control @error('regional_code') is-invalid @enderror" disabled>
                                    <option value="{{ $derivedRegionalCode }}" selected>
                                        {{ $regions->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="regional_code" value="{{ $derivedRegionalCode }}">
                                <small class="text-muted d-block mt-1">Derived from your assembly</small>
                            @else
                                <select class="form-control @error('regional_code') is-invalid @enderror" name="regional_code">
                                    <option disabled selected>Select Region</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->regional_code }}"
                                            {{ old('regional_code', $user->regional_code) == $region->regional_code ? 'selected' : '' }}>
                                            {{ $region->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('regional_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4"  id="assembly_code_field">
                            <label for="assembly_code" class="form-label">Assembly Code</label>
                            @if(!empty(Auth::user()->assembly_code))
                                <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code" disabled>
                                    <option value="{{ Auth::user()->assembly_code }}" selected>
                                        {{ $assemblies->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                            @else
                                <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly_code" name="assembly_code">
                                    <option value="">Select Assembly</option>
                                    @foreach ($assemblies as $assembly)
                                        <option value="{{ $assembly->assembly_code }}"
                                            {{ old('assembly_code', $user->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                            {{ $assembly->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('assembly_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 hidden" id="division_code_field">
                            <label for="division_code" class="form-label">Division Code</label>
                            @if(!empty(Auth::user()->division_code))
                                <select class="form-control @error('division_code') is-invalid @enderror" disabled>
                                    <option value="{{ Auth::user()->division_code }}" selected>
                                        {{ $divisions->firstWhere('division_code', Auth::user()->division_code)->division_name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="division_code" value="{{ Auth::user()->division_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your division</small>
                            @else
                                <select class="form-control @error('division_code') is-invalid @enderror" name="division_code">
                                    <option value="">Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->code }}"
                                            {{ old('division_code', $user->division_code) == $division->code ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('division_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fourth Row -->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                <option value="Active" {{ old('status', $user->status) == 'Active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="InActive"
                                    {{ old('status', $user->status) == 'InActive' ? 'selected' : '' }}>In
                                    Active</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
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
    <script src="{{ asset('assets/js/user.js?v=' . time()) }}"></script>

    <script>
        document.getElementById('access_level').addEventListener('change', function() {
            showFields(this.value)
        });

        @if(old('division_code', $user->division_code))
        showFields({{old('division_code', $user->division_code)}})
        @endif

        function showFields(value) {
            var assemblyField = document.getElementById('assembly_code_field');
            var divisionField = document.getElementById('division_code_field');
            if (value == 'Assembly_Administrator') {
                assemblyField.classList.remove('hidden')
                divisionField.classList.add('hidden')
            } else if (value == 'Assembly_Agent' || value == 'Assembly_Supervisor') {
                assemblyField.classList.remove('hidden')
                divisionField.classList.remove('hidden')
            } else {
                assemblyField.classList.add('hidden')
                divisionField.classList.add('hidden')
            }
        }
    </script>
@endsection
