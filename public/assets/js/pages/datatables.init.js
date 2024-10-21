/******/
(function() { // webpackBootstrap
    /*!***********************************************!*\
      !*** ./resources/js/pages/datatables.init.js ***!
      \***********************************************/
    /*
    Template Name: Skote - Admin & Dashboard Template
    Author: Themesbrand
    Website: https://themesbrand.com/
    Contact: themesbrand@gmail.com
    File: Datatables Js File
    */
    $(document).ready(function() {
        $('#datatable').DataTable({
            language: {
                // search: '_INPUT_',
                // searchPlaceholder: 'Search',
                oPaginate: {
                    sNext: '<i class="fa fa-forward"></i>',
                    sPrevious: '<i class="fa fa-backward"></i>',
                    sFirst: '<i class="fa fa-step-backward"></i>',
                    sLast: '<i class="fa fa-step-forward"></i>'
                },
            },
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ],
            "pageLength": 10,
            "pagingType": "full_numbers",
            // "columnDefs": [{ type: 'date', 'targets': [1] }],
            // "order": [0, "desc"],
            // orderCellsTop: true,
            // fixedHeader: true,
            "scrollX": true,
        });

        var table = $('#datatable-tasks').DataTable({
            language: {
                oPaginate: {
                    sNext: '<i class="fa fa-forward"></i>',
                    sPrevious: '<i class="fa fa-backward"></i>',
                    sFirst: '<i class="fa fa-step-backward"></i>',
                    sLast: '<i class="fa fa-step-forward"></i>'
                },
            },
            "pageLength": 10,
            "pagingType": "full_numbers",
            "columnDefs": [{ type: 'date', 'targets': [2] }],
            "order": [0, "desc"],
            "scrollX": true,
        });

        // fix for row containing a button dropdown causes vertical scrolling when scrollX is enabled
        $('#datatable-tasks').on('show.bs.dropdown', function() {
                $('.dataTables_scrollBody').addClass('dropdown-visible');
            })
            .on('hide.bs.dropdown', function() {
                $('.dataTables_scrollBody').removeClass('dropdown-visible');
            });


        var table = $('#datatable-buttons').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'colvis']
        });
        table.buttons().container().appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
        $(".dataTables_length select").addClass('form-select form-select-sm');
    });
    /******/
})();