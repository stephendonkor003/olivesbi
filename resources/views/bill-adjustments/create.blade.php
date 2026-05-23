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
                                Manage and create your bill adjustment here.
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
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('bill-adjustments.store') }}">
                        @csrf

                        <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                        <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">
                        <input type="hidden" id="old_division_id" value="{{ old('division_id') }}">
                        <input type="hidden" id="old_block_id" value="{{ old('block_id') }}">
                        <input type="hidden" id="old_discount_type" value="{{ old('discount_type') }}">
                        <input type="hidden" id="old_assembly_code" value="{{ old('assembly_code') }}">
                        <input type="hidden" id="old_erms_ids" value="{{ json_encode(old('erms_id', [])) }}">

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Revenue Stream Type</label>
                            <select class="form-control @error('revenue_stream_type_id') is-invalid @enderror"
                                id="revenue_stream_type_id" name="revenue_stream_type_id">
                                <option disabled selected>Select Revenue Stream Type</option>
                                @foreach ($revenueStreamTypes as $revenueStreamType)
                                    <option value="{{ $revenueStreamType->id }}" data-name="{{ $revenueStreamType->name }}"
                                        {{ old('revenue_stream_type_id') == $revenueStreamType->id ? 'selected' : '' }}>
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
                                    <option value="{{ $i }}" {{ old('discount_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                value="{{ old('discount_amount') }}">

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
                                <option disabled selected>Select Discount Type</option>
                                <option value="Assembly Discount" {{ old('discount_type') == 'Assembly Discount' ? 'selected' : '' }}>Assembly Discount</option>
                                <option value="Division Discount" {{ old('discount_type') == 'Division Discount' ? 'selected' : '' }}>Division Discount</option>
                                <option value="Block Discount" {{ old('discount_type') == 'Block Discount' ? 'selected' : '' }}>Block Discount</option>
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
                                            {{ old('assembly_code') == $assembly->assembly_code ? 'selected' : '' }}>
                                            {{ $assembly->name }} Assembly
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
                                id="erms_id" multiple>

                            </select>

                            @error('erms_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save</button>
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

                        // Load blocks if division is selected and discount type is Block Discount
                        if (selectedDivisionId && discountTypeSelect.value === 'Block Discount') {
                            const oldBlockId = document.getElementById('old_block_id')?.value;
                            loadBlocks(selectedDivisionId, oldBlockId);
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

            function updateDiscountTypeOptions(selectedName, preserveValue = null) {
                const currentValue = preserveValue || discountTypeSelect.value;

                discountTypeSelect.innerHTML = `
                    <option value="" disabled ${!currentValue ? 'selected' : ''}>Select Discount Type</option>
                    <option value="Assembly Discount" ${currentValue === 'Assembly Discount' ? 'selected' : ''}>Assembly Discount</option>
                    <option value="Division Discount" ${currentValue === 'Division Discount' ? 'selected' : ''}>Division Discount</option>
                    <option value="Block Discount" ${currentValue === 'Block Discount' ? 'selected' : ''}>Block Discount</option>
                `;

                if (selectedName && selectedName.includes("Property Rate")) {
                    discountTypeSelect.innerHTML +=
                        `<option value="Property Discount" ${currentValue === 'Property Discount' ? 'selected' : ''}>Property Discount</option>`;
                }

                if (selectedName && (selectedName.includes("Business") || selectedName.includes("BOP"))) {
                    discountTypeSelect.innerHTML +=
                        `<option value="BOP Discount" ${currentValue === 'BOP Discount' ? 'selected' : ''}>BOP Discount</option>`;
                }
            }

            if (revenueStreamSelect) {
                revenueStreamSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const selectedName = selectedOption ? selectedOption.getAttribute('data-name') : '';

                    updateDiscountTypeOptions(selectedName);
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
                            break;
                        case "BOP Discount":
                            if (divisionSelect) divisionSelect.disabled = true;
                            if (blockSelect) blockSelect.disabled = true;
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

            function loadOptions(url, labelType = 'property') {
                if (!propertySelect) return;

                const selectElement = $('select[name="erms_id[]"]');
                let selectedValues = [];

                // Check for old values from validation failure
                const oldErmsIds = document.getElementById('old_erms_ids')?.value;
                if (oldErmsIds) {
                    try {
                        const parsedOldValues = JSON.parse(oldErmsIds);
                        if (Array.isArray(parsedOldValues) && parsedOldValues.length > 0) {
                            selectedValues = parsedOldValues;
                        }
                    } catch (e) {
                        console.error('Error parsing old_erms_ids:', e);
                    }
                }

                // If no old values, try to get from current select
                if (selectedValues.length === 0 && selectElement.length) {
                    selectedValues = selectElement.val() || [];
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
                            }

                            propertySelect.appendChild(option);
                        });

                        if (select2Instance) {
                            try {
                                selectElement.select2('destroy');
                                initializeSelect2();

                                // Set the values after Select2 is reinitialized
                                selectElement.val(selectedValues).trigger('change');

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

            // Initialize old values after validation failure
            function initializeOldValues() {
                const oldDiscountType = document.getElementById('old_discount_type')?.value;
                const oldDivisionId = document.getElementById('old_division_id')?.value;
                const oldAssemblyCode = document.getElementById('old_assembly_code')?.value;

                // If there are old values (after validation failure), restore the form state
                if (oldDiscountType) {
                    // Get the currently selected revenue stream type to add Property/BOP options if needed
                    const selectedRevenueOption = revenueStreamSelect.options[revenueStreamSelect.selectedIndex];
                    const selectedRevenueName = selectedRevenueOption ? selectedRevenueOption.getAttribute('data-name') : '';

                    // Update discount type options while preserving the old value
                    if (selectedRevenueName) {
                        updateDiscountTypeOptions(selectedRevenueName, oldDiscountType);
                    }

                    // Get assembly code (from old value or current user assignment)
                    const assemblySelect = document.getElementById('assembly');
                    const assemblyCode = oldAssemblyCode ||
                        (assemblySelect ? assemblySelect.value : null) ||
                        (document.querySelector('input[name="assembly_code"]')?.value || '');

                    // Trigger discount type change to set up fields correctly
                    if (discountTypeSelect.value) {
                        const event = new Event('change');
                        discountTypeSelect.dispatchEvent(event);
                    }

                    // Load divisions/blocks if needed
                    if ((oldDiscountType === 'Division Discount' || oldDiscountType === 'Block Discount') && assemblyCode) {
                        loadDivisions(assemblyCode, oldDivisionId);
                    }
                }
            }

            // Call initialization after page load
            initializeOldValues();
        });
    </script>
@endsection
