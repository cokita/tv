var usuario = {
    cadastrar : function (){
        var id_usuario = $("#j_id_usuario").val();
        var erro = false;
        $("#j_msn_cpf").html("");
        
        if(($("#j_form_usuario").valid())) {
            if (validarCPF() == false) {
                $("#j_msn_cpf").html("O cpf informado já consta na base de dados.");
                $("#j_msn_cpf").addClass("fail");
                erro = true;
            }
            if(erro == false){
                $.ajax({
                    url      : "control.php",
                    type     : "POST",
                    dataType : "json",
                    data     : $("#j_form_usuario").serialize(),
                    beforeSend: function () {
                        block();
                    },
                    success: function(data) {
                        unblock();
                        voltarListagemUsuarios();
                    }
                });
            }
        }
    }
}

$(document).ready(function() {
       
    $('#j_cpf_usuario').tipsy({
        trigger: 'focus', 
        gravity: 's'
    });
    $('#j_telefone_usuario').tipsy({
        trigger: 'focus', 
        gravity: 's'
    });
    $('#j_ddd_usuario').tipsy({
        trigger: 'focus', 
        gravity: 's'
    });
    $('#j_ddi_usuario').tipsy({
        trigger: 'focus', 
        gravity: 's'
    });
    $('#j_nome_usuario').tipsy({
        trigger: 'focus', 
        gravity: 's'
    });
    
    $("#j_cadastrar_usuario").click(function(){
        usuario.cadastrar();
    });
    
    $("#j_voltar_usuario").click(function(){
        voltarListagemUsuarios();
    });

    $("#j_form_usuario").validate({
        rules: {
            nome_usuario: "required",
            cpf_usuario:  {
                required: true,
                number: true,
                maxlength: 11,
                minlength: 11
            },
            ddi_usuario:  {
                required: true,
                number: true,
                minlength: 2
            },
            ddd_usuario:  {
                required: true,
                number: true,
                minlength: 2
            },
            telefone_usuario:  {
                required: true,
                number: true,
                minlength: 8,
                maxlength: 8
            },
            agencia_usuario:  "required",
            conta_usuario:  "required"
        }
    });
});

function validarCPF(){
    var sucesso = false;
    var id_usuario = $("#j_id_usuario").val();

    if ($("#j_cpf_usuario").val() != "") {
        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            cache    : false,
            async    : false,
            data: {
                admin         : true,
                control       : "Usuario",
                action        : "checarCPF",
                returnType    : "JSON",
                cpf_usuario : $("#j_cpf_usuario").val(),
                id_usuario    : id_usuario
            },
            beforeSend: function(){
                block();  
            },
            success: function(data) {
                unblock();
                sucesso = data;
            }
        });
    }else{
        sucesso = true;
    }
    
    return sucesso;
}

function voltarListagemUsuarios(){
    var url = $("#j_url_sistema").val();
    window.location = url+"Usuario_Visualizar";
}