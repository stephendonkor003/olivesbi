$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
});