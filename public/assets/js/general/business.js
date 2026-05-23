$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('select[name="business_type"]').change(function () {
        businessClass($(this).val());
    });

    $('#entity_type').change(function() {
        var selectedType = $(this).val();
        var individualFields = $('#individual');
        var organizationFields = $('#organization');

        if (selectedType === 'individual') {
            individualFields.show();
            organizationFields.hide();
        } else if (selectedType === 'organization') {
            individualFields.hide();
            organizationFields.show();
        } else {
            individualFields.hide();
            organizationFields.hide();
        }
    });

    function businessClass(business_type) {
        var url = $("input[name='business_class_url']").attr("url");
        var formData = new FormData();
        formData.append("business_type", business_type);

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
                $("select[name='business_class']").html("");
                $("select[name='business_class']").append("<option disabled selected>Select Business Class</option>");

                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='business_class']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                alert(error.statusText + error.responseText);
            },
        });
    }

    $(".organization").repeater({
        initEmpty: false,
        isFirstItemUndeletable: true,
        show: function() {
            $(this).slideDown();
            $(this).find('select option:first').prop('selected', true);
        },
        hide: function(deleteElement) {
            if (confirm("Are you sure you want to delete this element?")) {
                $(this).slideUp(deleteElement);
            }
        },
    });

});