@extends('layout.base')

@section('page-styles')
    <link href="{{ asset('assets/css/bootstrap-duallistbox.css') }}" rel="stylesheet">
    <!-- Choices JS -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <style>
        #choices-multiple-default+.choices .choices__inner {
            background-color: #ffe4e1 !important;
            /* light pink to test */
        }
    </style>
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
                            <form method="POST" action="{{ route('task-assignments.store') }}">
                                @csrf

                                @if (empty($supervisor))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                            class="me-2">
                                            <polyline points="9 11 12 14 22 4"></polyline>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                        </svg>
                                        <strong>SORRY!!! YOU DONT HAVE THE RIGHT TO USE THIS PAGE. THIS PAGE IS FOR ONLY
                                            SUPERVISORS AND AGENTS</strong>
                                    </div>
                                @else
                                    <input type="hidden" name="assembly_code" value="{{ $data['assemblyCode'] }}">
                                    <input type="hidden" name="supervisor_id" value="{{ $supervisor->id }}">

                                    <div class="row">

                                        <div class="mb-4 col-md-6">
                                            <label class="form-label">Supervisor</label>
                                            <input type="text"
                                                class="form-control @error('supervisor') is-invalid @enderror"
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
                                            <input type="text"
                                                class="form-control @error('assembly') is-invalid @enderror" id="assembly"
                                                name="assembly" placeholder="Assembly" value="{{ $data['assembly'] }}"
                                                readonly>

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
                                                @foreach ($blocks as $block)
                                                    <option value="{{ $block->id }}">{{ $block->name }}</option>
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
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('agent_id')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label">Select Task(s)</label>

                                            <div class="form-check">
                                                <input class="form-check-input @error('task') is-invalid @enderror"
                                                    type="checkbox" name="task[]" value="Payment Collection"
                                                    id="task1">
                                                <label class="form-check-label" for="task1">Payment Collection</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input @error('task') is-invalid @enderror"
                                                    type="checkbox" name="task[]" value="Bill Distribution" id="task2">
                                                <label class="form-check-label" for="task2">Bill Distribution</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input @error('task') is-invalid @enderror"
                                                    type="checkbox" name="task[]" value="Data Collection"
                                                    id="task3">
                                                <label class="form-check-label" for="task3">Data Collection</label>
                                            </div>

                                            @error('task')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Save</button>
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
    <!-- Internal Choices JS -->
    <script src="{{ asset('assets/js/choices.js') }}"></script>
@endsection
