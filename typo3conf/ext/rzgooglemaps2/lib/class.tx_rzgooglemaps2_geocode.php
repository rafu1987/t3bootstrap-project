<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch <rafu1987@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

class tx_rzgooglemaps2_geocode {

    function user_geocode($PA, $fobj) {        
        $content = '<script type="text/javascript" src="../typo3conf/ext/rzgooglemaps2/res/js/jquery-1.7.2.min.js"></script>';
        $content .= '
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery("input.rzgooglemaps2_geocode").click(function(e) {
                        e.preventDefault();                    

                        jQuery.ajax({
                          url: "../typo3conf/ext/rzgooglemaps2/lib/geocode.php",
                          type: "POST",
                          data: {
                            address: jQuery("input[name=\'data[tt_content]['.$PA['row']['uid'].'][pi_flexform][data][sDEF][lDEF][address][vDEF]_hr\']").val(),
                            zip: jQuery("input[name=\'data[tt_content]['.$PA['row']['uid'].'][pi_flexform][data][sDEF][lDEF][zip][vDEF]_hr\']").val(),
                            city: jQuery("input[name=\'data[tt_content]['.$PA['row']['uid'].'][pi_flexform][data][sDEF][lDEF][city][vDEF]_hr\']").val(),
                            country: jQuery("input[name=\'data[tt_content]['.$PA['row']['uid'].'][pi_flexform][data][sDEF][lDEF][country][vDEF]_hr\']").val()
                          },
                          success: function(data) {                            
                            var obj = jQuery.parseJSON(data);

                            // Fill fields
                            jQuery("input#rzgooglemaps2_latitude").val(obj.lat);
                            jQuery("input#rzgooglemaps2_longitude").val(obj.lng);                            
                          }
                        });
                    });
                });
            </script>
        ';
        $content .= '<input type="submit" class="rzgooglemaps2_geocode" value="Geocode address" />';
        
        return $content;
    }    
    
    function user_latitude($PA, $fobj) {
        return '<input class="formField2" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($PA['itemFormElValue']) . '" id="rzgooglemaps2_latitude" />';
    }

    function user_longitude($PA, $fobj) {
        return '<input class="formField2" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($PA['itemFormElValue']) . '" id="rzgooglemaps2_longitude" />';
    }

}

?>
