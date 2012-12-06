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
 * l10nmgr module cm3
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_l10nmgr_cm3 extends t3lib_SCbase
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
$LANG->includeLLFile('EXT:l10nmgr/cm3/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_tools.php');


/**
 * Translation management tool
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_cm3 extends t3lib_SCbase {

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
		$this->content.=$this->doc->section('', $this->moduleContent((string)t3lib_div::_GP('table'),(int)t3lib_div::_GP('id'),t3lib_div::_GP('cmd')));

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
	function moduleContent($table,$uid,$cmd)	{
		if ($GLOBALS['TCA'][$table])	{
			
			$output = '';

			$this->l10nMgrTools = t3lib_div::makeInstance('tx_l10nmgr_tools');
			$this->l10nMgrTools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.

			switch((string)$cmd)	{
				case 'updateIndex':
					$output = $this->l10nMgrTools->updateIndexForRecord($table,$uid);
					t3lib_BEfunc::setUpdateSignal('updatePageTree');
				break;
				case 'flushTranslations':
					if ($GLOBALS['BE_USER']->isAdmin())	{
						$res = $this->l10nMgrTools->flushTranslations($table,$uid,t3lib_div::_POST('_flush')?TRUE:FALSE);

						if (!t3lib_div::_POST('_flush'))	{
							$output.='To flush the translations shown below, press the "Flush" button below:<br/><input type="submit" name="_flush" value="FLUSH" /><br/><br/>';
						} else {
							$output.='Translations below were flushed!';
						}
						$output.=t3lib_utility_Debug::viewArray($res[0]);
					
						if (t3lib_div::_POST('_flush'))	{
							$output.=$this->l10nMgrTools->updateIndexForRecord($table,$uid);
							t3lib_BEfunc::setUpdateSignal('updatePageTree');
						}
					}
				break;
				case 'createPriority':
					header('Location: '.t3lib_div::locationHeaderUrl($GLOBALS['BACK_PATH'].'alt_doc.php?returnUrl='.rawurlencode('db_list.php?id=0&table=tx_l10nmgr_priorities').'&edit[tx_l10nmgr_priorities][0]=new&defVals[tx_l10nmgr_priorities][element]='.rawurlencode($table.'_'.$uid)));
				break;
				case 'managePriorities':
					header('Location: '.t3lib_div::locationHeaderUrl($GLOBALS['BACK_PATH'].'db_list.php?id=0&table=tx_l10nmgr_priorities'));
				break;
			}

			return $output;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm3/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm3/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_l10nmgr_cm3');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>
