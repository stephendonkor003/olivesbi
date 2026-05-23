/**
 * Created by degre on 3/10/2021.
 */

var ISearch = function () {
    var searchForm = $("#search-form");
    var btnSearch = $("#btn-search-action");
    var btnReset = $("#btn-reset-action");
    var tblResult = $("#result-table");
    var resultModal = $("#result-modal");
    var resultDT = null;
    return {
        init: function () {
            // ISearch.validateForm();
            ISearch.initResultsTable(null);

            btnSearch.on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                ISearch.fireSearch();
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
                }else{
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
            var editUrl = searchForm.attr('data-edit-url') + "/" + record.id;
            var vwUrl = searchForm.attr('data-view-url') + "/" + record.id;
            var ownerUrl = searchForm.attr('data-owner-url') + "/" + record.ownerId;
            var $btns = '<div class="dropdown">';
            $btns += '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-original-title="" title="Register Actions"><i class="icofont icofont-navigation-menu"></i></button>';
            $btns += '<div class="dropdown-menu"><h6 class="dropdown-header">Register</h6>';
            $btns += '<a href="' + editUrl + '" class="dropdown-item btn-edit-action" title="Edit"><i class="icofont icofont-edit-alt text-primary"></i> Edit Information</a>';
            $btns += '<a href="' + vwUrl + '" class="dropdown-item btn-view-action" title="View Information" ><i class="icofont icofont-eye  text-primary"></i> View Information</a>';
            $btns += '<a href="' + ownerUrl + '" class="dropdown-item btn-owner-action" title="Business Owner Information" ><i class="icofont icofont-user-alt-2 text-primary"></i> Owner Information</a>';
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
                    {"data": "registrationNo"},
                    {"data": "businessName"},
                    {"data": "accountNo"},
                    {"data": "permitNo"},
                    {"data": "phoneNo"},
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
                email: {
                    email: true
                },
                phoneNo: {
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                }
            }, {});

        }
    };
}();

$(function () {
    ISearch.init();
});
