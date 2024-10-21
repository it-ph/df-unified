$(document).ready(function() {
    JOB.load();
});

const JOB = (() => {
    let this_job = {}
    let _job_id;

    // store / update data
    $('#editJobForm').on('submit', function(e) {
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
                    url: `${APP_URL}/job/store`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        JOB.load();
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
    this_job.load = () => {
        var id = $("#edit_id").val();
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        toastr.info('Retrieving Job data...');
        axios(`${APP_URL}/job/get_data/${id}`).then((response) => {
            _job_id = id;
            console.log(response.data);
            $("#name").val(response.data.data.name);
            $("#client_id").val(response.data.data.client_id).trigger("change");
            $("#site_id").val(response.data.data.site_id);
            $("#platform").val(response.data.data.platform).trigger("change");
            $("#developer_id").val(response.data.data.developer_id);
            $("#developer").val(response.data.data.developer);

            $("#request_type_id").val(response.data.data.request_type_id).trigger("change");
            $("#request_volume_id").val(response.data.data.request_volume_id).trigger("change");

            if (response.data.data.status == "Closed") {
                $(".edit-quality").show();
                $(".not-closed").hide();
                $(".closed").show();
                $("#request_type").show();
                $("#request_type").val(response.data.data.request_type);
                $("#request_volume").show();
                $("#request_volume").val(response.data.data.request_volume);
                $("#internal_quality").prop('disabled', false);
                $("#external_quality").prop('disabled', false);
            }

            $("#agreed_sla").val(response.data.data.agreed_sla);
            $("#salesforce_link").val(response.data.data.salesforce_link);
            $("input[name=special_request][value=" + response.data.data.special_request_raw + "]").prop('checked', true);
            $("#comments_special_request").val(response.data.data.comments_special_request);
            $("#addon_comments").val(response.data.data.addon_comments);
            $("#internal_quality").val(response.data.data.internal_quality).trigger("change");
            $("#external_quality").val(response.data.data.external_quality).trigger("change");

            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-save"></i> Update');
            $('#btn_save').prop("disabled", false);
            toastr.success('Job data retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // get sla
    $('.sla').change(function() {
        var typeId = $("#request_type_id").val();
        var volumeId = $("#request_volume_id").val();
        $("#request_sla_id").val(null);
        $("#agreed_sla").val(null);
        typeId && volumeId ? getSLA(typeId, volumeId) : '';
    });

    function getSLA(typeId, volumeId) {
        axios(`${APP_URL}/request/sla/get/${typeId}/${volumeId}`).then((response) => {
            if (response.data) {
                console.log(response.data);
                $("#request_sla_id").val(response.data.id);
                $("#agreed_sla").val(response.data.agreed_sla);
                $('#btn_save').empty();
                $('#btn_save').append('<i class="fa fa-save"></i> Save');
                $('#btn_save').prop("disabled", false);
                $('#agreed_slaError').hide();
                $('#agreed_slaError').text('');
                // toastr.success('Request SLA data retrieved successfully!');
            } else {
                toastr.error('Request SLA not found!');
                $("#request_sla_id").val(null);
                $("#agreed_sla").val(null);
                $('#agreed_slaError').show();
                $('#agreed_slaError').text('Request SLA not found!');
            }
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
                JOB.load();
            }
        });
    });

    return this_job;
})()