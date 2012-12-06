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
/**
 * Contains translation tools
 *
 * $Id: class.t3lib_loaddbgroup.php 1816 2006-11-26 00:43:24Z mundaun $
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   69: class tx_l10nmgr_tools
 *   95:	 function tx_l10nmgr_tools()
 *  113:	 function getRecordsToTranslateFromTable($table,$pageId)
 *  140:	 function getSingleRecordToTranslate($table,$uid)
 *  169:	 function translationDetails($table,$row,$sysLang,$flexFormDiff=array())
 *  282:	 function translationDetails_flexFormCallBack($dsArr, $dataValue, $PA, $structurePath, &$pObj)
 *  329:	 function translationDetails_addField($key, $TCEformsCfg, $dataValue, $translationValue, $diffDefaultValue='', $previewLanguageValues=array())
 *  372:	 function indexDetailsRecord($table,$uid)
 *  393:	 function indexDetailsPage($pageId)
 *  429:	 function diffCMP($old, $new)
 *  444:	 function compileIndexRecord($table,$fullDetails,$sys_lang,$pid)
 *  501:	 function updateIndexTable($record)
 *  513:	 function updateIndexTableFromDetailsArray($rDetails,$echo=FALSE)
 *  528:	 function flushIndexOfWorkspace($ws)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Contains translation tools
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_tools {

	// External:
	var $filters = array(
		'fieldTypes' => 'text,input',
		'noEmptyValues' => TRUE,
		'noIntegers' => TRUE,
		'l10n_categories' => '' // could be "text,media" for instance.
	);

	var $previewLanguages = array(); // Array of sys_language_uids, eg. array(1,2)
	var $verbose = TRUE; // If TRUE, when fields are not included there will be shown a detailed explanation.
	var $bypassFilter = FALSE; // If TRUE, do not call filter function
	var $includeFceWithDefaultLanguage = FALSE; //if set to true also FCE with language setting default will be included (not only All)

	// Internal:
	var $t8Tools = NULL; // Object to t3lib_transl8tools, set in constructor
	var $detailsOutput = array(); // Output for translation details
	var $sysLanguages = array(); // System languages initialized
	var $flexFormDiff = array(); // FlexForm diff data
	var $sys_languages = array(); // System languages records, loaded by constructor
	var $indexFilterObjects = array();

	/**
	 * Constructor
	 * Setting up internal variable ->t8Tools
	 *
	 * @return	void
	 */
	function tx_l10nmgr_tools() {
		/** @var $this->t8tools t3lib_transl8tool */
		$this->t8Tools = t3lib_div::makeInstance('t3lib_transl8tools');

		// Find all system languages:
		$this->sys_languages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'sys_language',
			''
		);
	}

	/**
	 * Returns true if the record can be included in index.
	 */
	function filterIndex($table, $uid, $pageId) {

		// Initialize (only first time)
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['indexFilter']) && !is_array($this->indexFilterObjects[$pageId])) {
			$this->indexFilterObjects[$pageId] = array();
			$c = 0;
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['indexFilter'] as $objArray) {
				$this->indexFilterObjects[$pageId][$c] = &t3lib_div::getUserObj($objArray[0]);
				$this->indexFilterObjects[$pageId][$c]->init($pageId);
				$c++;
			}
		}

		// Check record:
		if (is_array($this->indexFilterObjects[$pageId])) {
			foreach ($this->indexFilterObjects[$pageId] as $obj) {
				if (!$obj->filter($table, $uid)) {
					return FALSE;
				}
			}
		}

		return TRUE;
	}

	/**
	 * Selecting records from a table from a page which are candidates to be translated.
	 *
	 * @param	string		$table	Table name
	 * @param	integer		$pageId	Page id
	 * @return	array		Array of records from table (with all fields selected)
	 */
	function getRecordsToTranslateFromTable($table, $pageId) {
		global $TCA;

		if ($this->t8Tools->isTranslationInOwnTable($table)) {

			// Check for disabled field settings
			//print "###".$GLOBALS['BE_USER']->uc['moduleData']['xMOD_tx_l10nmgr_cm1']['noHidden']."---";
			if (!empty($GLOBALS['BE_USER']->uc['moduleData']['xMOD_tx_l10nmgr_cm1']['noHidden'])) {
				$hiddenClause = t3lib_BEfunc::BEenableFields($table, $inv = 0);
			} else {
				$hiddenClause = "";
			}

			// First, select all records that are default language OR international:
			$allRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$table,
				'pid=' . intval($pageId) .
					' AND ' . $TCA[$table]['ctrl']['languageField'] . '<=0' .
					($GLOBALS['TCA'][$table]['ctrl']['versioningWS'] ? ' AND ' . $table . '.t3ver_state<=0' : '') .
					$hiddenClause .
					t3lib_BEfunc::deleteClause($table) .
					t3lib_BEfunc::versioningPlaceholderClause($table)
			);

			return $allRows;
		}
	}

	/**
	 * Selecting single record from a table filtering whether it is a default language / international element.
	 *
	 * @param	string		$table	Table name
	 * @param	integer		$uid	Record uid
	 * @return	array		Record array if found, otherwise FALSE
	 */
	function getSingleRecordToTranslate($table, $uid) {
		global $TCA;

		if ($this->t8Tools->isTranslationInOwnTable($table)) {

			// First, select all records that are default language OR international:
			$allRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$table,
				'uid=' . intval($uid) .
					' AND ' . $TCA[$table]['ctrl']['languageField'] . '<=0' .
					($GLOBALS['TCA'][$table]['ctrl']['versioningWS'] ? ' AND ' . $table . '.t3ver_state<=0' : '') .
					t3lib_BEfunc::deleteClause($table) .
					t3lib_BEfunc::versioningPlaceholderClause($table)
			);

			return is_array($allRows) && count($allRows) ? $allRows[0] : FALSE;
		}
	}

	/**
	 * Generate details about translation
	 *
	 * @param	string		Table name
	 * @param	array		Row (one from getRecordsToTranslateFromTable())
	 * @param	integer		sys_language uid
	 * @param	array		FlexForm diff data
	 * @return	array		Returns details array
	 */
	function translationDetails($table, $row, $sysLang, $flexFormDiff = array()) {
		global $TCA;

		// Initialize:
		$tInfo = $this->t8Tools->translationInfo($table, $row['uid'], $sysLang);
		$this->detailsOutput = array();
		$this->flexFormDiff = $flexFormDiff;

		if (is_array($tInfo)) {
			// Initialize some more:
			$this->detailsOutput['translationInfo'] = $tInfo;
			t3lib_div::loadTCA($table);
			$this->sysLanguages = $this->t8Tools->getSystemLanguages();
			$this->detailsOutput['ISOcode'] = $this->sysLanguages[$sysLang]['ISOcode'];

			//decide how translations are stored:
			// there are three ways: flexformInternalTranslation (for FCE with langChildren)
			//												useOverlay (for 	elements with classic overlay record)
			//												noTranslation
			$translationModes = $this->_detectTranslationModes($tInfo, $table, $row);

			foreach ($translationModes as $translationMode) {
				switch ($translationMode) {
					case 'flexformInternalTranslation':
						$this->detailsOutput['log'][] = 'Mode: flexFormTranslation with no translation set; looking for flexform fields';
						$this->_lookForFlexFormFieldAndAddToInternalTranslationDetails($table, $row);
						break;
					case 'useOverlay':

						if (count($tInfo['translations'])) {
							$this->detailsOutput['log'][] = 'Mode: translate existing record';
							$translationUID = $tInfo['translations'][$sysLang]['uid'];
							$translationRecord = t3lib_BEfunc::getRecordWSOL($tInfo['translation_table'], $tInfo['translations'][$sysLang]['uid']);
						} else {
							// Will also suggest to translate a default language record which are in a container block with Inheritance or Separate mode. This might not be something people wish, but there is no way we can prevent it because its a deprecated localization paradigm to use container blocks with localization. The way out might be setting the langauge to "All" for such elements.
							$this->detailsOutput['log'][] = 'Mode: translate to new record';
							$translationUID = 'NEW/' . $sysLang . '/' . $row['uid'];
							$translationRecord = array();
						}

						if ($TCA[$tInfo['translation_table']]['ctrl']['transOrigDiffSourceField']) {
							$diffArray = unserialize($translationRecord[$TCA[$tInfo['translation_table']]['ctrl']['transOrigDiffSourceField']]);
							#					debug($diffArray);
						} else {
							$diffArray = array();
						}

						$prevLangRec = array();
						foreach ($this->previewLanguages as $prevSysUid) {
							$prevLangInfo = $this->t8Tools->translationInfo($table, $row['uid'], $prevSysUid);
							if ($prevLangInfo['translations'][$prevSysUid]) {
								$prevLangRec[$prevSysUid] = t3lib_BEfunc::getRecordWSOL($prevLangInfo['translation_table'], $prevLangInfo['translations'][$prevSysUid]['uid']);
							}
						}

						foreach ($TCA[$tInfo['translation_table']]['columns'] as $field => $cfg) {
							if ($TCA[$tInfo['translation_table']]['ctrl']['languageField'] !== $field
								&& $TCA[$tInfo['translation_table']]['ctrl']['transOrigPointerField'] !== $field
								&& $TCA[$tInfo['translation_table']]['ctrl']['transOrigDiffSourceField'] !== $field
							) {

								$key = $tInfo['translation_table'] . ':' . t3lib_BEfunc::wsMapId($tInfo['translation_table'], $translationUID) . ':' . $field;
								if ($cfg['config']['type'] == 'flex') {
									$dataStructArray = $this->_getFlexFormMetaDataForContentElement($table, $field, $row);
									if ($dataStructArray['meta']['langDisable'] && $dataStructArray['meta']['langDatabaseOverlay'] == 1) {
										// Create and call iterator object:
										$flexObj = t3lib_div::makeInstance('t3lib_flexformtools');
										$this->_callBackParams_keyForTranslationDetails = $key;
										$this->_callBackParams_translationXMLArray = t3lib_div::xml2array($translationRecord[$field]);
										foreach ($this->previewLanguages as $prevSysUid) {
											$this->_callBackParams_previewLanguageXMLArrays[$prevSysUid] = t3lib_div::xml2array($prevLangRec[$prevSysUid][$field]);
										}
										$this->_callBackParams_currentRow = $row;
										$flexObj->traverseFlexFormXMLData($table, $field, $row, $this, 'translationDetails_flexFormCallBackForOverlay');
									}
									$this->detailsOutput['log'][] = 'Mode: useOverlay looking for flexform fields!';
								}
								else {
									//handle normal fields:
									$diffDefaultValue = $diffArray[$field];
									$previewLanguageValues = array();
									foreach ($this->previewLanguages as $prevSysUid) {
										$previewLanguageValues[$prevSysUid] = $prevLangRec[$prevSysUid][$field];
									}
									//debug($row[$field]);

									$this->translationDetails_addField($key, $cfg, $row[$field], $translationRecord[$field], $diffDefaultValue, $previewLanguageValues, $row);
								}
							}
							//elseif ($cfg[
						}
						break;
				}
			} //foreach translationModes
		} else {
			$this->detailsOutput['log'][] = 'ERROR: ' . $tInfo;
		}

		return $this->detailsOutput;
	}

	/**
	 * Function checks which translationMode is used. Mainly it checks the FlexForm (FCE) logic and language returns a array with useOverlay | flexformInternalTranslation
	 *
	 * @param array		$tInfo	Translation info
	 * @param string	$table	Table name
	 * @param array		$row	Table row
	 * @return array
	 */
	function _detectTranslationModes($tInfo, $table, $row) {
		$translationModes = array();
		if ($table === 'pages') {
			$translationModes[] = 'flexformInternalTranslation';
			$this->detailsOutput['log'][] = 'Mode: "flexformInternalTranslation" detected because we have page Record';
		}

		if (count($tInfo['translations']) && $tInfo['sys_language_uid'] != -1) {
			$translationModes[] = 'useOverlay';
			$useOverlay = TRUE;
			$this->detailsOutput['log'][] = 'Mode: "useOverlay" detected because we have existing overlayrecord and language is not "ALL"';
		}
		if ($row['CType'] == 'templavoila_pi1' && !$useOverlay) {
			if (($this->includeFceWithDefaultLanguage && $tInfo['sys_language_uid'] == 0) || $tInfo['sys_language_uid'] == -1) {
				$dataStructArray = $this->_getFlexFormMetaDataForContentElement($table, 'tx_templavoila_flex', $row);

				if (is_array($dataStructArray) && $dataStructArray !== false) {
					if ($dataStructArray['meta']['langDisable']) {
						if ($dataStructArray['meta']['langDatabaseOverlay'] == 1) {
							$translationModes[] = 'useOverlay';
							$this->detailsOutput['log'][] = 'Mode: "useOverlay" detected because we have FCE with langDatabaseOverlay configured';
						}
						else {
							$this->detailsOutput['log'][] = 'Mode: "noTranslation" detected because we have FCE with langDisable';
						}
					}
					elseif ($dataStructArray['meta']['langChildren']) {
						$translationModes[] = 'flexformInternalTranslation';
						$this->detailsOutput['log'][] = 'Mode: "flexformInternalTranslation" detected because we have FCE with langChildren';
					}
				}
				else {
					$this->detailsOutput['log'][] = 'Mode: "noTranslation" detected because we have corrupt Datastructure!';
				}
			}
			else {
				$this->detailsOutput['log'][] = 'Mode: "noTranslation" detected because we FCE in Default Language and its not cofigured to include FCE in Default language';
			}
		}
		elseif ($tInfo['sys_language_uid'] == 0 && $tInfo['translation_table']) {
			//no FCE
			$translationModes[] = 'useOverlay';
			$this->detailsOutput['log'][] = 'Mode: "useOverlay" detected because we have a normal record (no FCE) in default language';
		}
		return array_unique($translationModes);
	}

	/*
	 * Return meta data of flexform field, or false if no flexform is found
	 *
	 * @param string $table Name of the table
	 * @param string $field Name of the field
	 * @param array $row Current row of data
	 * @return array|boolean Flexform structure (or false, if not found)
	 */
	protected function _getFlexFormMetaDataForContentElement($table, $field, $row) {
		$conf = $GLOBALS['TCA'][$table]['columns'][$field]['config'];
		$dataStructArray = t3lib_BEfunc::getFlexFormDS($conf, $row, $table);
		if (is_array($dataStructArray)) {
			return $dataStructArray;
		}

		return FALSE;
	}

	/**
	 * Look for flexform field and add to internal translation details
	 *
	 * @param string	$table	Table name
	 * @param array		$row	Table row
	 * @return void
	 */
	function _lookForFlexFormFieldAndAddToInternalTranslationDetails($table, $row) {
		global $TCA;
		foreach ($TCA[$table]['columns'] as $field => $cfg) {
			$conf = $cfg['config'];

			// For "flex" fieldtypes we need to traverse the structure looking for file and db references of course!
			if ($conf['type'] == 'flex') {
				// We might like to add the filter that detects if record is tt_content/CType is "tx_flex...:" since otherwise we would translate flexform content that might be hidden if say the record had a DS set but was later changed back to "Text w/Image" or so... But probably this is a rare case.

				// Get current data structure to see if translation is needed:
				$dataStructArray = t3lib_BEfunc::getFlexFormDS($conf, $row, $table);

				$this->detailsOutput['log'][] = 'FlexForm field "' . $field . '": DataStructure status: ' . (is_array($dataStructArray) ? 'OK' : 'Error: ' . $dataStructArray);

				if (is_array($dataStructArray) && !$dataStructArray['meta']['langDisable']) {
					$this->detailsOutput['log'][] = 'FlexForm Localization enabled, type: ' . ($dataStructArray['meta']['langChildren'] ? 'Inheritance: Continue' : 'Separate: Stop');

					if ($dataStructArray['meta']['langChildren']) {
						$currentValueArray = t3lib_div::xml2array($row[$field]);
						// Traversing the XML structure, processing files:
						if (is_array($currentValueArray)) {

							// Create and call iterator object:
							/** @var $flexObj t3lib_flexformtools */
							$flexObj = t3lib_div::makeInstance('t3lib_flexformtools');
							$flexObj->traverseFlexFormXMLData($table, $field, $row, $this, 'translationDetails_flexFormCallBack');
						}
					}
				} else {
					$this->detailsOutput['log'][] = 'FlexForm Localization disabled. Nothing to do.';
				}
			}
		}
	}

	/**
	 * FlexForm call back function, see translationDetails
	 *
	 * @param	array		$dsArr			Data Structure
	 * @param	string		$dataValue		Data value
	 * @param	array		$PA				Various stuff in an array
	 * @param	string		$structurePath	Path to location in flexform
	 * @param	object		$pObj			Reference to parent object
	 * @return	void
	 */
	function translationDetails_flexFormCallBack($dsArr, $dataValue, $PA, $structurePath, &$pObj) {

		// Only take lead from default values (since this is "Inheritance" localization we parse for)
		if (substr($structurePath, -5) == '/vDEF') {

			// So, find translated value:
			$baseStructPath = substr($structurePath, 0, -3);
			$structurePath = $baseStructPath . $this->detailsOutput['ISOcode'];
			$translValue = $pObj->getArrayValueByPath($structurePath, $pObj->traverseFlexFormXMLData_Data);

			// Generate preview values:
			$previewLanguageValues = array();
			foreach ($this->previewLanguages as $prevSysUid) {
				$previewLanguageValues[$prevSysUid] = $pObj->getArrayValueByPath($baseStructPath . $this->sysLanguages[$prevSysUid]['ISOcode'], $pObj->traverseFlexFormXMLData_Data);
			}

			$key = $ffKey = $PA['table'] . ':' . t3lib_BEfunc::wsMapId($PA['table'], $PA['uid']) . ':' . $PA['field'] . ':' . $structurePath;
			$ffKeyOrig = $PA['table'] . ':' . $PA['uid'] . ':' . $PA['field'] . ':' . $structurePath;

			// Now, in case this record has just been created in the workspace the diff-information is still found bound to the UID of the original record. So we will look for that until it has been created for the workspace record:
			if (!is_array($this->flexFormDiff[$ffKey]) && is_array($this->flexFormDiff[$ffKeyOrig])) {
				$ffKey = $ffKeyOrig;
				#	debug('orig...');
			}

			// Look for diff-value inside the XML (new way):
			if ($GLOBALS['TYPO3_CONF_VARS']['BE']['flexFormXMLincludeDiffBase']) {
				$diffDefaultValue = $pObj->getArrayValueByPath($structurePath . '.vDEFbase', $pObj->traverseFlexFormXMLData_Data);
			} else {
				// Set diff-value from l10n-cfg record (deprecated)
				if (is_array($this->flexFormDiff[$ffKey]) && trim($this->flexFormDiff[$ffKey]['translated']) === trim($translValue)) {
					$diffDefaultValue = $this->flexFormDiff[$ffKey]['default'];
				} else {
					$diffDefaultValue = '';
				}
			}

			// Add field:
			$this->translationDetails_addField($key, $dsArr['TCEforms'], $dataValue, $translValue, $diffDefaultValue, $previewLanguageValues);
		}
	}

	/**
	 * FlexForm call back function, see translationDetails. This is used for langDatabaseOverlay FCEs!
	 * Two additional paramas are used:
	 * $this->_callBackParams_translationXMLArray
	 * $this->_callBackParams_keyForTranslationDetails
	 *
	 * @param	array		$dsArr			Data Structure
	 * @param	string		$dataValue		Data value
	 * @param	array		$PA				Various stuff in an array
	 * @param	string		$structurePath	Path to location in flexform
	 * @param	object		$pObj			Reference to parent object
	 * @return	void
	 */
	function translationDetails_flexFormCallBackForOverlay($dsArr, $dataValue, $PA, $structurePath, &$pObj) {

		//echo $dataValue.'<hr>';
		$translValue = $pObj->getArrayValueByPath($structurePath, $this->_callBackParams_translationXMLArray);
		//TODO:
		$diffDefaultValue = $dataValue;

		foreach ($this->previewLanguages as $prevSysUid) {
			$previewLanguageValues[$prevSysUid] = $pObj->getArrayValueByPath($structurePath, $this->_callBackParams_previewLanguageXMLArrays[$prevSysUid]);
		}

		$key = $this->_callBackParams_keyForTranslationDetails . ':' . $structurePath;
		$this->translationDetails_addField($key, $dsArr['TCEforms'], $dataValue, $translValue, $diffDefaultValue, $previewLanguageValues, $this->_callBackParams_currentRow);
	}

	/**
	 * Add field to detailsOutput array. First, a lot of checks are done...
	 *
	 * @param	string		$key					Key is a combination of table, uid, field and structure path, identifying the field
	 * @param	array		$TCEformsCfg			TCA configuration for field
	 * @param	string		$dataValue				Default value (current)
	 * @param	string		$translationValue		Translated value (current)
	 * @param	string		$diffDefaultValue		Default value of time of current translated value (used for diff'ing with $dataValue)
	 * @param	array		$previewLanguageValues	Array of preview language values identified by keys (which are sys_language uids)
	 * @param	array		$contentRow				Content row
	 * @return	void
	 */
	function translationDetails_addField($key, $TCEformsCfg, $dataValue, $translationValue, $diffDefaultValue = '', $previewLanguageValues = array(), $contentRow = array()) {
		$msg = '';

		list($kTableName, , $kFieldName) = explode(':', $key);

		if ($TCEformsCfg['config']['type'] !== 'flex') {
			if ($TCEformsCfg['l10n_mode'] != 'exclude') {
				if ($TCEformsCfg['l10n_mode'] == 'mergeIfNotBlank') {
					$msg .= 'This field is optional. If not filled in, the default language value will be used.';
				}
				if (t3lib_div::inList('shortcut,shortcut_mode,urltype,url_scheme', $kFieldName) && t3lib_div::inList('pages,pages_language_overlay', $kTableName)) {
					$this->bypassFilter = TRUE;
				}
				if (!t3lib_div::isFirstPartOfStr($TCEformsCfg['displayCond'], 'HIDE_L10N_SIBLINGS')) {
					if (!t3lib_div::isFirstPartOfStr($kFieldName, 't3ver_')) {
						if (!$this->filters['l10n_categories'] || t3lib_div::inList($this->filters['l10n_categories'], $TCEformsCfg['l10n_cat'])) {
							if (!$this->filters['fieldTypes'] || t3lib_div::inList($this->filters['fieldTypes'], $TCEformsCfg['config']['type']) || $this->bypassFilter) {
								if (!$this->filters['noEmptyValues'] || !(!$dataValue && !$translationValue)) { // Checking that no translation value exists either; if a translation value is found it is considered that it should be translated even if the default value is empty for some reason.
									if (!$this->filters['noIntegers'] || !t3lib_div::testInt($dataValue) || $this->bypassFilter) {
										$this->detailsOutput['fields'][$key] = array(
											'defaultValue' => $dataValue,
											'translationValue' => $translationValue,
											'diffDefaultValue' => $TCEformsCfg['l10n_display'] != 'hideDiff' ? $diffDefaultValue : '',
											'previewLanguageValues' => $previewLanguageValues,
											'msg' => $msg,
											'readOnly' => $TCEformsCfg['l10n_display'] == 'defaultAsReadonly',
											'fieldType' => $TCEformsCfg['config']['type'],
											'isRTE' => $this->_isRTEField($key, $TCEformsCfg, $contentRow)
										);
									} elseif ($this->verbose) {
										$this->detailsOutput['fields'][$key] = 'Bypassing; ->filters[noIntegers] was set and dataValue "' . $dataValue . '" was an integer';
									}
								} elseif ($this->verbose) {
									$this->detailsOutput['fields'][$key] = 'Bypassing; ->filters[noEmptyValues] was set and dataValue "' . $dataValue . '" was empty and no translation found either.';
								}
							} elseif ($this->verbose) {
								$this->detailsOutput['fields'][$key] = 'Bypassing; fields of type "' . $TCEformsCfg['config']['type'] . '" was filtered out in ->filters[fieldTypes]';
							}
						} elseif ($this->verbose) {
							$this->detailsOutput['fields'][$key] = 'Bypassing; ->filters[l10n_categories] was set to "' . $this->filters['l10n_categories'] . '" and l10n_cat for field ("' . $TCEformsCfg['l10n_cat'] . '") did not match.';
						}
					} elseif ($this->verbose) {
						$this->detailsOutput['fields'][$key] = 'Bypassing; Fieldname "' . $kFieldName . '" was prefixed "t3ver_"';
					}
				} elseif ($this->verbose) {
					$this->detailsOutput['fields'][$key] = 'Bypassing; displayCondition HIDE_L10N_SIBLINGS was set.';
				}
			} elseif ($this->verbose) {
				$this->detailsOutput['fields'][$key] = 'Bypassing; "l10n_mode" for the field was "exclude" and field is not translated then.';
			}
		} elseif ($this->verbose) {
			$this->detailsOutput['fields'][$key] = 'Bypassing; fields of type "flex" can only be translated in the context of an "ALL" language record';
		}

		$this->bypassFilter = FALSE;
	}

	/**
	 * Check if the field is an RTE in the Backend, for a given row of data
	 *
	 * @param string $key Key is a combination of table, uid, field and structure path, identifying the field
	 * @param array  $TCEformsCfg TCA configuration for field
	 * @param array  $contentRow The table row being handled
	 * @return boolean
	 */
	protected function _isRTEField($key, $TCEformsCfg, $contentRow) {
		$isRTE = FALSE;
		if (is_array($contentRow)) {
			list($table, $uid, $field) = explode(':', $key);
			// Check if the RTE is explicitly declared in the defaultExtras configuration
			if (isset($TCEformsCfg['defaultExtras']) && strpos($TCEformsCfg['defaultExtras'], 'richtext') !== FALSE) {
				$isRTE = TRUE;
				// If not, then we must check per type configuration
			} else {
				$typesDefinition = t3lib_BEfunc::getTCAtypes($table, $contentRow, TRUE);
				$isRTE = !empty($typesDefinition[$field]['spec']['richtext']);
			}
		}
		return $isRTE;
	}

	/**
	 * Creating localization index for a single record (which must be default/international language and an online version!)
	 *
	 * @param	string		$table	Table name
	 * @param	integer		$uid	Record UID
	 * @return	mixed		FALSE if the input record is not one that can be translated. Otherwise an array holding information about the status.
	 */
	function indexDetailsRecord($table, $uid) {
		$rec = $table == 'pages' ? t3lib_BEfunc::getRecord($table, $uid) : $this->getSingleRecordToTranslate($table, $uid);

		if (is_array($rec) && $rec['pid'] != -1) {
			$pid = $table == 'pages' ? $rec['uid'] : $rec['pid'];
			if ($this->bypassFilter || $this->filterIndex($table, $uid, $pid)) {
				t3lib_BEfunc::workspaceOL($table, $rec);
				$items = array();
				foreach ($this->sys_languages as $r) {
					$items['fullDetails'][$r['uid']] = $this->translationDetails($table, $rec, $r['uid']);
					$items['indexRecord'][$r['uid']] = $this->compileIndexRecord($table, $items['fullDetails'][$r['uid']], $r['uid'], $pid);
				}
				return $items;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Creating localization index for all records on a page
	 *
	 * @param	integer		$pageId	Page ID
	 * @return	array		Array of the traversed items
	 */
	function indexDetailsPage($pageId) {
		global $TCA;

		$items = array();

		// Traverse tables:
		foreach ($TCA as $table => $cfg) {

			// Only those tables we want to work on:
			if ($table === 'pages') {
				$items[$table][$pageId] = $this->indexDetailsRecord('pages', $pageId);
			} else {
				$allRows = $this->getRecordsToTranslateFromTable($table, $pageId);
				if (is_array($allRows)) {
					if (count($allRows)) {
						// Now, for each record, look for localization:
						foreach ($allRows as $row) {
							if (is_array($row)) {
								$items[$table][$row['uid']] = $this->indexDetailsRecord($table, $row['uid']);
							}
						}
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Update index for record
	 *
	 * @param	string	$table	Table name
	 * @param	int		$uid	UID
	 * @return	string
	 */
	function updateIndexForRecord($table, $uid) {
		$output = '';

		if ($table == 'pages') {
			$items = $this->indexDetailsPage($uid);
		} else {
			$items = array();
			if ($tmp = $this->indexDetailsRecord($table, $uid)) {
				$items[$table][$uid] = $tmp;
			}
		}

		if (count($items)) {
			foreach ($items as $tt => $rr) {
				foreach ($rr as $rUid => $rDetails) {
					$this->updateIndexTableFromDetailsArray($rDetails);
					$output .= 'Updated <em>' . $tt . ':' . $rUid . '</em></br>';
				}
			}
		} else {
			$output .= 'No records to update (you can only update records that can actually be translated)';
		}

		return $output;
	}

	/**
	 * @param	string	$table	Table name
	 * @param	int		$uid	UID
	 * @param	bool	$exec	Execution flag
	 * @return	array
	 */
	function flushTranslations($table, $uid, $exec = FALSE) {

		$flexToolObj = t3lib_div::makeInstance('t3lib_flexformtools');
		$TCEmain_data = array();
		$TCEmain_cmd = array();

		// Simply collecting information about indexing on a page to assess what has to be flushed. Maybe this should move to be an API in
		if ($table == 'pages') {
			$items = $this->indexDetailsPage($uid);
		} else {
			$items = array();
			if ($tmp = $this->indexDetailsRecord($table, $uid)) {
				$items[$table][$uid] = $tmp;
			}
		}

		$remove = array();
		if (count($items)) {
			foreach ($items as $tt => $rr) {
				foreach ($rr as $rUid => $rDetails) {

					if (is_array($rDetails['fullDetails'])) {
						foreach ($rDetails['fullDetails'] as $infoRec) {
							$tInfo = $infoRec['translationInfo'];
							if (is_array($tInfo)) {

								$flexFormTranslation = $tInfo['sys_language_uid'] == -1 && !count($tInfo['translations']);

								// Flexforms:
								if ($flexFormTranslation || $table === 'pages') {
									if (is_array($infoRec['fields'])) {
										foreach ($infoRec['fields'] as $theKey => $theVal) {
											$pp = explode(':', $theKey);
											if ($pp[3] && $pp[0] === $tt && (int)$pp[1] === (int)$rUid) {
												$remove['resetFlexFormFields'][$tt][$rUid][$pp[2]][] = $pp[3];

												if (!is_array($TCEmain_data[$tt][$rUid][$pp[2]])) {
													$TCEmain_data[$tt][$rUid][$pp[2]] = array();
												}
												$flexToolObj->setArrayValueByPath($pp[3], $TCEmain_data[$tt][$rUid][$pp[2]], '');
											}
										}
									}
								}

								// Looking for translations of element in terms of records. Those should be deleted then.
								if (!$flexFormTranslation && is_array($tInfo['translations'])) {
									foreach ($tInfo['translations'] as $translationChildToRemove) {
										$remove['deleteRecords'][$tInfo['translation_table']][$translationChildToRemove['uid']] = $translationChildToRemove;
										$TCEmain_cmd[$tInfo['translation_table']][$translationChildToRemove['uid']]['delete'] = 1;
									}
								}
							}
						}
					}
				}
			}
		}

		if ($exec) {
			// Now, submitting translation data:
			/** @var $tce t3lib_TCEmain */
			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values = FALSE;
			$tce->dontProcessTransformations = TRUE;
			$tce->clear_flexFormData_vDEFbase = TRUE;
			$tce->start($TCEmain_data, $TCEmain_cmd); // check has been done previously that there is a backend user which is Admin and also in live workspace
			$tce->process_datamap();
			$tce->process_cmdmap();
		}

		return array($remove, $TCEmain_cmd, $TCEmain_data, $tce->errorLog);
	}

	/**
	 * Diff-compare markup
	 *
	 * @param	string	$old	Old content
	 * @param	string	$new	New content
	 * @return	string	Marked up string.
	 */
	function diffCMP($old, $new) {
		// Create diff-result:
		$t3lib_diff_Obj = t3lib_div::makeInstance('t3lib_diff');
		return $t3lib_diff_Obj->makeDiffDisplay($old, $new);
	}

	/**
	 * Creates the record to insert in the index table.
	 *
	 * @param	string		$table			Table name
	 * @param	array		$fullDetails	Details as fetched (as gotten by ->translationDetails())
	 * @param	integer		$sys_lang		The language UID for which this record is made
	 * @param	integer		$pid			PID of record
	 * @return	array		Record.
	 */
	function compileIndexRecord($table, $fullDetails, $sys_lang, $pid) {

		$record = array(
			'hash' => '',
			'tablename' => $fullDetails['translationInfo']['table'],
			'recuid' => $fullDetails['translationInfo']['uid'],
			'recpid' => $pid,
			'sys_language_uid' => $fullDetails['translationInfo']['sys_language_uid'], // can be zero (default) or -1 (international)
			'translation_lang' => $sys_lang,
			'translation_recuid' => $fullDetails['translationInfo']['translations'][$sys_lang]['uid'],
			'workspace' => $GLOBALS['BE_USER']->workspace,
			'serializedDiff' => array(

			),
			'flag_new' => 0, // Something awaits to get translated => Put to TODO list as a new element
			'flag_unknown' => 0, // Status of this is unknown, probably because it has been "localized" but not yet translated from the default language => Put to TODO LIST as a priority
			'flag_noChange' => 0, // If only "noChange" is set for the record, all is well!
			'flag_update' => 0, // This indicates something to update
		);

		if (is_array($fullDetails['fields'])) {
			foreach ($fullDetails['fields'] as $key => $tData) {
				if (is_array($tData)) {
					list(, $uidString, $fieldName, $extension) = explode(':', $key);
					list($uidValue) = explode('/', $uidString);

					$noChangeFlag = !strcmp(trim($tData['diffDefaultValue']), trim($tData['defaultValue']));
					if ($uidValue === 'NEW') {
						$record['serializedDiff'][$fieldName . ':' . $extension] .= '';
						$record['flag_new']++;
					} elseif (!isset($tData['diffDefaultValue'])) {
						$record['serializedDiff'][$fieldName . ':' . $extension] .= '<em>No diff available</em>';
						$record['flag_unknown']++;
					} elseif ($noChangeFlag) {
						$record['serializedDiff'][$fieldName . ':' . $extension] .= '';
						$record['flag_noChange']++;
					} else {
						$record['serializedDiff'][$fieldName . ':' . $extension] .= $this->diffCMP($tData['diffDefaultValue'], $tData['defaultValue']);
						$record['flag_update']++;
					}
				}
			}
		}

		$record['serializedDiff'] = serialize($record['serializedDiff']);

		$record['hash'] = md5($record['tablename'] . ':' . $record['recuid'] . ':' . $record['translation_lang'] . ':' . $record['workspace']);

		return $record;
	}

	/**
	 * Updates translation index table with input record
	 *
	 * @param	array	$record	Array (generated with ->compileIndexRecord())
	 * @return	void
	 */
	function updateIndexTable($record) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_l10nmgr_index', 'hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($record['hash'], 'tx_l10nmgr_index'));
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_l10nmgr_index', $record);
	}

	/**
	 * Update translation index table based on a "details" record (made by indexDetailsRecord())
	 *
	 * @param	array		$rDetails	See output of indexDetailsRecord()
	 * @param	boolean		$echo		If true, will output log information for each insert
	 * @return	void
	 */
	function updateIndexTableFromDetailsArray($rDetails, $echo = FALSE) {
		if ($rDetails && is_array($rDetails['indexRecord']) && count($rDetails['indexRecord'])) {
			foreach ($rDetails['indexRecord'] as $rIndexRecord) {
				if ($echo) {
					echo "Inserting " . $rIndexRecord['tablename'] . ':' . $rIndexRecord['recuid'] . ':' . $rIndexRecord['translation_lang'] . ':' . $rIndexRecord['workspace'] . chr(10);
				}
				$this->updateIndexTable($rIndexRecord);
			}
		}
	}

	/**
	 * Flush Index Of Workspace - removes all index records for workspace - useful to nightly build-up of the index.
	 *
	 * @param	int		$ws	Workspace ID
	 * @return	void
	 */
	function flushIndexOfWorkspace($ws) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_l10nmgr_index', 'workspace=' . intval($ws));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/tools/class.tx_l10nmgr_tools.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/tools/class.tx_l10nmgr_tools.php']);
}
?>