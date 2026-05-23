@extends('layout.base')

@section('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('page-content')
    @php use Illuminate\Support\Str; @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="card">

                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-wallet-3-line me-2"></i> Bill Adjustment Management
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                You are editing bill adjustment record in your
                                central database repository.
                            </p>
                        </div>

                        @can('bills.create')
                            <a href="{{ route('bill-adjustments.index') }}" class="btn btn-sm btn-primary">
                                <i class="ri-add-line me-1"></i> Back
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('bill-adjustments.update', $billAdjustment) }}">
                        @csrf

                        <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                        <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">


                        @php
                            $ermsId = json_encode(old('erms_id', is_array($billAdjustment->erms_id) ? $billAdjustment->erms_id : []) ?? []);
                        @endphp

                        <input type="hidden" id="current_erms_ids" value="{{ $ermsId }}">

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Revenue Stream Type</label>
                            <select class="form-control @error('revenue_stream_type_id') is-invalid @enderror"
                                id="revenue_stream_type_id" name="revenue_stream_type_id">
                                <option disabled selected>Select Revenue Stream Type</option>
                                @foreach ($revenueStreamTypes as $revenueStreamType)
                                    <option value="{{ $revenueStreamType->id }}" data-name="{{ $revenueStreamType->name }}"
                                        {{ old('revenue_stream_type_id', $billAdjustment->revenue_stream_type_id) == $revenueStreamType->id ? 'selected' : '' }}>
                                        {{ $revenueStreamType->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('revenue_stream_type_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="discount_year" class="form-label">Discount Year</label>
                            <select class="form-control form-select @error('discount_year') is-invalid @enderror"
                                name="discount_year">
                                <option disabled selected>Select Discount Year</option>
                                @for ($i = 2010; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}"
                                        {{ $billAdjustment->discount_year == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>

                            @error('discount_year')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2" id="input1">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <input type="text" class="form-control @error('discount_amount') is-invalid @enderror"
                                name="discount_amount" placeholder="Discount Amount (Fixed Value Discount)"
                                value="{{ $billAdjustment->discount_amount }}">

                            @error('discount_amount')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="discount_type" class="form-label">Discount Type</label>
                            <select class="form-control form-select @error('discount_type') is-invalid @enderror"
                                id="discount_type" name="discount_type">
                                <option disabled>Select Discount Type</option>

                                <option value="Assembly Discount"
                                    {{ old('discount_type', $billAdjustment->discount_type) == 'Assembly Discount' ? 'selected' : '' }}>
                                    Assembly Discount
                                </option>
                                <option value="Division Discount"
                                    {{ old('discount_type', $billAdjustment->discount_type) == 'Division Discount' ? 'selected' : '' }}>
                                    Division Discount
                                </option>
                                <option value="Block Discount"
                                    {{ old('discount_type', $billAdjustment->discount_type) == 'Block Discount' ? 'selected' : '' }}>
                                    Block Discount
                                </option>

                                @php $revenueType = $billAdjustment->revenueStreamType->name ?? ''; @endphp

                                @if (Str::contains($revenueType, 'Property Rate'))
                                    <option value="Property Discount"
                                        {{ old('discount_type', $billAdjustment->discount_type) == 'Property Discount' ? 'selected' : '' }}>
                                        Property Discount
                                    </option>
                                @endif

                                @if (Str::contains($revenueType, 'Business') || Str::contains($revenueType, 'BOP'))
                                    <option value="BOP Discount"
                                        {{ old('discount_type', $billAdjustment->discount_type) == 'BOP Discount' ? 'selected' : '' }}>
                                        BOP Discount
                                    </option>
                                @endif
                            </select>

                            @error('discount_type')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="assembly" class="form-label">Assembly</label>
                            @if(!empty(Auth::user()->assembly_code))
                                <select class="form-control" id="assembly" disabled>
                                    @foreach ($assemblies as $assembly)
                                        <option value="{{ $assembly->assembly_code }}" selected>
                                            {{ $assembly->name }} Assembly
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                            @else
                                <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                    name="assembly_code">
                                    <option disabled selected>Select Assembly</option>
                                    @foreach ($assemblies as $assembly)
                                        <option value="{{ $assembly->assembly_code }}"
                                            {{ old('assembly_code', $billAdjustment->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                            {{ $assembly->name }}
                                            Assembly
                                        </option>
                                    @endforeach
                                </select>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="division_id" class="form-label">Division</label>
                            <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                name="division_id" disabled>
                                <option disabled selected>Select Division</option>
                            </select>

                            <input type="hidden" id="current_division_id" value="{{ $billAdjustment->division_id }}">
                            <input type="hidden" id="current_block_id" value="{{ $billAdjustment->block_id }}">
                            <input type="hidden" id="current_assembly_code" value="{{ $billAdjustment->assembly_code }}">

                            @error('division_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="block_id" class="form-label">Block</label>
                            <select class="form-control @error('block_id') is-invalid @enderror" id="block_id"
                                name="block_id" disabled>
                                <option disabled selected>Select Block</option>
                            </select>

                            @error('block_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="erms_id" class="form-label">Property/BOP</label>
                            <select class="form-control @error('erms_id') is-invalid @enderror" name="erms_id[]"
                                id="erms_id" multiple
                                {{ in_array($billAdjustment->discount_type, ['Property Discount', 'BOP Discount']) ? '' : 'disabled' }}>
                                @if(is_array($billAdjustment->erms_id) && count($billAdjustment->erms_id) > 0)
                                    @foreach($billAdjustment->erms_id as $erms)
                                        <option value="{{ $erms }}" selected>{{ $erms }}</option>
                                    @endforeach
                                @endif
                            </select>

                            @error('erms_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=' . time()) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueStreamSelect = document.getElementById('revenue_stream_type_id');
            const discountTypeSelect = document.getElementById('discount_type');
            const divisionSelect = document.getElementById('division_id');
            const blockSelect = document.getElementById('block_id');
            const ermsInput = document.getElementById('erms_id');
            const propertySelect = document.getElementById('erms_id');
            let select2Instance;
            let isFirstLoad = true;

            function initializeSelect2() {
                const selectElement = $('select[name="erms_id[]"]');
                if (selectElement.length) {
                    if (select2Instance) {
                        try {
                            selectElement.select2('destroy');
                        } catch (e) {
                            console.log('No existing Select2 to destroy');
                        }
                    }

                    select2Instance = selectElement.select2({
                        placeholder: 'Select Property / BOP',
                        allowClear: true,
                        multiple: true,
                        width: '100%',
                        minimumInputLength: 0,
                        closeOnSelect: false,
                        templateResult: function(data) {
                            if (data.loading) {
                                return data.text;
                            }
                            return $('<span>' + data.text + '</span>');
                        },
                        templateSelection: function(data) {
                            return data.text;
                        },
                        escapeMarkup: function(markup) {
                            return markup;
                        },
                        language: {
                            searching: function() {
                                return "Searching...";
                            },
                            noResults: function() {
                                return "No results found";
                            }
                        }
                    });
                }
            }

            initializeSelect2();

            function resetFields() {
                if (divisionSelect) divisionSelect.disabled = false;
                if (blockSelect) blockSelect.disabled = false;
                if (ermsInput) ermsInput.disabled = false;
            }

            function clearSelect(select, defaultText = 'Select') {
                if (select) {
                    select.innerHTML = `<option disabled selected>${defaultText}</option>`;
                }
            }

            function loadDivisions(assemblyCode, selectedDivisionId = null) {
                if (!divisionSelect || !assemblyCode) return;

                const divisionUrl = document.querySelector('input[name="division_url"]').getAttribute('url');

                clearSelect(divisionSelect, 'Loading...');
                if (!selectedDivisionId) {
                    clearSelect(blockSelect, 'Select Block');
                }

                fetch(divisionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ assembly_code: assemblyCode })
                })
                .then(response => response.json())
                .then(result => {
                    clearSelect(divisionSelect, 'Select Division');

                    if (result.data && result.data.length > 0) {
                        result.data.forEach(division => {
                            const option = document.createElement('option');
                            option.value = division.id;
                            option.textContent = division.name;
                            if (selectedDivisionId && division.id == selectedDivisionId) {
                                option.selected = true;
                            }
                            divisionSelect.appendChild(option);
                        });

                        // Load blocks if division is selected
                        if (selectedDivisionId && discountTypeSelect.value === 'Block Discount') {
                            const currentBlockId = document.getElementById('current_block_id')?.value;
                            loadBlocks(selectedDivisionId, currentBlockId);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading divisions:', error);
                    clearSelect(divisionSelect, 'Error loading divisions');
                });
            }

            function loadBlocks(divisionId, selectedBlockId = null) {
                if (!blockSelect || !divisionId) return;

                const blockUrl = document.querySelector('input[name="block_url"]').getAttribute('url');

                clearSelect(blockSelect, 'Loading...');

                fetch(blockUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ division_id: divisionId })
                })
                .then(response => response.json())
                .then(result => {
                    clearSelect(blockSelect, 'Select Block');

                    const blocks = result.message || result.data || [];
                    if (blocks.length > 0) {
                        blocks.forEach(block => {
                            const option = document.createElement('option');
                            option.value = block.id;
                            option.textContent = block.name;
                            if (selectedBlockId && block.id == selectedBlockId) {
                                option.selected = true;
                            }
                            blockSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading blocks:', error);
                    clearSelect(blockSelect, 'Error loading blocks');
                });
            }

            // Assembly change listener
            const assemblySelect = document.getElementById('assembly');
            if (assemblySelect && !assemblySelect.disabled) {
                assemblySelect.addEventListener('change', function() {
                    const assemblyCode = this.value;
                    clearSelect(divisionSelect, 'Select Division');
                    clearSelect(blockSelect, 'Select Block');

                    const discountType = discountTypeSelect.value;
                    if (discountType === 'Division Discount' || discountType === 'Block Discount') {
                        loadDivisions(assemblyCode);
                    }
                });
            }

            // Division change listener
            if (divisionSelect) {
                divisionSelect.addEventListener('change', function() {
                    const divisionId = this.value;
                    clearSelect(blockSelect, 'Select Block');

                    const discountType = discountTypeSelect.value;
                    if (discountType === 'Block Discount' && divisionId) {
                        loadBlocks(divisionId);
                    }
                });
            }

            // Initialize existing values on page load
            function initializeExistingValues() {
                const currentAssemblyCode = document.getElementById('current_assembly_code')?.value;
                const currentDivisionId = document.getElementById('current_division_id')?.value;
                const discountType = discountTypeSelect.value;

                if (discountType === 'Division Discount' && currentAssemblyCode) {
                    loadDivisions(currentAssemblyCode, currentDivisionId);
                } else if (discountType === 'Block Discount' && currentAssemblyCode) {
                    loadDivisions(currentAssemblyCode, currentDivisionId);
                }
            }

            // Initialize on page load
            if (discountTypeSelect.value) {
                initializeExistingValues();
            }

            if (revenueStreamSelect) {
                revenueStreamSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const selectedName = selectedOption ? selectedOption.getAttribute('data-name') : '';

                    discountTypeSelect.innerHTML = `
                        <option value="" disabled selected>Select Discount Type</option>
                        <option value="Assembly Discount">Assembly Discount</option>
                        <option value="Division Discount">Division Discount</option>
                        <option value="Block Discount">Block Discount</option>
                    `;

                    if (selectedName && selectedName.includes("Property Rate")) {
                        discountTypeSelect.innerHTML +=
                            `<option value="Property Discount">Property Discount</option>`;
                    }

                    if (selectedName && (selectedName.includes("Business") || selectedName.includes(
                            "BOP"))) {
                        discountTypeSelect.innerHTML +=
                            `<option value="BOP Discount">BOP Discount</option>`;
                    }

                    resetFields();
                });
            }

            if (discountTypeSelect) {
                discountTypeSelect.addEventListener('change', function() {
                    const selected = this.value;
                    resetFields();

                    // Clear dependent dropdowns
                    clearSelect(divisionSelect, 'Select Division');
                    clearSelect(blockSelect, 'Select Block');

                    // Get assembly code (from select or hidden input)
                    const assemblySelect = document.getElementById('assembly');
                    const assemblyCode = assemblySelect ? assemblySelect.value :
                        (document.querySelector('input[name="assembly_code"]')?.value || '');

                    switch (selected) {
                        case "Assembly Discount":
                            if (divisionSelect) divisionSelect.disabled = true;
                            if (blockSelect) blockSelect.disabled = true;
                            if (ermsInput) ermsInput.disabled = true;
                            break;
                        case "Division Discount":
                            if (blockSelect) blockSelect.disabled = true;
                            if (ermsInput) ermsInput.disabled = true;
                            // Load divisions if assembly is selected
                            if (assemblyCode) {
                                loadDivisions(assemblyCode);
                            }
                            break;
                        case "Block Discount":
                            if (ermsInput) ermsInput.disabled = true;
                            // Load divisions if assembly is selected
                            if (assemblyCode) {
                                loadDivisions(assemblyCode);
                            }
                            break;
                        case "Property Discount":
                            if (divisionSelect) divisionSelect.disabled = true;
                            if (blockSelect) blockSelect.disabled = true;
                            if (ermsInput) ermsInput.disabled = false; // Enable for Property Discount
                            break;
                        case "BOP Discount":
                            if (divisionSelect) divisionSelect.disabled = true;
                            if (blockSelect) blockSelect.disabled = true;
                            if (ermsInput) ermsInput.disabled = false; // Enable for BOP Discount
                            break;
                    }

                    if (selected === "Property Discount" || (selected && selected.includes(
                            "Property Rate"))) {
                        loadOptions("{{ route('bills.properties.ajax') }}", 'property');
                    }

                    if (selected === "BOP Discount" || (selected && (selected.includes("Business") ||
                            selected.includes("BOP")))) {
                        loadOptions("{{ route('bills.businesses.ajax') }}", 'business');
                    }
                });
            }

            // Trigger initial state
            discountTypeSelect.dispatchEvent(new Event('change'));

            function loadOptions(url, labelType = 'property') {
                if (!propertySelect) return;

                const selectElement = $('select[name="erms_id[]"]');
                let selectedValues = [];

                // On first load, ALWAYS use the hidden input values
                if (isFirstLoad) {
                    const currentErmsIds = document.getElementById('current_erms_ids')?.value;
                    console.log('First load - current_erms_ids:', currentErmsIds);
                    if (currentErmsIds) {
                        try {
                            const parsedValues = JSON.parse(currentErmsIds);
                            if (Array.isArray(parsedValues)) {
                                selectedValues = parsedValues;
                                console.log('Parsed selected values:', selectedValues);
                            }
                        } catch (e) {
                            console.error('Error parsing current_erms_ids:', e);
                        }
                    }
                    isFirstLoad = false;
                } else {
                    // On subsequent loads, get values from the select
                    if (selectElement.length) {
                        selectedValues = selectElement.val() || [];
                        console.log('Subsequent load - selected values from select:', selectedValues);
                    }
                }

                if (select2Instance) {
                    selectElement.empty().append('<option value="">Loading...</option>').trigger('change');
                }

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Fetched data:', data);
                        propertySelect.innerHTML = '';

                        const emptyOption = document.createElement('option');
                        emptyOption.value = '';
                        emptyOption.textContent = '';
                        propertySelect.appendChild(emptyOption);

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.erms_id;

                            if (labelType === 'property') {
                                option.textContent = `${item.fullname} (${item.erms_id})`;
                            } else if (labelType === 'business') {
                                option.textContent = `${item.fullname} (${item.erms_id})`;
                            }

                            // Convert both to strings for comparison
                            const isSelected = selectedValues.some(val => String(val) === String(item.erms_id));
                            if (isSelected) {
                                option.selected = true;
                                console.log('Selecting option:', item.erms_id);
                            }

                            propertySelect.appendChild(option);
                        });

                        console.log('Values to be selected:', selectedValues);

                        if (select2Instance) {
                            try {
                                selectElement.select2('destroy');
                                initializeSelect2();

                                // Set the values after Select2 is reinitialized
                                selectElement.val(selectedValues).trigger('change');
                                console.log('Select2 values set to:', selectedValues);

                            } catch (e) {
                                console.error('Error refreshing Select2:', e);
                                initializeSelect2();
                            }
                        } else {
                            initializeSelect2();
                            selectElement.val(selectedValues).trigger('change');
                        }
                    })
                    .catch(error => {
                        console.error("Error loading options:", error);
                        if (select2Instance) {
                            selectElement.empty().append('<option value="">Error loading data</option>')
                                .trigger('change');
                        }
                    });
            }
        });
    </script>
@endsection
