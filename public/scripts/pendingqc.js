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
    $('#jobForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-primary btn-sm mt-2 mr-2',
            cancelButtonClass: 'btn btn-secondary btn-sm ms-2 mt-2 mr-2',
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
                    url: `${APP_URL}/pendingqc/store`,
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
            ajax: `${APP_URL}/pendingqc/api/all`,
            columns: [
                { data: 'job_id', name: 'thejob.name', },
                ...(isAdmin ? [{ data: 'client_id', name: 'theclient.name', className: 'text-center ' }] : []),
                { data: 'request_type_id', name: 'thejob.therequesttype.name', className: 'text-center' },
                { data: 'request_volume_id', name: 'thejob.therequestvolume.name', className: 'text-center' },
                { data: 'special_request', name: 'thejob.special_request', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'request_sla_id', name: 'thejob.request_sla_id', className: 'text-center' },
                { data: 'time_taken', name: 'time_taken', className: 'text-center' },
                { data: 'sla_missed', name: 'thejob.sla_missed', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.last_name', className: 'text-center' },
                { data: 'developer_id', name: 'thejob.thedeveloper.first_name', className: 'hide-column' },
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

    // pick job
    this_job.pick = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, pick it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-primary btn-sm mt-2 mr-2',
            cancelButtonClass: 'btn btn-danger btn-sm ms-2 mt-2 mr-2',
            buttonsStyling: false,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $('#btn_pick_' + id).empty();
                $('#btn_pick_' + id).append('<i class="fa fa-spinner fa-spin"></i>');
                $('#btn_pick_' + id).prop("disabled", true);
                axios({
                        method: 'get',
                        url: `${APP_URL}/pendingqc/pick/${id}`,
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

    return this_job;
})()