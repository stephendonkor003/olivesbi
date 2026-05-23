var Auth = function () {
    var loginForm = $("#login-form");
    var btnLogin = $("#btn-login");
    var btnUnlock = $("#btn-unlock");
    var tfUsername = $("#username");
    var pfPassword = $("#password");
    return {
        initAuth: function () {
            Auth.validateAuthForm();
            btnLogin.on("click", function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                Auth.login();
            });
            btnUnlock.on("click", function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                //Auth.initUnlockAction();
            });
        },
        validateAuthForm: function () {
            Common.validateForm(loginForm, {
                name: {
                    required: true,
                },
                password: {
                    required: true
                }
            }, {});
        },
        getCredentials: function () {
            return {username: tfUsername.val(), password: pfPassword.val()};
        },
        login: function () {
            $.ajax({
                type: "POST",
                url: loginForm.attr('data-action'),
                data: loginForm.serialize(),
                dataType: "json",
                // contentType: 'application/json',
                beforeSend: function (xhr) {
                    if (loginForm.valid()) {
                        Common.showSpinner(loginForm, "Checking...");
                        return true;
                    } else {
                        return false;
                    }
                }
            }).done(function (response) {
               // console.log(response);
                // Common.hideSpinner(loginForm);
                if (response.status === 'ok') {
                    window.location.replace(response.url);
                } else {
                    Common.onError(response.message);
                }
            }).always(function (response) {
                Common.hideSpinner(loginForm);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                Common.hideSpinner(loginForm);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }

            });
        },
    };

}();
$(function () {
    Auth.initAuth();
});