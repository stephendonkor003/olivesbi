$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('select[name="regional_code"]').length > 0) {
        $('select[name="regional_code"]').on('change', function () {
            userAssembly($(this).val());
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
                $("select[name='assembly_code']").html("");
                $("select[name='assembly_code']").append("<option value=''>Select Assembly</option>");
                for (var i = 0; i < response.message.length; i++) {

                    $("select[name='assembly_code']").append(
                        "<option value='" + response.message[i].assembly_code + "'>" + response.message[i].name + "</option>"
                    );
                }
            },
            error: function (error) {
                //alert(error.statusText + error.responseText);
            },
        });
    }

});
