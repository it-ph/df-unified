$(document).ready(function() {
    CLIENT.load();
});

const CLIENT = (() => {
    let this_client = {}
    let _client_id;

    // store / update data
    $('#clientForm').on('submit', function(e) {
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
                    url: `${APP_URL}/client/store`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#loader').show();
                        $("#tbl_clients > tbody").empty();
                        $("#tbl_clients_info").hide();
                        $("#tbl_clients_paginate").hide();
                        resetForm();
                        CLIENT.load();
                        $('#clientModal').modal('hide');
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
    this_client.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        $('#tbl_clients').DataTable().clear().draw();
        $('#tbl_clients').DataTable().destroy();
        $('#tbl_clients').DataTable({
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
            // columnDefs: [{ type: 'date', 'targets': [0] }],
            processing: true,
            serverSide: true,
            ajax: `${APP_URL}/client/api/all`,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'workshift', name: 'start', },
                { data: 'workshift', name: 'end', className: 'hide-column' },
                { data: 'created_by', name: 'thecreatedby.first_name' },
                { data: 'created_by', name: 'thecreatedby.last_name', className: 'hide-column' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_by', name: 'theupdatedby.first_name' },
                { data: 'updated_by', name: 'theupdatedby.last_name', className: 'hide-column' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'action', name: 'action', },
            ],
        });
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        };
    }

    // show modal
    this_client.showModal = () => {
        $('#clientModal').modal('show');
        $('#clientModalTitle').text('Create New Client');
        resetForm();
    }

    // show data
    this_client.show = (id) => {
        resetForm();
        $('#clientModal').modal('show');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        $('#clientModalTitle').text('Update Client');
        toastr.info('Retrieving Client Data...');
        axios(`${APP_URL}/client/show/${id}`).then((response) => {
            _client_id = id;
            $("#edit_id").val(response.data.data.id);
            $("#name").val(response.data.data.name);
            $("#start").val(response.data.data.start);
            $("#end").val(response.data.data.end);
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
            toastr.success('Client data retrieved successfully!');
        }).catch(error => {
            toastr.error(error);
        });
    }

    // destroy data
    this_client.destroy = (id) => {
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
                        url: `${APP_URL}/client/delete/${id}`,
                    })
                    .then(function(response) {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            $('#loader').show();
                            $("#tbl_clients > tbody").empty();
                            $("#tbl_clients_info").hide();
                            $("#tbl_clients_paginate").hide();
                            resetForm();
                            toastr.success(response.data.message);
                            CLIENT.load();
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
        $('#clientModalTitle').text('Create New Client');
        $('#clientForm')[0].reset();
        $("#edit_id").val(null);
        $("#name").empty();
        $('.error').hide();
        $('.error').text('');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-save"></i> Save');
        console.clear();
    }

    return this_client;
})()
