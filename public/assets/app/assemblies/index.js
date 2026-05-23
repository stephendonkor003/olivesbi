/**
 * Created by degre on 1/13/2021.
 */
var ASM = function () {
    var asmForm = $("#assembly-form");
    var asmModal = $("#assembly-modal");
    var asmTable = $("#assemblies-table");
    var asmDT = null;
    return {
        initComponents: function () {
            ASM.validateForm();
            ASM.initAssembliesDT();

            // ASM.loadAssemblies([]);
            // ASM.getAssemblyList();
        },
        init: function () {
            ASM.initComponents();
            $(document).on('click', '#assemblies-table tbody tr .btn-edit-action', function (e) {
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
                ASM.onEdit(data);

            });
            $(document).on('click', '#assemblies-table tbody tr .btn-status-action', function (e) {
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
                ASM.onStatusChange(data);

            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ASM.saveAssembly();
            });
            $(document).on('click', '#btn-status-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ASM.fireStatus();
            });
            $(document).on('click', '#btn-add-assembly', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ASM.onAdd();
            });
            $(document).on('change', '.ass-status', function (e) {
                e.preventDefault();
                var assemblyId = $(this).attr('data-row-id');
                try {
                    var table = $(this).closest('table');
                    //var row = $(this).closest('tr');
                    var current_row = $(this).parents('tr');//Get the current row
                    if (current_row.hasClass('child')) {//Check if the current row is a child row
                        current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
                    }
                    // var data = table.DataTable().row(current_row).data();
                    current_row.prop('id', 'row-' + assemblyId);
                    if ($(this).is(':checked')) {
                        ASM.changeStatus(assemblyId, true);
                    } else {
                        ASM.changeStatus(assemblyId, false);
                    }
                } catch (e) {
                    console.log(e);
                }


            });

            $(document).on('click', '.boundary-map', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var table = $(this).closest('table');
                var current_row = $(this).parents('tr');//Get the current row
                if (current_row.hasClass('child')) {//Check if the current row is a child row
                    current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
                }
                var data = table.DataTable().row(current_row).data();
                current_row.prop('id', 'row-' + data.id);
                var assemblyJson = {assembly: {info: data, raw: JSON.stringify({id: data.id, code: data.code})}};
                //console.log(assemblyJson.assembly.raw);
                loadPage('asm_boundary', assemblyJson, function () {
                    $("#app-map-container").data("assembly", data);
                    MapController.initGeoman('map', Common.constants().ASSEMBLY);
                }, true);
            });
        },

        getModal: function () {
            return asmModal;
        },
        getForm: function () {
            return asmForm;
        },
        addRow: function (record) {
            try {
                //var $table = asmTable.DataTable();
                asmDT.row.add(record).draw();

            } catch (e) {
                console.log(e);
            }
        },
        updateRow: function (rowId) {
            var $table = $("#assemblies-table");
            var current_row = $("#row-" + rowId);//Get the current row
            if (current_row.hasClass('child')) {//Check if the current row is a child row
                current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
            }
            var data = $table.DataTable().row(current_row).data();
            data.name = $("#assemblyName").val();
            data.abbrName = $("#abbrName").val();
            data.email = $("#email").val();
            data.address = $("#address").val();
            // data.invoiceDueLength = $("#invoiceDue").val();
            data.phoneNo = $("#telephone").val();
            //$table.DataTable().row(current_row).data(data).draw();
            $table.DataTable().row(current_row).invalidate().draw();
        },
        validateForm: function () {
            Common.validateForm(asmForm, {
                name: 'required',
                abbrName: 'required',
                phoneNo: {
                    required: true,
                    digits: true,
                    maxlength: 10,
                    minlength: 10
                },
                address: 'required',
                email: {
                    required: true,
                    email: true
                },
                // assmLogoPath: {
                //     required: true,
                //     extension: "png|jpeg|jpg"
                // },
                invoiceDueLength: {
                    required: true,
                    digits: true,
                    minlength: 1,
                    maxlength: 4
                },
                fname: 'required',
                lname: 'required',
                //  mname:'required',
                username: 'required',
                passsecret: {
                    required: true,
                    minlength: 6
                },
                cpasssecret: {
                    required: true,
                    equalTo: "#password"
                }
            }, {
                // file: "File must be JPEG or PNG"
            });
        },
        refreshDT: function () {
            try {
                asmTable.DataTable().ajax.reload();
            } catch (e) {
            }
        },
        saveAssembly: function () {
            var ajaxOptions = {
                type: 'POST',
                url: asmForm.attr('data-action'),
                data: asmForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (asmForm.valid()) {
                        Common.showSpinner(asmForm, "Saving..");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(asmForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    ASM.resetAssmForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(asmForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(asmForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        saveAssemblyOld: function () {
            var $form = ASM.getForm();
            var assemblyId = parseInt($("#btn-save-action").prop("data-assembly-id"));
            var requestUrl = asmForm.attr('data-action');
            var action = "POST";
            var userId = asmForm.attr('data-user-id');
            if (assemblyId > 0) {
                requestUrl += "/" + assemblyId;
                action = "PUT";
                var updatedByField = $("<input type=\"hidden\" name=\"updatedBy\"  id=\"updatedBy\"  value=\"" + userId + "\" />");
                if ($("#updatedBy").length === 0) {
                    $form.append(updatedByField);
                }
            } else {
                $("#igfref").val($("#abbrName").val());
                var createdByField = $("<input type=\"hidden\" name=\"createdBy\"  id=\"createdBy\" value=\"" + userId + "\" />");
                if ($("#createdBy").length === 0) {
                    $form.append(createdByField);
                }
            }
            var ajaxOptions = {
                type: action,
                url: requestUrl,
                dataType: 'json',
                beforeSend: function (xhr) {
                    if ($form.valid()) {
                        Common.showSpinner(asmModal, "Saving Assembly...");
                        return true;
                    } else {
                        return false;
                    }
                },
                beforeSubmit: function (formData, jqForm, options) {
                    if ($form.valid()) {
                        Common.showSpinner(asmModal, "Saving Assembly...");
                        return true;
                    } else {
                        return false;
                    }
                },  // pre-submit callback
                success: function (response, statusText, xhr, $form) {
                    Common.hideSpinner(asmModal);
                    if (response.status === "ok" && response.code === "00") {
                        ASM.addRow(response.data);
                        Common.showMessage(response.message);
                        Common.resetForm($form, null);
                    } else {
                        Common.onError(response.message);
                    }
                },// post-submit callback
                error: function (xhr, statusText) {
                    Common.hideSpinner(asmModal);
                }
            };
            if (assemblyId > 0) {
                delete ajaxOptions.beforeSubmit;
                delete ajaxOptions.success;
                delete ajaxOptions.error;
                var formData = Common.formToJSON($form[0].elements);
                Common.checkEmptyObj(formData);
                //formData.boundary = {"lan": 5.2222, "long": -0.3244};
                formData.invoiceDueLength = parseInt(formData.invoiceDueLength);
                ajaxOptions.contentType = "application/json; charset=utf-8";
                ajaxOptions.data = JSON.stringify(formData);
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(asmModal);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        ASM.updateRow(assemblyId);
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(asmModal);
                }).fail(function (xhr) {
                    Common.hideSpinner(asmModal);
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                });
            } else {
                delete ajaxOptions.beforeSend;
                //console.log(ajaxOptions);
                $form.ajaxSubmit(ajaxOptions);
            }
            return false;
        },
        onEdit: function (record) {
            $("#admin-widget").hide();
            $("#assemblyId").val(record.id);
            $("#assemblyName").val(record.name);
            $("#abbrName").val(record.abbrName);
            $("#email").val(record.email);
            $("#address").val(record.address);
            $("#invoiceDue").val(record.invoiceDueLength);
            $("#telephone").val(record.phoneNo);
            // $("#igfref").val(record.igfref);
            $("#assembly-modal").modal('show');
        },
        onStatusChange: function (record) {
            $("#recordId").val(record.id);
            if(record.isActive){
                $("#rad-active").prop('checked',true);
            }else{
                $("#rad-inactive").prop('checked',true);
            }
            $("#status-modal").modal('show');
        },
        resetAssmForm: function () {
            var assemblyId = parseInt($("#assemblyId").val());
            if (assemblyId <= 0) {
                Common.resetForm(asmForm, null);
                asmModal.modal('hide');
            }
            ASM.refreshDT();
        },
        onAdd: function () {
            Common.resetForm(asmForm, null);
            $("#admin-widget").show();
            $("#assemblyId").val(0);
            $("#logo-div").hide();
            $("#assembly-modal").modal('show');
        },
        getActionButtons: function (row) {
            var btns = '<a href="javascript:void(0)" class="mr-3 lnk-edit-action" data-toggle="tooltip" title="Edit" data-original-title="Edit" ><i class="fe fe-edit-2 text-dark fs-16"></i></a>\n' +
                '<a href="javascript:void(0)" class="mr-3 boundary-map" data-toggle="tooltip" title="Set Boundary For Assembly" data-original-title="Set Boundary For Assembly"><i class="fe fe-map text-dark fs-16"></i></a>' +
                '<label class="custom-switch pl-0" data-row-id="' + row.id + '"   title="Enable/Disable Assembly">';
            if (row.isActive === "1" || row.isActive === 1) {
                btns += '<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input ass-status"  data-row-id="' + row.id + '"  id="chk-box-' + row.id + '"  checked="checked" title="Enable/Disable Assembly">';
            } else {
                btns += '<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input ass-status"  data-row-id="' + row.id + '"  id="chk-box-' + row.id + '"   title="Enable/Disable Assembly">';
            }
            btns += '<span class="custom-switch-indicator"></span>' +
                '</label>' +
                '<a href="javascript:void(0)" class="mr-3" data-toggle="tooltip" title="Admin Account Settings" data-original-title="Admin Account Settings"><i class="fe fe-lock text-dark fs-16"></i></a>';
            return btns;
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
                    ASM.refreshDT();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(statusForm);
            }).fail(function (xhr) {
            });
        },
        loadAssemblies: function (assemlyList) {
            if (!$.fn.DataTable.isDataTable('table#assemblies-table')) {
                // asmDT = ASM.initDataTable(assemlyList);
            } else {
                $("table#assemblies-table").DataTable().destroy();
                // asmDT = ASM.initDataTable(assemlyList);
            }
        },
        getAssemblyList: function () {
            var tableUI = asmTable;
            $.ajax({
                type: "GET",
                url: asmTable.attr('data-ajax-url'),
                dataType: "json",
                contentType: 'application/json',
                beforeSend: function (xhr) {
                    Common.showSpinner(tableUI, 'Loading Assemblies....');
                    return true;
                }
            }).done(function (response) {
                if (response.status === "ok" && response.code === "00") {
                    try {
                        var assemblyList = (response.data.assemblies !== null) ? response.data.assemblies : [];
                        ASM.loadAssemblies(assemblyList);
                    } catch (e) {
                        ASM.loadAssemblies([]);
                    }
                } else {
                    ASM.loadAssemblies([]);
                }
            }).always(function (xhr) {
                Common.hideSpinner(tableUI);
            }).fail(function (xhr) {
                Common.hideSpinner(tableUI);
            });
        },
        initAssembliesDT: function () {
            asmDT = asmTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": asmTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {
                        "data": 'assmLogoPath',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            ///console.log(row);
                            return '<img class="rounded-circle" src="' + row.assmLogoPath + '" style="width:50px;height:50px;"/>';
                        },
                        "sortable": false,
                        "searchable": false
                    },
                    {"data": "name"},
                    {"data": "abbrName"},
                    {"data": "phoneNo"},
                    {"data": "email"},
                    {"data": "invoiceDueLength"},
                    {
                        "data": 'isActive',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isActive) {
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
            // asmDT.buttons().container()
            //     .appendTo('#assemblies-table_wrapper .col-md-6:eq(0)');
            return asmDT;
        },

    }
}();

$(function () {
    ASM.init();
});