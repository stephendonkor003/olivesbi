/**
 * Created by degre on 8/16/2021.
 */
/**
 * Created by degre on 1/16/2021.
 */

var PWD = function () {

    var pwdForm = $("#pwd-form");
    var pwdModal = $("#pwd-modal");
    var lnkPwd=$("#lnk-pwd-reset");
    return {
        initComponents: function () {

            try {
                PWD.validateForm();
            } catch (e) {

            }
        },
        init: function () {
            PWD.initComponents();

            $(document).on('click', '#lnk-pwd-reset', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                pwdModal.modal('show');
            });

            $(document).on('click', '#btn-reset-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                PWD.fireReset();
            });
        },

        getModal: function () {
            return pwdModal;
        },
        getForm: function () {
            return pwdForm;
        },

        validateForm: function () {
            Common.validateForm(pwdForm, {
                oldPassword: 'required',
                newPassword: 'required',
                cpassword: {
                    required: true,
                    equalTo: "#pwd"
                }
            }, {
                // file: "File must be JPEG or PNG"
            });
        },
        fireReset: function () {
            var ajaxOptions = {
                type: 'POST',
                url: pwdForm.attr('data-action'),
                data: pwdForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (pwdForm.valid()) {
                        Common.showSpinner(pwdForm, "Saving..");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(pwdForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    PWD.resetPwdForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(pwdForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(pwdForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },


        resetPwdForm: function () {
            Common.resetForm(pwdForm, null);
            pwdModal.modal('hide');
        }

    };
}();

$(function () {
    PWD.init();
});