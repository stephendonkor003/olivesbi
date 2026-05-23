@extends('layout.base')

@section('page-styles')
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }
    </style>
    <link href='https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.css' rel='stylesheet' />
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-settings-5-line me-2"></i> Assembly - Configurations
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            Manage your assembly here.
                        </p>
                    </div>
                    @can('assemblies.create')
                        @if (auth()->user()->access_level === 'Super_User')
                            <div>
                                <a href="{{ route('assembly.create') }}" class="btn btn-primary btn-sm ms-2">
                                    <i class="ri-building-line me-1"></i> Create Assembly
                                </a>

                                <a href="{{ route('assembly.import') }}" class="btn btn-success btn-sm ms-2">+ Upload Bulk
                                    Assembly</a>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 active-p">

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

                <div class="card">
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table id="file-export" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Assembly Name</th>
                                        <th>Assembly Code</th>
                                        <th>Assembly Category</th>
                                        <th>Regional Code</th>
                                        <th>Regional Name</th>
                                        <th>Set-Up Status</th>
                                        <th>Supervisor</th>
                                        <th>Date Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assemblies as $index => $assembly)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $assembly->name }} Assembly</td>
                                            <td>{{ $assembly->assembly_code }}</td>
                                            <td>{{ $assembly->assembly_category ?? 'N/A' }}</td>
                                            <td>{{ $assembly->regional_code }}</td>
                                            <td>{{ $assembly->region->name ?? 'N/A' }}</td>
                                            <td>{{ $assembly->status ?? 'Not Active' }}</td>
                                            <td>{{ $assembly->assemblySupervisor->name ?? 'N/A' }}</td>
                                            <td>{{ $assembly->created_at ?? 'N/A' }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                            </path>
                                                            <path
                                                                d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                            </path>
                                                            <path
                                                                d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <div class="py-2">
                                                            <a class="dropdown-item"
                                                                href=" {{ route('assembly.show', $assembly->id) }}">View
                                                            </a>
                                                            <a class="dropdown-item"
                                                                href=" {{ route('assembly.edit', $assembly->id) }}">Edit
                                                            </a>
                                                        </div>
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
@endsection


@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/crm.js') }}"></script>
@endsection
