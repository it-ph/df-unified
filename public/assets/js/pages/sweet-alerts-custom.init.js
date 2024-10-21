function store(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function update(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function idelete(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function start(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, start it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function stop(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, stop it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function pause(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, pause it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function resume(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, resume it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-primary mt-2',
        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    }).then(function(result) {
        if (result.value) {
            Swal.fire({
                title: 'Thank you!',
                icon: 'success',
                allowOutsideClick: false
            });
            $("#" + form).submit();
        }
    });
}

function has_active_task() {
    Swal.fire({
        title: 'Invalid Action',
        text: "Please On Hold or Complete your current task before creating a new one!",
        icon: 'error',
        confirmButtonText: 'Okay!',
        confirmButtonClass: 'btn btn-primary mt-2',
        buttonsStyling: false,
        allowOutsideClick: false
    });
}
