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
 * Addition of an item to the clickmenu
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   55: class tx_l10nmgr_cm1
 *   66:     function main(&$backRef,$menuItems,$table,$uid)
 *  101:     function includeLL()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */






/**
 * Context menu processing
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_cm1 {

	/**
	 * Main function
	 *
	 * @param	[type]		$$backRef: ...
	 * @param	[type]		$menuItems: ...
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @return	[type]		...
	 */
	function main(&$backRef,$menuItems,$table,$uid)	{
		global $BE_USER,$TCA,$LANG;

		$localItems = Array();
		if (!$backRef->cmLevel)	{

				// Returns directly, because the clicked item was not from the pages table
			if ($table=="tx_l10nmgr_cfg")   {	

					// Adds the regular item:
				$LL = $this->includeLL();

					// Repeat this (below) for as many items you want to add!
					// Remember to add entries in the localconf.php file for additional titles.
				$url = t3lib_extMgm::extRelPath("l10nmgr")."cm1/index.php?id=".$uid;
				$localItems[] = $backRef->linkItem(
					$GLOBALS["LANG"]->getLLL("cm1_title",$LL),
					$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("l10nmgr").'cm1/cm_icon.gif" width="15" height="12" border="0" align="top" />'),
					$backRef->urlRefForCM($url),
					1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
				);
			}

                        $localItems["moreoptions_tx_l10nmgr_cm3"] = $backRef->linkItem(
                                'L10Nmgr tools',
                                '',
                                "top.loadTopMenu('".t3lib_div::linkThisScript()."&cmLevel=1&subname=moreoptions_tx_l10nmgrXX_cm3');return false;",
                                0,
                                1
                        );

				// Simply merges the two arrays together and returns ...
			$menuItems=array_merge($menuItems,$localItems);
		} elseif (t3lib_div::_GET('subname')=='moreoptions_tx_l10nmgrXX_cm3') {

			$url = t3lib_extMgm::extRelPath("l10nmgr")."cm3/index.php?id=".$uid.'&table='.$table;
                        $localItems[] = $backRef->linkItem('Create priority','',$backRef->urlRefForCM($url.'&cmd=createPriority'),1);
                        $localItems[] = $backRef->linkItem('Manage priorities','',$backRef->urlRefForCM($url.'&cmd=managePriorities'),1);
                        $localItems[] = $backRef->linkItem('Update Index','',$backRef->urlRefForCM($url.'&cmd=updateIndex'),1);
                        $localItems[] = $backRef->linkItem('Flush Translations','',$backRef->urlRefForCM($url.'&cmd=flushTranslations'),1);

			$menuItems=array_merge($menuItems,$localItems);
		}


		return $menuItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return	array		Local lang value.
	 */
	function includeLL()	{
		global $LANG;

		$LOCAL_LANG = $LANG->includeLLFile('EXT:l10nmgr/locallang.xml',FALSE);
		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.tx_l10nmgr_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.tx_l10nmgr_cm1.php']);
}

?>
