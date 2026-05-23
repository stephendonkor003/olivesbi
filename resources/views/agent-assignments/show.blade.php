@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-user-settings-line me-2"></i> Agents Assignments
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            Easily View A Rocord of agents in one section.
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
            <div class="col-md-12">
                <div class="card">


                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('agent-assignments.update', $agentAssignment) }}">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label for="assembly" class="form-label">Assembly</label>
                                <input type="text" class="form-control @error('assembly') is-invalid @enderror"
                                    id="assembly" placeholder="Enter division name" name="assembly"
                                    value="{{ $agentAssignment->assembly->name ?? '' }}" readonly>

                                @error('assembly')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supervisor_id" class="form-label">Supervisor Name</label>
                                <input type="text" class="form-control @error('supervisor_id') is-invalid @enderror"
                                    id="supervisor_id" name="supervisor_id" placeholder="Enter block code"
                                    value="{{ $agentAssignment->supervisor->name ?? '' }}" readonly>

                                @error('supervisor_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="agent_id" class="form-label">Agent Name</label>
                                <input type="text" class="form-control @error('agent_id') is-invalid @enderror"
                                    id="agent_id" name="agent_id" placeholder="Enter block name"
                                    value="{{ $agentAssignment->agent->name ?? '' }}" readonly>

                                @error('agent_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="division_name" class="form-label">Assigned Date</label>
                                <input type="text" class="form-control @error('division_code') is-invalid @enderror"
                                    id="division_code" placeholder="Enter division code" name="division_code"
                                    value="{{ $agentAssignment->created_at }}" readonly>

                                @error('division_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general/app.js') }}"></script>
@endsection
