@extends('layout.base')

@section('page-styles')
    <link href="{{ asset('assets/css/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endsection

@section('page-content')
    <div class="container-fluid mh-auto">
        <div class="card">
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-user-settings-line me-2"></i> Tasks Assignments
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            Easily manage assignments in one section.
                        </p>
                    </div>

                    @can('task-assignments.create')
                        <a href="{{ route('task-assignments.index') }}" class="btn btn-primary">
                            <i class="ri-arrow-go-back-line"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="POST" action="{{ route('task-assignments.store') }}">
                                @csrf

                                <div class="row">

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label">Supervisor</label>
                                        <input type="text" class="form-control @error('supervisor') is-invalid @enderror"
                                            id="supervisor" name="supervisor" placeholder="Supervisor"
                                            value="{{ $taskAssignment->supervisor->name ?? '' }}" readonly>

                                        @error('supervisor')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="assembly_code" class="form-label">Assembly</label>
                                        <input type="text" class="form-control @error('assembly') is-invalid @enderror"
                                            id="assembly" name="assembly" placeholder="Assembly"
                                            value="{{ $taskAssignment->assembly->name ?? '' }}" readonly>

                                        @error('assembly')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="agent_id" class="form-label">Agent</label>
                                        <input type="text" class="form-control @error('agent_id') is-invalid @enderror"
                                            id="agent_id" name="agent_id" placeholder="Agent_id"
                                            value="{{ $taskAssignment->agent->name ?? '' }}" readonly>

                                        @error('agent_id')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="task" class="form-label">Task</label>
                                        <input type="text" class="form-control @error('task') is-invalid @enderror"
                                            id="task" name="task" placeholder="Task"
                                            value="{{ $taskAssignment->task }}" readonly>

                                        @error('task')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    @if (!empty($taskAssignment->block_data))
                                        <div class="alert alert-warning alert-dismissible fade show"
                                            style="margin-top:30px">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                                stroke-width="2" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round" class="me-2">
                                                <polyline points="9 11 12 14 22 4"></polyline>
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                            </svg>
                                            <strong>AGENT ASSIGNED BLOCKS DETAILS</strong>
                                        </div>

                                        <div class="tab-content" id="myTabContent-3">
                                            <div class="tab-pane fade show active" id="withoutBorder" role="tabpanel"
                                                aria-labelledby="home-tab-3">
                                                <div class="card-body pt-0">
                                                    <div class="table-responsive">
                                                        <table id="example4" class="display table" style="width: 100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/N</th>
                                                                    <th>Block Code</th>
                                                                    <th>Block Name</th>
                                                                    <th>Assembly</th>
                                                                    <th>Task</th>
                                                                    <th>Status</th>
                                                                    <th>Assigned By</th>
                                                                    <th>Created Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($blocks as $index => $block)
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ $block->block_code }}</td>
                                                                        <td>{{ $block->block_name }}</td>
                                                                        <td>{{ $block->assembly }}</td>
                                                                        <td>{{ $block->task }}</td>
                                                                        <td>{{ $block->status }}</td>
                                                                        <td>{{ $block->created_by }}</td>
                                                                        <td>{{ $block->created_at }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
@endsection
