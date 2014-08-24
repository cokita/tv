var usuario = {
    reload  : function() {
        $.ajax({
            url      : "control.php",
            type     : "POST",
            cache    : false,
            async    : false,
            data: {
                admin         : true,
                control       : "Usuario_Administrador",
                action        : "dadosUsuario",
                returnType    : "tpl"
            },
            beforeSend: function () {
                block();
            },
            success: function(data) {
                $("#j_conteudo_usuarios").html(data);
                $('.j_excluir_usuario').click(function() {
                    var id_usuario = $(this).attr("id_usuario");
                    usuario.excluir(id_usuario);
                });
                $(".j_editar_usuario").click(function(){
                    var id_usuario = $(this).attr("id_usuario");
                    var id_usuario_logado = $("#j_id_usuario_logado").val();
                    usuario.loadUsuario(id_usuario, id_usuario_logado);
                });
                unblock();
            }
        });
    },

    cadastrar : function (){
        var id_usuario = $("#j_id_usuario").val();
        var erro = false;
        $("#j_msn_email").html("");
        $("#j_msn_login").html("");

        if (validarEmail() == false) {
            $("#j_msn_email").html("O e-mail informado já consta na base de dados.");
            $("#j_msn_email").addClass("fail");
            erro = true;
        }
        if (validarlogin() == false) {
            $("#j_msn_login").html("O login informado já consta na base de dados.");
            $("#j_msn_login").addClass("fail");
            erro = true;
        }
        if(erro == false) {
            $.ajax({
                url      : "control.php",
                type     : "POST",
                dataType : "json",
                data     : $("#j_form_usuario").serialize(),
                beforeSend: function () {
                    block();
                },
                success: function(data) {
                    $("#j_cadastro_usuario").addClass("none");
                    usuario.reload();
                    unblock();
                }
            });
        }
    },

    loadUsuario : function (id_usuario){

        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            data: {
                admin         : true,
                control       : "Usuario_Administrador",
                action        : "buscarDadosUsuario",
                returnType    : "JSON",
                id_usuario    : id_usuario
            },
            beforeSend: function () {
                block();
            },
            success: function(data) {
                $("#j_id_usuario").val(id_usuario);
                $("#j_cadastro_usuario").removeClass("none");
                $("#j_nome_usuario").val(data.nome);
                $("#j_login_usuario").val(data.login);
                $("#j_email_usuario").val(data.email);
                if(data.ativo == 1){
                    $("#j_ativo_usuario").attr('checked', true);
                }else{
                    $("#j_ativo_usuario").attr('checked', false);
                }
                unblock();
            }
        });
    },
    
    excluir : function(id_usuario){
        $.prompt("Tem certeza que deseja excluir este usuário?", {
            buttons  : {
                ok : "sim",
                cancelar : false
            },
            callback : function(valida) {
                if (valida) {
                    $.ajax({
                        type : "POST",
                        url  : "control.php",
                        dataType : "json",
                        data : {
                            admin         : true,
                            control       : "Usuario_Administrador",
                            action        : "excluirUsuario",
                            id_usuario    : id_usuario,
                            returnType    : "json"
                        },
                        beforeSend: function () {
                            block();
                        },
                        success: function(data){
                            if(data){
                                usuario.reload();
                            } else {
                                $.prompt("Não foi possível excluir o usuário");
                            }
                            unblock();
                        }
                    });
                }
            }
        });
    }


}

$(document).ready(function() {
    $("#j_cadastrar_usuario").click(function(){

        $('#j_form_usuario').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                nome_usuario: {
                    validators: {
                        notEmpty: {},
                        stringLength: {
                            min: 6,
                            max: 30,
                            message: 'The username must be more than 6 and less than 30 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_]+$/,
                            message: 'The username can only consist of alphabetical, number and underscore'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'The email is required and cannot be empty'
                        },
                        emailAddress: {
                            message: 'The input is not a valid email address'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e){
            e.preventDefault();
            usuario.cadastrar();
        });
    });
    $(".j_excluir_usuario").click(function(){
        usuario.excluir($(this).attr("id_usuario"));
    });
    $(".j_editar_usuario").click(function(){
        usuario.loadUsuario($(this).attr("id_usuario"), $("#j_id_usuario_logado").val());
    });

    
    $("#j_adicionar_usuario").click(function(){
        $("#j_id_usuario").val("")
        $("#j_cadastro_usuario").removeClass("hidden");
        $("#j_nome_usuario").val("");
        $("#j_login_usuario").val("");
        $("#j_email_usuario").val("");
        $("#j_admin_usuario").attr('checked', false);
    });

    $("#j_cancelar_usuario").click(function(){
        $("#j_cadastro_usuario").attr("class", "hidden");
    });

//    $('#j_form_usuario').bootstrapValidator({
//
//    });

});

function validarEmail(){
    var sucesso = false;
    var id_usuario = $("#j_id_usuario").val();

    if ($("#j_email_usuario").val() != "") {
        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            cache    : false,
            async    : false,
            data: {
                admin         : true,
                control       : "Usuario_Administrador",
                action        : "checarEmail",
                returnType    : "JSON",
                email_usuario : $("#j_email_usuario").val(),
                id_usuario    : id_usuario
            },
            success: function(data) {
                sucesso = data;
            }
        });
    }else{
        sucesso = true;
    }
    return sucesso;
}
function validarlogin(){
    var sucesso = false;
    var id_usuario = $("#j_id_usuario").val();

    if ($("#j_login_usuario").val() != "") {
        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            cache    : false,
            async    : false,
            data: {
                admin         : true,
                control       : "Usuario_Administrador",
                action        : "checarlogin",
                returnType    : "JSON",
                login_usuario : $("#j_login_usuario").val(),
                id_usuario    : id_usuario
            },
            success: function(data) {
                sucesso = data;
            }
        });
    }else{
        sucesso = true;
    }
    return sucesso;
}