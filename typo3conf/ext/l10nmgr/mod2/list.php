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
 * Module 'Workspace Tasks' for the 'l10nmgr' extension.
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */






	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:l10nmgr/mod2/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
	
	
require_once(t3lib_extMgm::extPath('l10nmgr').'class.l10nmgr_tcemain_hook.php');
	

class tx_l10nmgr_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

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
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;


			// Draw the header.
		$this->doc = t3lib_div::makeInstance("noDoc");
		$this->doc->backPath = $BACK_PATH;
		$this->doc->form='<form action="" method="post">';

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';

			// Setting up the context sensitive menu:
		$CMparts = $this->doc->getContextMenuCode();
		$this->doc->JScode.= $CMparts[0];
		$this->doc->bodyTagAdditions = $CMparts[1];
		$this->doc->postCode.= $CMparts[2];


		$this->content.=$this->doc->startPage($LANG->getLL("title"));
		$this->content.=$this->doc->header($LANG->getLL("title"));
		$this->content.=$this->doc->spacer(5);

		// Render content:
		$this->moduleContent();


		// ShortCut
		if ($BE_USER->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
		}

		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{

			// Selecting priorities:
		$priorities = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_l10nmgr_priorities','1=1'.t3lib_BEfunc::deleteClause('tx_l10nmgr_priorities'),'','sorting');	
		$tRows = array();
		$c=0;
		foreach($priorities as $priorityRecord)	{

			if ($lTable = $this->languageRows($priorityRecord['languages'],$priorityRecord['element']))	{
				$c++;
				$tRows[] = '
				<tr>
					<td class="bgColor5"><strong>#'.($c).': '.htmlspecialchars($priorityRecord['title']).'</strong><br>'.htmlspecialchars($priorityRecord['description']).'</td>
				</tr>
				<tr>
					<td>'.$lTable.'</td>
				</tr>';
			}
		}
			
		$content.='<table border="0" cellpadding="4" cellspacing="2">'.implode('',$tRows).'</table>';
			
		$this->content.=$this->doc->section("Priority list:",$content,0,1);
	}
	
	function languageRows($languageList,$elementList)	{
		
			// Initialization:
		$elements = $this->explodeElement($elementList);
		$firstEl = current($elements);
		$hookObj = t3lib_div::makeInstance('tx_l10nmgr_tcemain_hook');
	
		$this->l10nMgrTools = t3lib_div::makeInstance('tx_l10nmgr_tools');
		$this->l10nMgrTools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.
		$inputRecord = t3lib_BEfunc::getRecord($firstEl[0],$firstEl[1],'pid');
		$this->sysLanguages = $this->l10nMgrTools->t8Tools->getSystemLanguages($firstEl[0]=='pages' ? $firstEl[1] : $inputRecord['pid']);
		$languages = $this->getLanguages($languageList,$this->sysLanguages);

		if (count($languages))	{
			$tRows = array();

				// Header:
			$cells = '<td class="bgColor2 tableheader">Element:</td>';
			foreach($languages as $l)	{
				if ($l>=1) {
					$baseRecordFlag = '<img src="'.htmlspecialchars($GLOBALS['BACK_PATH'].$this->sysLanguages[$l]['flagIcon']).'" alt="'.htmlspecialchars($this->sysLanguages[$l]['title']).'" title="'.htmlspecialchars($this->sysLanguages[$l]['title']).'" />';
					$cells.= '<td class="bgColor2 tableheader">'.$baseRecordFlag.'</td>';
				}
			}
			$tRows[] = $cells;

			foreach($elements as $el)	{
				$cells = '';
			
				// Get CURRENT online record and icon based on "t3ver_oid":
				$rec_on = t3lib_BEfunc::getRecord($el[0],$el[1]);
				$icon = t3lib_iconWorks::getIconImage($el[0], $rec_on, $this->doc->backPath,' align="top" title="'.t3lib_BEfunc::getRecordIconAltText($rec_on,$el[0]).'"');
				$icon = $this->doc->wrapClickMenuOnIcon($icon, $el[0], $rec_on['uid'],2);

				$linkToIt = '<a href="#" onclick="'.htmlspecialchars('parent.list_frame.location.href="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('l10nmgr').'cm2/index.php?table='.$el[0].'&uid='.$el[1].'"; return false;').'" target="listframe">
					'.t3lib_BEfunc::getRecordTitle($el[0],$rec_on,TRUE).'
						</a>';

				if ($el[0]=='pages')	{	
						// If another page module was specified, replace the default Page module with the new one
					$newPageModule = trim($GLOBALS['BE_USER']->getTSConfigVal('options.overridePageModule'));
					$pageModule = t3lib_BEfunc::isModuleSetInTBE_MODULES($newPageModule) ? $newPageModule : 'web_layout';
				
					$path_module_path = t3lib_div::resolveBackPath($GLOBALS['BACK_PATH'].'../'.substr($GLOBALS['TBE_MODULES']['_PATHS'][$pageModule],strlen(PATH_site)));
					$onclick = 'parent.list_frame.location.href="'.$path_module_path.'?id='.$el[1].'"; return false;';
					$pmLink = '<a href="#" onclick="'.htmlspecialchars($onclick).'" target="listframe"><i>[Edit page]</i></a>';
				} else {$pmLink = '';}
					
				$cells = '<td>'.$icon.$linkToIt.$pmLink.'</td>';

				foreach($languages as $l)	{
					if ($l>=1) $cells.= '<td align="center">'.$hookObj->calcStat(array($el[0],$el[1]), $l).'</td>';
				}
			
				$tRows[] = $cells;
			}
		
			return '<table border="0" cellpadding="0" cellspacing="0"><tr>'.implode('</tr><tr>',$tRows).'</tr></table>';
		}
	}
	
	function explodeElement($elementList)	{
		$elements = t3lib_div::trimExplode(',',$elementList);
		foreach($elements as $k => $element)	{
			$elements[$k] = t3lib_div::revExplode('_',$element,2);
		}
		return $elements;
	}
	
	function getLanguages($limitLanguageList,$sysLanguages)	{
		$languageListArray =  explode(',',$GLOBALS['BE_USER']->groupData['allowed_languages'] ? $GLOBALS['BE_USER']->groupData['allowed_languages'] : implode(',',array_keys($sysLanguages)));

		foreach($languageListArray as $kkk=>$val)	{
			if ($limitLanguageList && !t3lib_div::inList($limitLanguageList,$val))	unset($languageListArray[$kkk]);
		}

		return $languageListArray;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/mod2/list.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/mod2/list.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_l10nmgr_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
