/**
 * Created by degre on 3/10/2021.
 */

var ISearch = function () {
    var searchForm = $("#search-form");
    var btnSearch = $("#btn-search-action");
    var btnReset = $("#btn-reset-action");
    var tblResult = $("#result-table");
    var resultModal = $("#result-modal");

    var accountModal=$("#account-modal");
    var accountForm=$("#account-form");
    var btnAccountNo=$("#btn-account-no");
    var btnAccountSearch=$("#btn-account-search");

    var resultDT = null;
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return {
        init: function () {
             ISearch.validateAccountForm();
            ISearch.initResultsTable(null);

            btnSearch.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ISearch.fireSearch();
            });
            btnReset.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                try{
                    searchForm.resetForm();
                    searchForm.clearForm();
                }catch (e){

                }

            });

            btnAccountSearch.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ISearch.fireSearchByAccountNo();
            });

            btnAccountNo.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                try{
                    accountForm.resetForm();
                    accountForm.clearForm();
                }catch (e){

                }
                accountModal.modal('show');
            });

            $(document).on('click', '#result-table tbody tr .lnk-pay-action', function (e) {
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
                IPay.onPay(data);
            });
        },
        fireSearch: function () {
            var ajaxOptions = {
                type: 'POST',
                url: searchForm.attr('data-action'),
                data: searchForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    Common.showSpinner(searchForm, "Searching...");
                    return true;
                    // if (searchForm.valid()) {
                    //     Common.showSpinner(searchForm, "Searching...");
                    //     return true;
                    // } else {
                    //     return false;
                    // }

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
        fireSearchByAccountNo: function () {
            var ajaxOptions = {
                type: 'POST',
                url: accountForm.attr('data-action'),
                data: accountForm.serialize(),
                dataType: "json",
                beforeSend: function (xhr) {
                    // Common.showSpinner(accountForm, "Searching...");
                    // return true;
                    if (accountForm.valid()) {
                        Common.showSpinner(accountForm, "Searching...");
                        return true;
                    } else {
                        return false;
                    }

                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(accountForm);
                if (response.status === "ok" && response.code === "00") {
                    accountModal.modal('hide');
                    ISearch.loadResults(response.data);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (xhr) {
                Common.hideSpinner(accountForm);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(accountForm);
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
            var vwUrl = searchForm.attr('data-view-url') + "/" + record.id;
            var $btns = '<div class="dropdown">';
            $btns += '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-original-title="" title="Register Actions"><i class="icofont icofont-navigation-menu"></i></button>';
            $btns += '<div class="dropdown-menu"><h6 class="dropdown-header">Invoice Actions</h6>';
            $btns += '<a href="' + vwUrl + '" class="dropdown-item lnk-view-action" title="View Information" ><i class="icofont icofont-eye  text-primary"></i> View Information</a>';
            $btns += '<a href="#" class="dropdown-item lnk-pay-action" title="Pay" ><i class="icofont icofont-pay text-primary"></i> Pay </a>';
            $btns += '</div>';
            $btns += '</div>';
            return $btns;

        },
        initResultsTable: function (results) {
            resultDT = tblResult.DataTable({
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
                    {"data": "invoiceNo"},
                    {
                        "data": "invoiceType",
                        "render": function (data, type, row, meta) {
                            switch (row.invoiceTypeId) {
                                case 'bop':
                                    return 'Business Operating Permit';
                                case 'prr':
                                    return 'Property Rate';
                                default:
                                    return row.invoiceTypeId;
                            }
                        }
                    },
                    {
                        "data": "invoiceMonth",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return monthNames[row.invoiceMonth - 1];
                        }
                    },
                    {"data": "invoiceYear"},
                    {
                        "data": "arrears",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return accounting.formatMoney(row.arrears, '');
                        }
                    },
                    {
                        "data": "amountDue",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return accounting.formatMoney(row.amountDue, '');
                        }
                    },
                    {"data": "dueDate"},
                    {
                        "data": "amountDue",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return accounting.formatMoney(row.adjustment, '');
                        }
                    },
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
        },
        validateAccountForm: function () {
            Common.validateForm(accountForm, {
                accountNo: 'required',
                searchType:'required'
            }, {});
        }
    };
}();

$(function () {
    ISearch.init();
});
