$(document).ready(function() {
    $( '#j_form_imagem' )
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
                                    window.location = 'Imagem';
                                }
                            }
                        };
                    }
                    noty(message);


                    $('#j_upload_foto').val("");
                    $('#j_arquivo_selecionado').val("");
                }
            } );
            e.preventDefault();
        } );

    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        $('#j_arquivo_selecionado').val(label);
    });


    $("#j_inativar").click(function(){
        var arrValues = new Array();
        $("input[type=checkbox]:checked").each ( function() {
            arrValues.push($(this).val());
        });
        if(arrValues.length > 0){

            $('#j_imagens_selecionadas').val(arrValues);
            noty({
                text: 'Deseja inativar as imagens selecionadas?',
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
                                    control       : "Imagem",
                                    action        : "inativar",
                                    idsImagens    : $('#j_imagens_selecionadas').val(),
                                    returnType    : "JSON"
                                },
                                success: function(data) {
                                    noty ({
                                        text: "Imagens inativadas com sucesso",
                                        layout: "center",
                                        type: "success",
                                        timeout: 4000,
                                        callback: {
                                            afterClose: function() {
                                                window.location = 'Imagem';
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
                text: "Selecione pelo menos uma imagem para inativar!",
                layout: "center",
                type: "error",
                timeout: 2000
            });
        }

    });

});


$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});