$(document).ready(function() {
    $('.fancybox-content-media')
    .attr('rel', 'media-gallery')
    .fancybox({
        arrows : false,
        helpers : {
            media : {}
        }
    });
});