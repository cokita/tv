$(document).ready(function() {

    $('#imgs').sortable({
        cursor: 'move',
        connectWith: "#imgs"
    });

    var $imagens = $( "#imagens"),
        $galeria = $( "#galeria" );


    $( "li", $imagens ).draggable({
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

    $( "li", $galeria ).draggable({
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

    $galeria.droppable({
        accept: "#imagens li",
        activeClass: "ui-state-highlight",
        refreshPositions: true,
        drop: function( event, ui ) {
            addImage( ui.draggable );
        }
    });

    $imagens.droppable({
        accept: "#galeria li",
        activeClass: "ui-state-highlight",
        drop: function( event, ui ) {
            recycleImage( ui.draggable );
        }
    });


    function addImage( $item ) {
        $item.fadeOut(function() {
            var $list = $( "ul", $galeria ).length ?
                $( "ul", $galeria ) :
                $( "<ul class='gallery ui-helper-reset'/>" ).appendTo( $galeria );

            $item.appendTo( $list ).fadeIn();
        });
    }

    function recycleImage( $item ) {
        $item.fadeOut(function() {
            $item.find( "img" )
                .css( "height", "100px" )
                .end()
                .appendTo( '#imgs' )
                .fadeIn();
        });
    }


    /* Salvar Galeria*/

    $("#j_salvar_galeria").click(function(){
        var ids = new Array();
        $( "#galeria li" ).each(function( index ) {
            ids.push($( this ).attr('id'));
        });


        $.ajax({
            url      : "control.php",
            type     : "POST",
            dataType : "json",
            data: {
                admin         : true,
                control       : "Galeria",
                action        : "salvar",
                ids           : ids,
                returnType    : "JSON"
            },
            success: function(data) {
                window.location = 'Galeria';
            }
        });
    });


});
