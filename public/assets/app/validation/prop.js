/**
 * Created by degre on 2/6/2021.
 */

var VLD = function () {
    var tblUnValidated = $("#unvld-properties-table");
    var tblReValidated = $("#revld-properties-table");
    var tblOnHold = $("#ohvld-properties-table");
    var tblSuccess = $("#sucvld-properties-table");
    var unVldDT = null;
    var reVldDT = null;
    var onHoldDT = null;
    var successDT = null;
    var vldModal = $("#validate-modal")
    var vldForm = $("#validate-form");
    return {
        init: function () {
            VLD.validateForm();
            VLD.initUnValidatedTable();
            VLD.initReValidatedTable();
            VLD.initOnHoldTable();
            VLD.initSuccessTable();

            $(document).on('click', '#btn-vld-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                VLD.validateAction();
            });
            $(document).on('click', '.prop-table tbody tr .btn-validate-action', function (e) {
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
                VLD.resetForm();
                vldForm.append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'idList[]')
                        .val(data.id)
                );
                vldModal.modal('show');

            });
        },
        resetForm: function () {
            VLD.removeIdList();
            $("#typeId").val('').trigger('change');
            $("#validmsg").val('');
        },
        validateForm: function () {
            Common.validateForm(vldForm, {
                "idList[]": {
                    required: true
                },
                typeId: "required",
                validmsg: "required"
            }, {
                "idList[]": "Please select properties before you continue",
            });
        },
        validateAction: function () {
            var ajaxOptions = {
                type: "POST",
                url: vldForm.attr('data-action'),
                // contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                data: vldForm.serialize(),
                beforeSend: function (formData, jqForm, options) {
                    if (vldForm.valid()) {
                        Common.showSpinner(vldModal, "Saving...");
                        return true;
                    }
                    return false;
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(vldModal);
                if (response.status === "ok") {
                    Common.showMessage(response.msg);
                    VLD.reloadTables();
                    VLD.resetForm();
                } else {
                    Common.onError(response.msg);
                }
            }).always(function (xhr) {
                Common.hideSpinner(vldModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(vldModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        getSelectedRecords: function (dataTable) {
            // var rows_selected =plateDT.column(0).checkboxes.selected();
            var rows_selected = $.map(dataTable.rows('.selected').data(), function (property, index) {
                return property.id;
            });
            return rows_selected;
            // alert(rows_selected.join(","));
        },
        selectRecords: function (dataTable) {
            dataTable.row(i).nodes().to$().toggleClass('selected');
            //$("#myTable").DataTable().row( i).nodes().to$().toggleClass( 'selected' );
        },
        reloadTables: function () {
            try {
                unVldDT.ajax.reload();
                reVldDT.ajax.reload();
                onHoldDT.ajax.reload();
                successDT.ajax.reload();

            } catch (e) {

            }
        },
        appendIdList: function (dataTable) {
            VLD.resetForm();
            var selectedRows = VLD.getSelectedRecords(dataTable);
            if (selectedRows.length > 0) {
                $.each(selectedRows, function (index, rowId) {
                    // Create a hidden element
                    vldForm.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'idList[]')
                            .val(rowId)
                    );
                });
                vldModal.modal('show');
            } else {
                Common.onError('Please kindly select at least one property before you continue.')
            }
        },
        removeIdList: function () {
            // Remove added elements
            $('input[name="idList\[\]"]', vldForm).remove();
        },
        initUnValidatedTable: function () {
            unVldDT = tblUnValidated.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": {
                    type: 'POST',
                    url: tblUnValidated.attr('data-ajax-url'),
                    data: function (d) {
                        d.validationId = tblUnValidated.attr('data-status');
                        d._token = tblUnValidated.attr('data-token');
                    }
                },
                rowId: 'id',
                "columns": [
                    {
                        data: null,
                        'checkboxes': {
                            'selectRow': true
                        },
                        orderable: false,
                        "searchable": false
                    },
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
                buttons: [{
                    text: '<i class="icofont icofont-check-circled"></i> Validate',
                    action: function () {
                        VLD.appendIdList(unVldDT);
                    }
                },
                    'copy', 'excel', 'pdf'],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return unVldDT;
        },
        initReValidatedTable: function () {
            reVldDT = tblReValidated.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": {
                    type: 'POST',
                    url: tblReValidated.attr('data-ajax-url'),
                    data: function (d) {
                        d.validationId = tblReValidated.attr('data-status');
                        d._token = tblReValidated.attr('data-token');
                    }
                },
                rowId: 'id',
                "columns": [
                    {
                        data: null,
                        'checkboxes': {
                            'selectRow': true
                        },
                        orderable: false,
                        "searchable": false
                    },
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
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
                buttons: [{
                    text: '<i class="icofont icofont-check-circled"></i> Validate',
                    action: function () {
                        VLD.appendIdList(reVldDT);
                    }
                },
                    'copy', 'excel', 'pdf'],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return reVldDT;
        },
        initOnHoldTable: function () {
            onHoldDT = tblOnHold.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": {
                    type: 'POST',
                    url: tblOnHold.attr('data-ajax-url'),
                    data: function (d) {
                        d.validationId = tblOnHold.attr('data-status');
                        d._token = tblOnHold.attr('data-token');
                    }
                },
                rowId: 'id',
                "columns": [
                    {
                        data: null,
                        'checkboxes': {
                            'selectRow': true
                        },
                        orderable: false,
                        "searchable": false
                    },
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
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
                buttons: [{
                    text: '<i class="icofont icofont-check-circled"></i> Validate',
                    action: function () {
                        VLD.appendIdList(onHoldDT);
                    }
                },
                    'copy', 'excel', 'pdf'],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return onHoldDT;
        },
        initSuccessTable: function () {
            successDT = tblSuccess.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": {
                    type: 'POST',
                    url: tblSuccess.attr('data-ajax-url'),
                    data: function (d) {
                        d.validationId = tblSuccess.attr('data-status');
                        d._token = tblSuccess.attr('data-token');
                    }
                },
                rowId: 'id',
                "columns": [
                    {
                        data: null,
                        'checkboxes': {
                            'selectRow': true
                        },
                        orderable: false,
                        "searchable": false
                    },
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
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
                buttons: [{
                    text: '<i class="icofont icofont-check-circled"></i> Validate',
                    action: function () {
                        VLD.appendIdList(successDT);
                    }
                },
                    'copy', 'excel', 'pdf'],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return successDT;
        },
    };
}();

$(function () {
    VLD.init();
});
