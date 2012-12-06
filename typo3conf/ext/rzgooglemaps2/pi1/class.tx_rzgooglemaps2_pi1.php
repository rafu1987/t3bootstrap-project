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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */
require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Google Maps' for the 'rzgooglemaps2' extension.
 *
 * @author    Raphael Zschorsch <rafu1987@gmail.com>
 * @package    TYPO3
 * @subpackage    tx_rzgooglemaps2
 */
class tx_rzgooglemaps2_pi1 extends tslib_pibase {

    var $prefixId = 'tx_rzgooglemaps2_pi1';  // Same as class name
    var $scriptRelPath = 'pi1/class.tx_rzgooglemaps2_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 'rzgooglemaps2'; // The extension key.
    var $pi_checkCHash = true;

    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        // Read Flexform    
        $this->pi_initPIflexForm();

        // Add API
        $GLOBALS['TSFE']->additionalHeaderData[$extKey] = '<script type="text/javascript" src="' . $this->conf['apiUrl'] . '"></script>';

        // Add CSS
        $GLOBALS['TSFE']->additionalHeaderData[$extKey] .= '<link rel="stylesheet" href="' . $this->conf['cssFile'] . '" />';

        // Get flexform values
        $lat = $this->getFlexform('latitude');
        $lng = $this->getFlexform('longitude');
        $address = $this->getFlexform('address');
        $zip = $this->getFlexform('zip');
        $city = $this->getFlexform('city');
        $country = $this->getFlexform('country');
        $infoText = $this->pi_RTEcssText($this->getFlexform('infoText'));
        $width = $this->getFlexform('width', 'options');
        $height = $this->getFlexform('height', 'options');
        $zoom = $this->getFlexform('zoom', 'options');
        $mapType = $this->getFlexform('mapType', 'options');
        $controls = $this->getFlexform('controls', 'options');
        $infoWindowAutoOpen = $this->getFlexform('infoWindowAutoOpen', 'options');
        $navigation = $this->getFlexform('navigation', 'options');
        $ceID = $this->cObj->data['uid'];

        // Default values
        if (!$width)
            $width = '450px';
        if (!$height)
            $height = '250';
        if (!$zoom || !is_numeric($zoom))
            $zoom = 10;
        if (!$mapType)
            $mapType = 'MapTypeId.ROADMAP';

        // Controls
        if ($controls) {
            $controlsArr = explode(",", $controls);
            $controlsConfig = ',';
            foreach ($controlsArr as $c) {
                if ($c == 'scaleControl') {
                    $controlsConfig .= '
                        ' . $c . ': true,
                    ';
                } else {
                    $controlsConfig .= '
                        ' . $c . ': false,
                    ';
                }
            }
            $controlsConfig = substr($controlsConfig, 0, -1);
        }

        // Navigation
        if ($navigation == 1) {
            // Checks if t3jquery is loaded
            if (t3lib_extMgm::isLoaded('t3jquery')) {
                require_once(t3lib_extMgm::extPath('t3jquery') . 'class.tx_t3jquery.php');
            }

            // If t3jquery is loaded and the custom library had been created
            if (T3JQUERY === true) {
                tx_t3jquery::addJqJS();

                // Read t3jquery extConf
                $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);
                $integrateToFooter = $this->extConf['integrateToFooter'];

                // Include JS to footer
                if ($integrateToFooter == 1) {
                    $GLOBALS['TSFE']->additionalFooterData[$extKey] .= '<script type="text/javascript" src="' . $this->conf['jsFile'] . '"></script>';
                } else {
                    $GLOBALS['TSFE']->additionalHeaderData[$extKey] .= '<script type="text/javascript" src="' . $this->conf['jsFile'] . '"></script>';
                }
            } else {
                $GLOBALS['TSFE']->additionalHeaderData[$extKey] .= '<script src="' . $this->conf['jQueryLibrary'] . '" type="text/javascript"></script>';
                $GLOBALS['TSFE']->additionalHeaderData[$extKey] .= '<script type="text/javascript" src="' . $this->conf['jsFile'] . '"></script>';
            }

            // Address
            $address_final = $address . ', ' . $zip . ' ' . $city . ', ' . $country;

            // Template
            $this->templateHtml = $this->cObj->fileResource($this->conf['templateFile']);
            $template['route'] = $this->cObj->getSubpart($this->templateHtml, '###TEMPLATE###');
            
            $markerArray['###CE_ID###'] = $ceID;  
            $markerArray['###FROM_HERE_LABEL###'] = $this->pi_getLL("from_here");  
            $markerArray['###TO_HERE_LABEL###'] = $this->pi_getLL("to_here");  
            $markerArray['###ROUTE_LABEL###'] = $this->pi_getLL("calculate_route");  
            $markerArray['###ADDRESS###'] = $address_final;  
            $markerArray['###BUTTON_CLASSES###'] = $this->conf['googleMapsSubmitClasses'];
            
            // Output route template
            $navigationOutput = $this->cObj->substituteMarkerArrayCached($template['route'], $markerArray, array());

            $infoWindowContent = $infoText . $navigationOutput;
        } else {
            $infoWindowContent = $infoText;
        }

        // Display map
        $content .= '<div id="map_canvas_' . $ceID . '" style="width:' . $width . ';height:' . $height . 'px;"></div>';

        // Build map
        $content .= '
            <script type="text/javascript">
                var map' . $ceID . ';
                function initialize() {
                  var myLatlng' . $ceID . ' = new google.maps.LatLng(' . $lat . ', ' . $lng . ');
                  
                  var myOptions' . $ceID . ' = {
                      zoom: ' . $zoom . ',
                      center: myLatlng' . $ceID . ',
                      mapTypeId: google.maps.' . $mapType . '
                      ' . $controlsConfig . '
                  }
                  map' . $ceID . ' = new google.maps.Map(document.getElementById("map_canvas_' . $ceID . '"), myOptions' . $ceID . ');
                  
                  var contentString' . $ceID . ' = \'' . str_replace(array("\n", "\r"), array("", ""), $infoWindowContent) . '\';
  
                  var infowindow' . $ceID . ' = new google.maps.InfoWindow({
                      content: contentString' . $ceID . '
                  });
  
                  var marker' . $ceID . ' = new google.maps.Marker({
                      position: myLatlng' . $ceID . ', 
                      map: map' . $ceID . '
                  });                     
        ';

        if ($infoWindowAutoOpen == 1) {
            $content .= '
                setTimeout(function() {    
                    infowindow' . $ceID . '.open(map' . $ceID . ', marker' . $ceID . ');
                },1250);
            ';
        }

        $content .= '
                google.maps.event.addListener(marker' . $ceID . ', \'click\', function() {
                  infowindow' . $ceID . '.open(map' . $ceID . ',marker' . $ceID . ');
                });   
                }
                google.maps.event.addDomListener(window, \'load\', initialize);             
            </script>            
        ';

        // Output map
        return $this->pi_wrapInBaseClass($content);
    }

    function getFlexform($key, $sheet = '', $confOverride = '') {
        // Default sheet is sDEF
        $sheet = (!empty($sheet)) ? $sheet : 'sDEF';
        $flexform = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $key, $sheet);

        // Possible override through TS
        if ($confOverride == '') {
            return $flexform;
        } else {
            $value = $flexform ? $flexform : $this->conf[$confOverride];
            return $value;
        }
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzgooglemaps2/pi1/class.tx_rzgooglemaps2_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzgooglemaps2/pi1/class.tx_rzgooglemaps2_pi1.php']);
}
?>