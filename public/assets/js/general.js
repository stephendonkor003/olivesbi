$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('select[name="cluster_id"]').change(function () {
        business_center($(this).val());
    });

    if ($('select[name="property_use_id"]').length > 0) {
        $('select[name="zone_id"]').change(function () {
            propertyUser($(this).val());
        });
    }

    if ($('select[name="division_id"]').length > 0) {
        $('select[name="assembly_code"]').change(function () {
            division($(this).val());
        });
    }

    if ($('select[name="block_id"]').length > 0) {
        $('select[name="division_id"]').change(function () {
            block($(this).val());
        });
    }

    if ($('select[name="payment_mode"]').length > 0) {
        $('select[name="payment_mode"]').change(function() {
            togglePaymentModeDetails();
        });
    }

    if ($('input[name="minimum_rate"]').length > 0) {
        $('select[name="assembly_code"]').change(function() {
            zone($(this).val());
        });
    }

    if ($('select[name="bin_type_id"]').length > 0 && $('input[name="rate"]').length > 0) {
        $(document).on('change', 'select[name^="bin_data"][name$="[bin_type_id]"]', function () {
            customerBin($(this).val(), this);
        });
    }

    if ($('select[name="regional"]').length > 0) {
        $('select[name="regional"]').change(function() {
            $('input[name="regional_code"]').val($(this).val());

            if ($(this).val() === 'OTH') {
                convertToInput();
            } else {
                convertToSelect($(this).val());
            }
        });
    }

    if ($('select[name="name"]').length > 0) {
        if ($('input[name="regional_code"]').length > 0) {
            if ($('input[name="assembly_code"]').length > 0) {
                $(document).on('change', 'select[name="name"]', function () {
                    let assembly_code = $(this).find(':selected').attr('assembly_code');

                    if (assembly_code) {
                        if (assembly_code === "OTH") {
                            convertToInput();
                        } else {
                            fetchAssembly(assembly_code);
                        }
                    }
                });
            }
        }
    }

    if ($('select[name="regional_code"]').length > 0) {
        $('select[name="name"]').on('change', function () {
            let code = $(this).find(':selected').attr('code');
            $('input[name="assembly_code"]').val(code);
        });
    }

    function propertyUser(zone_id) {
        var url = $("input[name='property_use_url']").attr("url");
        var formData = new FormData();
        formData.append("zone_id", zone_id);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='property_use_id']").html("");
                $("select[name='property_use_id']").append("<option value=''>Select Property Use</option>");
                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='property_use_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function zone(assembly_code) {
        var url = $("input[name='zone_url']").attr("url");
        var formData = new FormData();
        formData.append("assembly_code", assembly_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='zone_id']").html("");
                $("select[name='zone_id']").append("<option value=''>Select Zone</option>");

                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='zone_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function division(assembly_code) {
        var url = $("input[name='division_url']").attr("url");
        var formData = new FormData();
        formData.append("assembly_code", assembly_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='division_id']").html("");
                $("select[name='division_id']").append("<option value=''>Select Division</option>");
                for (var i = 0; i < response.data.length; i++) {
                    $("select[name='division_id']").append("<option value=" + response.data[i].id + ">" +
                        response.data[i].name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function block(division_id) {
        var url = $("input[name='block_url']").attr("url");
        var formData = new FormData();
        formData.append("division_id", division_id);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("select[name='block_id']").html("");
                $("select[name='block_id']").append("<option value=''>Select Block</option>");
                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='block_id']").append("<option geofence='" + JSON.stringify(response.message[i].geofence) + "' value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function assembly(regional_code) {
        var url = $("input[name='assembly_url']").attr("url");
        var formData = new FormData();
        formData.append("regional_code", regional_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                // $('#register').preloader();
                // $("#send").prop("disabled", true);
            },
            success: function (response) {
                console.log(response);
                $("select[name='name']").html("");
                $("select[name='name']").append("<option value=''>Select Assembly</option>");
                for (var i = 0; i < response.message.length; i++) {

                    $("select[name='name']").append(
                        "<option assembly_code='" + response.message[i].assembly_code + "' regional_code ='" + response.message[i].regional_code+"' value='" + response.message[i].name + "'>" +
                        response.message[i].name +
                        "</option>"
                    );
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function fetchAssembly(assembly_code) {
        var url = $("input[name='fetch_assembly_url']").attr("url");
        var formData = new FormData();
        formData.append("assembly_code", assembly_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                // $('#register').preloader();
                // $("#send").prop("disabled", true);
            },
            success: function (response) {
                console.log(response.message);
                $('input[name="assembly_id"]').val(response.message.id);
                $('input[name="regional_code"]').val(response.message.regional_code);
                $('input[name="assembly_code"]').val(response.message.assembly_code);
                $('select[name="assembly_category"]').val(response.message.assembly_category);
                $('input[name="address"]').val(response.message.address);
                $('input[name="phone"]').val(response.message.phone);
                $('input[name="supervisor_id"]').val(response.message.assembly_supervisor.name +" - "+ response.message.assembly_supervisor.phone);
                $('input[name="supervisor"]').val(response.message.assembly_supervisor.id);
                $('input[name="bank_name"]').val(response.message.bank_name);
                $('input[name="bank_number"]').val(response.message.bank_number);
                $('input[name="bank_branch"]').val(response.message.bank_branch);

                var logoContainer = $('#assembly-logo-container');
                if(response.message.logo) {
                    logoContainer.html(
                        `<label>Assembly Logo</label>
                        <img src="/storage/images/logo/${response.message.logo}"
                            width="300" height="340" style="border-radius: 10px;">`
                    );
                } else {
                    logoContainer.html('<h4 style="color:red">No logo uploaded for the selected assembly</h4>');
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function userAssembly(regional_code) {
        var url = $("input[name='assembly_url']").attr("url");
        var formData = new FormData();
        formData.append("regional_code", regional_code);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                // $('#register').preloader();
                // $("#send").prop("disabled", true);
            },
            success: function (response) {
                $("select[name='name']").html("");
                $("select[name='name']").append("<option value=''>Select Assembly</option>");
                for (var i = 0; i < response.message.length; i++) {

                    $("select[name='name']").append(
                        "<option value='" + response.message[i].assembly_code + "'>" + response.message[i].assembly_name + "</option>"
                    );
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function customerBin(bin_type_id, element) {
        var url = $("input[name='bin_type_url']").attr("url");
        var formData = new FormData();
        formData.append("bin_type_id", bin_type_id);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $(element).closest('[data-repeater-item]').find('input[name^="bin_data"][name$="[rate]"]').val(response.message.rate);
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function togglePaymentModeDetails() {
        var paymentMode = $('select[name="payment_mode"]').val();

        if (paymentMode === 'cash') {
            $('.phone').slideUp();
            $('.network').slideUp();
            $('.receipt').slideDown();
        } else {
            $('.phone').slideDown();
            $('.network').slideDown();
            $('.receipt').slideUp();
        }
    }

    function clearInput() {
        $('input[name="assembly_id"]').val("");
        $('input[name="assembly_code"]').val("");
        $('select[name="assembly_category"]').val("");
        $('input[name="address"]').val("");
        $('input[name="phone"]').val("");
        $('input[name="supervisor_id"]').val("");
        $('input[name="supervisor"]').val("");
        $('input[name="bank_name"]').val("");
        $('input[name="bank_number"]').val("");
        $('input[name="bank_branch"]').val("");
        $('input[name="logo"]').val("");
        $('#assembly-logo-container').empty();
    }

    function convertToInput() {
        var selectElement = $('#name');
        var nameAttr = selectElement.attr('name');
        var idAttr = selectElement.attr('id');
        var classAttr = selectElement.attr('class');

        var inputElement = $('<input>', {
            type: 'text',
            name: nameAttr,
            id: idAttr,
            class: classAttr,
            placeholder: 'Enter Assembly Name'
        });

        selectElement.replaceWith(inputElement);
        inputElement.focus();
        clearInput();
    }

    function convertToSelect(input) {
        var inputElement = $('#name');
        var nameAttr = inputElement.attr('name');
        var idAttr = inputElement.attr('id');
        var classAttr = inputElement.attr('class');

        var selectElement = $('<select>', {
            name: nameAttr,
            id: idAttr,
            class: classAttr
        });

        selectElement.append('<option disabled selected>Select Assembly Name</option>');

        assembly(input, selectElement);

        inputElement.replaceWith(selectElement);
    }
});
