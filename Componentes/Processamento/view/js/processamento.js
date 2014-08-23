$(document).ready(function() {

    processar();

});

function processar(){
    $(".j_processar").click(function(){
        var id_transacao = $(this).parent().attr("id");
        var td_transacao = "j_transacao_"+id_transacao;
        if(id_transacao){
            $.ajax({
            url      : "control.php",
            type     : "POST",
            data: {
                admin         : true,
                control       : "Processamento",
                action        : "processarTransacao",
                returnType    : "JSON",
                id_transacao : id_transacao
            },
            beforeSend: function(){
                block();  
            },
            success: function(data) {
                unblock();
                $("#"+td_transacao).remove();
                var count = $("#count_processamentos").val() - 1;
                $("#count_processamentos").val(count);
                if($("#count_processamentos").val() == 0){
                    $("#j_titulo_processamento").html('Nenhum processamento pendente!');
                }
                $.prompt("Transação realizada com sucesso!");
            }
        });
        }
    });
}
