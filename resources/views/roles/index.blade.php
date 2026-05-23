@extends('layout.base')

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 active-p">
                <div class="card">
                    <!-- CARD HEADER (separate block) -->
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-shield-user-line me-2"></i> Role Management
                                </h4>
                                <p class="mb-0 text-muted fs-14">
                                    Easily manage user access by creating, editing, and viewing all defined system roles.
                                </p>
                            </div>

                            @can('roles.create')
                                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-1"></i> Create Role
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- FLASH MESSAGE -->
                    @if (session()->has('status'))
                        <div class="alert alert-success alert-dismissible fade show m-4">
                            <strong>{{ session('status') }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-4">
                            <strong>{{ session('error') }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
                <div class="card">



                    <!-- CARD TABLE SECTION -->
                    <div class="card-body pt-4">
                        <div class="table-responsive px-2">
                            <table id="file-export" class="table table-bordered text-nowrap w-100 custom-hover-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Role Name</th>
                                        <th>Status</th>
                                        <th>Date Created</th>
                                        <th>Interval Analysis</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        @php
                                            $createdAt = $role->created_at;
                                            $timeAgo = $createdAt->diffForHumans();
                                            $daysAgo = $createdAt->diffInDays();
                                            $progress = match (true) {
                                                $daysAgo >= 365 => ['100%', 'secondary'],
                                                $daysAgo >= 30 => ['75%', 'primary'],
                                                $daysAgo >= 7 => ['50%', 'danger'],
                                                $daysAgo >= 1 => ['25%', 'success'],
                                                default => ['10%', 'success'],
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $role->name }}</td>
                                            <td>
                                                @if($role->active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $createdAt->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $progress[1] }}">{{ $timeAgo }}</span>
                                                <div class="progress progress-xs mt-2">
                                                    <div class="progress-bar bg-{{ $progress[1] }}"
                                                        style="width: {{ $progress[0] }}"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @can('roles.view')
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('roles.show', $role) }}"><i
                                                                    class="fa fa-eye me-2"></i>View</a></li>
                                                        @endcan
                                                        @can('roles.update')
                                                            <li><a class="dropdown-item"
                                                                   href="{{ route('roles.edit', $role) }}"><i
                                                                        class="fa fa-edit me-2"></i>Edit</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('roles.activate', $role) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item text-danger"
                                                                            onclick="return confirm('Are you sure you want to deactivate this role?')">
                                                                        <i class="fa fa-ban me-2"></i>Deactivate
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
@endsection

@section('page-styles')
    <style>
        /* Hover style for table rows */
        .custom-hover-table tbody tr:hover {
            background-color: rgb(43, 116, 41) !important;
            color: #812727 !important;
            cursor: pointer;
        }

        .custom-hover-table tbody tr:hover td a,
        .custom-hover-table tbody tr:hover td i {
            color: #9c2b2b !important;
        }

        .fs-14 {
            font-size: 14px;
        }
    </style>
@endsection
