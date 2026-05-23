/**
 * Created by degre on 3/13/2021.
 */
/**
 * Created by degre on 3/10/2021.
 */

var ISearch = function () {
    var searchForm = $("#search-form");
    var btnSearch = $("#btn-search-action");
    var btnReset = $("#btn-reset-action");
    var tblOwners = $("#owners-table");
    var resultModal = $("#result-modal");
    var resultDT = null;
    return {
        hideAll: function () {
            $("#idiv").hide();
            $("#odiv").hide();
        },
        init: function () {
            ISearch.hideAll();
            ISearch.validateForm();
            ISearch.initResultsTable(null);

            btnSearch.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ISearch.fireSearch();
            });
            $(document).on('change', '#entityTypeId', function (e) {
                var val = $(this).val();
                switch (val) {
                    case 'i':
                        ISearch.hideAll();
                        $("#idiv").show();
                        break;
                    case 'o':
                        ISearch.hideAll();
                        $("#odiv").show();
                        break;
                    default:
                        ISearch.hideAll();
                        break;

                }
            });

            $(document).on('click', '#owners-table tbody tr .lnk-prop-owner', function (e) {
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
                var fullName = data.fname + ' ' + data.lname + ' ' + data.mname;
                var bizName = searchForm.attr('data-biz-name');
                var msg = 'Are You Sure You Want To Set ' + fullName + " As The New Owner For The Selected Property";
                Common.confirmAction(msg, function (e) {
                    ISearch.changeOwner(data.id);
                }, null);

            });

        },
        changeOwner: function (ownerId) {
            var ajaxOptions = {
                type: 'POST',
                url: searchForm.attr('data-change-url'),
                data: JSON.stringify({_token: searchForm.attr('data-token'), propId: searchForm.attr('data-prop-id'), ownerId: ownerId}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(tblOwners, "Saving...");
                    return true;
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(tblOwners);
                if (response.status === "ok" && response.code === "00") {
                    Common.showMessage(response.message);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(tblOwners);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(tblOwners);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });

        },
        fireSearch: function () {
            var ajaxOptions = {
                type: 'POST',
                url: searchForm.attr('data-action'),
                data: searchForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    if (searchForm.valid()) {
                        Common.showSpinner(searchForm, "Searching...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(searchForm);
                if (response.status === "ok" && response.code === "00") {
                    //Common.showMessage(response.message);
                    ISearch.loadResults(response.data);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(searchForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(searchForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },

        loadResults: function (data) {
            if (resultDT !== null) {
                resultDT.clear().draw();
                resultDT.rows.add(data).draw();
            } else {
                resultDT = ISearch.initResultsTable(data);
            }
            try {
                if (data.length > 0) {
                    resultModal.modal('show');
                } else {
                    Common.onError("Your Search Did Not Yield Any Results");
                }
            } catch (e) {

            }
        },
        resetDataTable: function () {
            if (resultDT !== null) {
                resultDT.clear().draw();
            } else {
                resultDT = ISearch.initResultsTable(null);
            }
        },
        getActionBtns: function (record) {
            return '<a class="btn btn-pill btn-xs btn-outline-primary btn-primary-dark  text-bold lnk-prop-owner" title="Set As New Owner"><i class="icofont icofont-user text-primary"></i> Set As New Owner</a>';

        },
        initResultsTable: function (results) {
            resultDT = tblOwners.DataTable({
                "responsive": true,
                "processing": true,
                "deferRender": true,
                lengthChange: true,
                rowId: 'id',
                data: results,
                "columns": [
                    {
                        "data": null, "render": function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return row.fname+' '+row.lname+' '+row.mname;
                        }
                    },
                    // {"data": "fname"},
                    // {"data": "lname"},
                    // {"data": "mname"},
                    // {"data": "gender"},
                    {"data": "phoneNoPrimary"},
                    {"data": "digitalAddress"},
                    {"data": "houseNo"},
                    {
                        data: null,
                        "render": function (data, type, row) {
                            return ISearch.getActionBtns(row);
                        },
                        "orderable": false,
                        "searchable": false,
                        "className": 'text-center'
                    }
                ]
            });
            return resultDT;
        },
        validateForm: function () {
            Common.validateForm(searchForm, {
                entityTypeId: 'required',
            }, {});

        }
    };
}();

$(function () {
    ISearch.init();
});