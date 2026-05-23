/**
 * Created by degre on 3/13/2021.
 */
/**
 * Created by degre on 3/13/2021.
 */
var PROP = function () {
    var propForm = $("#prop-form");
    var btnSave = $("#btn-save-action");
    var btnReset = $("#btn-reset-action");
    var slPropUse = $("#propUse");
    var slPropClass = $('#propRevId');
    return {

        init: function () {
            try {
                PROP.validateForm();
                $(".app-select-2").select2();
                PROP.loadPropClassUses();
                slPropUse.on('select2:select', function (e) {
                    var data = e.params.data;
                    if (data.id !== "Select") {
                        Common.clearSelectField(slPropClass);
                        var revenueItems = PROP.getRevenueItems(data.revenueItems);
                        Common.loadDataInToSelect(slPropClass, revenueItems, "revenueId", "revenue");

                    }
                });
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
                    //PROP.resetForm();
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
            var ownerId = propForm.attr('data-owner-id');
            propForm.resetForm();
            propForm.clearForm();
            $("#ownerId").val(ownerId);
            $("#isRated").val('no').trigger('change');
        },
        validateForm: function () {
            Common.validateForm(propForm, {
                digitaladdress: 'required',
                hseno: 'required',
                location: 'required',
               // streetname: 'required',
                isRated: 'required',
                rateableValue: {
                    required: function () {
                        return ($("#isRated").val() === 'yes');
                    },
                    number: true,
                    min: function () {
                        return ($("#isRated").val() === 'yes') ? 1 : 0;
                    }
                },
                vlstNo:{
                    required: function () {
                        return ($("#isRated").val() === 'yes');
                    }
                },
                propRevId: 'required'

            }, {});

        },
        getRevenueItems: function (data) {
            var isRated = ($("#isRated").val() === 'yes') ? true : false;
            return $.grep(data, function (v) {
                return v.isRated === isRated;
            });
        },
        loadPropClassUses: function () {
            Common.showSpinner(propForm, "Property Uses...");
            var requestUrl = propForm.attr("data-rev-items");
            $.get(requestUrl, {}, function (response) {
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.category; // replace name with the property used for the text
                    obj.id = obj.categoryId; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slPropUse.select2({data: data});
                   // var propId = slPropClass.attr('data-prop-rev-id');
                   // slPropClass.val(propId).trigger('change');
                }
            }, "json").always(function () {
                Common.hideSpinner(propForm);
            });

        },
    };
}();

$(function () {
    PROP.init();
});