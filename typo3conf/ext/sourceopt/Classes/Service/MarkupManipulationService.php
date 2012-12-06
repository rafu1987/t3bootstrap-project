<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Tim Lochmüller
 * 
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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

require_once(t3lib_extMgm::extPath('sourceopt', 'Classes/Service/MarkupManipulationConfigurationService.php'));

/**
 * Manipulation methods for the markup 
 */
class Tx_Sourceopt_Service_MarkupManipulationService extends Tx_Sourceopt_Service_MarkupManipulationConfigurationService {

	/**
	 * Remove the generator Tag
	 * 
	 * @return Tx_Sourceopt_Service_MarkupManipulationService 
	 */
	public function manipulateRemoveGenerator() {
		$this->setHtml(preg_replace("/<meta name=\"?generator\"?.+?>/is", "", $this->getHtml()));
		return $this;
	}

	/**
	 * Remove all comments exept the whitelisted comments
	 * Whitelist is a array of patterns
	 * 
	 * @param array $whiteListCommentsPatterns
	 * @return Tx_Sourceopt_Service_MarkupManipulationService 
	 */
	public function manipulateRemoveComments(array $whiteListCommentsPatterns = array()) {
		$html = $this->getHtml();
		// match all styles, scripts and comments
		$matches = array();
		preg_match_all("/(?s)((<!--.*?-->)|(<[ \n\r]*style[^>]*>.*?<[ \n\r]*\/style[^>]*>)|(<[ \n\r]*script[^>]*>.*?<[ \n\r]*\/script[^>]*>))/im", $html, $matches);
		for ($i = 0; $i < count($matches[0]); $i++) {
			if (preg_match("/^\<\!\-\-(.*?)\-\-\>$/usi", $matches[0][$i])    // if a comment
				   && !$this->keepComment($matches[0][$i], $whiteListCommentsPatterns)) { // and not defined in keepComment
				$html = str_replace($matches[0][$i], "", $html);
			}
		}
		$this->setHtml($html);
		return $this;
	}

	/**
	 * TYPO3 adds to each page a small script: 
	 *                <script language="javascript">
	 *                <!--
	 *                browserName = navigator.appName;
	 *                browserVer = parseInt(navigator.appVersion);
	 *                var msie4 = (browserName == "Microsoft Internet Explorer" && browserVer >= 4);
	 *                if ((browserName == "Netscape" && browserVer >= 3) || msie4 || browserName=="Konqueror") {version = "n3";} else {version = "n2";}
	 *                function blurLink(theObject){
	 *                if (msie4){theObject.blur();}
	 *                }
	 *                // -->
	 *                </script>
	 * Obviously used for client-side browserdetection - but thats not necessary if your page doesn't use JS
	 * 
	 * @return Tx_Sourceopt_Service_MarkupManipulationService                        
	 */
	function manipulateRemoveBlurScript() {
		$html = $this->getHtml();
		if (strlen($html) < 100000) {
			$pattern = "/<script (type=\"text\/javascript\"|language=\"javascript\")>.+?Konqueror.+function blurLink.+theObject.blur.+?<\/script>/is";
			$html = preg_replace($pattern, "", $html); // in head
		}
		$html = str_replace(' onfocus="blurLink(this);"', "", $html); // in body
		$this->setHtml($html);
		return $this;
	}

	/**
	 * help function
	 * check if a comment is defined to be kept in a pattern whiteListOfComments
	 *         
	 * @param string $commentHtml
	 * @param array $whiteListCommentsPatterns
	 * @return boolean 
	 */
	protected function keepComment($commentHtml, $whiteListCommentsPatterns) {
		if (sizeof($whiteListCommentsPatterns)) {
			$commentHtml = str_replace("<!--", "", $commentHtml);
			$commentHtml = str_replace("-->", "", $commentHtml);
			$commentHtml = trim($commentHtml);
			foreach ($whiteListCommentsPatterns as $pattern) {
				if (preg_match($pattern, $commentHtml)) {
					return true;
				}
			}
		}
		return false;
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/Classes/Service/MarkupManipulationService.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/Classes/Service/MarkupManipulationService.php"]);
}
?>