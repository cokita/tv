$(document).ready(function() {

    $('#j_form_login')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                user_login: {
                    validators: {
                        notEmpty: {}
                    }
                },
                senha_login: {
                    validators: {
                        notEmpty: {},
                        stringLength: {
                            min: 6,
                            max: 45
                        }
                    }
                }
            }
        })
    .on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();
        // Get the form instance
        var $form = $(e.target);

        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');

        var dataVerify = {control: "Login",
                          action: "checarLogineSenha",
                          user_login: $('#user_login').val(),
                          senha_login: $('#senha_login').val(),
                          returnType: "json"
            }
            $.ajax({
                url      : "control.php",
                type     : "POST",
                dataType : "json",
                data     : dataVerify,
                success: function(data) {
                    if(data == 0){
                        noty ({
                            text: "Usuário ou senha inválidos!",
                            layout: "center",
                            type: "error",
                            timeout: 3000
                        });
                        return false;
                    }else{
                        bv.defaultSubmit();
                    }
                }
            });

    });

});