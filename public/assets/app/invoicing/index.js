/**
 * Created by degre on 4/4/2021.
 */

var IFactory = function () {
    var $form = $("#divisions-form");
    var $table = $("#invoice-table");
    var $modal = $("#business-modal");
    var $dataTable = null;
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return {
        initComponents: function () {
            IFactory.initDataTable();
        },
        init: function () {
            IFactory.initComponents();

            $(document).on('click', '#invoice-table tbody tr .lnk-pay-action', function (e) {
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
        refreshDT: function () {
            try {
                $table.DataTable().ajax.reload();
            } catch (e) {
            }
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
    IFactory.init();
});
