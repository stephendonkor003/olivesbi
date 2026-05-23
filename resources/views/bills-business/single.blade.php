@extends('layout.base')

@section('page-styles')
    <link href="{{ asset('assets/css/bootstrap-duallistbox.css') }}" rel="stylesheet">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">
            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-wallet-3-line me-2"></i> BOP Bills Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are about to generate single BOP bill record into your
                            central database repository.
                        </p>
                    </div>

                    <a href="{{ route('bills.bus.index') }}" class="btn btn-sm btn-primary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="POST"  id="generate-bill-form" action="{{ route('bills.bus.singleStore') }}">
                                @csrf

                                <input type="hidden" name="business_url" url="{{ route('bills.business') }}">
                                <input type="hidden" name="preview_url" url="{{ route('bills.bus.preview') }}">

                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label for="assembly_code" class="form-label">Assembly Code</label>
                                        @if(!empty(Auth::user()->assembly_code))
                                            <select class="form-control @error('assembly_code') is-invalid @enderror"
                                                    id="assembly_code" disabled>
                                                <option value="{{ Auth::user()->assembly_code }}" selected>
                                                    {{ $assemblies->first()->name ?? 'N/A' }}
                                                </option>
                                            </select>
                                            <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                            <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                        @else
                                            <select class="form-control @error('assembly_code') is-invalid @enderror"
                                                    id="assembly_code" name="assembly_code" required>
                                                <option disabled selected>Select Assembly</option>
                                                @foreach ($assemblies as $assembly)
                                                    <option value="{{ $assembly->assembly_code }}">{{ $assembly->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif

                                        @error('assembly_code')
                                        <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-4 col-md-6">
                                        <label class="form-label">Bill Year</label>
                                        <select class="form-control form-select @error('bills_year') is-invalid @enderror"
                                                name="bills_year">
                                            <option disabled selected>Select Bill Year</option>
                                            @for ($i = 2010; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>

                                        @error('bills_year')
                                        <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-12 mb-3">
                                        <label for="business_id" class="form-label">Business</label>
                                        <select class="form-control @error('business_id') is-invalid @enderror"
                                            name="business_id[]" id="business_id" multiple style="height:300px;">
                                            <option disabled selected>Loading businesses...</option>
                                        </select>

                                        @error('business_id')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" id="preview-bills-bus" class="btn btn-secondary">Preview
                                        Bill</button>
                                    <button type="submit" id="generate-bills-btn" class="btn btn-success">Generate Bill</button>
                                </div>
                            </form>
                        </div>

                        <hr />
                        <div id="bill-preview-section" class="mt-4 d-none">
                            <h5>Bill Preview</h5>
                            <div class="table-responsive">
                                <table id="file-export" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Business No</th>
                                            <th>Owner Name</th>
                                            <th>Rate Impost</th>
                                            <th>Flat Rate</th>
                                            <th>Min Amount</th>
                                            <th>Discount</th>
                                            <th>Final Bill</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-tbody">
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
    <script src="{{ asset('assets/js/bill.js?v1=' . time()) }}"></script>
    <script src="{{ asset('assets/js/jquery.bootstrap-duallistbox.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('business_id');
            const assemblySelect = document.getElementById('assembly_code');

            $('select[name="business_id[]"]').bootstrapDualListbox({
                nonSelectedListLabel: 'Available Businesses',
                selectedListLabel: 'Selected Businesses',
                preserveSelectionOnMove: 'moved',
                moveOnSelect: false
            });

            // Function to load businesses for a given assembly and bill year
            function loadBusinesses(assemblyCode, billYear) {
                if (!assemblyCode) {
                    select.innerHTML = '<option disabled>Please select an assembly first</option>';
                    $('select[name="business_id[]"]').bootstrapDualListbox('refresh');
                    return;
                }

                if (!billYear) {
                    select.innerHTML = '<option disabled>Please select a bill year first</option>';
                    $('select[name="business_id[]"]').bootstrapDualListbox('refresh');
                    return;
                }

                const url = $("input[name='business_url']").attr("url");
                const formData = new FormData();
                formData.append("assembly_code", assemblyCode);
                formData.append("bills_year", billYear);

                select.innerHTML = '<option disabled>Loading businesses...</option>';
                $('select[name="business_id[]"]').bootstrapDualListbox('refresh');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        select.innerHTML = '';

                        if (response.message && response.message.length > 0) {
                            response.message.forEach(business => {
                                const option = document.createElement('option');
                                option.value = business.id;
                                option.textContent = business.name;
                                select.appendChild(option);
                            });
                        } else {
                            select.innerHTML = '<option disabled>No businesses available for this year</option>';
                        }

                        $('select[name="business_id[]"]').bootstrapDualListbox('refresh', true);
                    },
                    error: function(error) {
                        console.error("Error loading businesses:", error);
                        select.innerHTML = '<option disabled>Error loading businesses</option>';
                        $('select[name="business_id[]"]').bootstrapDualListbox('refresh');
                    }
                });
            }

            const billYearSelect = document.querySelector('select[name="bills_year"]');

            // Function to get current values and reload businesses
            function reloadBusinesses() {
                const assemblyCode = @if(!empty(Auth::user()->assembly_code))
                    '{{ Auth::user()->assembly_code }}'
                @else
                    assemblySelect.value
                @endif;
                const billYear = billYearSelect.value;

                loadBusinesses(assemblyCode, billYear);
            }

            // Initialize with message to select year
            select.innerHTML = '<option disabled>Please select a bill year first</option>';
            $('select[name="business_id[]"]').bootstrapDualListbox('refresh');

            // Listen for bill year change
            billYearSelect.addEventListener('change', function() {
                reloadBusinesses();
            });

            // Listen for assembly change (only if not auto-assigned)
            @if(empty(Auth::user()->assembly_code))
            assemblySelect.addEventListener('change', function() {
                reloadBusinesses();
            });
            @endif

            // Handle form submission with JSON to avoid max_input_vars limit
            const generateBillForm = document.getElementById('generate-bill-form');
            const generateBtn = document.getElementById('generate-bills-btn');

            generateBillForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const businessIds = $('select[name="business_id[]"]').val() || [];
                const assemblyCode = @if(!empty(Auth::user()->assembly_code))
                    '{{ Auth::user()->assembly_code }}'
                @else
                document.querySelector('select[name="assembly_code"]').value
                @endif;
                const billYear = document.querySelector('select[name="bills_year"]').value;

                // Validation
                if (!assemblyCode) {
                    alert('Please select an assembly');
                    return;
                }

                if (!billYear) {
                    alert('Please select a bill year');
                    return;
                }

                if (businessIds.length === 0) {
                    alert('Please select at least one business');
                    return;
                }

                // Prepare JSON payload
                const payload = {
                    assembly_code: assemblyCode,
                    bills_year: billYear,
                    business_id: businessIds
                };

                // Show loading state
                generateBtn.disabled = true;
                generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating...';

                // Submit via AJAX with JSON
                $.ajax({
                    url: '{{ route('bills.bus.singleStore') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        // Redirect on success
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '{{ route('bills.bus.index') }}';
                        }
                    },
                    error: function(xhr) {
                        // Re-enable button
                        generateBtn.disabled = false;
                        generateBtn.innerHTML = 'Generate Bill';

                        // Show error message
                        let errorMsg = 'An error occurred while generating bills.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(errorMsg);
                    }
                });
            });
        });
    </script>
@endsection
