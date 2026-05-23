/**
 * Created by degre on 4/8/2021.
 */

var ROLE = function () {
    var $table = $("#role-table");
    var roleDT = null;
    return {
        init: function () {
            ROLE.initDT();

            $(document).on('click', '#role-table tbody tr .btn-status-action', function (e) {
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
                ROLE.onStatusChange(data);

            });
            $(document).on('click', '#role-table tbody tr .btn-visible-action', function (e) {
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
                ROLE.onVisibilityChange(data);

            });

            $(document).on('click', '#btn-status-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ROLE.fireStatus();
            });
            $(document).on('click', '#btn-visible-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ROLE.fireVisibility();
            });
        },
        fireStatus: function () {
            var statusForm = $("#status-form");
            $.ajax({
                type: "POST",
                url: statusForm.attr('data-action'),
                data: statusForm.serialize(),
                dataType: 'json',
                // contentType: "application/json; charset=utf-8",
                beforeSend: function (xhr) {
                    Common.showSpinner(statusForm, "Changing Status....");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(statusForm);
                if (response.status === 'ok' && response.code === "00") {
                    Common.showMessage(response.message);
                    ROLE.refreshDT();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(statusForm);
            }).fail(function (xhr) {
            });
        },
        fireVisibility: function () {
            var statusForm = $("#visibility-form");
            $.ajax({
                type: "POST",
                url: statusForm.attr('data-action'),
                data: statusForm.serialize(),
                dataType: 'json',
                // contentType: "application/json; charset=utf-8",
                beforeSend: function (xhr) {
                    Common.showSpinner(statusForm, "Changing Visibility....");
                    return true;
                }
            }).done(function (response) {
                Common.hideSpinner(statusForm);
                if (response.status === 'ok' && response.code === "00") {
                    Common.showMessage(response.message);
                    ROLE.refreshDT();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(statusForm);
            }).fail(function (xhr) {
            });
        },

        onStatusChange: function (record) {
            $("#roleId").val(record.id);
            if (record.isActive) {
                $("#rad-active").prop('checked', true);
            } else {
                $("#rad-inactive").prop('checked', true);
            }
            $("#status-modal").modal('show');
        },
        onVisibilityChange: function (record) {
            $("#recordId").val(record.id);
            if (record.isVisible) {
                $("#rad-visible").prop('checked', true);
            } else {
                $("#rad-invisible").prop('checked', true);
            }
            $("#visibility-modal").modal('show');
        },
        refreshDT: function () {
            try {
                $table.DataTable().ajax.reload();
            } catch (e) {
            }
        },
        initDT: function () {
            roleDT = $table.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": $table.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "name"},
                    {
                        "data": 'isActive',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isActive) {
                                return '<span class="badge badge-pill badge-primary">Active</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">InActive</span>';
                            }
                        }
                    },
                    {
                        "data": 'isVisible',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isVisible) {
                                return '<span class="badge badge-pill badge-primary">Visible</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">Invisible</span>';
                            }
                        }
                    },
                    {
                        "data": 'isVisible',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isGlobal) {
                                return '<span class="badge badge-pill badge-primary">Yes</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">No</span>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            });
            return roleDT;
        }

    };
}();

$(function () {
    ROLE.init();
});