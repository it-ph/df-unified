$(document).ready(function() {
    // JOB.load();
});

const JOB = (() => {
    let this_job = {}

    // Fetch the CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Determine if user is admin
    const isAdmin = Array.isArray(window.userRoles) && window.userRoles.includes('admin');

    // Initialize the DataTable
    this_job.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        // $('#tbl_jobs').DataTable().clear().draw();
        $('#tbl_jobs').DataTable().destroy();
        $('#tbl_jobs').DataTable({
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                oPaginate: {
                    sNext: '<i class="fa fa-forward"></i>',
                    sPrevious: '<i class="fa fa-backward"></i>',
                    sFirst: '<i class="fa fa-step-backward"></i>',
                    sLast: '<i class="fa fa-step-forward"></i>'
                },
            },
            // searching: false,
            scrollX: true,
            pagingType: "full_numbers",
            pageLength: 25,
            order: [0, "desc"],
            processing: true,
            serverSide: true,

            ajax: {
                url: `${APP_URL}/reports/export/api/jobs`,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken // Add the CSRF token here
                },
                data: function(d) {
                    // Pass filter values to the backend
                    d.client_id = $('#client_id').val();
                    d.platform = $('#platform').val();
                    d.request_type_id = $('#request_type_id').val();
                    d.developer_id = $('#developer_id').val();
                    d.status = $('#status_').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', className: 'text-center' },
                { data: 'name', name: 'name' },
                ...(isAdmin ? [{ data: 'client_id', name: 'theclient.name', className: 'text-center ' }] : []),
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', className: 'text-center' },
                { data: 'site_id', name: 'site_id', className: 'text-center' },
                { data: 'platform', name: 'platform', className: 'text-center' },
                { data: 'developer_id', name: 'thedeveloper.first_name', className: 'text-center' },
                { data: 'developer_id', name: 'thedeveloper.last_name', className: 'hide-column' },
                { data: 'request_type_id', name: 'therequesttype.name', className: 'text-center' },
                { data: 'request_volume_id', name: 'therequestvolume.name', className: 'text-center' },
                { data: 'request_sla_id', name: 'request_sla_id', className: 'text-center' },
                { data: 'sla_missed', name: 'sla_missed', className: 'text-center' },
                { data: 'sla_miss_reason', name: 'sla_miss_reason' },
                { data: 'time_taken', name: 'time_taken', className: 'text-center' },
                { data: 'qc_rounds', name: 'qc_rounds', className: 'text-center' },
                { data: 'salesforce_link', name: 'salesforce_link' },
                { data: 'special_request', name: 'special_request', className: 'text-center' },
                { data: 'comments_special_request', name: 'comments_special_request' },
                { data: 'addon_comments', name: 'addon_comments' },
                { data: 'template_followed', name: 'template_followed', className: 'text-center' },
                { data: 'template_issue', name: 'template_issue', className: 'text-center' },
                { data: 'comments_template_issue', name: 'comments_template_issue' },
                { data: 'auto_recommend', name: 'auto_recommend', className: 'text-center' },
                { data: 'comments_auto_recommend', name: 'comments_auto_recommend' },
                { data: 'img_localstock', name: 'img_localstock', className: 'text-center' },
                { data: 'img_customer', name: 'img_customer', className: 'text-center' },
                { data: 'img_num', name: 'img_num', className: 'text-center' },
                { data: 'shared_folder_location', name: 'shared_folder_location' },
                { data: 'dev_comments', name: 'dev_comments' },
                { data: 'internal_quality', name: 'internal_quality', className: 'text-center' },
                { data: 'external_quality', name: 'external_quality', className: 'text-center' },
                { data: 'c_external_quality', name: 'c_external_quality' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'start_at', name: 'start_at', className: 'text-center' },
                { data: 'end_at', name: 'end_at', className: 'text-center' },
                { data: 'end_at', name: 'end_at', className: 'text-center' },
                { data: 'created_by', name: 'thecreatedby.first_name', className: 'text-center' },
                { data: 'created_by', name: 'thecreatedby.last_name', className: 'hide-column' },
            ],
        });

        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        };
    };

    // Handle the search button click event
    $('#btn_search').click(function(e) {
        e.preventDefault();
        $('.ihide').fadeIn(1000);

        this_job.load(); // Reload the DataTable with the new filter parameters
        $('#btn_search').empty();
        $('#btn_search').append('<i class="fa fa-spinner fa-spin"></i> Searching...');
        $('#btn_search').prop("disabled", true);
        $('#btn_reset').prop("disabled", true);
        $('#btn_export').prop("disabled", true);
        setTimeout(function() {
            $('#btn_search').empty();
            $('#btn_search').append('<i class="fa fa-search"></i> Search');
            $('#btn_search').prop("disabled", false);
            $('#btn_reset').prop("disabled", false);
            $('#btn_export').prop("disabled", false);
        }, 2000);
    });

    $('#btn_export').on('click', function(e) {
        e.preventDefault();
        var form = $('#report_form');
        var formData = form.serialize(); // Serialize form data
        var date_range = $('#date_range').val();
        date_range = date_range.split(" to ");
        var date_from = date_range[0];
        var date_to = date_range[1];
        var filename = date_from == date_to ? 'JOB_LOG_REPORT_' + date_from : 'JOB_LOG_REPORT_' + date_from + ' to ' + date_to + '.xlsx';

        $('#btn_export').empty();
        $('#btn_export').append('<i class="fa fa-spinner fa-spin"></i> Exporting...');
        $('#btn_export').prop("disabled", true);
        $('#btn_search').prop("disabled", true);
        $('#btn_reset').prop("disabled", true);
        toastr.info('Exporting Data...');
        $.ajax({
            url: `${APP_URL}/reports/export/jobs`,
            method: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob' // This is important for file downloads
            },
            success: function(data) {
                // Create a link element and trigger download
                var url = window.URL.createObjectURL(data);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                $('#btn_export').empty();
                $('#btn_export').append('<i class="fa fa-download"></i> Export');
                $('#btn_export').prop("disabled", false);
                $('#btn_search').prop("disabled", false);
                $('#btn_reset').prop("disabled", false);
            },
            error: function() {
                toastr.error('Export Failed!');
                $('#btn_export').empty();
                $('#btn_export').append('<i class="fa fa-download"></i> Export');
                $('#btn_export').prop("disabled", false);
                $('#btn_search').prop("disabled", false);
                $('#btn_reset').prop("disabled", false);
            }
        });
    });

    // cancel
    $('#btn_reset').click((e) => {
        e.preventDefault();
        $('.ihide').fadeOut(1000);

        var start = moment().startOf('week');
        var end = moment().endOf('week');

        function cb(start, end) {
            $('#datefilter span').html(start.format('MMM DD, YYYY') + ' to ' + end.format('MMM DD, YYYY'));
            $('#date_range').val(start.format('YYYY-MM-D') + ' to ' + end.format('YYYY-MM-D'));
        }

        $('#datefilter').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-danger',
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Week': [moment().startOf('week'), moment().endOf('week')],
                'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().endOf('week').subtract(7, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end);

        isAdmin ? $("#client_id").val('all').trigger("change") : '';
        $("#platform").val('all').trigger("change");
        $("#request_type_id").val('all').trigger("change");
        $("#developer_id").val('all').trigger("change");
        $("#status_").val('all').trigger("change");
        $('#btn_reset').empty();
        $('#btn_reset').append('<i class="fa fa-spinner fa-spin"></i> Resetting...');
        $('#btn_reset').prop("disabled", true);
        $('#btn_search').prop("disabled", true);
        $('#btn_export').prop("disabled", true);

        // this_job.load();
        setTimeout(function() {
            $('#btn_reset').empty();
            $('#btn_reset').append('<i class="fa fa-refresh"></i> Reset');
            $('#btn_reset').prop("disabled", false);
            $('#btn_search').prop("disabled", false);
            $('#btn_export').prop("disabled", false);
        }, 1000);
    });

    // show history
    this_job.show_history = (id) => {
        $('#jobHistoryModal').modal('show');
        $('#loader-history').show();
        $('#tbl_histories').DataTable().clear().draw();
        $('#tbl_histories').DataTable().destroy();
        $('#btn_cancel_history').empty();
        $('#btn_cancel_history').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_cancel_history').prop("disabled", true);
        axios(`${APP_URL}/job/show/history/${id}`).then(function(response) {
            $('#tbl_histories').DataTable().clear().draw();
            $('#tbl_histories').DataTable().destroy();
            var table;
            console.log(response.data.data)
            response.data.data.forEach(val => {
                table +=
                    `<tr>
                        <td class="text-center">${val.created_at}</td>
                        <td class="text-center">${val.created_by}</td>
                        <td>${val.job_name}</td>
                        <td>${val.activity}</td>

                    </tr>`;
            });
            $('#tbl_histories tbody').html(table)

            $('#tbl_histories').DataTable({
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
                    oPaginate: {
                        sNext: '<i class="fa fa-forward"></i>',
                        sPrevious: '<i class="fa fa-backward"></i>',
                        sFirst: '<i class="fa fa-step-backward"></i>',
                        sLast: '<i class="fa fa-step-forward"></i>'
                    },
                },
                // buttons: ['excel'],
                pageLength: 25,
                "order": [0, "desc"],
                "columnDefs": [{ type: 'date', 'targets': [1] }],
                "scrollX": true,
            });

            $('#loader-history').hide();
            $('#btn_cancel_history').empty();
            $('#btn_cancel_history').append('<i class="fa fa-times"></i> Close');
            $('#btn_cancel_history').prop("disabled", false);

            if (response.data.data.length > 0)
                toastr.success(response.data.message);
            else
                toastr.info(response.data.message);
        }).catch(error => {
            toastr.error(null);
        });
    }

    return this_job;
})()