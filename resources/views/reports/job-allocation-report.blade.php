@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-file-chart-line me-2"></i> Job Allocation Report
                        </h4>
                        <p class="mb-0 text-muted fs-14">
                            You are Generating Job Allocation Report from your
                            central database repository.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card dz-card" id="accordion-four">

                    <input type="hidden" name="job-allocation-report_url"
                        url="{{ route('job-allocation-reports.index') }}">

                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control">
                                </div>

                                <div id="detail_field">
                                    <div class="row">

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Assembly</label>
                                            <select
                                                class="form-control form-select @error('assembly_code') is-invalid @enderror"
                                                name="assembly_code">
                                                <option disabled selected>Select Assembly</option>
                                                @foreach ($assemblies as $assembly)
                                                    <option value="{{ $assembly->assembly_code }}">
                                                        {{ $assembly->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Supervisor</label>
                                            <select
                                                class="form-control form-select @error('supervisor_id') is-invalid @enderror"
                                                id="supervisor_id" name="supervisor_id">
                                                <option disabled selected>Select Supervisor</option>
                                                @foreach ($supervisors as $supervisor)
                                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Agent</label>
                                            <select class="form-control form-select @error('agent_id') is-invalid @enderror"
                                                id="agent_id" name="agent_id">
                                                <option disabled selected>Select Agent</option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4" style="display:none">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-select @error('report_type') is-invalid @enderror" id="report_type"
                                        name="report_type">
                                        <option value="1">Customer Report</option>
                                        <option value="2">Summary Report</option>
                                    </select>
                                </div>

                                <center>
                                    <div class="mb-4 col-md-12">
                                        <button type="submit" class="btn btn-primary generate_report"
                                            style="width:200px">Generate
                                            Report</button>
                                    </div>
                                </center>
                                <hr />
                            </div>
                        </div>
                    </div>

                    <div class="tab-content" id="myTabContent-3">
                        <div class="tab-pane fade show active" id="withoutBorder" role="tabpanel"
                            aria-labelledby="home-tab-3">
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <div id="details">
                                        <table id="file-export" class="table table-bordered text-nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Supervisor Name</th>
                                                    <th>Agent Name</th>
                                                    <th>Task/Job</th>
                                                    <th>Block Allocated (Block - Status)</th>
                                                    <th>Assembly</th>
                                                    <th>Assigned By</th>
                                                    <th>Assignment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div style="display:none" id="summary">
                                        <table id="example5" class="display table" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th id="header">Name</th>
                                                    <th>Total Customer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th id="title"></th>
                                                    <th id="customer_total"></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
    <script src="{{ asset('assets/js/report/job-allocation-report.js?v1=1234') }}"></script>
@endsection
