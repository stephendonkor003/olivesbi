@extends('layout.base')

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 active-p">
                <div class="card">

                    <!-- HEADER SECTION -->
                    <div class="card-body border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="ri-lock-unlock-line me-2"></i> Permissions Management
                                </h4>
                                <p class="mb-0 text-muted fs-14">
                                    View all system permissions and assign privileges to roles.
                                </p>
                            </div>
                            {{-- Optional future button --}}
                            {{-- <a href="#" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i> Create New
                                Permission</a> --}}

                        </div>
                    </div>
                </div>
                <div class="card">
                    <!-- FLASH MESSAGE -->
                    @if (session()->has('status'))
                        <div class="alert alert-success alert-dismissible fade show m-4">
                            <strong>{{ session('status') }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- TABLE SECTION -->
                    <div class="card-body pt-4">
                        <div class="table-responsive px-2">
                            <table id="file-export" class="table table-bordered text-nowrap w-100 custom-hover-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Permission Name</th>
                                        <th>Date Created</th>
                                        <th>Interval Analysis</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $index => $role)
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
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $role->name }}</td>
                                            <td>{{ $createdAt->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $progress[1] }}">{{ $timeAgo }}</span>
                                                <div class="progress progress-xs mt-2">
                                                    <div class="progress-bar bg-{{ $progress[1] }}"
                                                        style="width: {{ $progress[0] }}"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('permissions.edit', $role) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-lock me-1"></i> Assign Privileges
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end table section -->
                </div>
            </div> <!-- end card -->
        </div>
    </div>
    </div>
@endsection

@section('page-styles')
    <style>
        /* FULL-ROW HOVER: Applies your logic correctly */
        .custom-hover-table tbody tr:hover {
            background-color: rgb(43, 116, 41) !important;
            color: #812727 !important;
            cursor: pointer;
        }

        .custom-hover-table tbody tr:hover td a,
        .custom-hover-table tbody tr:hover td i {
            color: #812727 !important;
        }

        .fs-14 {
            font-size: 14px;
        }
    </style>
@endsection
