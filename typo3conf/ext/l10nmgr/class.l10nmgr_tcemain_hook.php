<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2001-2006 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Updating translation index - hook for tcemain
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_l10nmgr_tcemain_hook
 *   71:     function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj)
 *  123:     function stat($p,$pObj)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



	// Include API
require_once (t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_tools.php');


/**
 * Hook for updating translation index
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_tcemain_hook {

	/**
	 * Hook for updating translation index when records are edited (hooks into TCEmain)
	 *
	 * @param	[type]		$status: ...
	 * @param	[type]		$table: ...
	 * @param	[type]		$id: ...
	 * @param	[type]		$fieldArray: ...
	 * @param	[type]		$pObj: ...
	 * @return	[type]		...
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj)	{
		global $TCA;

			// Check if
#		debug(array($status, $table, $id));


			// Map id for new records:
		if ($status=="new")	{
			$id = $pObj->substNEWwithIDs[$id];
#			echo "New fixed<br>";
		}

			// Find live record if any:
		if (!($liveRecord = t3lib_BEfunc::getLiveVersionOfRecord($table,$id)))	{	// If it was a version we find live...
			$liveRecord = t3lib_BEfunc::getRecord($table, $id);		// Otherwise we load live record.
#			echo "Live version<br>";
		}

		if (is_array($liveRecord))	{

				// Now, see if this record is a translation of another one:
			$t8ToolsObj = t3lib_div::makeInstance('t3lib_transl8tools');
			if ($t8ToolsObj->isTranslationInOwnTable($table) && $liveRecord[$TCA[$table]['ctrl']['transOrigPointerField']])	{
					// So it had a translation pointer - lets look for the root record then:
				$liveRecord = t3lib_BEfunc::getRecord($table, $liveRecord[$TCA[$table]['ctrl']['transOrigPointerField']],'uid');
#				echo "Finding root version<br>";
			} elseif ($TCA[$table]['ctrl']['transOrigPointerTable'] && $liveRecord[$TCA[$table]['ctrl']['transOrigPointerField']])	{
				$fld = $TCA[$table]['ctrl']['transOrigPointerField'];
				$table = $TCA[$table]['ctrl']['transOrigPointerTable'];	// Changeing table value here on purpose!
				$liveRecord = t3lib_BEfunc::getRecord($table, $liveRecord[$fld],'uid');
			}

			if (is_array($liveRecord))	{
#				echo "indexing id ".$liveRecord['uid'];
					// Finally, we have found the "root record" and will check it:
				$t8Tools = t3lib_div::makeInstance('tx_l10nmgr_tools');
				$t8Tools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.

#				debug($t8Tools->indexDetailsRecord($table,$liveRecord['uid']));
				$t8Tools->updateIndexTableFromDetailsArray($t8Tools->indexDetailsRecord($table,$liveRecord['uid']));
			}
		}
	}

	/**
	 * Hook for displaying small icon in page tree, web>List and page module.
	 *
	 * @param	[type]		$p: ...
	 * @param	[type]		$pObj: ...
	 * @return	[type]		...
	 */
	function stat($p,$pObj)	{

		if (strcmp($GLOBALS['BE_USER']->groupData['allowed_languages'],''))	{

			return $this->calcStat($p,$GLOBALS['TYPO3_DB']->cleanIntList($GLOBALS['BE_USER']->groupData['allowed_languages']));
		}
	}
	
	function calcStat($p,$languageList,$noLink=FALSE)	{
			//
		if ($p[0]!='pages')	{
			$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				'tx_l10nmgr_index',
				'tablename='.$GLOBALS['TYPO3_DB']->fullQuoteStr($p[0],'tx_l10nmgr_index').
					' AND recuid='.intval($p[1]).
					' AND translation_lang IN ('.$languageList.')'.
					' AND workspace='.intval($GLOBALS['BE_USER']->workspace)
			);
		} else {
			$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				'tx_l10nmgr_index',
				'recpid='.intval($p[1]).
					' AND translation_lang IN ('.$languageList.')'.
					' AND workspace='.intval($GLOBALS['BE_USER']->workspace)
			);
		}

		$flags = array();
		foreach($records as $r)	{
			$flags['new']+= $r['flag_new'];
			$flags['unknown']+= $r['flag_unknown'];
			$flags['update']+= $r['flag_update'];
			$flags['noChange']+= $r['flag_noChange'];
		}

		if (count($records))	{
				// Setting icon:
			$msg = '';
			if ($flags['new'] && !$flags['unknown'] && !$flags['noChange'] && !$flags['update'])	{
				$msg.= 'None of '.$flags['new'].' elements are translated.';
				$output = '<img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'flags_new.png" hspace="2" width="10" height="16" alt="'.htmlspecialchars($msg).'" title="'.htmlspecialchars($msg).'" />';
			} elseif ($flags['new'] || $flags['update']) {
				if ($flags['update'])	$msg.= $flags['update'].' elements to update. ';
				if ($flags['new'])	$msg.= $flags['new'].' new elements found. ';
				$output = '<img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'flags_update.png" hspace="2" width="10" height="16" alt="'.htmlspecialchars($msg).'" title="'.htmlspecialchars($msg).'" />';
			} elseif ($flags['unknown'])	{
				$msg.= 'Translation status is unknown for '.$flags['unknown'].' elements. Please check and update. ';
				$output = '<img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'flags_unknown.png" hspace="2" width="10" height="16" alt="'.htmlspecialchars($msg).'" title="'.htmlspecialchars($msg).'" />';
			} elseif ($flags['noChange'])	{
				$msg.= 'All '.$flags['noChange'].' translations OK';
				$output = '<img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'flags_ok.png" hspace="2" width="10" height="16" alt="'.htmlspecialchars($msg).'" title="'.htmlspecialchars($msg).'" />';
			} else {
				$msg.= 'Nothing to do. ';
				$msg.='[n/?/u/ok='.implode('/',$flags).']';
				$output = '<img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'flags_none.png" hspace="2" width="10" height="16" alt="'.htmlspecialchars($msg).'" title="'.htmlspecialchars($msg).'" />';
			}

			$output = !$noLink ? '<a href="#" onclick="'.htmlspecialchars('parent.list_frame.location.href="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'cm2/index.php?table='.$p[0].'&uid='.$p[1].'&languageList='.rawurlencode($languageList).'"; return false;').'" target="listframe">'.$output.'</a>' : $output;
			return $output;
		}		
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.l10nmgr_tcemain_hook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.l10nmgr_tcemain_hook.php']);
}
?>
