$(document).ready(function(){
    var player;
    $('#myCarousel').carousel({
        interval: 3000
    });

    $('#myCarousel').on('slid.bs.carousel', function (e) {
        if($('.active').find('.j_tipo').html() == 'v'){
            $('#myCarousel').carousel('pause');
            $('.active').append('<div id="player"></div>');
            onYouTubePlayerAPIReady();
        }
    });



    function onYouTubePlayerAPIReady() {
        var code = $('.active').find('.url_code').html();
        player = new YT.Player('player', {
            height: '720',
            width: '1280',
            videoId: code,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });

    }

    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        if(event.data === 0) {
            $('#myCarousel').carousel('cycle');
            $('#player').remove();
        }
    }
});




