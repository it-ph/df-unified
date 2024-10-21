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
            // "bStateSave": true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
                oPaginate: {
                    sNext: '<i class="fa fa-forward"></i>',
                    sPrevious: '<i class="fa fa-backward"></i>',
                    sFirst: '<i class="fa fa-step-backward"></i>',
                    sLast: '<i class="fa fa-step-forward"></i>'
                },
            },
            scrollX: true,
            pagingType: "full_numbers",
            pageLength: 25,
            order: [0, "desc"],
            columnDefs: [{ type: 'date', 'targets': [4] }],
            processing: true,
            serverSide: true,

            ajax: {
                url: `${APP_URL}/reports/export/api/auditlogs`,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken // Add the CSRF token here
                },
                data: function(d) {
                    // Pass filter values to the backend
                    d.client_id = $('#client_id').val();
                    d.platform = $('#platform').val();
                    d.request_type_id = $('#request_type_id').val();
                    d.auditor_id = $('#auditor_id').val();
                    d.status = $('#status_').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', className: 'text-center' },
                { data: 'job_id', name: 'thejob.name' },
                ...(isAdmin ? [{ data: 'client_id', name: 'theclient.name', className: 'text-center ' }] : []),
                { data: 'thejob.site_id', name: 'thejob.site_id', className: 'text-center' },
                { data: 'thejob.platform', name: 'thejob.platform', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.last_name', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.first_name', className: 'hide-column' },
                { data: 'request_type_id', name: 'thejob.therequesttype.name', className: 'text-center' },
                { data: 'request_volume_id', name: 'thejob.therequestvolume.name', className: 'text-center' },
                { data: 'preview_link', name: 'preview_link' },
                { data: 'self_qc', name: 'self_qc', className: 'text-center' },
                { data: 'dev_comments', name: 'dev_comments' },
                { data: 'time_taken', name: 'time_taken', className: 'text-center' },
                { data: 'qc_round', name: 'qc_round', className: 'text-center' },
                { data: 'auditor_id', name: 'theauditor.first_name', className: 'text-center' },
                { data: 'auditor_id', name: 'theauditor.last_name', className: 'hide-column' },
                { data: 'qc_status', name: 'qc_status', className: 'text-center' },
                { data: 'for_rework', name: 'for_rework', className: 'text-center' },
                { data: 'num_times', name: 'num_times', className: 'text-center' },
                { data: 'alignment_aesthetics', name: 'alignment_aesthetics', className: 'text-center' },
                { data: 'c_alignment_aesthetics', name: 'c_alignment_aesthetics' },
                { data: 'availability_formats', name: 'availability_formats', className: 'text-center' },
                { data: 'c_availability_formats', name: 'c_availability_formats' },
                { data: 'accuracy', name: 'accuracy', className: 'text-center' },
                { data: 'c_accuracy', name: 'c_accuracy' },
                { data: 'functionality', name: 'functionality', className: 'text-center' },
                { data: 'c_functionality', name: 'c_functionality' },
                { data: 'qc_comments', name: 'qc_comments' },
                { data: 'start_at', name: 'start_at', className: 'text-center' },
                { data: 'end_at', name: 'end_at', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
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
        var filename = date_from == date_to ? 'AUDIT_LOG_REPORT_' + date_from : 'AUDIT_LOG_REPORT_' + date_from + ' to ' + date_to + '.xlsx';

        $('#btn_export').empty();
        $('#btn_export').append('<i class="fa fa-spinner fa-spin"></i> Exporting...');
        $('#btn_export').prop("disabled", true);
        $('#btn_search').prop("disabled", true);
        $('#btn_reset').prop("disabled", true);
        toastr.info('Exporting Data...');
        $.ajax({
            url: `${APP_URL}/reports/export/auditlogs`,
            method: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob' // This is important for file downloads
            },
            success: function(data, status, xhr) {
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
    return this_job;
})()