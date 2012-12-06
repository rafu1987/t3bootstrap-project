<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Kasper Skårhøj <kasperYYYY@typo3.com>
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

require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_translationData.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_xmltools.php');

/**
 * Function for managing the Import of CAT XML
 *
 * @author	Daniel Poetzinger <ext@aoemedia.de>
 *
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_CATXMLImportManager {

	/**
	 * @var	string		$file		filepath with XML
	 */
	var $file = '';

	/**
	 * @var	string		$xml		CATXML
	 */
	var $xml = '';

	/**
	 * @var	string		$xmlNodes		parsed XML
	 */
	var $xmlNodes = '';

	/**
	 * @var	string		$headerData		headerData of the XML
	 */
	var $headerData = '';

	/**
	 * @var	integer		$sysLang		selected import language (for check purposes - sys_language_uid)
	 */
	var $sysLang;

	/**
	 * @var	array		$_errorMsg		accumulated errormessages
	 */
	var $_errorMsg = array();

	function tx_l10nmgr_CATXMLImportManager($file, $sysLang, $xmlString) {
		$this->sysLang = $sysLang;
		if (!empty($file)) {
			$this->file    = $file;
		}
		if (!empty($xmlString)) {
			$this->xmlString    = $xmlString;
		}
	}

	function parseAndCheckXMLFile() {
		global $LANG;

		$fileContent    = t3lib_div::getUrl($this->file);
		$this->xmlNodes = t3lib_div::xml2tree(str_replace('&nbsp;',' ',$fileContent),3);	// For some reason PHP chokes on incoming &nbsp; in XML!

		if (!is_array($this->xmlNodes)) {
			$this->_errorMsg[] = $LANG->getLL('import.manager.error.parsing.xml2tree.message') . $this->xmlNodes;
			return false;
		}

		$headerInformationNodes = $this->xmlNodes['TYPO3L10N'][0]['ch']['head'][0]['ch'];
		if (!is_array($headerInformationNodes)) {
			$this->_errorMsg[] = $LANG->getLL('import.manager.error.missing.head.message');
			return false;
		}

		$this->_setHeaderData($headerInformationNodes);
		if ($this->_isIncorrectXMLFile()) {
			return false;
		}
	}

	function parseAndCheckXMLString() {
		global $LANG;

		$catXmlString   = $this->xmlString;
		$this->xmlNodes = t3lib_div::xml2tree(str_replace('&nbsp;',' ',$catXmlString),3);	// For some reason PHP chokes on incoming &nbsp; in XML!

		if (!is_array($this->xmlNodes)) {
			$this->_errorMsg[] = $LANG->getLL('import.manager.error.parsing.xml2tree.message') . $this->xmlNodes;
			return false;
		}

		$headerInformationNodes = $this->xmlNodes['TYPO3L10N'][0]['ch']['head'][0]['ch'];
		if (!is_array($headerInformationNodes)) {
			$this->_errorMsg[] = $LANG->getLL('import.manager.error.missing.head.message');
			return false;
		}

		$this->_setHeaderData($headerInformationNodes);
		if ($this->_isIncorrectXMLString()) {
			return false;
		}

	}

	function getErrorMessages() {
		return implode('<br />', $this->_errorMsg);
	}

	function &getXMLNodes() {
		return $this->xmlNodes;
	}

	function _isIncorrectXMLFile() {
		global $LANG;
		$error = array();

		if (!isset($this->headerData['t3_formatVersion']) || $this->headerData['t3_formatVersion'] != L10NMGR_FILEVERSION) {
			$error[] = sprintf(
							$LANG->getLL('import.manager.error.version.message'),
							$this->headerData['t3_formatVersion'],
							L10NMGR_FILEVERSION
						);
		}
		if (!isset($this->headerData['t3_workspaceId']) || $this->headerData['t3_workspaceId'] != $GLOBALS['BE_USER']->workspace) {
		$GLOBALS['BE_USER']->workspace=$this->headerData['t3_workspaceId'];
			$error[] = sprintf(
							$LANG->getLL('import.manager.error.workspace.message'),
							$GLOBALS['BE_USER']->workspace,
							$this->headerData['t3_workspaceId']
						);
		}
		if (!isset($this->headerData['t3_sysLang']) || $this->headerData['t3_sysLang'] != $this->sysLang) {
		

			$error[] = sprintf(
							$LANG->getLL('import.manager.error.language.message'),
							$this->sysLang,
							$this->headerData['t3_sysLang']
						);
		}
		if (count($error)>0) {
			$this->_errorMsg = array_merge($this->_errorMsg, $error);
			return true;
		}
		return false;
	}

	function _isIncorrectXMLString() {
		global $LANG;
		$error = array();

		if (!isset($this->headerData['t3_formatVersion']) || $this->headerData['t3_formatVersion'] != L10NMGR_FILEVERSION) {
			$error[] = sprintf(
							$LANG->getLL('import.manager.error.version.message'),
							$this->headerData['t3_formatVersion'],
							L10NMGR_FILEVERSION
						);
		}
		if (!isset($this->headerData['t3_workspaceId']) || $this->headerData['t3_workspaceId'] != $GLOBALS['BE_USER']->workspace) {
			$error[] = sprintf(
							$LANG->getLL('import.manager.error.workspace.message'),
							$GLOBALS['BE_USER']->workspace,
							$this->headerData['t3_workspaceId']
						);
		}
		if (!isset($this->headerData['t3_sysLang'])) {
		//if (!isset($this->headerData['t3_sysLang']) || $this->headerData['t3_sysLang'] != $this->sysLang) {
		
			$error[] = sprintf(
							$LANG->getLL('import.manager.error.language.message'),
							$this->sysLang,
							$this->headerData['t3_sysLang']
						);
		}
		if (count($error)>0) {
			$this->_errorMsg = array_merge($this->_errorMsg, $error);
			return true;
		}
		return false;
	}

	function _setHeaderData($headerInformationNodes) {

		if (!is_array($headerInformationNodes)) {
			return;
		}
		foreach ($headerInformationNodes as $k=>$v) {
			$this->headerData[$k]=$v[0]['values'][0];
		}
	}

	/**
	 * Get pageGrp IDs for preview link generation
	 *
	 * @param	array		XML nodes from CATXML
	 * @return	array		Page IDs for preview
	 */
	function getPidsFromCATXMLNodes(&$xmlNodes) {
		$pids = array();

		if (is_array($xmlNodes['TYPO3L10N'][0]['ch']['pageGrp']))	{
		   	foreach($xmlNodes['TYPO3L10N'][0]['ch']['pageGrp'] as $pageGrp)	{
				$pids[]=$pageGrp[attrs][id];
			}
		}
		return $pids;
	}

	/**
	 * Get uids for which localizations shall be removed on 2nd import if option checked
	 *
	 * @param	array		XML nodes from CATXML
	 * @return	array		Uids for which localizations shall be removed
	 */
	function getDelL10NDataFromCATXMLNodes(&$xmlNodes) {
		//get L10Ns to be deleted before import

		$delL10NUids = array();
		if (is_array($xmlNodes['TYPO3L10N'][0]['ch']['pageGrp']))	{
		   	foreach($xmlNodes['TYPO3L10N'][0]['ch']['pageGrp'] as $pageGrp)	{
				if (is_array($pageGrp['ch']['data'])) {
					foreach($pageGrp['ch']['data'] as $row)	{
						if (preg_match('/NEW/',$row['attrs']['key'])){
							$delL10NUids[] = $row['attrs']['table'].':'.$row['attrs']['elementUid'];
						}
					}
				}
			}
		}
		return array_unique($delL10NUids);
	}

	/**
	 * Delete previous localisations
	 *
	 * @param	array		table:id combinations to be deleted
	 * @return	int		Number of deleted elements		
	 */
	function delL10N($delL10NData) {

		//delete previous L10Ns
		$cmdCount = 0;
		foreach ($delL10NData as $element) {
			list($table,$elementUid) = explode(':', $element);
			$where = "l18n_parent = $elementUid AND sys_language_uid = ".$this->headerData['t3_sysLang']." AND t3ver_wsid = ".$this->headerData['t3_workspaceId'];
			if ($table == 'pages') {
				$table = 'pages_language_overlay';
				$where = "pid = $elementUid AND sys_language_uid = ".$this->headerData['t3_sysLang']." AND t3ver_wsid = ".$this->headerData['t3_workspaceId'];
			}
			$delDataQuery = $GLOBALS['TYPO3_DB']->exec_DELETEquery($table,$where);
			$cmdCount++;
		}
		return $cmdCount;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/class.tx_l10nmgr_CATXMLImportManager.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/class.tx_l10nmgr_CATXMLImportManager.php']);
}
?>
