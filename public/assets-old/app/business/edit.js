/**
 * Created by degre on 3/13/2021.
 */
/**
 * Created by degre on 3/10/2021.
 */

var BIZ = function () {
    var bizForm = $("#biz-form");
    var btnSave = $("#biz-save-action");
    var btnReset = $("#biz-reset-action");
    var slBizUse = $("#bussType");
    var slBizClass = $('#bussRevId');
    window.revItems = [];
    return {

        init: function () {
            try {
                $(".app-select-2").select2();
                BIZ.validateForm();
                BIZ.loadBizTypes();
                slBizUse.on('select2:select', function (e) {
                    var data = e.params.data;
                    if (data.id !== "Select") {
                        Common.clearSelectField(slBizClass);
                        // var revenueItems = BIZ.getRevenueItems();
                        Common.loadDataInToSelect(slBizClass, data.revenueItems, "revenueId", "revenue");
                    }
                });
            } catch (e) {
                console.log(e);
            }
            btnSave.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BIZ.saveBiz();
            });
            btnReset.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BIZ.resetBizForm();
            });
        },
        saveBiz: function () {
            var ajaxOptions = {
                type: 'POST',
                url: bizForm.attr('data-action'),
                data: bizForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (bizForm.valid()) {
                        Common.showSpinner(bizForm, "Saving...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(bizForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(bizForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(bizForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },

        validateForm: function () {
            Common.validateForm(bizForm, {
                businessName: 'required',
                // registrationNo: 'required',
                // permitNo: 'required',
                phoneNo: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },
                email: {
                    required: true,
                    email: true
                },
                digitaladdress: 'required',
                bussRevId: 'required',
                location: 'required',
                hseno: 'required',
                // streetName: 'required',
                address: 'required',
                quantity: {
                    required: function () {
                        return BIZ.isRated();
                    }
                }

            }, {});

        },
        setRevItem: function () {
            try {
                var revId=bizForm.attr('data-rev-id');
                var data = $.map(revItems, function (obj) {
                    return obj.revenueItems;
                });
                var revItem = $.grep(data, function (v) {
                    return (v.revenueId == revId);
                });
                Common.clearSelectField(slBizClass);
                Common.loadDataInToSelect(slBizClass, revItem, "revenueId", "revenue");
                $("#bussRevId").val(revId).trigger('change');
            } catch (e) {
                console.log(e);
            }
        },
        isRated: function () {
            var data = slBizClass.select2('data')[0];
            if (data.hasOwnProperty('isRated')) {
                return data.isRated;
            }
            return false;
        },
        loadBizTypes: function () {
            Common.showSpinner(bizForm, "Business Types...");
            var requestUrl = bizForm.attr("data-rev-items");
            $.get(requestUrl, {}, function (response) {
                revItems = response.data;
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.category; // replace name with the property used for the text
                    obj.id = obj.categoryId; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slBizUse.select2({data: data});
                }
                BIZ.setRevItem();
            }, "json").always(function () {
                Common.hideSpinner(bizForm);
            });

        },
    };
}();

$(function () {
    BIZ.init();
});