<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Kasper Skårhøj <kasperYYYY@typo3.com>
*
*  @author	Fabian Seltmann <fs@marketing-factory.de>
*
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
 * Abstract class for all export views
 *
 * @author	Fabian Seltmann <fs@marketing-factory.de>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 **/
abstract class tx_l10nmgr_abstractExportView {

	/**
 	 * @var	array List of messages issued during rendering
 	 */
 	protected $internalMessages = array();

	/**
	 * @var	tx_l10nmgr_l10nConfiguration The language configuration object
	 */
	var $l10ncfgObj;

	/**
	 * @var	integer The sys_language_uid of language to export
	 */
	var $sysLang;

	/**
	 *	 flags for controlling the fields which should render in the output:
	 */
	var $modeOnlyChanged = FALSE;
	var $modeNoHidden = FALSE;
	var $modeOnlyNew = FALSE;
	var $exportType;

	/**
	 * @var string
	 */
	var $filename = '';


	function __construct($l10ncfgObj, $sysLang) {
		$this->sysLang = $sysLang;
		$this->l10ncfgObj = $l10ncfgObj;
	}

	function getExportType() {
		return $this->exportType;
	}

	function setModeNoHidden() {
		$this->modeNoHidden = TRUE;
	}
	function setModeOnlyChanged() {
		$this->modeOnlyChanged = TRUE;
	}
	function setModeOnlyNew() {
		$this->modeOnlyNew = TRUE;
	}

	/**
 	 * Store a message in the internal queue
 	 *
 	 * Note: this method is protected. Messages should not be set from the outside.
 	 *
 	 * @param string $message Text of the message
 	 * @param string $key Key identifying the element where the problem happened
 	 * @return void
 	 */
 	protected function setInternalMessage($message, $key) {
 		$this->internalMessages[] = array(
 			'message' => $message,
 			'key' => $key
 		);
 	}

 	/**
 	 * Returns the list of internal messages
 	 *
 	 * @return array List of messages
 	 */
 	public function getMessages() {
 		return $this->internalMessages;
 	}

	/**
	 * Get filename
	 *
	 * @return string File name
	 */
	function getFilename(){
		if(empty($this->filename)) {
			$this->setFilename();
		}
		return $this->filename;
	}

	/**
	 * Set filename
	 *
	 * @return void
	 */
	function setFilename() {
		$sourceLang = '';
		$targetLang = '';

		if ($this->exportType == '0') {
			$fileType = 'excel';
		} else {
			$fileType = 'catxml';
		}

		if ($this->l10ncfgObj->getData('sourceLangStaticId') && t3lib_extMgm::isLoaded('static_info_tables')) {
			$sourceIso2L = '';
			$staticLangArr = t3lib_BEfunc::getRecord('static_languages',$this->l10ncfgObj->getData('sourceLangStaticId'), 'lg_iso_2');
			$sourceIso2L = ' sourceLang = "' . $staticLangArr['lg_iso_2'] . '"';
		}

		if ($this->sysLang && t3lib_extMgm::isLoaded('static_info_tables')) {
			$targetLangSysLangArr = t3lib_BEfunc::getRecord('sys_language', $this->sysLang);
			$targetLangArr = t3lib_BEfunc::getRecord('static_languages', $targetLangSysLangArr['static_lang_isocode']);
		}

			// Set sourceLang for filename
		if (isset( $staticLangArr['lg_iso_2'] ) && !empty( $staticLangArr['lg_iso_2'] )) {
			$sourceLang = $staticLangArr['lg_iso_2'];
		}

			// Use locale for targetLang in filename if available
		if (isset($targetLangArr['lg_collate_locale']) && !empty($targetLangArr['lg_collate_locale'])) {
			$targetLang = $targetLangArr['lg_collate_locale'];
			// Use two letter ISO code if locale is not available
		} elseif (isset($targetLangArr['lg_iso_2']) && !empty($targetLangArr['lg_iso_2'])) {
			$targetLang = $targetLangArr['lg_iso_2'];
		}

		$fileNamePrefix = (trim($this->l10ncfgObj->getData('filenameprefix'))) ? $this->l10ncfgObj->getData('filenameprefix').'_'.$fileType : $fileType ;

			// Setting filename:
		$filename =  $fileNamePrefix . '_' . $sourceLang . '_to_' . $targetLang . '_' . date('dmy-His').'.xml';
		$this->filename = $filename;
	}

	/**
	 * Saves the information of the export in the database table 'tx_l10nmgr_sava_data'
	 *
	 * @return resource Handle to the database query
	 */
	function saveExportInformation() {

		// get current date
		$date = time();

		//To-Do get source language if another than default is selected
		$sourceLanguageId=0;

		// query to insert the data in the database
		$field_values = array(
			'source_lang' => $sourceLanguageId,
			'translation_lang' => $this->sysLang,
			'crdate' => $date,
			'tstamp' => $date,
			'l10ncfg_id' => $this->l10ncfgObj->getData('uid'),
			'pid' => $this->l10ncfgObj->getData('pid'),
			'tablelist' => $this->l10ncfgObj->getData('tablelist'),
			'title' => $this->l10ncfgObj->getData('title'),
			'cruser_id' => $this->l10ncfgObj->getData('cruser_id'),
			'filename' => $this->getFilename(),
			'exportType' => $this->exportType
		);

		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_l10nmgr_exportdata', $field_values);

		#t3lib_div::debug();
		return $res;
	}

