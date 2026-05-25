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
                            Easily Edit assignments in one section.
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
                        <div class="basic-form">
                            <form method="POST" action="{{ route('task-assignments.update', $taskAssignment) }}">
                                @csrf

                                <input type="hidden" name="assembly_code" value="{{ $assemblyData['assemblyCode'] }}">
                                <input type="hidden" name="supervisor_id" value="{{ $supervisor->id }}">

                                <div class="row">

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label">Supervisor</label>
                                        <input type="text" class="form-control @error('supervisor') is-invalid @enderror"
                                            id="supervisor" name="supervisor" placeholder="Supervisor"
                                            value="{{ $supervisor->name }}" readonly>

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
                                            value="{{ $assemblyData['assembly'] }}" readonly>

                                        @error('assembly')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="block_data" class="form-label">Block</label>
                                        <select multiple class="form-control @error('block_data') is-invalid @enderror"
                                            id="choices-multiple-default" name="block_data[]" required>
                                            @foreach ($blockss as $block)
                                                <option value="{{ $block->id }}"
                                                    @if (collect($taskAssignment->block_data ?? [])->contains('block_id', $block->id)) selected @endif>
                                                    {{ $block->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('block_data')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="agent_id" class="form-label">Agent</label>
                                        <select class="form-control @error('agent_id') is-invalid @enderror"
                                            name="agent_id">
                                            <option value="">Select Agent</option>
                                            @foreach ($agents as $agent)
                                                <option value="{{ $agent->id }}"
                                                    {{ old('agent_id', $taskAssignment->agent_id) == $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('agent_id')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 mb-3">
                                        <label for="task" class="form-label">Task</label>
                                        <select class="form-control @error('task') is-invalid @enderror" name="task">
                                            <option value="">Select Task</option>
                                            <option value="Payment Collection"
                                                {{ $taskAssignment->task == 'Payment Collection' ? 'selected' : '' }}>
                                                Payment Collection
                                            </option>
                                            <option value="Bill Distribution"
                                                {{ $taskAssignment->task == 'Bill Distribution' ? 'selected' : '' }}>
                                                Bill Distribution
                                            </option>
                                            <option value="Data Collection"
                                                {{ $taskAssignment->task == 'Data Collection' ? 'selected' : '' }}>Data
                                                Collection
                                            </option>
                                        </select>

                                        @error('task')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    @if (\Auth::user()->access_level == 'Assembly_Supervisor')
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    @endif
                                </div>

                                @if (!empty($taskAssignment->block_data))
                                    <div class="alert alert-warning alert-dismissible fade show" style="margin-top:30px">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                            class="me-2">
                                            <polyline points="9 11 12 14 22 4"></polyline>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                        </svg>
                                        <strong>AGENT ASSIGNED BLOCKS DETAILS</strong>
                                    </div>

                                    @if (session()->has('status'))
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                                stroke-width="2" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round" class="me-2">
                                                <polyline points="9 11 12 14 22 4"></polyline>
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                            </svg>
                                            <strong>{{ session('status') }}</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span>
                                            </button>
                                        </div>
                                    @endif

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
                                                                @if (\Auth::user()->access_level == 'Assembly_Agent')
                                                                    <th>Action</th>
                                                                @endif
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
                                                                    @if ($block->status == 'Completed')
                                                                        <td><span
                                                                                class="badge light badge-success">{{ $block->status }}</span>
                                                                        </td>
                                                                    @else
                                                                        <td><span
                                                                                class="badge light badge-danger">{{ $block->status }}</span>
                                                                        </td>
                                                                    @endif
                                                                    <td>{{ $block->created_by }}</td>
                                                                    <td>{{ $block->created_at }}</td>
                                                                    @if (\Auth::user()->access_level == 'Assembly_Agent')
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <div class="btn-link"
                                                                                    data-bs-toggle="dropdown"
                                                                                    aria-expanded="false">
                                                                                    <svg width="24" height="24"
                                                                                        viewBox="0 0 24 24" fill="none"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path
                                                                                            d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                                                                            stroke="#737B8B"
                                                                                            stroke-width="2"
                                                                                            stroke-linecap="round"
                                                                                            stroke-linejoin="round">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                                                                            stroke="#737B8B"
                                                                                            stroke-width="2"
                                                                                            stroke-linecap="round"
                                                                                            stroke-linejoin="round">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                                                                            stroke="#737B8B"
                                                                                            stroke-width="2"
                                                                                            stroke-linecap="round"
                                                                                            stroke-linejoin="round">
                                                                                        </path>
                                                                                    </svg>
                                                                                </div>
                                                                                <div
                                                                                    class="dropdown-menu dropdown-menu-end">
                                                                                    <div class="py-2">
                                                                                        <a class="dropdown-item"
                                                                                            href="{{ route('task-assignments.updateStatus', ['id' => $block->assignment_uuid, 'block_id' => $block->block_uuid]) }}">Mark
                                                                                            As Done
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                    @endif
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
            @endif
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
