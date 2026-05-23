/**
 * Created by degre on 3/12/2021.
 */
/**
 * Created by degre on 3/10/2021.
 */

var EOwner = function () {
    var ownerForm = $("#owner-form");
    var btnSave = $("#btn-save-action");
    var slEntityType = $('#entityTypeId');
    return {
        hideAll: function () {
            $("#idiv").hide();
            $("#odiv").hide();
            var val = slEntityType.val();//$("#pg-ct").attr('data-entity');
            switch (val) {
                case 'i':
                    $("#odiv").hide();
                    $("#idiv").show();
                    break;
                case 'o':
                    $("#idiv").hide();
                    $("#odiv").show();
                    break;
                default:
                    $("#idiv").hide();
                    $("#odiv").hide();
                    break;

            }
        },
        init: function () {
            EOwner.hideAll();
            try {
                EOwner.validateForm();
            } catch (e) {
                console.log(e);
            }
            btnSave.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                EOwner.saveOwner();
            });
            $(document).on('change', '#entityTypeId', function (e) {
                var val = $(this).val();
                switch (val) {
                    case 'i':
                        EOwner.hideAll();
                        $("#idiv").show();
                        break;
                    case 'o':
                        EOwner.hideAll();
                        $("#odiv").show();
                        break;
                    default:
                        EOwner.hideAll();
                        break;

                }
            });

        },
        saveOwner: function () {
            var ajaxOptions = {
                type: 'POST',
                url: ownerForm.attr('data-action'),
                data: ownerForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (ownerForm.valid()) {
                        Common.showSpinner(ownerForm, "Saving...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(ownerForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(ownerForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(ownerForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        resetForm: function () {
            ownerForm.resetForm();
            ownerForm.clearForm();
        },
        validateForm: function () {
            Common.validateForm(ownerForm, {
                entityTypeId: 'required',
                fname: {
                    required: function () {
                        return (slEntityType.val() === 'i');
                    }
                },
                lname: {
                    required: function () {
                        return (slEntityType.val() === 'i');
                    }
                },
                gender: {
                    required: function () {
                        return (slEntityType.val() === 'i');
                    }
                },
                // tinNo: {
                //     required: function () {
                //         return (slEntityType.val() === 'i');
                //     }
                // },
                organizationName: {
                    required: function () {
                        return (slEntityType.val() === 'o');
                    }
                },
                email: {
                    email: true
                },
                phoneNoPrimary: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },
                phoneNoSecondary: {
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },


                // houseNo: 'required',
                // digitalAddress: 'required',
                // postalAddress: 'required'
            }, {});

        },

    };
}();

$(function () {
    EOwner.init();
});
