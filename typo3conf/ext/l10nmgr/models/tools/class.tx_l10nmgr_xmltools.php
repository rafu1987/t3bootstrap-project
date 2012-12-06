<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Kasper Sk�rh�j <kasperYYYY@typo3.com>
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
/**
 * Contains xml tools
 *
 * $Id$
 *
 * @author	Daniel P�tzinger <development@aoemedia.de>
 */

 require_once(t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_utf8tools.php');

class tx_l10nmgr_xmltools {
	/** @var $parseHTML t3lib_parsehtml_proc */
	var $parseHTML;

	function tx_l10nmgr_xmltools() {
		$this->parseHTML = t3lib_div::makeInstance('t3lib_parsehtml_proc');

	}
	function isValidXMLString($xmlString) {
		return $this->isValidXML('<!DOCTYPE dummy [ <!ENTITY nbsp " "> ]><dummy>'.$xmlString.'</dummy>');
	}
	function isValidXML($xml) {
		$parser = xml_parser_create();
		$vals = array();
		$index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parse_into_struct($parser, $xml, $vals, $index);
		if (xml_get_error_code($parser))
			return false;
		else
			return true;
	}

	/**
	 * Transforms a RTE Field to valid XML
	 *
	 *
	 * @param	string		HTML String which should be transformed
	 * @return	mixed		false if transformation failed, string with XML if all fine
	 */
	function RTE2XML($content, $withStripBadUTF8 = 0) {
	//function RTE2XML($content,$withStripBadUTF8=$GLOBALS['BE_USER']->getModuleData('l10nmgr/cm1/checkUTF8', '')) {
		//if (!$withStripBadUTF8) {
		//	$withStripBadUTF8 = $GLOBALS['BE_USER']->getModuleData('l10nmgr/cm1/checkUTF8', '');
		//}
		//echo '###'.$withStripBadUTF8;
		$content_org = $content;
			// First call special transformations (registered using hooks)
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['transformation'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['transformation'] as $classReference) {
				$processingObject = t3lib_div::getUserObj($classReference);
				$content = $processingObject->transform_rte($content, $this->parseHTML);
			}
		}
		$content = $this->parseHTML->TS_images_rte($content);
		$content = $this->parseHTML->TS_links_rte($content);
		$content = $this->parseHTML->TS_transform_rte($content, 1);

		//substitute & with &amp;
		//$content=str_replace('&','&amp;',$content); Changed by DZ 2011-05-11
		$content = str_replace('<hr>','<hr />',$content);
		$content = str_replace('<br>','<br />',$content);

		$content = t3lib_div::deHSCentities($content);
		if ($withStripBadUTF8 == 1) {
			$content = tx_l10nmgr_utf8tools::utf8_bad_strip($content);
		}
		if ($this->isValidXMLString($content)) {
			return $content;
		}
		else {
			return false;
		}
	}
	/**
	 * Transforms a XML back to RTE / reverse function of RTE2XML
	 *
	 *
	 * @param	string		XMLString which should be transformed
	 * @return	string		string with HTML
	 */
	function XML2RTE($xmlstring) {
		//fixed setting of Parser (TO-DO set it via typoscript)

			//Added because import failed
			$xmlstring=str_replace('<br/>','<br>',$xmlstring);
			$xmlstring=str_replace('<br />','<br>',$xmlstring);

			$xmlstring=str_replace('<hr/>','<hr>',$xmlstring);
			$xmlstring=str_replace('<hr />','<hr>',$xmlstring);
			$xmlstring=str_replace('<p/>','<p></p>',$xmlstring); // DZ: Added 2011-05-11 to avoid import problem with <p/> elements

			$this->parseHTML->procOptions['typolist']=FALSE;
			$this->parseHTML->procOptions['typohead']=FALSE;
			$this->parseHTML->procOptions['keepPDIVattribs']=TRUE;
			$this->parseHTML->procOptions['dontConvBRtoParagraph']=TRUE;

			if (!is_array($this->parseHTML->procOptions['HTMLparser_db.'])) {
				$this->parseHTML->procOptions['HTMLparser_db.']=array();
			}

			$this->parseHTML->procOptions['HTMLparser_db.']['xhtml_cleaning']=TRUE;
				//trick to preserve strong tags
			$this->parseHTML->procOptions['denyTags']='strong';
			$this->parseHTML->procOptions['preserveTables']=TRUE;
			//$parseHTML->procOptions['disableUnifyLineBreaks']=TRUE;
			$this->parseHTML->procOptions['dontRemoveUnknownTags_db']=TRUE;

				//Writes debug information for CLI import to syslog if $TYPO3_CONF_VARS['SYS']['enable_DLOG'] is set.
			if (TYPO3_DLOG) {
				t3lib_div::sysLog(__FILE__.': Before RTE transformation:'.LF.$xmlstring.LF, 'l10nmgr');
			}

			$content = $this->parseHTML->TS_transform_db($xmlstring,$css=0); // removes links from content if not called first!
			$content = $this->parseHTML->TS_images_db($content);
			$content = $this->parseHTML->TS_links_db($content);
				// Last call special transformations (registered using hooks)
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['transformation'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['transformation'] as $classReference) {
					$processingObject = t3lib_div::getUserObj($classReference);
					$content = $processingObject->transform_db($content, $this->parseHTML);
				}
			}

			//substitute URL in <link> for CLI import
			$content = preg_replace('/<link http(s)?:\/\/[\w\.\/]*\?id=/', '<link ', $content);

				//Writes debug information for CLI import to syslog if $TYPO3_CONF_VARS['SYS']['enable_DLOG'] is set.
			if (TYPO3_DLOG) {
				t3lib_div::sysLog(__FILE__.': After RTE transformation:'.LF.$content.LF, 'l10nmgr');
			}

			return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/tools/class.tx_l10nmgr_xmltools.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/tools/class.tx_l10nmgr_xmltools.php']);
}
?>
