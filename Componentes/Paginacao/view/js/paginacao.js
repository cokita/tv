function paginacao(control, action, content, admin){
    if(admin == 1){
        admin = "&admin=1";
    }else{
        admin = "";
    }

    $(".j_paginacao_item").click(function(){
        $("#j_pg_pagina").val($(this).val());
        $.ajax({
            type: "POST",
            data: $("#j_frmpaginacao").serialize(),
            url: "control.php?control="+control+"&action="+action+"&returnType=tpl&pg_pagina="+$(this).val()+""+admin,
            success: function(data){
                $(content).html(data);
            }
        });
    });
}
