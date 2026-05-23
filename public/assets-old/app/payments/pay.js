/**
 * Created by degre on 4/5/2021.
 */

var IPay = function () {
    var paymentForm = $("#payment-form");
    var paymentModal = $("#payment-modal");
    var slMode = $("#paymentMode");
    var momoDiv = $("#momo-div");
    var cheqDiv = $("#cheque-div");
    var cardDiv = $("#card-div");
    var btnPay = $("#btn-pay");
    return {
        hideAll: function () {
            momoDiv.hide();
            cardDiv.hide();
            cheqDiv.hide();
        },
        init: function () {
            IPay.hideAll();
            slMode.select2({placeholder: 'Select Payment Mode'});
            IPay.validateForm();
            try {
                IPay.loadPaymentModes();
            } catch (e) {
            }
            btnPay.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                IPay.pay();
            });
            slMode.on('change', function (e) {
                switch ($(this).val()) {
                    case "cash":
                        IPay.hideAll();
                        break;
                    case 'momo':
                        IPay.hideAll();
                        momoDiv.show();
                        break;
                    case 'cheq':
                        IPay.hideAll();
                        cheqDiv.show();
                        break;
                    case 'visa':
                    case 'mast':
                        IPay.hideAll();
                        cardDiv.show();
                        break;
                    default:
                        IPay.hideAll();
                        break;

                }
            });

        },
        onPay: function (invoice) {
            $("#invoiceNo").val(invoice.invoiceNo);
            $("#payment-modal-head-title").text('Payment Details For Invoice #' + invoice.invoiceNo);
            paymentModal.modal('show');

        },
        pay: function () {
            var ajaxOptions = {
                type: 'POST',
                url: paymentForm.attr('data-action'),
                data: paymentForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (paymentForm.valid()) {
                        Common.showSpinner(paymentForm, "Processing...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(paymentForm);
                if (response.status === "ok" && response.code === "00") {
                    IPay.resetPayForm();
                    Common.showMessage(response.message);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(paymentForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(paymentForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        loadPaymentModes: function () {
            Common.showSpinner(paymentForm, "Loading Payment Modes...");
            var requestUrl = paymentForm.attr("data-pay-modes");
            $.get(requestUrl, {}, function (response) {
                //console.log(response);
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.text || obj.name; // replace name with the property used for the text
                    obj.id = obj.id || obj.id; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slMode.select2({data: data, placeholder: 'Select Payment Mode'});
                    // slMode.empty();
                    // slMode.append(new Option("Choose", "", true, true));
                    // response.data.forEach(function (item) {
                    //     slMode.append(new Option(item.name, item.id, false, false));
                    // });
                }
            }, "json").always(function () {
                Common.hideSpinner(paymentForm);
            });

        },
        resetPayForm:function () {
            paymentForm.clearForm();
            paymentForm.resetForm();
            paymentModal.modal('hide');
        },
        validateForm: function () {
            Common.validateForm(paymentForm, {
                paymentMethodId: 'required',
                amountPaid: {
                    required: true,
                    currency: ['$', false]
                },
                gcrNo: 'required',
                network: {
                    required: function () {
                        return slMode.val() === 'momo';
                    }
                },
                momoNumber: {
                    required: function () {
                        return slMode.val() === 'momo';
                    },
                    digits:true
                },
                voucherCode: {
                    required: function () {
                        return ((slMode.val() === 'momo') && ($("#network").val() === 'vodafone'));
                    }
                },
                chequeNo: {
                    required: function () {
                        return slMode.val() === 'cheq';
                    },
                    digits:true
                },
                chequeDate: {
                    required: function () {
                        return slMode.val() === 'cheq';
                    }
                },
                bank: {
                    required: function () {
                        return slMode.val() === 'cheq';
                    }
                },
                cardNo: {
                    required: function () {
                        return ((slMode.val() === 'visa') || (slMode.val() === 'mast'));
                    }
                },
                cvvCode: {
                    required: function () {
                        return ((slMode.val() === 'visa') || (slMode.val() === 'mast'));
                    }
                },
                expiryDate: {
                    required: function () {
                        return ((slMode.val() === 'visa') || (slMode.val() === 'mast'));
                    }
                },
                paidBy: 'required',
                phoneNo: {
                    required: true,
                    minlength: 10,
                    maxlength: 10
                }


            }, {});
        }
    };
}();

$(function () {
    IPay.init();
});