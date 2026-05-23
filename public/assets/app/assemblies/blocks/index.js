/**
 * Created by degre on 1/18/2021.
 */

var BLK = function () {
    var BLKForm = $("#block-form");
    var BLKTable = $("#blocks-table");
    var BLKModal = $("#block-modal");
    var BLKDT = null;
    return {
        initComponents: function () {
            BLK.validateForm();
            BLK.initDataTable();
        },
        init: function () {
            BLK.initComponents();

            $(document).on('click', '#btn-add-block', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BLK.onAdd();
            });
            $(document).on('click', '#blocks-table tbody tr .lnk-edit-action', function (e) {
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
                BLK.onEdit(data);

            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                BLK.saveBlock();
            });

        },
        saveBlock: function () {
            var blockId = parseInt($("#btn-save-action").prop("data-block-id"));
            var ajaxOptions = {
                type: 'POST',
                url: BLKForm.attr('data-action'),
                data: BLKForm.serialize() + '&blockId=' + encodeURIComponent(blockId),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (BLKForm.valid()) {
                        Common.showSpinner(BLKModal, "Saving Block...");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(BLKModal);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    if (blockId > 0) {
                        BLK.updateRow(blockId);
                    } else {
                        BLK.addRow(response.data);
                        Common.resetForm(BLKForm, null);
                    }
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(BLKModal);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(BLKModal);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        onEdit: function (record) {
            $("#btn-save-action").prop('data-block-id', record.id);
            $("#divName").val(record.name);
            $("#divCode").val(record.code);
            BLKModal.modal('show');
        },
        onAdd: function () {
            Common.resetForm(BLKForm, null);
            $("#btn-save-action").prop('data-block-id', 0);
            BLKModal.modal('show');
        },
        updateRow: function (rowId) {
            var $table = BLKTable;
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
            Common.validateForm(BLKForm, {
                name: 'required',
                code: 'required'
            }, {});

        },
        initDataTable: function () {
            BLKDT = BLKTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": BLKTable.attr('data-ajax-url'),
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
            return BLKDT;
        },

    };
}();
$(function () {
    BLK.init();
});