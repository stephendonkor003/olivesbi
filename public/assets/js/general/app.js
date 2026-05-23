$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('select[name="assembly_code"]').change(function () {
        divisions($(this).val());
    });

    function divisions(assembly_code) {
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
                $("select[name='division_id']").append("<option disabled selected>Select Division</option>");

                for (var i = 0; i < response.message.length; i++) {
                    $("select[name='division_id']").append("<option value=" + response.message[i].id + ">" +
                        response.message[i].name + "</option>");
                }
            },
            error: function (error) {
                alert(error.statusText + error.responseText);
            },
        });
    }

});