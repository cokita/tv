function block(){
    $.blockUI({
            message: '<h1>Aguarde...</h1>',
            css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }});

        //setTimeout($.unblockUI, 2000);
}

function unblock(){
    $.unblockUI();
}

function isLoading(){
    $("#j_loading").append("<img src='images/colorbox/loading.gif' border='0' id='j_img_loading' width='20'>");
}
function isLoaded(){
    $("#j_img_loading").remove();
}

function checarDatas(data_inicio, data_fim){

    var Compara01 = parseInt(data_inicio.split("/")[2].toString() + data_inicio.split("/")[1].toString() + data_inicio.split("/")[0].toString());
    var Compara02 = parseInt(data_fim.split("/")[2].toString() + data_fim.split("/")[1].toString() + data_fim.split("/")[0].toString());

    if (Compara01 < Compara02) {
        return true;
    }
    else {
        return false;
    }
}
