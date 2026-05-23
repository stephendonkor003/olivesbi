/**
 * Created by degre on 1/18/2021.
 */

var DVN = function () {
    var dvnForm = $("#divisions-form");
    var dvnTable = $("#divisions-table");
    var dvnModal = $("#division-modal");
    var dvnDT = null;
    return {
        initComponents: function () {
            DVN.validateForm();
            DVN.initDataTable();
        },
        init: function () {
            DVN.initComponents();

            $(document).on('click', '#btn-add-division', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                DVN.onAdd();
            });
            $(document).on('click', '#divisions-table tbody tr .lnk-edit-action', function (e) {
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
                DVN.onEdit(data);

            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                DVN.saveDivision();
            });

        },
        saveDivision: function () {
            var divisionId = parseInt($("#btn-save-action").prop("data-division-id"));
            var ajaxOptions = {
                type: 'POST',
                url: dvnForm.attr('data-action'),
                data: dvnForm.serialize() + '&divisionId=' + encodeURIComponent(divisionId),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (dvnForm.valid()) {
                        Common.showSpinner(dvnModal, "Saving Division...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(dvnModal);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    if (divisionId > 0) {
                        DVN.updateRow(divisionId);
                    } else {
                        DVN.addRow(response.data);
                        Common.resetForm(dvnForm, null);
                    }
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(dvnModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(dvnModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        onEdit: function (record) {
            $("#btn-save-action").prop('data-division-id', record.id);
            $("#divName").val(record.name);
            $("#divCode").val(record.code);
            dvnModal.modal('show');
        },
        onAdd: function () {
            Common.resetForm(dvnForm, null);
            $("#btn-save-action").prop('data-division-id', 0);
            dvnModal.modal('show');
        },
        updateRow: function (rowId) {
            var $table = $("#divisions-table");
            var current_row = $("#row-" + rowId);//Get the current row
            if (current_row.hasClass('child')) {//Check if the current row is a child row
                current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
            }
            var data = $table.DataTable().row(current_row).data();
            data.name = $("#divName").val();
            data.code = $("#divCode").val();
            $table.DataTable().row(current_row).invalidate().draw();
        },
        validateForm: function () {
            Common.validateForm(dvnForm, {
                name: 'required',
                code: 'required'
            }, {});

        },
        initDataTable: function () {
            dvnDT = dvnTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": dvnTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "name"},
                    {"data": "code"},
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
            return dvnDT;
        },

    };
}();
$(function () {
    DVN.init();
});