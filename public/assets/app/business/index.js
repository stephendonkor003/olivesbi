/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/18/2021.
 */

var BIZ = function () {
    var BIZForm = $("#divisions-form");
    var BIZTable = $("#business-table");
    var BIZModal = $("#business-modal");
    var BIZDT = null;
    var bizClassForm = $("#biz-class-form");
    var bizClassModal = $("#biz-class-modal");
    var slBizUse = $("#bussType");
    var slBizClass = $('#bussRevId');
    window.revItems = [];
    return {
        initComponents: function () {
            $(".app-select-2").select2();
            BIZ.validateBizClassifyForm();
            BIZ.initDataTable();

            BIZ.loadBizTypes();
            slBizUse.on('select2:select', function (e) {
                var data = e.params.data;
                if (data.id !== "Select") {
                    Common.clearSelectField(slBizClass);
                    // var revenueItems = BIZ.getRevenueItems();
                    Common.loadDataInToSelect(slBizClass, data.revenueItems, "revenueId", "revenue");
                }
            });
        },
        init: function () {
            BIZ.initComponents();

            $(document).on('click', '#btn-classify-biz', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BIZ.reClassifyAction();
            });
            $(document).on('click', '#business-table tbody tr .btn-classify-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var table = $(this).closest('table');
                //var row = $(this).closest('tr');
                var current_row = $(this).parents('tr');//Get the current row
                if (current_row.hasClass('child')) {//Check if the current row is a child row
                    current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
                }
                var data = table.DataTable().row(current_row).data();
                current_row.prop('id', 'row-' + data.id);
                BIZ.onReclassify(data);
            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BIZ.saveBusiness();
            });

        },
        onReclassify: function (record) {
            $("#bizId").val(record.id);
            $("#biz-reclassify-modal-head-title").text("Reclassify " + record.businessName);
            $("#quantity").val(record.quantity);
            BIZ.setRevItem(record);
            bizClassModal.modal('show');
        },
        reClassifyAction: function () {
            var ajaxOptions = {
                type: 'POST',
                url: bizClassForm.attr('data-action'),
                data: bizClassForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (bizClassForm.valid()) {
                        Common.showSpinner(bizClassModal, "Saving...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(bizClassModal);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    BIZ.reloadTable();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(bizClassModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(bizClassModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        reloadTable: function () {
            try {
                BIZDT.ajax.reload();
            } catch (e) {
            }
        },

        validateBizClassifyForm: function () {
            Common.validateForm(bizClassForm, {
                quantity: {
                    required: function () {
                        return BIZ.isRated();
                    },
                    digits: true
                },
                bussRevId: 'required'
            }, {});

        },
        initDataTable: function () {
            BIZDT = BIZTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": BIZTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    // {"data": "registrationNo"},
                    {"data": "businessName"},
                    {"data": "accountNo"},
                    {"data": "phoneNo"},
                    {"data": "permitNo"},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return BIZDT;
        },
        isRated: function () {
            var data = slBizClass.select2('data')[0];
            if (data.hasOwnProperty('isRated')) {
                return data.isRated;
            }
            return false;
        },
        loadBizTypes: function () {
            // Common.showSpinner(bizForm, "Loading Business Types...");
            var requestUrl = BIZTable.attr("data-rev-items");
            $.get(requestUrl, {}, function (response) {
                revItems=response.data;
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.category; // replace name with the property used for the text
                    obj.id = obj.categoryId; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slBizUse.select2({data: data});
                }
            }, "json").always(function () {
                // Common.hideSpinner(bizForm);
            });

        },
        setRevItem: function (record) {
            try {
                var data = $.map(revItems, function (obj) {
                    return obj.revenueItems;
                });
                var revItem = $.grep(data, function (v) {
                    return (v.revenueId == record.bussRevId);
                });
                Common.clearSelectField(slBizClass);
                Common.loadDataInToSelect(slBizClass, revItem, "revenueId", "revenue");
                $("#bussRevId").val(record.bussRevId).trigger('change');
            } catch (e) {
                console.log(e);
            }
        },
    };
}();
$(function () {
    BIZ.init();
});