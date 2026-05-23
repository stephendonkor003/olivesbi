@extends('layout.base')

@section('page-content')
    <div class="container-fluid">
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
                                    Easily manage System Users by creating, editing, and viewing all Users In One Section.
                                </p>
                            </div>

                            @can('users.create')
                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-1"></i> Create New User
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-3">
                        <div class="card custom-card rounded-md overflow-hidden p-2">
                            <div class="card-body bg-primary bg-opacity-10 rounded-2 ps-4 medical-cards">
                                <div class="d-flex gap-2 align-items-center ps-2">
                                    <div class="align-self-start">
                                        <div class="fw-medium mb-2"> Active Users</div>
                                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['totalActiveUsers'] }}</h4>
                                    </div>
                                    <div class="ms-auto text-end align-self-end">
                                        <div class="avatar avatar-md avatar-rounded bg-primary shadow shadow-primary mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                class="svg-icon-med text-fixed-white" fill="currentColor"
                                                viewBox="0 0 256 256">
                                                <path d="M136,108A52,52,0,1,1,84,56,52,52,0,0,1,136,108Z" opacity="0.2">
                                                </path>
                                                <path
                                                    d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                                </path>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card custom-card rounded-md overflow-hidden p-2">
                            <div class="card-body bg-secondary bg-opacity-10 rounded-2 ps-4 medical-cards secondary">
                                <div class="d-flex gap-2 align-items-center ps-2">
                                    <div class="align-self-start">
                                        <div class="fw-medium mb-2">In-Active Users </div>
                                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['totalInactiveUsers'] }} </h4>
                                    </div>
                                    <div class="ms-auto text-end align-self-end">
                                        <div
                                            class="avatar avatar-md avatar-rounded bg-secondary shadow shadow-secondary mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                class="svg-icon-med text-fixed-white" fill="currentColor"
                                                viewBox="0 0 256 256">
                                                <path d="M240,160a32,32,0,1,1-32-32A32,32,0,0,1,240,160Z" opacity="0.2">
                                                </path>
                                                <path
                                                    d="M220,160a12,12,0,1,1-12-12A12,12,0,0,1,220,160Zm-4.55,39.29A48.08,48.08,0,0,1,168,240H144a48.05,48.05,0,0,1-48-48V151.49A64,64,0,0,1,40,88V40a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56V88a48,48,0,0,0,48.64,48c26.11-.34,47.36-22.25,47.36-48.83V48H136a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8V87.17c0,32.84-24.53,60.29-56,64.31V192a32,32,0,0,0,32,32h24a32.06,32.06,0,0,0,31.22-25,40,40,0,1,1,16.23.27ZM232,160a24,24,0,1,0-24,24A24,24,0,0,0,232,160Z">
                                                </path>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card custom-card rounded-md overflow-hidden p-2">
                            <div class="card-body bg-success bg-opacity-10 rounded-2 ps-4 medical-cards success">
                                <div class="d-flex gap-2 align-items-center ps-2">
                                    <div class="align-self-start">
                                        <div class="fw-medium mb-2">Male Users</div>
                                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['totalMaleUsers'] }}</h4>
                                    </div>
                                    <div class="ms-auto text-end align-self-end">
                                        <div class="avatar avatar-md avatar-rounded bg-success shadow shadow-success mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                class="svg-icon-med text-fixed-white" fill="currentColor"
                                                viewBox="0 0 256 256">
                                                <path d="M216,48V88H40V48a8,8,0,0,1,8-8H208A8,8,0,0,1,216,48Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-48-56a8,8,0,0,1-8,8H136v16a8,8,0,0,1-16,0V160H104a8,8,0,0,1,0-16h16V128a8,8,0,0,1,16,0v16h16A8,8,0,0,1,160,152Z">
                                                </path>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card custom-card rounded-md overflow-hidden p-2">
                            <div class="card-body bg-info bg-opacity-10 rounded-2 ps-4 medical-cards info">
                                <div class="d-flex gap-2 align-items-center ps-2">
                                    <div class="align-self-start">
                                        <div class="fw-medium mb-2">Female Users </div>
                                        <h4 class="fw-semibold mb-0 lh-1">{{ $total['totalFemaleUsers'] }}</h4>
                                    </div>
                                    <div class="ms-auto text-end align-self-end">
                                        <div class="avatar avatar-md avatar-rounded bg-info shadow shadow-info mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                class="svg-icon-med text-fixed-white" fill="currentColor"
                                                viewBox="0 0 256 256">
                                                <path
                                                    d="M168,144a40,40,0,1,1-40-40A40,40,0,0,1,168,144ZM64,56A32,32,0,1,0,96,88,32,32,0,0,0,64,56Zm128,0a32,32,0,1,0,32,32A32,32,0,0,0,192,56Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M244.8,150.4a8,8,0,0,1-11.2-1.6A51.6,51.6,0,0,0,192,128a8,8,0,0,1,0-16,24,24,0,1,0-23.24-30,8,8,0,1,1-15.5-4A40,40,0,1,1,219,117.51a67.94,67.94,0,0,1,27.43,21.68A8,8,0,0,1,244.8,150.4ZM190.92,212a8,8,0,1,1-13.85,8,57,57,0,0,0-98.15,0,8,8,0,1,1-13.84-8,72.06,72.06,0,0,1,33.74-29.92,48,48,0,1,1,58.36,0A72.06,72.06,0,0,1,190.92,212ZM128,176a32,32,0,1,0-32-32A32,32,0,0,0,128,176ZM72,120a8,8,0,0,0-8-8A24,24,0,1,1,87.24,82a8,8,0,1,0,15.5-4A40,40,0,1,0,37,117.51,67.94,67.94,0,0,0,9.6,139.19a8,8,0,1,0,12.8,9.61A51.6,51.6,0,0,1,64,128,8,8,0,0,0,72,120Z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session()->has('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i
                                    class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="file-export" class="table table-bordered text-nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Full Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Access Level</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $index => $user)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if ($user->access_level == 'Assembly_Administrator')
                                                            Assembly Administrator
                                                        @elseif ($user->access_level == 'customer')
                                                            Rate Payer
                                                        @elseif ($user->access_level == 'Assembly_Agent')
                                                            Assembly Agent
                                                        @elseif ($user->access_level == 'Assembly_Supervisor')
                                                            Assembly Supervisor
                                                        @elseif (empty($user->access_level))
                                                            Melchia Supper User
                                                        @elseif ($user->access_level == 'GOG_Administrator')
                                                            GOG Administrator
                                                        @else
                                                            {{ $user->access_level }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $user->roleName->name ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        @if ($user->status === 'Active')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">In-Active</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div class="dropdown">
                                                            <div class="btn-link" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                        stroke="#737B8B" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path
                                                                        d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                        stroke="#737B8B" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path
                                                                        d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                        stroke="#737B8B" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                            <div class="dropdown-menu dropdown-menu-right" style="">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('users.show', $user) }}">View
                                                                    Information</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('users.edit', $user) }}">Edit
                                                                    Information</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
@endsection
