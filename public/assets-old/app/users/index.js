/**
 * Created by degre on 1/16/2021.
 */

var USR = function () {

    var userForm = $("#user-form");
    var userModal = $("#user-modal");
    var userTable = $("#users-table");
    var userDT = null;
    var slRole = $("#roleId");
    return {
        initComponents: function () {

            try {
                slRole.select2({placeholder: 'Select an option', allowClear: true});
                USR.validateForm();
                USR.initUserDT();
            } catch (e) {

            }
            try {
                USR.loadUserRoles();
            } catch (e) {

            }

        },
        init: function () {
            USR.initComponents();
            $(document).on('click', '#users-table tbody tr .btn-edit-action', function (e) {
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
                USR.onEdit(data);

            });
            $(document).on('click', '#users-table tbody tr .btn-status-action', function (e) {
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
                USR.onStatusChange(data);

            });
            $(document).on('click', '#btn-save-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                USR.saveUser();
            });
            $(document).on('click', '#btn-add-user', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                USR.onAdd();
            });

            $(document).on('click', '#btn-status-action', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                USR.fireStatus();
            });
        },
        onStatusChange: function (record) {
            $("#recordId").val(record.id);
            if (record.isEnabled) {
                $("#rad-active").prop('checked', true);
            } else {
                $("#rad-inactive").prop('checked', true);
            }
            $("#status-modal").modal('show');
        },
        getModal: function () {
            return userModal;
        },
        getForm: function () {
            return userForm;
        },
        addRow: function (record) {
            try {
                //var $table = asmTable.DataTable();
                userDT.row.add(record).draw();

            } catch (e) {
                console.log(e);
            }
        },
        updateRow: function (rowId) {
            var $table = userTable;
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
            Common.validateForm(userForm, {
                fname: 'required',
                lname: 'required',
                phoneNo: {
                    required: true,
                    digits: true,
                    maxlength: 10,
                    minlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                roleId: 'required',
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
        saveUser: function () {
            var ajaxOptions = {
                type: 'POST',
                url: userForm.attr('data-action'),
                data: userForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (userForm.valid()) {
                        Common.showSpinner(userForm, "Saving..");
                        return true;
                    } else {
                        return false;
                    }
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(userForm);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                    USR.resetUserForm();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(userForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(userForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
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
                    USR.refreshDT();
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(statusForm);
            }).fail(function (xhr) {
            });
        },

        resetUserForm: function () {
            var userId = $("#userId").val();
            if (userId === 'nil') {
                Common.resetForm(userForm, null);
                userModal.modal('hide');
            }
            USR.refreshDT();
        },
        refreshDT: function () {
            try {
                userTable.DataTable().ajax.reload();
            } catch (e) {
            }
        },
        onEdit: function (record) {
            $("#admin-widget").hide();
            $("#userId").val(record.id);
            $("#firstName").val(record.fname);
            $("#lastName").val(record.lname);
            $("#email").val(record.email);
            $("#telephone").val(record.phoneNo);
            $("#middleName").val(record.mname);
            $("#roleId").val(record.roleId).trigger('change');
            userModal.modal('show');
        },
        onAdd: function () {
            Common.resetForm(userForm, null);
            $("#admin-widget").show();
            $("#userId").val('nil');
            $("#logo-div").hide();
            userModal.modal('show');
        },
        loadUserRoles: function () {
            // Common.showSpinner(userForm, "Loading Roles...");
            var requestUrl = userForm.attr("data-roles-url");
            $.get(requestUrl, {}, function (response) {
                //console.log(response);
                var data = $.map(response.data, function (obj) {
                    obj.text = obj.text || obj.name; // replace name with the property used for the text
                    obj.id = obj.id || obj.id; // replace pk with your identifier
                    return obj;
                });
                if (data.length > 0) {
                    slRole.select2({data: data, placeholder: 'Select Role'});
                    // slMode.empty();
                    // slMode.append(new Option("Choose", "", true, true));
                    // response.data.forEach(function (item) {
                    //     slMode.append(new Option(item.name, item.id, false, false));
                    // });
                }
            }, "json").always(function () {
                //Common.hideSpinner(paymentForm);
            });

        },

        initUserDT: function () {
            userDT = userTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": userTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "username"},
                    {"data": "fname"},
                    {"data": "lname"},
                    {"data": "phoneNo"},
                    {"data": "email"},
                    {
                        "data": 'isEnabled',
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            if (row.isEnabled) {
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
            return userDT;
        }
    };
}();

$(function () {
    USR.init();
});