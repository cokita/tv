$(document).ready(function(){
    //alert("oi");

    processamentos.load();
    processar();
});

var processamentos = {
    load : function (){
        $.ajax({
            url      : "control.php",
            type     : "POST",
            data: {
                control       : "Processamento",
                action        : "index",
                admin         : 1,
                returnType    : "tpl"
            },
            beforeSend: function(){
                $("#j_listProcessamento").addClass("none");
                $("#j_processando").removeClass("none");
            },
            success: function(data) {
                $("#j_listProcessamento").html(data);
                setTimeout(function () {
                    processamentos.load();
                }, 10000);
                processar();
                contagem(10);
                $("#j_listProcessamento").removeClass("none");
                $("#j_processando").addClass("none");
            }
        });
        
    }
    
}
    function contagem(tempo){
        if(tempo > 0){
            $("#tempo").html('Próxima atualização em: '+tempo);

            tempo = tempo-1;

            setTimeout('contagem(\''+tempo+'\')', 1000);

        }
    }




