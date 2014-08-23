var usuario_visualizar = {
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
                            control       : "Usuario",
                            action        : "excluirUsuario",
                            id_usuario    : id_usuario,
                            returnType    : "json"
                        },
                        beforeSend: function () {
                            block();
                        },
                        success: function(data){
                            if(data){
                                $("#j_usuario_"+id_usuario).remove();
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
    $(".j_editar_usuario").click(function(){
        var url = $("#j_url_sistema").val();
        var id = $(this).attr("id_usuario");
        window.location = url+"Usuario/"+id;
    });
    
    $(".j_detalhe_usuario").click(function(){
        var url = $("#j_url_sistema").val();
        var id = $(this).attr("id_usuario");
        window.location = url+"Usuario_Detalhe/"+id;
    });
    
    
    $(".j_excluir_usuario").click(function(){
        usuario_visualizar.excluir($(this).attr("id_usuario"));
    });
});