$(document).ready(function() {
    $("#login").validate();

    $("#j_autenticar").click( function() {
        var email = $(this).attr("user_login");
        var senha = $(this).attr("senha_login");
        if ($("#login").valid()) {
            if (ajaxChecarLogineSenha() == true) {
                $("#login").submit();
            } else {
                $("#mensagem_login").hide();
                $("#mensagem_login").html("usuário ou senha inválidos").fadeIn("slow");
                $("#senha_login").val("");
            }
        }

        return false;
    });

    function ajaxChecarLogineSenha() {
        sucesso = false;

        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            cache    : false,
            async    : false,
            data: {
                control    : "Login",
                action     : "checarLogineSenha",
                email      : $("#user_login").val(),
                senha      : $("#senha_login").val(),
                returnType : "JSON"
            },
            success: function(data) {
                console.log(data);
                sucesso = data;
            },
            error: function(data) {
                console.log(data);
                sucesso = data;
            }
        });

        return sucesso;
    }
});