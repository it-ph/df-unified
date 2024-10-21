$('#eventForm').on('submit', function(e) {
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
                url: `${APP_URL}/event/store`,
                data: formdata
            }).then((response) => {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    resetForm();
                    $('#eventModal').modal('hide');
                    location.reload();
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

$('#btn_delete').click(function(e) {
    var id = $("#edit_id").val();
    destroy(id);
});

// destroy data
function destroy(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "#00599D",
        cancelButtonColor: "#F46A6A",
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            $('.error').hide();
            $('.error').text('');
            $('#btn_save').prop("disabled", true);
            $('#btn_delete').empty();
            $('#btn_delete').append('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            $('#btn_delete').prop("disabled", true);
            axios({
                    method: 'post',
                    url: `${APP_URL}/event/delete/${id}`,
                })
                .then(function(response) {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#eventModal').modal('hide');
                        toastr.success(response.data.message);
                        location.reload();
                    } else {
                        toastr.error(response.data.message);
                    }
                }).catch(error => {
                    toastr.error(null);
                });
        }
    });
}

// cancel
$('#btn_cancel').click(() => {
    resetForm();
});

function resetForm() {
    $('#eventModalTitle').text('Create New Event');
    $('#eventForm')[0].reset();
    $("#edit_id").val(null);
    // $("#client_id").val(null).trigger('change');
    $("#event_type").val(null).trigger('change');
    $('.error').hide();
    $('.error').text('');
    $('#btn_save').empty();
    $('#btn_save').prop("disabled", false);
    $('#btn_save').append('<i class="fa fa-save"></i> Save');
    console.clear();
}
