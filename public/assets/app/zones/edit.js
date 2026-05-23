
var PROP = function () {
    var propForm = $("#zone-form");
    var btnSave = $("#btn-save-action");
    var btnReset = $("#btn-reset-action");
    return {

        init: function () {
            try {
                PROP.validateForm();
                $(".app-select-2").select2();
            } catch (e) {
                console.log(e);
            }
            btnSave.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                PROP.save();
            });
            btnReset.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                PROP.resetForm();
            });
        },
        save: function () {
            var ajaxOptions = {
                type: 'POST',
                url: propForm.attr('data-action'),
                data: propForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (propForm.valid()) {
                        Common.showSpinner(propForm, "Saving...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(propForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                  //  PROP.resetForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(propForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(propForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        resetForm: function () {
            propForm.resetForm();
            propForm.clearForm();
        },
        validateForm: function () {
            Common.validateForm(propForm, {
                zone: 'required',
                classId: 'required',
                blockId: 'required'
            }, {});

        },
    };
}();

$(function () {
    PROP.init();
});