	/**
	 * Checks if an export exists
	 *
	 * @return boolean
	 */
	function checkExports(){
		$ret = FALSE;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'l10ncfg_id,exportType,translation_lang',
			'tx_l10nmgr_exportdata',
			'l10ncfg_id =' . $this->l10ncfgObj->getData('uid') . ' AND exportType = ' . $this->exportType . ' AND translation_lang = ' . $this->sysLang
		);

		if (!$GLOBALS['TYPO3_DB']->sql_error()) {
			$numRows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		} else {
			$numRows = 0;
		}

		if ( $numRows > 0){
			$ret = FALSE;
		} else {
			$ret = TRUE;
		}

		return $ret;
	}

	/**
	 * Fetches saved exports based on configuration, export format and target language.
	 *
	 * @author Andreas Otto <andreas.otto@dkd.de>
	 * @return array Information about exports.
	 */
	function fetchExports() {
		$exports = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'crdate,l10ncfg_id,exportType,translation_lang,filename',
			'tx_l10nmgr_exportdata',
				'l10ncfg_id = ' . $this->l10ncfgObj->getData('uid') . ' AND exportType = ' . $this->exportType . ' AND translation_lang = ' . $this->sysLang
		);

		if (is_array($res)) {
			$exports = $res;
		}

		return $exports;
	}

	/**
	 * Renders a list of saved exports as HTML table.
	 *
	 * @return string HTML table
	 */
	function renderExports() {
		global $LANG;
		$out = '';
		$content = array();
		$exports = $this->fetchExports();

		foreach( $exports AS $export => $exportData ) {
			$content[$export] = sprintf('
<tr class="db_list_normal">
	<td>%s</td>
	<td>%s</td>
	<td>%s</td>
	<td>%s</td>
	<td>%s</td>
</tr>',
				t3lib_BEfunc::datetime($exportData['crdate']),
				$exportData['l10ncfg_id'],
				$exportData['exportType'],
				$exportData['translation_lang'],
				sprintf('<a href="%suploads/tx_l10nmgr/saved_files/%s">%s</a>',
					t3lib_div::getIndpEnv('TYPO3_SITE_URL'),
					$exportData['filename'],
					$exportData['filename']
				)
			);
		}

		$out = sprintf('
<table border="0" cellpadding="0" cellspacing="0" class="typo3-dblist">
	<thead>
		<tr class="t3-row-header">
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
		</tr>
	</thead>
	<tbody>
%s
	</tbody>
</table>',
			$LANG->getLL('export.overview.date.label'),
			$LANG->getLL('export.overview.configuration.label'),
			$LANG->getLL('export.overview.type.label'),
			$LANG->getLL('export.overview.targetlanguage.label'),
			$LANG->getLL('export.overview.filename.label'),
			implode(chr(10), $content)
		);

		return $out;
	}

	/**
	 * Saves the exported files to the folder /uploads/tx_l10nmgr/jobs/out/
	 *
	 * @param string $fileContent The content to save to file
	 * @return string $fileExportName The complete filename
	 */
	function saveExportFile($fileContent) {
		$fileExportName = 'uploads/tx_l10nmgr/jobs/out/' . $this->getFilename();
		t3lib_div::writeFile(PATH_site . $fileExportName, $fileContent);

		return $fileExportName;
	}

	/**
	 * Diff-compare markup
	 *
	 * @param string $old Old content
	 * @param string $new New content
	 * @return string Marked up string.
	 */
	function diffCMP($old, $new) {
			// Creates diff-result
			/** @var $t3lib_diff_Obj t3lib_diff */
		$t3lib_diff_Obj = t3lib_div::makeInstance('t3lib_diff');
		return $t3lib_diff_Obj->makeDiffDisplay($old,$new);
	}

	/**
	 * Renders internal messages as flash message.
	 * If the export was successful, check if there were any internal warnings.
	 * If yes, display them below the success message.
	 *
	 * @param string $status Flag which indicates if the export was successful.
	 * @return string Rendered flash message or empty string if there are no messages.
	 */
	function renderInternalMessagesAsFlashMessage($status) {
		$ret = '';
		if ($status == t3lib_FlashMessage::OK) {
			$internalMessages = $this->getMessages();
			if (count($internalMessages) > 0) {
				$messageBody = '';
				foreach ($internalMessages as $messageInformation) {
					$messageBody .= $messageInformation['message'] . ' (' . $messageInformation['key'] . ')<br />';
				}
					/** @var $flashMessage t3lib_FlashMessage */
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$messageBody,
					$GLOBALS['LANG']->getLL('export.ftp.warnings'),
					t3lib_FlashMessage::WARNING
				);
				$ret .= $flashMessage->render();
			}
		}

		return $ret;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/class.tx_l10nmgr_abstractExportView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/class.tx_l10nmgr_abstractExportView.php']);
}
?>