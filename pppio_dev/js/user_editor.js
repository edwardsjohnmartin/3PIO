function reset_password(user_id) {
    clearAlerts();

    var conf = confirm("Do you want to reset this users password to a randomly generated password?");

    if (conf) {
        $.ajax({
            type: 'GET',
            url: '?controller=user&action=reset_password',
            contentType: 'application/json; charset=utf-8',
            data: { user_id: user_id },
            complete: function (data) {
                if (data.responseJSON.success) {
                    fillPreOutput(data.responseJSON.user_name, data.responseJSON.p);
                } else {
                    m = 'You were not able to set a new password for ' + data.responseJSON.user_name;
                    showAlert(m, 'danger');
                }
            }
        });
    }
}

function showAlert(alertMessage, html_class) {
    div_alert.innerHTML += '<div class="alert alert-' + html_class + ' alert-dismissible mar-0" role="alert" id="infoAlert">'
        + alertMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span ' +
        'aria-hidden="true">&times;</span></button></div>';
}

function clearAlerts() {
    div_alert.innerHTML = '';
    pre_output.innerHTML = '';
}

function fillPreOutput(user_name, p) {
    document.getElementById('pre_output').innerHTML =
        'Hello, ' + user_name + ',' + '\n\n' +
        'Your password has been reset on 3pio.cose.isu.edu to a randomly generated one.\n' +
        'Your new password is: ' + p + '\n\n' +
        'Please log in using this new password and change it as soon as you can.\n' +
        'You can do this by logging in and clicking your name at the top right.\n\n' +
        'This takes you to the profile page where you can change your current password.\n' +
        'If you have any questions or if you are still unable to log into the website,\n' +
        'please respond to this email.\n\n' +
        'Thank you,\n' +
        '3PIO';
}