<?php
/***************************************************************
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
 ***************************************************************/

// require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Markdown Content' for the 'medmarkdown' extension.
 *
 * @author	Raphael Zschorsch <zschorsch@medialis.net>
 * @package	TYPO3
 * @subpackage	tx_medmarkdown
 */
 
require_once(PATH_typo3conf . 'ext/medmarkdown/lib/markdown.php');
 
class tx_medmarkdown_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_medmarkdown_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_medmarkdown_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'medmarkdown';	// The extension key.
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
	
		// Get text
		$text = $this->getFlexform("text");
			
		// TypoLink transformations for internal links
		$url = preg_match_all('/\[(.*)\]\((.*)\)/', $text, $match);
		
		foreach($match[2] as $m) {
			if(is_numeric($m)) {
				$linkConf['stdWrap.']['typolink.']['parameter'] = $m;
				$linkConf['stdWrap.']['typolink.']['returnLast'] = 'url';
				
				$link = $this->cObj->TEXT($linkConf);
				
				$text = preg_replace('/\[(.*)\]\(('.$m.')\)/', '<a href="'.$link.'">$1</a>', $text);
			}		
		}
		
		// Text transformations
		$text = Markdown($text);
	
		$content = $text;
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	public function makeLink($content, $conf, $id) {
		$this->init($content, $conf);
		
		$linkConf['value'] = '';
		$linkConf['stdWrap.']['typolink.']['parameter'] = $id;
		//$linkConf['stdWrap.']['typolink.']['returnLast'] = 'url';
		
		return $this->cObj->TEXT($linkConf);
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



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medmarkdown/pi1/class.tx_medmarkdown_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/medmarkdown/pi1/class.tx_medmarkdown_pi1.php']);
}

?>