/**
 * Created by degre on 5/3/2021.
 */

var IReport = function () {
    var reportTable = $('#tbl-report');
    var rptDT = null;
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return {
        init: function () {
            try {
                IReport.initDatePickers();
            } catch (e) {
                console.log(e);
            }
            try {
                IReport.initReport();
            } catch (e) {
                console.log(e);
            }
            $(document).on('click', '#btn-load-rpt', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                IReport.refreshTable();

            });
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
        refreshTable: function () {
            // table.DataTable().ajax.reload();
            rptDT.ajax.reload();
        },
        initReport: function () {
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();
            var title = 'Period: ' + startDate + '-' + endDate;
            var exportTitle = "Invoice Report";
            reportTable.append('<caption style="caption-side: bottom">' + title + '</caption>');

            rptDT = reportTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "ordering": true,
                "searching": true,
                "lengthChange": true,
                "pageLength": 1000,
                "serverSide": true,
                "ajax": {
                    type: 'POST',
                    url: reportTable.attr('data-ajax-url'),
                    data: function (d) {
                        d.strDate = $("#startDate").val();
                        d.endDate = $("#endDate").val();
                        d._token = reportTable.attr('data-token');
                    },
                    beforeSend: function () {
                        Common.showSpinner(reportTable, 'Loading Report Data');
                        return true;
                    }
                },
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "invoiceNo"},
                    {
                        "data": "invoiceType"
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
                    {"data": "generatedOn"}
                ],
                "lengthMenu": [ [1000, 50000, 100000, -1], [1000, 50000, 100000, "All"] ],

                buttons: [
                    'copy',
                    {
                        extend: 'excelHtml5',
                        title: exportTitle,
                        messageTop: title
                    },
                    {
                        extend: 'pdfHtml5',
                        title: exportTitle,
                        messageTop: title,
                        messageBottom: null
                    },
                    {
                        extend: 'print',
                        title: exportTitle,
                        messageTop: title,
                        messageBottom: null
                    }
                ],
                "initComplete": function (settings, json) {
                  Common.hideSpinner(reportTable);
                },
                "drawCallback": function (settings) {
                    Common.hideSpinner(reportTable);

                },
            });
        },

    };
}();

$(function () {
    IReport.init();
});
