/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/18/2021.
 */

var PROP = function () {
    var PROPTable = $("#properties-table");
    var PROPDT = null;
    var propClassForm = $("#biz-class-form");
    var propClassModal = $("#biz-class-modal");
    var slPropUse = $("#propUse");
    var slPropClass = $('#propRevId');
    window.revItems = [];
    return {
        initComponents: function () {
            $(".app-select-2").select2({
                dropdownParent: $('.modal')
            });
            PROP.validateClassifyForm();
            PROP.initDataTable();

            try {
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

            }
        },
        init: function () {
            PROP.initComponents();

            $(document).on('click', '#btn-classify-biz', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                PROP.reClassifyAction();
            });
            $(document).on('click', '#properties-table tbody tr .btn-classify-action', function (e) {
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
                PROP.onReclassify(data);
            });
            $(document).on('change', "#isRated", function (e) {
                Common.clearSelectField(slPropClass);
                slPropUse.val(null).trigger("change");
            });
        },
        onReclassify: function (record) {
            $("#propId").val(record.id);
            $("#biz-reclassify-modal-head-title").text("Reclassify Property"); //+ record.businessName);
            $("#isRated").val(((record.isRated) ? 'yes' : 'no')).trigger('change');
            $("#rateableValue").val(record.rateableValue);
            $("#vlstNo").val(record.vlstNo);
            PROP.setRevItem(record);
            propClassModal.modal('show');
        },
        reClassifyAction: function () {
            var ajaxOptions = {
                type: 'POST',
                url: propClassForm.attr('data-action'),
                data: propClassForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (propClassForm.valid()) {
                        Common.showSpinner(propClassModal, "Saving...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(propClassModal);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    PROP.reloadTable();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(propClassModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(propClassModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        reloadTable: function () {
            try {
                PROPDT.ajax.reload();
            } catch (e) {
            }
        },

        validateClassifyForm: function () {
            Common.validateForm(propClassForm, {
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
                vlstNo: {
                    required: function () {
                        return ($("#isRated").val() === 'yes');
                    }
                },
                propRevId: 'required'
            }, {});

        },
        initDataTable: function () {
            PROPDT = PROPTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": PROPTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "accountNo"},
                    {"data": "digitaladdress"},
                    {"data": "hseno"},
                    {"data": "location"},
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
            return PROPDT;
        },
        getRevenueItems: function (data) {
            var isRated = ($("#isRated").val() === 'yes') ? true : false;
            return $.grep(data, function (v) {
                return v.isRated === isRated;
            });
        },
        setRevItem: function (record) {
            try {
                var data = $.map(revItems, function (obj) {
                    return obj.revenueItems;
                });
                var revItem = $.grep(data, function (v) {
                    return (v.revenueId == record.propRevId);
                });
                Common.clearSelectField(slPropClass);
                Common.loadDataInToSelect(slPropClass, revItem, "revenueId", "revenue");
                $("#propRevId").val(record.propRevId).trigger('change');
            } catch (e) {
                console.log(e);
            }
        },
        loadPropClassUses: function () {
            // Common.showSpinner(propForm, "Property Uses...");
            var requestUrl = PROPTable.attr("data-rev-items");
            $.get(requestUrl, {}, function (response) {
                revItems = response.data;
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.category; // replace name with the property used for the text
                    obj.id = obj.categoryId; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slPropUse.select2({data: data});
                }
            }, "json").always(function () {
                //  Common.hideSpinner(propForm);
            });

        },

    };
}();
$(function () {
    PROP.init();
});
