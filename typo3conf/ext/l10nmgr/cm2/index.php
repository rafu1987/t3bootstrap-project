<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Kasper Skårhøj <kasperYYYY@typo3.com>
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
 * l10nmgr module cm2
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_l10nmgr_cm2 extends t3lib_SCbase
 *   72:     function menuConfig()
 *   83:     function main()
 *   95:     function jumpToUrl(URL)
 *  119:     function printContent()
 *  132:     function moduleContent($table,$uid)
 *  199:     function makeTableRow($rec)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:l10nmgr/cm2/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_tools.php'); 


/**
 * Translation management tool
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_cm2 extends t3lib_SCbase {

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;

		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('noDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->form='<form action="" method="post" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';

			// Header:
		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content.=$this->doc->header($LANG->getLL('title'));

		$this->content.=$this->doc->divider(5);


			// Render the module content (for all modes):
		$this->content.=$this->doc->section('', $this->moduleContent((string)t3lib_div::_GP('table'),(int)t3lib_div::_GP('uid')));

		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Printing output content
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @return	[type]		...
	 */
	function moduleContent($table,$uid)	{
		if ($GLOBALS['TCA'][$table])	{

			$this->l10nMgrTools = t3lib_div::makeInstance('tx_l10nmgr_tools');
			$this->l10nMgrTools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.
			
			$output = '';
			if (t3lib_div::_POST('_updateIndex'))	{
				$output.=$this->l10nMgrTools->updateIndexForRecord($table,$uid);
				t3lib_BEfunc::setUpdateSignal('updatePageTree');
			}


			$inputRecord = t3lib_BEfunc::getRecord($table,$uid,'pid');
			
			$pathShown = t3lib_BEfunc::getRecordPath($table=='pages'?$uid:$inputRecord['pid'], '', 20);

			$this->sysLanguages = $this->l10nMgrTools->t8Tools->getSystemLanguages($table=='pages' ? $uid : $inputRecord['pid']);
			$languageListArray = explode(',',$GLOBALS['BE_USER']->groupData['allowed_languages'] ? $GLOBALS['BE_USER']->groupData['allowed_languages'] : implode(',',array_keys($this->sysLanguages)));
			$limitLanguageList = trim(t3lib_div::_GP('languageList'));

			foreach($languageListArray as $kkk=>$val)	{
				if ($limitLanguageList && !t3lib_div::inList($limitLanguageList,$val))	unset($languageListArray[$kkk]);
			}
			if (!count($languageListArray))	$languageListArray[]=0; 
			$languageList = implode(',',$languageListArray);

				// Fetch translation index records:
			if ($table!='pages')	{
				$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					'tx_l10nmgr_index',
					'tablename='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_l10nmgr_index').
						' AND recuid='.intval($uid).
						' AND translation_lang IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($languageList).')'.
						' AND workspace='.intval($GLOBALS['BE_USER']->workspace).
						' AND (flag_new>0 OR flag_update>0 OR flag_noChange>0 OR flag_unknown>0)',
					'',
					'translation_lang, tablename, recuid'
				);
			} else {
				$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					'tx_l10nmgr_index',
					'recpid='.intval($uid).
						' AND translation_lang IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($languageList).')'.
						' AND workspace='.intval($GLOBALS['BE_USER']->workspace).
						' AND (flag_new>0 OR flag_update>0 OR flag_noChange>0 OR flag_unknown>0)',
					'',
					'translation_lang, tablename, recuid'
				);
			}

		#	t3lib_div::debugRows($records,'Index entries for '.$table.':'.$uid);

			$tRows = array();
			$tRows[] = '<tr class="bgColor2 tableheader">
				<td colspan="2">Base element:</td>
				<td colspan="2">Translation:</td>
				<td>Action:</td>
				<td><img src="../flags_new.png" width="10" height="16" alt="New" title="New" /></td>
				<td><img src="../flags_unknown.png" width="10" height="16" alt="Unknown" title="Unknown" /></td>
				<td><img src="../flags_update.png" width="10" height="16" alt="Update" title="Update" /></td>
				<td><img src="../flags_ok.png" width="10" height="16" alt="OK" title="OK" /></td>
				<td>Diff:</td>
			</tr>';
		//t3lib_div::debugRows($records);

			foreach($records as $rec)	{
				if ($rec['tablename']=='pages') $tRows[] = $this->makeTableRow($rec);
			}
			if (count($tRows)>1)	{
				$tRows[] = 	'<tr><td colspan="8">&nbsp;</td></tr>';
			}
			foreach($records as $rec)	{
				if ($rec['tablename']!='pages') $tRows[] = $this->makeTableRow($rec);
			}

			$output.= 'Path: <i>'.$pathShown.'</i><br><table border="0" cellpadding="1" cellspacing="1">'.implode('',$tRows).'</table>';
			
				// Updating index
			if ($GLOBALS['BE_USER']->isAdmin())	{
				$output.='<br><br>Functions for "'.$table.':'.$uid.'":<br/>
					<input type="submit" name="_updateIndex" value="Update Index" /><br>
					<input type="submit" name="_" value="Flush Translations" onclick="'.htmlspecialchars('document.location="../cm3/index.php?table='.htmlspecialchars($table).'&id='.intval($uid).'&cmd=flushTranslations";return false;').'"/><br>
					<input type="submit" name="_" value="Create priority" onclick="'.htmlspecialchars('document.location="'.$GLOBALS['BACK_PATH'].'alt_doc.php?returnUrl='.rawurlencode('db_list.php?id=0&table=tx_l10nmgr_priorities').'&edit[tx_l10nmgr_priorities][0]=new&defVals[tx_l10nmgr_priorities][element]='.rawurlencode($table.'_'.$uid).'";return false;').'"/><br>
					';
			}
			return $output;
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$rec: ...
	 * @return	[type]		...
	 */
	function makeTableRow($rec)	{

					//Render information for base record:
		$baseRecord = t3lib_BEfunc::getRecordWSOL($rec['tablename'],$rec['recuid']);
		$icon = t3lib_iconWorks::getIconImage($rec['tablename'],$baseRecord,$GLOBALS['BACK_PATH'],'align="top" title="'.htmlspecialchars($rec['tablename'].':'.$rec['recuid']).'"');
		$title = t3lib_BEfunc::getRecordTitle($rec['tablename'],$baseRecord,1);
		$baseRecordFlag = '<img src="'.htmlspecialchars($GLOBALS['BACK_PATH'].$this->sysLanguages[$rec['sys_language_uid']]['flagIcon']).'" alt="" title="" />';
		$tFlag = '<img src="'.htmlspecialchars($GLOBALS['BACK_PATH'].$this->sysLanguages[$rec['translation_lang']]['flagIcon']).'" alt="'.htmlspecialchars($this->sysLanguages[$rec['translation_lang']]['title']).'" title="'.htmlspecialchars($this->sysLanguages[$rec['translation_lang']]['title']).'" />';
		$baseRecordStr = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick('&edit['.$rec['tablename'].']['.$rec['recuid'].']=edit',$this->doc->backPath)).'">'.$icon.$title.'</a>';

			// Render for translation if any:
		$translationRecord = FALSE;
		if ($rec['translation_recuid'])	{
			$translationTable = $this->l10nMgrTools->t8Tools->getTranslationTable($rec['tablename']);
			$translationRecord = t3lib_BEfunc::getRecordWSOL($translationTable,$rec['translation_recuid']);
			$icon = t3lib_iconWorks::getIconImage($translationTable,$translationRecord,$GLOBALS['BACK_PATH'],'align="top" title="'.htmlspecialchars($translationTable.':'.$rec['translation_recuid']).'"');
			$title = t3lib_BEfunc::getRecordTitle($translationTable,$translationRecord,1);
			$translationRecStr = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick('&edit['.$translationTable.']['.$translationRecord['uid'].']=edit',$this->doc->backPath)).'">'.$icon.$title.'</a>';
		} else 	$translationRecStr = '';

			// Action:
		if (is_array($translationRecord))	{
			$action = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick('&edit['.$translationTable.']['.$translationRecord['uid'].']=edit',$this->doc->backPath)).'"><em>[Edit]</em></a>';
		} elseif ($rec['sys_language_uid']==-1) {
			$action = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick('&edit['.$rec['tablename'].']['.$rec['recuid'].']=edit',$this->doc->backPath)).'"><em>[Edit]</em></a>';
		} else {
			$action = '<a href="'.htmlspecialchars($this->doc->issueCommand(
							'&cmd['.$rec['tablename'].']['.$rec['recuid'].'][localize]='.$rec['translation_lang']
							)).'"><em>[Localize]</em></a>';
		}


		return '<tr class="bgColor4-20">
			<td valign="top">'.$baseRecordFlag.'</td>
			<td valign="top" nowrap="nowrap">'.$baseRecordStr.'</td>
			<td valign="top">'.$tFlag.'</td>
			<td valign="top" nowrap="nowrap">'.$translationRecStr.'</td>
			<td valign="top">'.$action.'</td>
			<td align="center"'.($rec['flag_new']?' bgcolor="#91B5FF"':'').'>'.($rec['flag_new']?$rec['flag_new']:'').'</td>
			<td align="center"'.($rec['flag_unknown']?' bgcolor="#FEFF5A"':'').'>'.($rec['flag_unknown']?$rec['flag_unknown']:'').'</td>
			<td align="center"'.($rec['flag_update']?' bgcolor="#FF7161"':'').'>'.($rec['flag_update']?$rec['flag_update']:'').'</td>
			<td align="center"'.($rec['flag_noChange']?' bgcolor="#78FF82"':'').'>'.($rec['flag_noChange']?$rec['flag_noChange']:'').'</td>
			<td>'.implode('<br/>',unserialize($rec['serializedDiff'])).'</td>
		</tr>';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm2/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm2/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_l10nmgr_cm2');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>
