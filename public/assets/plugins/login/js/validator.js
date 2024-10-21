$(document).ready(function() {

    $("#login-email").focusout(function() {
        check_email();
        checkFields();
    });

    $("#login-password").focusout(function() {
        check_password();
        checkFields();
    });

    $("#login-password").keyup(function() {
        $("#error_notif").fadeOut("slower");
        $("#password_alert").fadeOut("slower")
        checkFields();
    })

    $("#login-email").keyup(function() {
        $("#error_notif").fadeOut("slower");

        ln = $(this).val().length
        if (ln >= 0 && !check_email()) {
            $("#email_alert").fadeIn().html("<i class='fa fa-exclamation-triangle'></i> Please use your Personiv Email")
            if (check_email()) {
                $("#email_alert").fadeOut("slower")
                checkFields();
            }
        } else {
            $("#email_alert").fadeOut("slower")
            checkFields();
        }
    })

    $("#login_form").submit(function(e) {
        e.preventDefault();
        if (check_email() && check_password()) {
            $("#login-loader").show();
            e.currentTarget.submit();
        } else {
            return false;
        }
    })




});

function check_email() {
    var pattern = new RegExp(/(.*)personiv(.*)\.com$/i);
    var email_ln = $("#login-email").val().length;

    var error_email_pattern = false;
    var error_email_required = false;

    if (pattern.test($("#login-email").val())) {
        // console.log("WALANG ERROR")

    } else {
        // console.log("MAY ERROR")
        error_email_pattern = true;
    }

    if (email_ln <= 0) {
        error_email_required = true
        $("#email_alert").fadeIn().html("<i class='fa fa-exclamation-triangle'></i> Personiv Email is Required")

    }

    if (error_email_pattern == false && error_email_required == false) {
        return true; // No Error

    } else {
        return false;
    }

}

function check_password() {
    var password_ln = $("#login-password").val().length;

    if (password_ln <= 0) {
        $("#password_alert").fadeIn().html("<i class='fa fa-exclamation-triangle'></i> Password is Required")
        return false;
    } else {
        $("#password_alert").fadeOut("slower")
        return true; // No Error
    }

}

function checkFields() {
    if (check_email() && check_password()) {
        console.log("REMOVE NA")
        $("#btnlogin").removeClass('login-not-allowed');
    } else {
        $("#btnlogin").addClass('login-not-allowed');
    }
}