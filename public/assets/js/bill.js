$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('select[name="assembly_code"]').change(function () {
        var option = $(this).data('option');

        if (option == 'bop') {
            fetchBusiness($(this).val());
        } else {
            fetchProperty($(this).val());
        }
    });

    function fetchProperty(assembly_code) {
        var url = $("input[name='property_url']").attr("url");
        var formData = new FormData();
        formData.append("assembly_code", assembly_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='property_id[]']").html("");

                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='property_id[]']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }

                $("select[name='property_id[]").bootstrapDualListbox('refresh', true);
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function fetchBusiness(assembly_code) {
        var url = $("input[name='business_url']").attr("url");
        var formData = new FormData();
        formData.append("assembly_code", assembly_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='business_id[]']").html("");

                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='business_id[]']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }

                $("select[name='business_id[]").bootstrapDualListbox('refresh', true);
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    $('#preview-bills').on('click', function() {
        let url = $("input[name='preview_url']").attr("url");

        // Get selected property IDs
        let propertyIds = $('select[name="property_id[]"]').val() || [];

        // Prepare JSON payload to avoid max_input_vars limit
        let payload = {
            assembly_code: $('input[name="assembly_code"]').val() || $('select[name="assembly_code"]').val(),
            bills_year: $('select[name="bills_year"]').val(),
            property_id: propertyIds
        };

        $.ajax({
            url: url,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function() {
                $('#preview-tbody').html(
                    '<tr><td colspan="9">Loading preview...</td></tr>'
                );
                $('#bill-preview-section').removeClass('d-none');
            },
            success: function(response) {
                if (response.error) {
                    $('#preview-tbody').html(
                        '<tr><td colspan="9" class="text-danger">' + response.error + '</td></tr>');
                    return;
                }

                if (response.data.length === 0) {
                    $('#preview-tbody').html(
                        '<tr><td colspan="9">No preview available.</td></tr>');
                    return;
                }

                let rows = '';
                response.data.forEach(function(item, index) {
                    rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.property_no}</td>
                    <td>${item.owner_name}</td>
                    <td>${item.rateable_value}</td>
                    <td>${item.rate_impost ?? '0.00'}</td>
                    <td>${item.flat_rate ?? '0.00'}</td>
                    <td>${item.min_amount ?? '0.00'}</td>
                    <td>${item.discount ?? '0.00'}</td>
                    <td><strong>${item.final_bill}</strong></td>
                </tr>`;
                });

                $('#preview-tbody').html(rows);
            },
            error: function(xhr) {
                let errorMsg = 'Failed to load preview.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                $('#preview-tbody').html(
                    '<tr><td colspan="9" class="text-danger">' + errorMsg + '</td></tr>');
            }
        });
    });

    $('#preview-bills-bus').on('click', function() {
        let url = $("input[name='preview_url']").attr("url");

        // Get selected business IDs (or empty array for bulk preview)
        let businessIds = $('select[name="business_id[]"]').val() || [];

        // Prepare JSON payload to avoid max_input_vars limit
        let payload = {
            assembly_code: $('input[name="assembly_code"]').val() || $('select[name="assembly_code"]').val(),
            bills_year: $('select[name="bills_year"]').val(),
            business_id: businessIds
        };

        $.ajax({
            url: url,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function() {
                $('#preview-tbody').html(
                    '<tr><td colspan="8">Loading preview...</td></tr>'
                );
                $('#bill-preview-section').removeClass('d-none');
            },
            success: function(response) {
                if (response.error) {
                    $('#preview-tbody').html(
                        '<tr><td colspan="8" class="text-danger">' + response.error + '</td></tr>');
                    return;
                }

                if (response.data.length === 0) {
                    $('#preview-tbody').html(
                        '<tr><td colspan="8">No preview available.</td></tr>');
                    return;
                }

                let rows = '';
                response.data.forEach(function(item, index) {
                    rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.business_no}</td>
                    <td>${item.owner_name}</td>
                    <td>${item.rate_impost ?? '0.00'}</td>
                    <td>${item.flat_rate ?? '0.00'}</td>
                    <td>${item.min_amount ?? '0.00'}</td>
                    <td>${item.discount ?? '0.00'}</td>
                    <td><strong>${item.final_bill}</strong></td>
                </tr>`;
                });

                $('#preview-tbody').html(rows);
            },
            error: function(xhr) {
                let errorMsg = 'Failed to load preview.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                $('#preview-tbody').html(
                    '<tr><td colspan="8" class="text-danger">' + errorMsg + '</td></tr>');
            }
        });
    });

    $('[name="property_id[]"]').bootstrapDualListbox({
        nonselectedlistlabel: 'Non-selected',
        selectedlistlabel: 'Selected',
        preserveselectiononmove: 'moved',
        moveonselect: false
    });

    $('[name="business_id[]"]').bootstrapDualListbox({
        nonselectedlistlabel: 'Non-selected',
        selectedlistlabel: 'Selected',
        preserveselectiononmove: 'moved',
        moveonselect: false
    });
});