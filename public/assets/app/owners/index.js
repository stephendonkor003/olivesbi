/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/25/2021.
 */
/**
 * Created by degre on 1/18/2021.
 */

var OWNER = function () {
    var OWNERForm = $("#owner-form");
    var OWNERTable = $("#owners-table");
    var OWNERModal = $("#owner-modal");
    var OWNERDT = null;
    return {
        initComponents: function () {
          //  OWNER.validateForm();
            OWNER.initDataTable();
        },
        init: function () {
            OWNER.initComponents();

            $(document).on('click', '#btn-add-division', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                OWNER.onAdd();
            });
            $(document).on('click', '#owners-table tbody tr .lnk-edit-action', function (e) {
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
                OWNER.onEdit(data);

            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                OWNER.saveBusiness();
            });

        },
        saveOwner: function () {
            var businessId = parseInt($("#btn-save-action").prop("data-owner-id"));
            var ajaxOptions = {
                type: 'POST',
                url: OWNERForm.attr('data-action'),
                data: OWNERForm.serialize() + '&businessId=' + encodeURIComponent(businessId),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (OWNERForm.valid()) {
                        Common.showSpinner(OWNERModal, "Saving Business...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(OWNERModal);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    if (businessId > 0) {
                        OWNER.updateRow(businessId);
                    } else {
                        OWNER.addRow(response.data);
                        Common.resetForm(OWNERForm, null);
                    }
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(OWNERModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(OWNERModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        onEdit: function (record) {
            $("#btn-save-action").OWNER('data-business-id', record.id);
            $("#divName").val(record.name);
            $("#divCode").val(record.code);
            OWNERModal.modal('show');
        },
        onAdd: function () {
            Common.resetForm(OWNERForm, null);
            $("#btn-save-action").OWNER('data-business-id', 0);
            OWNERModal.modal('show');
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
            Common.validateForm(OWNERForm, {
                name: 'required',
                code: 'required'
            }, {});

        },
        initDataTable: function () {
            OWNERDT = OWNERTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": OWNERTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "name"},
                    // {"data": "lname"},
                    // {"data": "mname"},
                    // {"data": "sex"},
                    {"data": "phoneNoPrimary"},
                    {"data": "digitalAddress"},
                    {"data": "houseNo"},
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
            return OWNERDT;
        },

    };
}();
$(function () {
    OWNER.init();
});