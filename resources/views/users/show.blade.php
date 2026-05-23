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
            background: rgba(0, 0, 0, 0.5);
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
    <div class="container-fluid">
        <!-- Wrap columns with a row for proper Bootstrap grid usage -->
        <div class="row">
            <div class="col-xl-12 active-p">
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

                <!-- Inner Profile Card -->
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="row g-3 needs-validation">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">E-mail</label>
                                    <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Date Of Joining</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control"
                                            value="{{ $user->created_at->format('Y-m-d') }}" readonly>
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Gender</label>
                                    <input type="text" class="form-control"
                                        value="{{ !empty($user->gender) ? $user->gender : 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Assembly</label>
                                    <input type="text" class="form-control"
                                        value="{{ $user->assembly->name ?? 'N/A' }}" readonly>
                                </div>

                                @if($user->division_code)
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Division</label>
                                        <input type="text" class="form-control"
                                               value="{{ $user->division->name ?? 'N/A' }}" readonly>
                                    </div>
                                @endif

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Region</label>
                                    <input type="text" class="form-control" value="{{ $user->region->name ?? 'N/A' }}"
                                        readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Access Level Identification</label>
                                    <input type="text" class="form-control" value="{{ $user->access_level ?? 'N/A' }}"
                                        readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Role Identification</label>
                                    <input type="text" class="form-control" value="{{ $user->roleName->name ?? 'N/A' }}"
                                        readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">User Status</label>
                                    <input type="text" class="form-control" value="{{ $user->status ?? 'N/A' }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Additional scripts or JavaScript libraries -->
@endsection
