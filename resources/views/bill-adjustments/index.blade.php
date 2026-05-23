@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-wallet-3-line me-2"></i> Bill Adjustment Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are viewing all bill adjustment record in your
                            central database repository.
                        </p>
                    </div>

                    @can('bills.create')
                        <a href="{{ route('bill-adjustments.create') }}" class="btn btn-sm btn-primary">
                            <i class="ri-add-line me-1"></i> Add Adjustment
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row">
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

            <div class="col-xl-12 active-p">
                <div class="tab-pane fade show active" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">

                    <div class="card">
                        <div class="card-body px-0">
                            <div class="table-responsive active-projects user-tbl  dt-filter">
                                <table id="file-export" class="table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Revenue Stream Type</th>
                                            <th>Year</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Assembly</th>
                                            <th>Division</th>
                                            <th>Block</th>
                                            <th>Properties/Businesses</th>
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customers">
                                        @foreach ($billAdjustments as $index => $bill)
                                            <tr class="btn-reveal-trigger">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $bill->revenueStreamType->name ?? 'N/A' }}</td>
                                                <td>{{ $bill->discount_year }}</td>
                                                <td>{{ $bill->discount_amount }}</td>
                                                <td>{{ $bill->discount_type }}</td>
                                                <td>{{ $bill->assembly->name ?? 'N/A' }}</td>
                                                <td>{{ $bill->division->name ?? 'N/A' }}</td>
                                                <td>{{ $bill->block->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($bill->erms_id)
                                                        {{count($bill->erms_id)}} {{ Str::plural('item', count($bill->erms_id)) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $bill->createdBy->name ?? 'N/A' }}</td>
                                                <td>{{ $bill->created_at }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <div class="btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
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
                                                                    href=" {{ route('bill-adjustments.show', $bill) }}">View
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href=" {{ route('bill-adjustments.edit', $bill) }}">Edit
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
    </div>
@endsection


@section('page-scripts')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
@endsection
