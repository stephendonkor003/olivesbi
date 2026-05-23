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
                            <i class="ri-user-settings-line me-2"></i> Agents Assignments
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            Easily add agents and manage assignments in one section.
                        </p>
                    </div>

                    @can('agent-assignments.create')
                        <a href="{{ route('agent-assignments.index') }}" class="btn btn-primary">
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
                            <form method="POST" action="{{ route('agent-assignments.store') }}">
                                @csrf

                                @if (empty($data['assembly']))
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

                                    <div class="row">

                                        <div class="mb-4 col-md-6">
                                            <label class="form-label">Supervisor</label>
                                            <select class="form-control @error('supervisor_id') is-invalid @enderror"
                                                name="supervisor_id">
                                                <option disabled selected>Select Supervisor</option>
                                                @foreach ($supervisors as $supervisor)
                                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('supervisor_id')
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

                                        <div class="col-sm-12 mb-3">
                                            <label for="agent_id" class="form-label">Select Agent(s)</label>

                                            @foreach ($agents as $agent)
                                                <div class="form-check">
                                                    <input class="form-check-input @error('agent_id') is-invalid @enderror"
                                                        type="checkbox" name="agent_id[]" value="{{ $agent->id }}"
                                                        id="agent_{{ $agent->id }}">
                                                    <label class="form-check-label" for="agent_{{ $agent->id }}">
                                                        {{ $agent->name }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            @error('agent_id')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">Save</button>
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
    <script src="{{ asset('assets/js/agent.js?v1=1234') }}"></script>
    <script src="{{ asset('assets/js/jquery.bootstrap-duallistbox.js') }}"></script>
@endsection
