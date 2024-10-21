$(document).ready(function() {
    REQUEST_SLA.load();
});

const REQUEST_SLA = (() => {
    let this_request_sla = {}
    let _request_sla_id;

    // store / update data
    $('#requestSLAForm').on('submit', function(e) {
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
                    url: `${APP_URL}/request/sla/store`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#loader').show();
                        $("#tbl_request_slas > tbody").empty();
                        $("#tbl_request_slas_info").hide();
                        $("#tbl_request_slas_paginate").hide();
                        resetForm();
                        REQUEST_SLA.load();
                        $('#requestSLAModal').modal('hide');
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
    this_request_sla.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        $('#tbl_request_slas').DataTable().clear().draw();
        $('#tbl_request_slas').DataTable().destroy();
        $('#tbl_request_slas').DataTable({
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
            order: [5, "asc"],
            columnDefs: [{ type: 'date', 'targets': [5] }],
            processing: true,
            serverSide: true,
            ajax: `${APP_URL}/request/sla/api/all`,
            columns: [
                { data: 'request_type_id', name: 'therequesttype.name', },
                { data: 'request_volume_id', name: 'therequestvolume.name', className: 'text-center' },
                { data: 'agreed_sla', name: 'agreed_sla', className: 'text-center' },
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
    this_request_sla.showModal = () => {
        $('#requestSLAModal').modal('show');
        $('#default-status').hide();
        $('#requestSLAModalTitle').text('Create New Request SLA');
        resetForm();
    }

    // show data
    this_request_sla.show = (id) => {
        resetForm();
        $('#requestSLAModal').modal('show');
        $('#default-status').show();
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        $('#requestSLAModalTitle').text('Update Request SLA');
        toastr.info('Retrieving Request SLA Data...');
        axios(`${APP_URL}/request/sla/show/${id}`).then((response) => {
            _request_sla_id = id;
            $("#edit_id").val(response.data.data.id);
            $("#request_type_id").val(response.data.data.request_type_id).trigger("change");
            $("#request_volume_id").val(response.data.data.request_volume_id).trigger("change");
            $("#agreed_sla").val(response.data.data.agreed_sla);
            $("#status_").val(response.data.data.status);
            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-save"></i> Update');
            $('#btn_save').prop("disabled", false);
            toastr.success('Request SLA data retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // destroy data
    this_request_sla.destroy = (id) => {
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
                        url: `${APP_URL}/request/sla/delete/${id}`,
                    })
                    .then(function(response) {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            $('#loader').show();
                            $("#tbl_request_slas > tbody").empty();
                            $("#tbl_request_slas_info").hide();
                            $("#tbl_request_slas_paginate").hide();
                            resetForm()
                            toastr.success(response.data.message);
                            REQUEST_SLA.load();
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
        $('#requestSLAModalTitle').text('Create New Request SLA');
        $('#requestSLAForm')[0].reset();
        $("#edit_id").val(null);
        $("#request_type_id").val(null).trigger("change");
        $("#request_volume_id").val(null).trigger("change");
        $("#status_").val("active").trigger("change");
        $('.error').hide();
        $('.error').text('');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-save"></i> Save');
        console.clear();
    }

    // sla template
    $('#btn_export').on('click', function(e) {
        e.preventDefault();
        $('#btn_export').empty();
        $('#btn_export').append('<i class="fa fa-spinner fa-spin"></i> Exporting...');
        $('#btn_export').prop("disabled", true);
        toastr.info('Exporting Template...');

        $.ajax({
            url: `${APP_URL}/sla/export/template`,
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
                a.download = filename || 'sla-upload-template.xlsx'; // Fallback filename
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
    this_request_sla.showUploadModal = () => {
        $('#uploadSLAModal').modal('show');
        resetButton();
        let errorList = $('#errorList');
        errorList.empty();
        errorList.hide();
    }

    $('#btn_import').on('click', function(e) {
        // a import
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
                data: formData,
                url: `${APP_URL}/sla/import`,
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                console.log(response.data);
                if (response.data.status === 'success') {
                    $('#uploadSLAModal').modal('hide');
                    toastr.success(response.data.message);
                    REQUEST_SLA.load();
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

    return this_request_sla;
})()
