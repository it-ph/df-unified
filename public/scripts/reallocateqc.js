$(document).ready(function() {
    JOB.load();
});

const JOB = (() => {
    let this_job = {}
    let _job_id;

    // Determine if user is admin
    const isAdmin = Array.isArray(window.userRoles) && window.userRoles.includes('admin');
    let sortColumnIndex = isAdmin ? 5 : 4;

    // store / update data
    $('#reallocateForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reallocate it!',
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
                $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Reallocating...');
                $('#btn_save').prop("disabled", true);
                // Send a POST request
                axios({
                    method: 'POST',
                    url: `${APP_URL}/reallocation/qcs/update`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('#loader').show();
                        $("#tbl_jobs > tbody").empty();
                        $("#tbl_jobs_info").hide();
                        $("#tbl_jobs_paginate").hide();
                        resetForm();
                        JOB.load();
                        $('#reallocateModal').modal('hide');
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
                    $('#btn_save').append('<i class="fa fa-handshake-o"></i> Reallocate');
                    $('#btn_save').prop("disabled", false);
                }).catch(error => {
                    toastr.error(error);
                });
            }
        });
    });

    // load data
    this_job.load = () => {
        $.fn.dataTable.ext.errMode = 'none';
        $('#tbl_jobs').DataTable().clear().draw();
        $('#tbl_jobs').DataTable().destroy();
        $('#tbl_jobs').DataTable({
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
            order: [sortColumnIndex, "desc"],
            columnDefs: [{ type: 'date', 'targets': [4] }],
            processing: true,
            serverSide: true,
            ajax: `${APP_URL}/reallocation/qcs/api/all`,
            columns: [
                { data: 'account_no', name: 'thejob.account_no', },
                { data: 'account_name', name: 'thejob.account_name', },
                ...(isAdmin ? [{ data: 'client_id', name: 'theclient.name', className: 'text-center ' }] : []),
                { data: 'request_type_id', name: 'thejob.therequesttype.name', className: 'text-center' },
                { data: 'request_volume_id', name: 'thejob.therequestvolume.name', className: 'text-center' },
                { data: 'start_at', name: 'start_at', className: 'text-center' },
                { data: 'request_sla_id', name: 'thejob.request_sla_id', className: 'text-center' },
                { data: 'sla_missed', name: 'thejob.sla_missed', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.first_name', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.last_name', className: 'hide-column' },
                { data: 'qc_round', name: 'qc_round', className: 'text-center' },
                { data: 'auditor_id', name: 'theauditor.first_name', className: 'text-center' },
                { data: 'auditor_id', name: 'theauditor.last_name', className: 'hide-column' },
                { data: 'action', name: 'action', className: 'text-center' },
            ],
        });
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        };
    }

    // release job
    this_job.release = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, release it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-primary btn-sm mt-2 mr-2',
            cancelButtonClass: 'btn btn-danger btn-sm ms-2 mt-2 mr-2',
            buttonsStyling: false,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $('#btn_release_' + id).empty();
                $('#btn_release_' + id).append('<i class="fa fa-spinner fa-spin"></i>');
                $('#btn_release_' + id).prop("disabled", true);
                axios({
                        method: 'get',
                        url: `${APP_URL}/pendingqc/release/${id}`,
                    })
                    .then(function(response) {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            JOB.load();
                            toastr.success(response.data.message);
                        } else {
                            toastr.error(response.data.message);
                        }
                    }).catch(error => {
                        toastr.error(null);
                    });
            }
        });
    }

    // reallocate qc
    this_job.show = (id) => {
        resetForm();
        $('#reallocateModal').modal('show');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn_save').prop("disabled", true);
        axios(`${APP_URL}/reallocation/qcs/show/${id}`).then((response) => {
            _job_id = id;
            $("#client_id").val(response.data.data.client_id);
            $("#edit_id").val(response.data.data.id);
            loadQCs()
            $('#btn_save').empty();
            $('#btn_save').append('<i class="fa fa-handshake-o"></i> Reallocate');
            $('#btn_save').prop("disabled", false);
        }).catch(error => {
            toastr.error(error);
        });
    }

    function loadQCs() {
        let client_id = $('#client_id').val();
        $('#auditor_id').empty();
        $("#auditor_id").select2({
            placeholder: 'Please wait...'
        });

        $.ajax({
            type: 'GET',
            url: 'get_auditors/' + `${client_id}`,
            dataType: 'json',
            success: function(result) {
                if (result.length > 0) {
                    $("#auditor_id").select2({
                        placeholder: '-- Select Proofreader --'
                    });
                    $('#auditor_id').append('<option value="">' + '-- Select Proofreader --' + '</option>');
                    $.each(result, function(index, value) {
                        $('#auditor_id').append('<option value="' + value.id + '">' + value.full_name + '</option>');
                    });
                } else {
                    $("#auditor_id").select2({
                        placeholder: '-- Select Proofreader --'
                    });
                    $('#auditor_id').append('<option value="">' + '-- Select Proofreader --' + '</option>');
                    $('#auditor_id option[value=""]').prop('selected', true);
                }

            },

            error: function(error) {
                console.log(error);
            }
        });
    }

    function resetForm() {
        $('#reallocateForm')[0].reset();
        $("#client_id").val(null);
        $("#edit_id").val(null);
        $("#auditor_id").val(null).trigger("change");
        $('.error').hide();
        $('.error').text('');
        $('#btn_save').empty();
        $('#btn_save').append('<i class="fa fa-handshake"></i> Reallocate');
        console.clear();
    }

    return this_job;
})()