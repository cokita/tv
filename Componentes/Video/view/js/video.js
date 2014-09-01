$(document).ready(function() {
    $( '#j_form_video' )
        .submit( function( e ) {
            $.ajax( {
                url: 'control.php',
                type: 'POST',
                data: new FormData( this ),
                processData: false,
                dataType: "json",
                contentType: false,
                success: function(data){
                    var result = $.parseJSON(data);
                    var message = {};
                    if(result.error == 1){
                        message = {
                            text: result.msg,
                            layout: "center",
                            type: "error",
                            timeout: 5000
                        };
                    }else{
                        message = {
                            text: result.msg,
                            layout: "center",
                            type: "success",
                            timeout: 1000,
                            callback: {
                                afterClose: function() {
                                    window.location = 'Video';
                                }
                            }
                        };
                    }
                    noty(message);
                    $('#j_youtube_url').val("");
                }
            } );
            e.preventDefault();
        });


    $("#j_inativar").click(function(){
        var arrValues = new Array();
        $("input[type=checkbox]:checked").each ( function() {
            arrValues.push($(this).val());
        });
        if(arrValues.length > 0){

            $('#j_videos_selecionados').val(arrValues);
            noty({
                text: 'Deseja inativar os vídeos selecionados?',
                layout: "center",
                buttons: [
                    {
                        addClass: 'btn btn-primary',
                        text: 'Inativar',
                        onClick: function($noty) {

                            $.ajax({
                                url      : "control.php",
                                type     : "POST",
                                dataType : "json",
                                data: {
                                    admin         : true,
                                    control       : "Video",
                                    action        : "inativar",
                                    idsVideos    : $('#j_videos_selecionados').val(),
                                    returnType    : "JSON"
                                },
                                success: function(data) {
                                    noty ({
                                        text: "Vídeos inativados com sucesso",
                                        layout: "center",
                                        type: "success",
                                        timeout: 1000,
                                        callback: {
                                            afterClose: function() {
                                                window.location = 'Video';
                                            }
                                        }
                                    });
                                }
                            });
                            $noty.close();
                        }
                    },
                    {
                        addClass: 'btn btn-danger',
                        text: 'Cancelar',
                        onClick: function($noty) {
                            $noty.close();
                        }
                    }
                ]
            });
        }else{
            noty ({
                text: "Selecione pelo menos um vídeo para inativar!",
                layout: "center",
                type: "error",
                timeout: 2000
            });
        }

    });

});