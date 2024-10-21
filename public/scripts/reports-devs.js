$(document).ready(function() {

});

const JOB = (() => {
    let this_job = {}

    // Fetch the CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#btn_send').on('click', function(e) {
        e.preventDefault();
        var form = $('#report_form');
        var formData = form.serialize(); // Serialize form data
        var date_range = $('#date_range').val();
        date_range = date_range.split(" to ");
        var date_from = date_range[0];
        var date_to = date_range[1];

        var platform = $('#platform').val();
        var filename = date_from == date_to ? 'Web Development_Report_' + platform + '_' + date_from + ' as of ' + moment().format('MMDDYY') + '.xlsx' :
            'Web Development_Report_' + platform + '_' + date_from + ' to ' + date_to + ' as of ' + moment().format('MMDDYY') + '.xlsx';

        $('#btn_send').empty();
        $('#btn_send').append('<i class="fa fa-spinner fa-spin"></i> Sending Email...');
        $('#btn_send').prop("disabled", true);
        toastr.info('Sending Report...');
        $.ajax({
            url: `${APP_URL}/reports/export/devs`,
            method: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob' // This is important for file downloads
            },
            success: function(data, status, xhr) {
                toastr.success('Report Sent successfully!');
                $('#btn_send').empty();
                $('#btn_send').append('<i class="fa fa-paper-plane"></i> Send Email');
                $('#btn_send').prop("disabled", false);
            },
            error: function() {
                toastr.error('Sending Failed!');
                $('#btn_send').empty();
                $('#btn_send').append('<i class="fa fa-paper-plane"></i> Send Email');
                $('#btn_send').prop("disabled", false);
            }
        });
    });
    return this_job;
})()