(function($) {
    $(document).ready(function() {
        // Drop IE support
        if ($.browser.msie && parseInt($.browser.version, 10) <= 7) {
            if(lang == 0) {
                var message = '<a href="javascript:void(0);" class="close-update-notice" title="Schließen"></a> Diese Seite unterstützt nicht den Internet Explorer 7. Bitte laden Sie sich einen <a href="http://browsehappy.com" target="_blank">neuen Browser</a> herunter.';
            }
            else {
                var message = '<a href="javascript:void(0);" class="close-update-notice" title="Close"></a> This site does not support Internet Explorer 6. Please consider downloading a <a href="http://browsehappy.com/?locale=en" target="_blank">newer browser</a>.';
            }

            var div = $('<div id="ie-warning"></div>').html(message).css({
                height: "30px",
                lineHeight: "30px",
                backgroundColor: "#f9db17",
                textAlign: "center",
                fontFamily: "Arial, Helvetica, sans-serif",
                fontSize: "14px",
                fontWeight: "bold",
                color: "#444444",
                position: "fixed",
                top: 0,
                left: 0,
                zIndex: 9000,
                width: "100%"
            }).hide().find('a').css({
                color: "#333"
            }).end();
            div.prependTo(document.body).slideDown(500);
        }     
        
        $("a.close-update-notice").click(function() {
            $(this).parent().slideUp(500, function() {
                $(this).remove();
            });
        });          
        
        // Remove width and height attributes from all images
        $("img").removeAttr("width").removeAttr("height");
    
        // Disable links / buttons
        $("a.disabled").click(function(e) {
            e.preventDefault();
        });   
        
        // To top link (user)
        $("a.btn-totop").click(function(e) {
            e.preventDefault();    
            
            $("html, body").animate({
                scrollTop: 0
            },800); 
        });
        
        // To top link 
        $("body").append('<a href="javascript:void(0);" class="scrollup">Scroll</a>');
        
        $(window).scroll(function(){
            if ($(this).scrollTop() > 650) {
                $(".scrollup").fadeIn();
            } else {
                $(".scrollup").fadeOut();
            }
        });    
        
        $(".scrollup").click(function(e){
            e.preventDefault();
            
            $("html, body").animate({
                scrollTop: 0
            }, 800);
        }); 
        
        // Powermail submit link instead of button
        $("a.powermail_submit").click(function(e) {
            $(this).parents("form").submit();
        });
        
        // Text white links (not for buttons)
        $(".text-white a").not(".btn").css({
	        color: "white"
        });
        
        // Fitvids
        $(".fitvid").fitVids();
    });
})(jQuery);