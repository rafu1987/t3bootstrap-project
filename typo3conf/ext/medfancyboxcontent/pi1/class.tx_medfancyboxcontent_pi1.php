<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch <zschorsch@medialis.net>
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

// require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Fancybox Content' for the 'medfancyboxcontent' extension.
 *
 * @author	Raphael Zschorsch <zschorsch@medialis.net>
 * @package	TYPO3
 * @subpackage	tx_medfancyboxcontent
 */
class tx_medfancyboxcontent_pi1 extends tslib_pibase {

    public $prefixId = 'tx_medfancyboxcontent_pi1';  // Same as class name
    public $scriptRelPath = 'pi1/class.tx_medfancyboxcontent_pi1.php'; // Path to this script relative to the extension dir.
    public $extKey = 'medfancyboxcontent'; // The extension key.
    public $pi_checkCHash = TRUE;

    /**
     * The main method of the Plugin.
     *
     * @param string $content The Plugin content
     * @param array $conf The Plugin configuration
     * @return string The content that is displayed on the website
     */
    public function init($content, array $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();
    }

    public function main($content, array $conf) {
        $this->init($content, $conf);
        
        /*
        * @todo There's still an error with the original fancybox image enlargement
        **/
        
        // Get base url
        $baseUrl = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'];
        $baseUrl = trim($baseUrl,"/").'/';
        
        // If no baseurl is set, display error message
        if(!$baseUrl) {             	
	        $GLOBALS['TSFE']->additionalFooterData['fancybox-error'] = '
	        	<script type="text/javascript">
	            	(function($) {
	                	$(document).ready(function() {
	                		$(".fancybox-content-error:not(:last)").remove();
		        			$(".fancybox-content-error").append(\'<div class="alert alert-error">'.$this->pi_getLL('noBaseUrl').'</div>\');
		        		});
		        	})(jQuery);
	        	</script>
	        ';
	        
	        $content = '<div class="fancybox-content-error"></div>';
	        	        
	        return $content;
        }

        // Get content elements
        $ce = explode(",", $this->getFlexform("ce"));

        // Get content element ID
        $ceID = $this->cObj->data['uid'];
        $GLOBALS['TSFE']->register['ceID']  = $ceID;
        
        // Get link title
        $linkTitle = $this->getFlexform("linkTitle");
        if(!$linkTitle) $linkTitle = $this->pi_getLL('linkTitle');
        
        // Get link class
        $linkClass = $this->getFlexform("linkClass", "options");
        if(!$linkClass) $linkClass = $this->conf['settings.']['link.']['class'];
        
        // Get type
        $type = $this->getFlexform("type");
        
        if($type == 'ce') {   
            // Set JS
            $this->setJS($content, $conf, $ceID, '');            
            
            $content = $this->cObj->stdWrap('<a href="#c'.$ceID.'-fancybox" class="'.$linkClass.' fancybox-content-'.$ceID.'">'.$linkTitle.'</a>', $this->conf['settings.']['link.']);
        
            foreach ($ce as $contentEl) {
                // Output the Content Elements        
                $contentConf = array(
                    'tables' => 'tt_content',
                    'source' => $contentEl,
                    'dontCheckPid' => 1
                );

                $contentOutput .= $this->cObj->RECORDS($contentConf);
            }
        }
        else if($type == 'iframe') {
            // Set JS
            $this->setJS($content, $conf, $ceID, '');               
            
            $url = trim($this->getFlexform("link"));
            
            if(!is_numeric($url)) {        
            	$urlConf['stdWrap.']['typolink.']['parameter'] = $url;
            }
            else {
	        	$urlConf['stdWrap.']['typolink.']['parameter'] = $url;    
            }
            $urlConf['stdWrap.']['typolink.']['returnLast'] = 'url';
            
            $urlFinal = $baseUrl.''.$this->cObj->TEXT($urlConf);
                        
            $content = $this->cObj->stdWrap('<a href="'.$urlFinal.'" data-fancybox-type="iframe" class="'.$linkClass.' fancybox-content-'.$ceID.'">'.$linkTitle.'</a>', $this->conf['settings.']['link.']);
        }
        else if($type == 'media') {
            // Add JS
            $GLOBALS['TSFE']->additionalFooterData['fancybox-media'] = '
                <script type="text/javascript" src="typo3conf/ext/medfancyboxcontent/res/js/jquery.fancybox-media.js"></script>
            ';
            
            // Set JS
            $this->setJS($content, $conf, $ceID, 'fancybox-content-media');               
            
            $url = $this->getFlexform("link");
            $urlConf['stdWrap.']['typolink.']['parameter'] = $url;
            $urlConf['stdWrap.']['typolink.']['returnLast'] = 'url';
            
            $urlFinal = $this->cObj->TEXT($urlConf);  
            
            $content = $this->cObj->stdWrap('<a href="'.$urlFinal.'" class="'.$linkClass.' fancybox-content-media-'.$ceID.'">'.$linkTitle.'</a>', $this->conf['settings.']['link.']);
        }
        
        
        $content .= $this->cObj->stdWrap($contentOutput, $this->conf['settings.']['contentWrap.']);

        return $this->pi_wrapInBaseClass($content);
    }
    
    public function setJS($content, array $conf, $ceID, $class) {
        $this->init($content, $conf);
        
        /* Get values BEGIN */
        
        // Width
        $width = $this->getFlexform("width", "options");       
        if($width) {
            if(is_numeric($width)) $width = $width;
            else $width = '"'.$width.'"';
        }
        else {
            $width = $this->conf['settings.']['fancybox.']['width'];
            if(!$width) $width = '800';
        }
        
        // Height
        $height = $this->getFlexform("height", "options");    
        if($height) {
            if(is_numeric($height)) $height = $height;
            else $height = '"'.$height.'"';   
        }
        else {
            $height = $this->conf['settings.']['fancybox.']['height'];
            if(!$height) $height = '800';
        }
        
        // Auto width
        $autoWidth = $this->getFlexform("autoWidth", "options");
        
        if($autoWidth == 0) {
            $autoWidth = 'false';
        }
        else {
            $autoWidth = 'true';
        }
        
        // Auto height
        $autoHeight = $this->getFlexform("autoHeight", "options");
        
        if($autoHeight == 0) {
            $autoHeight = 'false';
        }
        else {
            $autoHeight = 'true';
        }
        
        // Autosize
        $autoSize = $this->getFlexform("autoSize", "options");
        
        if($autoSize == 0) {
            $autoSize = 'false';
        }
        else {
            $autoSize = 'true';
        }
        
        // Close button
        $closeBtn = $this->getFlexform("closeBtn", "options");
        
        if($closeBtn == 0) {
            $closeBtn = 'false';
        }
        else {
            $closeBtn = 'true';
        }
        
        // Close click
        $closeClick = $this->getFlexform("closeClick", "options");
        
        if($closeClick == 0) {
            $closeClick = 'false';
        }
        else {
            $closeClick = 'true';
        }        
        
        // Padding
        $padding = $this->getFlexform("padding", "options");
        if(is_numeric($padding)) $padding = $padding;
        else {
            $padding = $this->conf['settings.']['fancybox.']['padding'];
            if(!$padding) $padding = 20;
        }
        
        // Modal
        $modal = $this->getFlexform("modal", "options");
        
        if($modal == 0) {
            $modal = 'false';
        }
        else {
            $modal = 'true';
        } 
       
        // Fit to view
        $fitToView = $this->getFlexform("fitToView", "options");
        
        if($fitToView == 0) {
            $fitToView = 'false';
        }
        else {
            $fitToView = 'true';
        }   
        
        // Top ratio
        $topRatio = $this->getFlexform("topRatio", "options");
        if(!$topRatio) $topRatio = $this->conf['settings.']['fancybox.']['topRatio'];
        
        // Auto open
        $autoOpen = $this->getFlexform("autoOpen", "options");
        if($autoOpen == 0) {
	        $autoOpen = '';
        }
        else {
        	$autoOpen = '.trigger("click")';
        }
        
        // Class
        if($class) {
            $class = '.'.$class.'-'.$ceID;
            
            $GLOBALS['TSFE']->additionalFooterData[$this->extKey.'-'.$ceID] = '
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            $("'.$class.'").fancybox({
                                arrows: false,
                                helpers: {
                                    media: {}
                                }, 
                                width: '.$width.',
                                height: '.$height.', 
                                autoWidth: '.$autoWidth.',
                                autoHeiht: '.$autoHeight.',                             
                                autoSize: '.$autoSize.',
                                maxWidth: "100%",
                                closeBtn: '.$closeBtn.',
                                closeClick: '.$closeClick.',
                                padding: '.$padding.',
                                modal: '.$modal.',
                                topRatio: '.$topRatio.',
                                fitToView: '.$fitToView.'
                            })'.$autoOpen.';
                        });
                    })(jQuery);     
                </script>
            ';            
        }
        else {
            $class = '.fancybox-content-'.$ceID;
            
            $GLOBALS['TSFE']->additionalFooterData[$this->extKey.'-'.$ceID] = '
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            $("'.$class.'").fancybox({
                                width: '.$width.',
                                height: '.$height.',
                                autoWidth: '.$autoWidth.',
                                autoHeight: '.$autoHeight.',
                                autoSize: '.$autoSize.',
                                maxWidth: "100%",
                                closeBtn: '.$closeBtn.',
                                closeClick: '.$closeClick.',
                                padding: '.$padding.',
                                modal: '.$modal.',
                                topRatio: '.$topRatio.',
                                fitToView: '.$fitToView.'
                            })'.$autoOpen.';
                        });
                    })(jQuery);     
                </script>
            ';            
        }
    }

    private function getFlexform($key, $sheet = '', $confOverride = '') {
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

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medfancyboxcontent/pi1/class.tx_medfancyboxcontent_pi1.php'])) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medfancyboxcontent/pi1/class.tx_medfancyboxcontent_pi1.php']);
}
?>