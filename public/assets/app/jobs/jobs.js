/**
 * Created by degre on 2/20/2021.
 */

var JOB = function () {
    var jobForm = $("#job-form");
    var jobModal = $("#job-modal");
    var jobTable = $("#jobs-table");
    var unAssignForm = $("#unassign-form");

    var jobDT = null;

    return {
        init: function () {
            JOB.validateForm();
            JOB.initDatePickers();
            JOB.initDropDowns();
            JOB.initDataTable();

            $("#btn-save-action").on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                JOB.allocateJob();
            });

            $("#btn-assign-job").on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                JOB.onAssign();
            });
            $(document).on('click', '#btn-load-jobs', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                JOB.reloadTable();

            });
            $(document).on('click', '#jobs-table tbody tr .lnk-edit-action', function (e) {
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
                JOB.onEdit(data);

            });

        },
        initDropDowns: function () {
            $("#jobId").select2();
            $("#blockId").select2();
            $("#agentId").select2();
        },

        initDatePickers: function () {
            $.datetimepicker.setLocale('en');
            var $from = moment(new Date()).format("YYYY-MM-DD");
            var $to = moment(new Date()).format("YYYY-MM-DD");
            // $from += " 00:00";
            //$to += " 23:59";
            //var options={formatTime: 'H:i', formatDate: 'Y-m-d'};
            var options = {format: 'Y-m-d', timepicker: false, theme: 'classic'};
            $("#startDate").val($from);
            $("#endDate").val($to);
            $("#startDate").datetimepicker(options);
            $("#endDate").datetimepicker(options);
        },

        allocateJob: function () {
            var ajaxOptions = {
                type: 'POST',
                url: jobForm.attr('data-action'),
                data: jobForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (jobForm.valid()) {
                        Common.showSpinner(jobForm, "Allocation Job...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(jobForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    JOB.reloadTable();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(jobForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(jobForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },

        onEdit: function (record) {
            $('input[name="id"]', jobForm).remove();
            jobForm.append($('<input>').attr('type', 'hidden').attr('name', 'id').val(record.id));
            $("#blockId > option").each(function () {
                if ($(this).text() == record.block) {
                    $("#blockId").val($(this).val()).trigger('change');
                    $(this).prop('selected',true);
                }
            });
            $("#jobId > option").each(function () {
                if ($(this).text() == record.job) {
                    $("#jobId").val($(this).val()).trigger('change');
                    $(this).prop('selected',true);
                }
            });
            $("#agentId > option").each(function () {
                if ($(this).text() == record.agent) {
                    $("#agentId").val($(this).val()).trigger('change');
                    $(this).prop('selected',true);
                }
            });
            jobModal.modal('show');
        },
        onAssign: function () {
            Common.resetForm(jobForm, null);
            $('input[name="id"]', jobForm).remove();
            $("#blockId").val('').trigger('change');
            $("#jobId").val('').trigger('change');
            $("#agentId").val('').trigger('change');
            jobModal.modal('show');
        },
        updateRow: function (rowId) {
            var $table = jobTable;
            var current_row = $("#row-" + rowId);//Get the current row
            if (current_row.hasClass('child')) {//Check if the current row is a child row
                current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
            }
            var data = $table.DataTable().row(current_row).data();
            data.block = $("#blockId").val();
            data.job = $("#jobId").val();
            data.agent = $("#agentId").val();
            $table.DataTable().row(current_row).invalidate().draw();
        },
        validateForm: function () {
            Common.validateForm(jobForm, {
                jobId: 'required',
                blockId: 'required',
                agentId: 'required'
            }, {});
        },
        reloadTable: function () {
            try {
                jobDT.ajax.reload();
            } catch (e) {

            }
        },
        unAllocateJobs: function () {
            var ajaxOptions = {
                type: 'POST',
                url: unAssignForm.attr('data-action'),
                data: unAssignForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (jobForm.valid()) {
                        Common.showSpinner(jobTable, "UnAllocating...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(jobTable);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    JOB.reloadTable();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(jobTable);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(jobTable);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        resetUnAssignForm: function () {
            JOB.removeIdList();
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
        appendIdList: function (dataTable) {
            JOB.resetUnAssignForm();
            var selectedRows = JOB.getSelectedRecords(dataTable);
            if (selectedRows.length > 0) {
                $.each(selectedRows, function (index, rowId) {
                    // Create a hidden element
                    unAssignForm.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'idList[]')
                            .val(rowId)
                    );
                });
                JOB.unAllocateJobs();
            } else {
                Common.onError('Please kindly select at least one Job before you continue.')
            }
        },
        removeIdList: function () {
            // Remove added elements
            $('input[name="idList\[\]"]', unAssignForm).remove();
        },
        initDataTable: function () {
            jobDT = jobTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                //"ajax": jobTable.attr('data-ajax-url'),
                "ajax": {
                    type: 'POST',
                    url: jobTable.attr('data-ajax-url'),
                    data: function (d) {
                        d.strDate = $("#startDate").val();
                        d.endDate = $("#endDate").val();
                        d._token = jobTable.attr('data-token');
                    },
                    beforeSend: function () {
                        Common.showSpinner(jobTable, 'Loading Jobs Data');
                        return true;
                    }
                },
                "pageLength": 100,
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
                    {"data": "job"},
                    {"data": "block"},
                    {"data": "agent"},
                    {"data": "assignedBy"},
                    {"data": "assignedOn"},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                buttons: [{
                    text: '<i class="icofont icofont-cancel"></i> UnAssign',
                    action: function () {
                        JOB.appendIdList(jobDT);
                    }
                }, 'copy', 'excel', 'pdf', 'colvis'],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },

                "initComplete": function (settings, json) {
                    Common.hideSpinner(jobTable);
                },
                "drawCallback": function (settings) {
                    Common.hideSpinner(jobTable);

                }
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return jobDT;
        },
    };
}();

$(function () {
    JOB.init();
});