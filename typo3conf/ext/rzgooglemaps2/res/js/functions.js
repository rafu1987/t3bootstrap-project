jQuery(document).ready(function() {    
    jQuery(".rzgooglemaps2_navigation_menue a").live("click", function(e) {
        e.preventDefault();    
    
        // Add font-weight
        jQuery(this).css("fontWeight","bold");
        jQuery(this).siblings().css("fontWeight","normal");
        
        // Change input field
        if(jQuery(".rzgooglemaps2_input_1").attr("name") == 'saddr') {
            jQuery(".rzgooglemaps2_input_1").attr("name","daddr");
        }   
        else if(jQuery(".rzgooglemaps2_input_1").attr("name") == 'daddr') {
            jQuery(".rzgooglemaps2_input_1").attr("name","saddr");
        }  
        
        if(jQuery(".rzgooglemaps2_input_2").attr("name") == 'saddr') {
            jQuery(".rzgooglemaps2_input_2").attr("name","daddr");
        }   
        else if(jQuery(".rzgooglemaps2_input_2").attr("name") == 'daddr') {
            jQuery(".rzgooglemaps2_input_2").attr("name","saddr");
        }          
    });
    
    jQuery("a.rzgooglemaps2_submit").live("click", function() {
        jQuery(this).parents("form").submit();
    });
});