/**
 * Created by degre on 3/14/2021.
 */

var PROP = function () {
    var PROPTable = $("#properties-table");
    var PROPDT = null;

    return {
        initComponents: function () {
            PROP.initDataTable();
        },
        init: function () {
            PROP.initComponents();
        },
        reloadTable: function () {
            try {
                PROPDT.ajax.reload();
            } catch (e) {
            }
        },

        initDataTable: function () {
            PROPDT = PROPTable.DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "responsive": true,
                "processing": true,
                "deferRender": true,
                "serverSide": true,
                lengthChange: true,
                "ajax": PROPTable.attr('data-ajax-url'),
                rowId: 'id',
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {"data": "zone"},
                    {"data": "classId"},
                    {"data": "blockId"},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            });
            // dataTable.buttons().container()
            //     .appendTo('#divisions-table_wrapper .col-md-6:eq(0)');
            return PROPDT;
        },

    };
}();
$(function () {
    PROP.init();
});