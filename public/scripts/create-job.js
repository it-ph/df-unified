$(document).ready(function() {
    resetForm();
});

// store / update data
$('#addJobForm').on('submit', function(e) {
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
            $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Saving...');
            $('#btn_save').prop("disabled", true);
            // Send a POST request
            axios({
                method: 'POST',
                url: `${APP_URL}/job/store`,
                data: formdata
            }).then((response) => {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    resetForm();
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
                $('#btn_save').append('<i class="fa fa-save"></i> Save');
                $('#btn_save').prop("disabled", false);
            }).catch(error => {
                toastr.error(error);
            });
        }
    });
});

// get sla
$('.sla').change(function() {
    var typeId = $("#request_type_id").val();
    var volumeId = $("#request_volume_id").val();
    $("#request_sla_id").val(null);
    $("#agreed_sla").val(null);
    typeId && volumeId ? getSLA(typeId, volumeId) : '';
});

// get sla
$('#client_id').change(function() {
    loadDevs();
});

// auto NA when the user clicks no
$('.no').click((e) => {
    let name = $(e.target).attr('name');
    var na = $('input[name = "' + name + '"]:checked').val();
    var value = na == 0 ? "NA" : null;
    $('#comments_' + name).val(value);
});

function loadDevs() {
    let client_id = $('#client_id').val();
    $('#developer_id').empty();
    $("#developer_id").select2({
        placeholder: 'Please wait...'
    });

    $.ajax({
        type: 'GET',
        url: 'get_devs/' + `${client_id}`,
        dataType: 'json',
        success: function(result) {
            if (result.length > 0) {
                $("#developer_id").select2({
                    placeholder: '-- Select Designer --'
                });
                $('#developer_id').append('<option value="">' + '-- Select Designer --' + '</option>');
                $.each(result, function(index, value) {
                    $('#developer_id').append('<option value="' + value.id + '">' + value.full_name + '</option>');
                });
            } else {
                $("#developer_id").select2({
                    placeholder: '-- Select Designer --'
                });
                $('#developer_id').append('<option value="">' + '-- Select Designer --' + '</option>');
                $('#developer_id option[value=""]').prop('selected', true);
            }

        },

        error: function(error) {
            console.log(error);
        }
    });
}

function getSLA(typeId, volumeId) {
    $("#agreed_sla").val('Please wait...');
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
            toastr.success('Request SLA data retrieved successfully!');
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

function resetForm() {
    $('#addJobForm')[0].reset();
    // $("#client_id").val(null).trigger("change");
    // $("#platform").val(null).trigger("change");
    $("#developer_id").val(null).trigger("change");
    $("#request_type_id").val(null).trigger("change");
    $("#request_volume_id").val(null).trigger("change");
    loadDevs();
    $('.error').hide();
    $('.error').text('');
    $('#btn_save').empty();
    $('#btn_save').append('<i class="fa fa-save"></i> Save');
    console.clear();
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
            resetForm();
        }
    });
});