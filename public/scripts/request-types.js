$(document).ready(function() {
    REQUEST_TYPE.load();
});

const REQUEST_TYPE = (() => {
    let this_request_type = {}
    let _request_type_id;

    // store / update data
    $('#requestTypeForm').on('submit', function(e) {
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
                    url: `${APP_URL}/request/type/store`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#loader').show();
                        $("#tbl_request_types > tbody").empty();
                        $("#tbl_request_types_info").hide();
                        $("#tbl_request_types_paginate").hide();
                        resetForm();
                        REQUEST_TYPE.load();
                        $('#requestTypeModal').modal('hide');
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
    this_request_type.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        $('#tbl_request_types').DataTable().clear().draw();
        $('#tbl_request_types').DataTable().destroy();
        $('#tbl_request_types').DataTable({
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
            order: [3, "asc"],
            columnDefs: [{ type: 'date', 'targets': [3] }],
            processing: true,
            serverSide: true,
            ajax: `${APP_URL}/request/type/api/all`,
            columns: [
                { data: 'name', name: 'name', },
                { data: 'created_by', name: 'thecreatedby.first_name' },
                { data: 'created_by', name: 'thecreatedby.last_name', className: 'hide-column' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_by', name: 'theupdatedby.first_name' },
                { data: 'updated_by', name: 'theupdatedby.last_name', className: 'hide-column' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', className: 'text-center' },
            ],
        });
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        };
    }

    // show modal
    this_request_type.showModal = () => {
        $('#requestTypeModal').modal('show');
        $('#default-status').hide();
        $('#requestTypeModalTitle').text('Create New Request Type');
        resetForm();
    }

    // show data
    this_request_type.show = (id) => {
        resetForm();
        $('#requestTypeModal').modal('show');
        $('#default-status').show();
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        $('#requestTypeModalTitle').text('Update Request Type');
        toastr.info('Retrieving Request Type Data...');
        axios(`${APP_URL}/request/type/show/${id}`).then((response) => {
            _request_type_id = id;
            $("#edit_id").val(response.data.data.id);
            $("#name").val(response.data.data.name);
            $("#status_").val(response.data.data.status).trigger("change");
            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-save"></i> Update');
            $('#btn_save').prop("disabled", false);
            toastr.success('Request Type data retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // destroy data
    this_request_type.destroy = (id) => {
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
                axios({
                        method: 'post',
                        url: `${APP_URL}/request/type/delete/${id}`,
                    })
                    .then(function(response) {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            $('#loader').show();
                            $("#tbl_request_types > tbody").empty();
                            $("#tbl_request_types_info").hide();
                            $("#tbl_request_types_paginate").hide();
                            resetForm()
                            toastr.success(response.data.message);
                            REQUEST_TYPE.load();
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
        $('#requestTypeModalTitle').text('Create New Request Type');
        $('#requestTypeForm')[0].reset();
        $("#edit_id").val(null);
        $("#name").empty();
        $("#status_").val("active").trigger("change");
        $('.error').hide();
        $('.error').text('');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-save"></i> Save');
        console.clear();
    }

    return this_request_type;
})()
