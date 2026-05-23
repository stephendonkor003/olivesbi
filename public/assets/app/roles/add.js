/**
 * Created by degre on 4/8/2021.
 */

var ROLE = function () {
    var $form = $("#role-form");
    var btnSave = $("#btn-save-action");
    return {
        init: function () {
            ROLE.validateForm();
            btnSave.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ROLE.save();
            });
        },

        save: function () {
            var ajaxOptions = {
                type: 'POST',
                url: $form.attr('data-action'),
                data: $form.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if ($form.valid()) {
                        Common.showSpinner($form, "Saving...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner($form);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    ROLE.resetForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner($form);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner($form);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        resetForm: function () {
            $form.resetForm();
            $form.clearForm();
        },
        validateForm: function () {
            Common.validateForm($form, {
                name: 'required',
                isActive: 'required',
                isVisible: 'required',
                isGlobal: {
                    required: function () {
                        var isGlobal = parseInt($form.attr('data-global'));
                        return isGlobal < 4;
                    }
                }


            }, {});

        },
    };
}();

$(function () {
    ROLE.init();
});