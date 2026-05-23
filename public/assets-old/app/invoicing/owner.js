var IBOP = function () {
    var $table = $("#bop-table");
    var $form = $("#bop-form");
    var $modal = $("#bop-modal");
    var $btnSearch = $("#btn-fire-search");
    var $dataTable = null;
    var btnInstant = $("#btn-instant");
    var btnSchedule = $("#btn-schedule");
    var slBcTypes = $("#slBcTypes");
    var scheduleModal = $("#schedule-modal");
    var scheduleForm = $("#schedule-form");
    return {

        init: function () {
            try {
                IBOP.initDropDowns();
                IBOP.validateForm();
                IBOP.validateScheduleForm();
                IBOP.initDataTable();
            } catch (e) {
                console.log(e);
            }
            $btnSearch.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                IBOP.onSearchCreate();
            });
            btnInstant.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $("#genType").val('instant');
                IBOP.saveSearch();
            });
            btnSchedule.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $("#genType").val('schedule');
                IBOP.saveSearch();
            });


            $(document).on('click', '#btn-save-schedule', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                IBOP.schedule();
            });
            $(document).on('click', '#bop-table tbody tr .lnk-schedule-action', function (e) {
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
                IBOP.onSchedule(data);
            });
        },
        onSearchCreate: function () {
            $modal.modal('show');
        },

        saveSearch: function () {
            var ajaxOptions = {
                type: 'POST',
                url: $form.attr('data-action'),
                data: $form.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if ($form.valid()) {
                        Common.showSpinner($form, "Saving..");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner($form);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    IBOP.resetForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner($form);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner($form);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        initDropDowns: function () {
            slBcTypes.bootstrapDualListbox({
                nonSelectedListLabel: 'Non-selected',
                selectedListLabel: 'Selected',
                preserveSelectionOnMove: false,
                moveOnSelect: false
            });

            try {
                IBOP.loadOwners();
            } catch (e) {
                console.log(e);
            }


        },
        loadOwners: function () {
            Common.showSpinner($form, "Loading Entity Owners...");
            var requestUrl = $form.attr("data-owners-url");
            $.get(requestUrl, {}, function (response) {
                var data = $.map(response.data, function (obj) {
                    obj.fullname = obj.text || obj.fname + " " + obj.lname + " " + obj.mname; // replace name with the property used for the text
                    obj.id = obj.id || obj.id; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    Common.populateDropDown(slBcTypes, data, "id", "fullname");
                    slBcTypes.bootstrapDualListbox('refresh');
                }
            }, "json").always(function () {
                Common.hideSpinner($form);
            });

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
                        "data": "genVirtualInv",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.genVirtualInv) {
                                return '<span class="badge badge-pill badge-primary">Yes</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">No</span>';
                            }
                        }
                    },
                    {
                        "data": "genFinalInv",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.genFinalInv) {
                                return '<span class="badge badge-pill badge-primary">Yes</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">No</span>';
                            }
                        }
                    },
                    {
                        "data": "genInvPdf",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.genInvPdf) {
                                return '<span class="badge badge-pill badge-primary">Yes</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">No</span>';
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
        validateForm: function () {
            Common.validateForm($form, {
                genType: 'required',
                searchName: 'required',
                'entityOwnerIds[]': {
                    required: true
                },
                invoiceType: 'required',
                genVirtualInv: {
                    require_from_group: [1, ".inv-options"]
                },
                genFinalInv: {
                    require_from_group: [1, ".inv-options"]
                },

            }, {});
        },
        refreshDT: function () {
            try {
                $table.DataTable().ajax.reload();
            } catch (e) {
            }
        },
        resetForm: function () {
            $form.resetForm();
            $form.clearForm();
            $("#genFinalInv").prop('checked', true);
            $("#genVirtualInv").prop('checked', true);
            $("#genInvPdf").prop('checked', true);
            slBcTypes.val(null);
            slBcTypes.bootstrapDualListbox('refresh');
            IBOP.refreshDT();
        },
        onSchedule: function (record) {
            $("#searchId").val(record.id);
            $("#schedule-modal-head-title").text(record.searchName + " Schedule Details");
            scheduleModal.modal('show');
        },
        schedule: function () {
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
                    Common.showMessage(response.message);
                    IBOP.resetScheduleForm();
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
        resetScheduleForm: function () {
            scheduleForm.resetForm();
            scheduleForm.clearForm();
            scheduleModal.modal('hide');
        },
        validateScheduleForm: function () {
            Common.validateForm(scheduleForm, {
                scheduleYear: 'required',
                scheduleMonth: 'required',
                scheduleDay: 'required'
            }, {});
        },
    };
}();

$(function () {
    IBOP.init();
});