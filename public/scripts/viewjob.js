const JOB = (() => {
    let this_job = {}
    let _job_id;

    // show modal start
    this_job.showStartModal = () => {
        $('#startJobModal').modal('show');
    }

    // show modal pause
    this_job.showPauseModal = () => {
        $('#pauseJobModal').modal('show');
    }

    // show modal resume
    this_job.showResumeModal = () => {
        $('#resumeJobModal').modal('show');
    }

    // start job
    this_job.start = (id) => {
        $('#btn_start').empty();
        $('#btn_start').append('<i class="fa fa-spinner fa-spin"></i> Starting...');
        $('#btn_start').prop("disabled", true);
        axios({
                method: 'get',
                url: `${APP_URL}/myjob/start/${id}`,
            })
            .then(function(response) {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    toastr.success(response.data.message);
                    $('#startJobModal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.data.message);
                }
            }).catch(error => {
                toastr.error(null);
            });
    }

    // pause job
    this_job.pause = (id) => {
        $('#btn_pause').empty();
        $('#btn_pause').append('<i class="fa fa-spinner fa-spin"></i> Pausing...');
        $('#btn_pause').prop("disabled", true);
        axios({
                method: 'get',
                url: `${APP_URL}/myjob/pause/${id}`,
            })
            .then(function(response) {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    toastr.success(response.data.message);
                    $('#pauseJobModal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.data.message);
                }
            }).catch(error => {
                toastr.error(null);
            });
    }

    // resume job
    this_job.resume = (id) => {
        $('#btn_resume').empty();
        $('#btn_resume').append('<i class="fa fa-spinner fa-spin"></i> Resuming...');
        $('#btn_resume').prop("disabled", true);
        axios({
                method: 'get',
                url: `${APP_URL}/myjob/resume/${id}`,
            })
            .then(function(response) {
                console.log(response.data.status)
                if (response.data.status === 'success') {
                    toastr.success(response.data.message);
                    $('#resumeJobModal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.data.message);
                }
            }).catch(error => {
                toastr.error(null);
            });
    }

    // submit details
    $('#submitDetailsForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
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
                    url: `${APP_URL}/myjob/submitdetails`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('.to-hide').hide();
                        toastr.success(response.data.message);
                        location.reload();
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
                    $('#btn_save').append('<i class="fa fa-save"></i> Submit');
                    $('#btn_save').prop("disabled", false);
                }).catch(error => {
                    toastr.error(error);
                });
            }
        });
    });

    // send for QC
    $('#sendForQCForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
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
                $('#btn_send').empty();
                $('#btn_send').append('<i class="fa fa-spinner fa-spin"></i> Sending...');
                $('#btn_send').prop("disabled", true);
                // Send a POST request
                axios({
                    method: 'POST',
                    url: `${APP_URL}/myjob/sendforqc`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('.to-hide').hide();
                        window.scrollTo(0, 0);
                        toastr.success(response.data.message);
                        location.reload();
                    } else if (response.data.status === 'warning') {
                        Object.keys(response.data.error).forEach((key) => {
                            $(`#${[key]}Error`).show();
                            $(`#${[key]}Error`).text(response.data.error[key][0]);
                            toastr.error(response.data.error[key][0]);
                        });
                    } else {
                        toastr.error(response.data.message);
                    }
                    $('#btn_send').empty();
                    $('#btn_send').append('<i class="fa fa-paper-plane"></i> Submit');
                    $('#btn_send').prop("disabled", false);
                }).catch(error => {
                    toastr.error(error);
                });
            }
        });
    });

    // update external quality details
    $('#updateExternalQualityForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
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
                $('#btn_ext_qc').empty();
                $('#btn_ext_qc').append('<i class="fa fa-spinner fa-spin"></i> Saving...');
                $('#btn_ext_qc').prop("disabled", true);
                // Send a POST request
                axios({
                    method: 'POST',
                    url: `${APP_URL}/job/externalquality`,
                    data: formdata
                }).then((response) => {
                    console.log(response.data.status)
                    if (response.data.status === 'success') {
                        $('.to-hide').hide();
                        window.scrollTo(0, 0);
                        toastr.success(response.data.message);
                        location.reload();
                    } else if (response.data.status === 'warning') {
                        Object.keys(response.data.error).forEach((key) => {
                            $(`#${[key]}Error`).show();
                            $(`#${[key]}Error`).text(response.data.error[key][0]);
                            toastr.error(response.data.error[key][0]);
                        });
                    } else {
                        toastr.error(response.data.message);
                    }
                    $('#btn_ext_qc').empty();
                    $('#btn_ext_qc').append('<i class="fa fa-save"></i> Submit');
                    $('#btn_ext_qc').prop("disabled", false);
                }).catch(error => {
                    toastr.error(error);
                });
            }
        });
    });

    // add sla miss reason
    $('#slaMissReasonForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
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
                $('#btn_reason').empty();
                $('#btn_reason').append('<i class="fa fa-spinner fa-spin"></i> Saving...');
                $('#btn_reason').prop("disabled", true);

                var sla_miss_reason = $('#sla_miss_reason').val();
                if (sla_miss_reason == "") {
                    $(`#sla_miss_reasonError`).show();
                    $(`#sla_miss_reasonError`).text('SLA Miss Reason is required.');
                    toastr.error('SLA Miss Reason is required.');
                    $('#btn_reason').empty();
                    $('#btn_reason').append('<i class="fa fa-save"></i> Submit');
                    $('#btn_reason').prop("disabled", false);
                } else {
                    // Send a POST request
                    axios({
                        method: 'POST',
                        url: `${APP_URL}/myjob/slamissreason`,
                        data: formdata
                    }).then((response) => {
                        console.log(response.data.status)
                        if (response.data.status === 'success') {
                            $('.to-hide').hide();
                            window.scrollTo(0, 0);
                            toastr.success(response.data.message);
                            location.reload();
                        } else {
                            toastr.error(response.data.message);
                        }
                        $('#btn_reason').empty();
                        $('#btn_reason').append('<i class="fa fa-save"></i> Submit');
                        $('#btn_reason').prop("disabled", false);
                    }).catch(error => {
                        toastr.error(error);
                    });
                }
            }
        });
    });

    // auto NA when the user clicks no
    $('.no').click((e) => {
        let name = $(e.target).attr('name');
        var na = $('input[name = "' + name + '"]:checked').val();
        var value = na == 0 ? "NA" : null;
        $('#comments_' + name).val(value);
    });

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
                location.reload();
            }
        });
    });

    return this_job;
})()
