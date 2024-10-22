$(document).ready(function() {
    USER.load();
});

const USER = (() => {
    let this_user = {}
    let _user_id;

    // Determine if user is admin
    const isAdmin = Array.isArray(window.userRoles) && window.userRoles.includes('admin');

    // store / update data
    $('#userForm').on('submit', function(e) {
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
                    url: `${APP_URL}/user/store`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#loader').show();
                        $("#tbl_users > tbody").empty();
                        $("#tbl_users_info").hide();
                        $("#tbl_users_paginate").hide();
                        resetForm();
                        USER.load();
                        $('#userModal').modal('hide');
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

    // load data
    this_user.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        $('#tbl_users').DataTable().clear().draw();
        $('#tbl_users').DataTable().destroy();
        $('#tbl_users').DataTable({
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
            pageLength: 20,
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            order: [0, "asc"],
            // columnDefs: [{ type: 'date', 'targets': [5] }],
            processing: true,
            serverSide: true,
            ajax: `${APP_URL}/user/api/all`,
            columns: [
                { data: 'full_name', name: 'first_name' },
                { data: 'full_name', name: 'last_name', className: 'hide-column' },
                { data: 'email', name: 'email' },
                ...(isAdmin ? [{ data: 'client_id', name: 'theclient.name' }] : []),
                { data: 'supervisor_id', name: 'thesupervisor.first_name' },
                { data: 'supervisor_id', name: 'thesupervisor.last_name', className: 'hide-column' },
                { data: 'theroles', name: 'theroles.name' },
                { data: 'last_login_at', name: 'last_login_at' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', className: 'text-center' },
            ],
        });
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        };
    }

    // show modal
    this_user.showModal = () => {
        $('#userModal').modal('show');
        $('#default-status').hide();
        $('#userModalTitle').text('Create User');
        resetForm();
    }

    // show data
    this_user.show = (id) => {
        resetForm();
        $('#userModal').modal('show');
        $('#default-status').show();
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        $('#userModalTitle').text('Update User');
        toastr.info('Retrieving User Data...');
        axios(`${APP_URL}/user/show/${id}`).then((response) => {
            _user_id = id;

            let roles = [];
            response.data.data.theroles.forEach(role => {
                roles.push(role.name);
            });
            $('#role-ctr').val(roles);
            $('input[name="roles[]"]').val(roles);
            $("#edit_id").val(response.data.data.id);
            $("#first_name").val(response.data.data.first_name);
            $("#last_name").val(response.data.data.last_name);
            $("#email").val(response.data.data.email);
            $("#client_id").val(response.data.data.client_id).trigger("change");
            $("#supervisor_id").val(response.data.data.supervisor_id).trigger("change");
            $("#status_").val(response.data.data.status).trigger("change");
            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-save"></i> Update');
            $('#btn_save').prop("disabled", false);
            toastr.success('User data retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // check if has role
    $('.theroles').change(function() {
        let roles = [];
        $.each($("input[name='roles[]']:checked"), function() {
            roles.push($(this).val());
        });

        $('#role-ctr').val(roles);
    });

    // destroy data
    this_user.destroy = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#00599D",
            cancelButtonColor: "#F46A6A",
            confirmButtonText: 'Yes, deactivate it!',
            cancelButtonText: 'No, cancel!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                axios({
                        method: 'post',
                        url: `${APP_URL}/user/delete/${id}`,
                    })
                    .then(function(response) {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            $('#loader').show();
                            $("#tbl_users > tbody").empty();
                            $("#tbl_users_info").hide();
                            $("#tbl_users_paginate").hide();
                            resetForm();
                            toastr.success(response.data.message);
                            USER.load();
                        } else {
                            toastr.error(response.data.message);
                        }
                    }).catch(error => {
                        toastr.error(null);
                    });
            }
        });
    }

    // user template
    $('#btn_export').on('click', function(e) {
        e.preventDefault();
        $('#btn_export').empty();
        $('#btn_export').append('<i class="fa fa-spinner fa-spin"></i> Exporting...');
        $('#btn_export').prop("disabled", true);
        toastr.info('Exporting Template...');

        $.ajax({
            url: `${APP_URL}/user/export/template`,
            method: 'GET',
            xhrFields: {
                responseType: 'blob' // This is important for file downloads
            },
            success: function(data, status, xhr) {
                var filename = ""; // This needs to be set, or derive it from response headers if needed
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition) {
                    var matches = /filename="([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1];
                    }
                }

                var url = window.URL.createObjectURL(data);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename || 'user-upload-template.xlsx'; // Fallback filename
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

    // show upload modal
    this_user.showUploadModal = () => {
        $('#uploadUserModal').modal('show');
        resetButton();
        let errorList = $('#errorList');
        errorList.empty();
        errorList.hide();
    }

    // user import
    $('#btn_import').on('click', function(e) {
        e.preventDefault();

        var formData = new FormData();
        var fileInput = document.getElementById('import_file');
        let errorList = $('#errorList');
        errorList.empty();
        errorList.hide();

        if (fileInput.files.length === 0) {
            $('#import_fileError').show();
            toastr.error('Please select a file to upload.');
            return;
        }
        $('#import_fileError').hide();
        $('#btn_import').empty();
        $('#btn_import').append('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        $('#btn_import').prop("disabled", true);
        toastr.info('Uploading Data...');


        formData.append('import_file', fileInput.files[0]);

        axios({
                method: 'POST',
                url: `${APP_URL}/user/import`,
                data: formData,
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                console.log(response.data);
                if (response.data.status === 'success') {
                    $('#uploadUserModal').modal('hide');
                    toastr.success(response.data.message);
                    USER.load();
                } else if (response.data.status === 'warning') {
                    toastr.error(response.data.message);
                    errorList.show();
                    Object.entries(response.data.error).forEach(([key, value]) => {
                        let li = $('<li></li>').text(value);
                        errorList.append(li);
                    });
                }
            })
            .catch(error => {
                console.error('An error occurred:', error);
                toastr.error(error);
            })
            .finally(() => {
                resetButton();
            });
    });

    function resetButton() {
        $('#import_fileError').hide();
        $('#btn_import').html('<i class="fa fa-save"></i> Upload');
        $('#btn_import').prop("disabled", false);
        $('#import_file').val('');
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
                resetForm();
            }
        });
    });

    function resetForm() {
        $('#userModalTitle').text('Create New User');
        $('#userForm')[0].reset();
        $("#edit_id").val(null);
        $("#username").empty();
        $("#email").empty();
        $("#client_id").val(null).trigger("change");
        $("#supervisor_id").val(null).trigger("change");
        $("#status_").val('active').trigger("change");
        $('.error').hide();
        $('.error').text('');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-save"></i> Save');
        console.clear();
    }

    return this_user;
})()