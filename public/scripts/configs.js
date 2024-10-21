$(document).ready(function() {
    CLIENT.load();
});

const CLIENT = (() => {
    let this_client = {}
    let _client_id;

    // store / update data
    $('#configsForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-primary btn-sm mt-2 mr-2',
            cancelButtonClass: 'btn btn-danger btn-sm ms-2 mt-2 mr-2',
            buttonsStyling: false,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                var formdata = new FormData(this);
                $('.error').hide();
                $('.error').text('');
                $('#btn_save').empty();
                $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Updating...');
                $('#btn_save').prop("disabled", true);
                // Send a POST request
                axios({
                    method: 'POST',
                    url: `${APP_URL}/client/updateEmailConfig`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        CLIENT.load();
                        toastr.success(response.data.message);
                    } else if (response.data.status === 'warning') {
                        Object.keys(response.data.error).forEach((key) => {
                            $(`#${[key]}Error`).show();
                            $(`#${[key]}Error`).text(response.data.error[key][0]);
                            toastr.error(response.data.error[key][0]);
                        });
                    } else {
                        toastr.error(response.data.message);
                    }
                    $('#btn_save').empty();
                    $('#btn_save').append('<i class="fa fa-save"></i> Update');
                    $('#btn_save').prop("disabled", false);
                }).catch(error => {
                    toastr.error(error);
                });
            }
        });
    });

    // load data
    this_client.load = () => {
        var id = $("#edit_id").val();
        // resetForm();
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        toastr.info('Retrieving Configuration...');
        axios(`${APP_URL}/config/show/${id}`).then((response) => {
            _client_id = id;
            $("#name").val(response.data.data.name);
            $("#start").val(response.data.data.start);
            $("#end").val(response.data.data.end);
            $("#shift_hours").val(response.data.data.shift_hours);
            $("#sla_threshold").val(response.data.data.sla_threshold);
            $("#sla_threshold_to").val(response.data.data.sla_threshold_to);
            $("#sla_threshold_cc").val(response.data.data.sla_threshold_cc);
            $("#sla_missed_to").val(response.data.data.sla_missed_to);
            $("#sla_missed_cc").val(response.data.data.sla_missed_cc);
            $("#new_job_cc").val(response.data.data.new_job_cc);
            $("#qc_send_cc").val(response.data.data.qc_send_cc);
            $("#daily_report_to").val(response.data.data.daily_report_to);
            $("#daily_report_cc").val(response.data.data.daily_report_cc);
            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-save"></i> Update');
            $('#btn_save').prop("disabled", false);
            toastr.success('Configuration retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // cancel
    $('#btn_cancel').click(() => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            confirmButtonClass: 'btn btn-primary btn-sm mt-2 mr-2',
            cancelButtonClass: 'btn btn-danger btn-sm ms-2 mt-2 mr-2',
            buttonsStyling: false,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                toastr.success('Cancelled successfully!');
                $('.error').hide();
                $('.error').text('');
                CLIENT.load();
            }
        });
    });

    return this_client;
})()