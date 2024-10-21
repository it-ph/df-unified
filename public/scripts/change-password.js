// change password
$('#changePasswordForm').on('submit', function(e) {
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
                url: `${APP_URL}/change-password`,
                data: formdata
            }).then((response) => {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    $('#changePasswordForm')[0].reset();
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