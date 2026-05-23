$(document).ready(function () {

    $(document).on('change', '.check_all', function () {
        if ($(this).is(':checked')) {
            $(this).closest('.check_group')
                .find('.form-check-input')
                .each(function () {
                    $(this).prop("checked", true)
                });
        } else {
            $(this).closest('.check_group')
                .find('.form-check-input')
                .each(function () {
                    $(this).prop("checked", false)
                });
        }
    });
});