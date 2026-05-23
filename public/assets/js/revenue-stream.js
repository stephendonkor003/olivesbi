$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    is_rated = $("select[name='is_rated']").val();
    hideAndshow(is_rated);

    $("select[name='is_rated']").on('change', function () {
        if ($(this).val() === 'Y') {
            $("#input3").show();
            $("#input4").show();

            $("#input5").hide();
            $("#input6").hide();
            $("#input7").hide();
        } else if ($(this).val() === 'N') {
            $("#input3").hide();

            $("#input5").show();
            $("#input6").show();
            $("#input7").show();
        }
    });

    function hideAndshow(is_rated)
    {
        if (is_rated === 'Y') {
            $("#input3").show();
            $("#input4").show();

            $("#input5").hide();
            $("#input6").hide();
            $("#input7").hide();
        } else if (is_rated === 'N') {
            $("#input3").hide();

            $("#input5").show();
            $("#input6").show();
            $("#input7").show();
        }
    }

    // Handle assembly selection change for super users
    $('#assembly').on('change', function() {
        const selectedAssemblyCode = $(this).val();

        if (!selectedAssemblyCode) {
            // If no assembly selected, show all options
            showAllOptions('#zone_type_id');
            showAllOptions('#property_classes_id');
            showAllOptions('#property_type_id');
            return;
        }

        // Filter all assembly-dependent dropdowns
        filterDropdownByAssembly('#zone_type_id', selectedAssemblyCode);
        filterDropdownByAssembly('#property_classes_id', selectedAssemblyCode);
        filterDropdownByAssembly('#property_type_id', selectedAssemblyCode);
    });

    // Initialize filtering on page load if assembly is already selected
    const initialAssemblyCode = $('#assembly').val();
    if (initialAssemblyCode && !$('#assembly').is(':disabled')) {
        filterDropdownByAssembly('#zone_type_id', initialAssemblyCode);
        filterDropdownByAssembly('#property_classes_id', initialAssemblyCode);
        filterDropdownByAssembly('#property_type_id', initialAssemblyCode);
    }

    // Function to filter dropdown options by assembly code
    function filterDropdownByAssembly(selectId, assemblyCode) {
        const $select = $(selectId);
        const currentValue = $select.val();

        // Show/hide options based on assembly code
        let hasMatchingOption = false;
        $select.find('option').each(function() {
            const $option = $(this);
            const optionAssemblyCode = $option.data('assembly-code');

            // Always show the placeholder option
            if (!optionAssemblyCode) {
                return;
            }

            // Show only options matching the selected assembly
            if (optionAssemblyCode === assemblyCode) {
                $option.show();
                if ($option.val() === currentValue) {
                    hasMatchingOption = true;
                }
            } else {
                $option.hide();
            }
        });

        // Reset the dropdown if current value doesn't match the assembly
        if (!hasMatchingOption && currentValue) {
            $select.val('');
        }
    }

    // Function to show all options in a dropdown
    function showAllOptions(selectId) {
        const $select = $(selectId);
        $select.find('option').show();
        $select.val('');
    }
});