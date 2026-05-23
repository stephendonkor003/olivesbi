/**
 * Created by degre on 4/2/2021.
 */

var SCHEDULER = function () {
    var $table = $("#schedule-table");
    var $form = $("#schedule-form");
    var $modal = $("#schedule-modal");
    var $dataTable = null;
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const nth = function (d) {
        if (d > 3 && d < 21) return 'th';
        switch (d % 10) {
            case 1:
                return "st";
            case 2:
                return "nd";
            case 3:
                return "rd";
            default:
                return "th";
        }
    }
    return {
        init: function () {
            try {
                SCHEDULER.validateForm();
                SCHEDULER.initDataTable();
            } catch (e) {
                console.log(e);
            }

            $(document).on('click', '#btn-save-schedule', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                SCHEDULER.schedule();
            });
            $(document).on('click', '#schedule-table tbody tr .lnk-edit-action', function (e) {
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
                SCHEDULER.onSchedule(data);
            });
        },
        onSchedule: function (record) {
            $("#scheduleId").val(record.id);
            $("#scheduleYear").val(record.scheduleYear).trigger('change');
            $("#scheduleMonth").val(record.scheduleMonth).trigger('change');
            $("#scheduleDay").val(record.scheduleDay).trigger('change');
            var status = (record.isActive) ? 'active' : 'inactive';
            $("#isActive").val(status).trigger('change');
            $("#schedule-modal-head-title").text(record.searchName + " Schedule Details");
            $modal.modal('show');
        },
        schedule: function () {
            var scheduleForm = $form;
            var ajaxOptions = {
                type: 'POST',
                url: scheduleForm.attr('data-action'),
                data: scheduleForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (scheduleForm.valid()) {
                        Common.showSpinner(scheduleForm, "Scheduling..");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(scheduleForm);
                if (response.status === "ok" && response.code === "00") {
                    SCHEDULER.refreshDT();
                    Common.showMessage(response.message);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(scheduleForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(scheduleForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        refreshDT: function () {
            try{
                $table.DataTable().ajax.reload();
            }catch (e){}
        },
        validateForm: function () {
            Common.validateForm($form, {
                scheduleYear: 'required',
                scheduleMonth: 'required',
                scheduleDay: 'required',
                isActive: 'required'
            }, {});
        },
        initDataTable: function () {
            $dataTable = $table.DataTable({
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
                    {"data": "searchName"},
                    {
                        "data": "invoiceType",
                        "render": function (data, type, row, meta) {
                            switch (row.invoiceType) {
                                case 'bop':
                                    return 'Business Operating Permit';
                                case 'prr':
                                    return 'Property Rate';
                                default:
                                    return row.invoiceType;
                            }
                        }
                    },
                    {
                        "data": "scheduleYear",
                        "className": 'text-center'
                    },
                    {
                        "data": "scheduleMonth",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return monthNames[row.scheduleMonth - 1];
                        }
                    },
                    {
                        "data": "scheduleDay",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return row.scheduleDay + nth(row.scheduleDay);
                        }
                    },
                    {
                        "data": "isActive",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isActive) {
                                return '<span class="badge badge-pill badge-primary">Active</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">InActive</span>';
                            }
                        }
                    },
                    {"data": "createdOn"},
                    {"data": "createdBy"},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            });

            return $dataTable;
        },
    };
}();

$(function () {
    SCHEDULER.init();
});