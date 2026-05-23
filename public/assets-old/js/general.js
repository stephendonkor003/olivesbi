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

    if ($('select[name="bin_type_id"]').length > 0 && $('input[name="rate"]').length > 0) {
        $(document).on('change', 'select[name^="bin_data"][name$="[bin_type_id]"]', function () {
            customerBin($(this).val(), this);
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
                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='division_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].division_name + "</option>");
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
                    $("select[name='block_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].block_name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function district(branch_id) {
        var url = $("input[name='district_url']").attr("url");
        var formData = new FormData();
        formData.append("branch_id", branch_id);

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
                $("select[name='district_id']").html("");
                $("select[name='district_id']").append("<option value=''>Select District</option>");
                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='district_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

    function community(district_id) {
        var url = $("input[name='community_url']").attr("url");
        var formData = new FormData();
        formData.append("district_id", district_id);

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
                $("select[name='community_id']").html("");
                $("select[name='community_id']").append("<option value=''>Select Community</option>");
                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='community_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
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
        } else {
            $('.phone').slideDown();
            $('.network').slideDown();
        }
    }
});