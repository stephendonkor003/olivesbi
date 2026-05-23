/**
 * Created by degre on 5/3/2021.
 */
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
            try {
                IFactory.initDataTable();
            }catch (e){
                console.log(e);
            }
        },
        init: function () {
            IFactory.initComponents();
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
                        "data": "receiptNo"
                    },
                    {"data": "gcrNo"},
                    {
                        "data": "amountPaid",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return accounting.formatMoney(row.amountPaid, '');
                        }
                    },
                    {"data": "paymentMethodId"},

                    // {"data": "paidBy.name"},
                    // {"data": "collectedBy"},
                    {"data": "paymentDate"},
                    {
                        "data": "paymentMonth",
                        "className": 'text-center',
                        "render": function (data, type, row) {
                            return monthNames[row.paymentMonth - 1];
                        }
                    },
                    {"data": "paymentYear"},
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
        }

    };
}();
$(function () {
    IFactory.init();
});