$('#signInForm').on('submit', function(e) {
    e.preventDefault();
    $('#btn_submit').empty();
    $('#btn_submit').append('<i class="fa fa-spinner fa-spin"></i> SIGNING IN...');
    $('#btn_submit').prop("disabled", true);
    this.submit();
});

$('#verify_form').on('submit', function(e) {
    e.preventDefault();
    $('#btn_submit').empty();
    $('#btn_submit').append('<i class="fa fa-spinner fa-spin"></i> VERIFYING...');
    $('#btn_submit').prop("disabled", true);
    this.submit();
});