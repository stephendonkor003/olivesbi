@extends('layout.base')

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
                            Easily Add Users In One Section.
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-body">
                    <form id="userForm" class="row g-3 needs-validation" method="POST" action="{{ route('users.store') }}"
                        novalidate>
                        @csrf

                        <input type="hidden" name="assembly_url" url="{{ route('assembly.fetch') }}">

                        <!-- Form Fields -->
                        <div class="col-md-4">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Full Name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email Address" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Password confirmation" required>

                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Phone Number" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender"
                                required>
                                <option value="" @selected(old('gender', '') == '')>Select User Gender</option>
                                <option value="Male" @selected(old('gender', '') == 'Male')>Male</option>
                                <option value="Female" @selected(old('gender', '') == 'Female')>Female</option>
                            </select>

                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Access Level</label>
                            <select class="form-control @error('access_level') is-invalid @enderror" name="access_level"
                                id="access_level" required>
                                <option value="" @selected(old('access_level', '') == '')>Select Access Level</option>
                                @if (\Illuminate\Support\Str::contains(\Auth::user()->access_level, 'Assembly'))
                                    <option value="Assembly_Supervisor" @selected(old('access_level', '') == 'Assembly_Supervisor')>Assembly Supervisor</option>
                                    <option value="Assembly_Agent" @selected(old('access_level', '') == 'Assembly_Agent')>Assembly Agent</option>
                                @else
                                    <option value="Super_User" @selected(old('access_level', '') == 'Super_User')>Super User</option>
                                    <option value="Assembly_Administrator" @selected(old('access_level', '') == 'Assembly_Administrator')>Assembly Administrator</option>
                                    <option value="Assembly_Supervisor" @selected(old('access_level', '') == 'Assembly_Supervisor')>Assembly Supervisor</option>
                                    <option value="Assembly_Agent" @selected(old('access_level', '') == 'Assembly_Agent')>Assembly Agent</option>
                                    <option value="GOG_Administrator" @selected(old('access_level', '') == 'GOG_Administrator')>GOG Administrator</option>
                                @endif
                            </select>
                            @error('access_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role"
                                required>
                                <option value="">Select User Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>

                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Region</label>
                            @if(!empty(Auth::user()->regional_code))
                                <select class="form-control" disabled>
                                    <option value="{{ Auth::user()->regional_code }}" selected>
                                        {{ $regions->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="regional_code" value="{{ Auth::user()->regional_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your region</small>
                            @elseif(!empty($derivedRegionalCode))
                                <select class="form-control" disabled>
                                    <option value="{{ $derivedRegionalCode }}" selected>
                                        {{ $regions->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="regional_code" value="{{ $derivedRegionalCode }}">
                                <small class="text-muted d-block mt-1">Derived from your assembly</small>
                            @else
                                <select class="form-control" name="regional_code">
                                    <option disabled selected>Select Region</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->regional_code }}"
                                            {{ old('regional_code') == $region->regional_code ? 'selected' : '' }}>
                                            {{ $region->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="col-md-4"  id="assembly_code_field">
                            <label class="form-label">Assembly</label>
                            @if(!empty(Auth::user()->assembly_code))
                                <select class="form-control" disabled>
                                    <option value="{{ Auth::user()->assembly_code }}" selected>
                                        {{ $assemblies->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                                <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                            @else
                                <select class="form-control @error('assembly_code') is-invalid @enderror" name="assembly_code">
                                    <option value="" disabled {{ !old('assembly_code') ? 'selected' : '' }}>Select Assembly</option>
                                    @if(old('assembly_code'))
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}"
                                                {{ old('assembly_code') == $assembly->assembly_code ? 'selected' : '' }}>
                                                {{ $assembly->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                            @error('assembly_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 hidden" id="division_code_field">
                            <label class="form-label">Division</label>
                            @if(!empty(Auth::user()->division_code))
                                <select class="form-control" disabled>
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
                                            {{ old('division_code') == $division->code ? 'selected' : '' }}>
                                            {{ $division->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('division_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="InActive" {{ old('status') == 'InActive' ? 'selected' : '' }}>In Active
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save User</button>
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

        @if(old('division_code'))
            showFields({{old('division_code')}})
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

    <script>
        let startTime = new Date().getTime();

        function formatTime(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let sec = seconds % 60;

            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
        }

        function updateTimeSpent() {
            let currentTime = new Date().getTime();
            let elapsedTime = Math.floor((currentTime - startTime) / 1000);
            document.getElementById('timeSpent').textContent = formatTime(elapsedTime);
        }

        setInterval(updateTimeSpent, 1000);
    </script>
@endsection
