<?php
/***************************************************************
*  Copyright notice
*
*  (c)  2001-2008 Kasper Skårhøj (kasperYYYY@typo3.com)  All rights reserved
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

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');


/**
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Daniel Brün <dbruen@saltation.de>
 * @author	Ingmar Schlecht <ingmars@web.de>
 * @author	Ingo Renner <ingo@typo3.org>
 */
class tx_kickstarter_section_cm extends tx_kickstarter_sectionbase {
	var $sectionID = 'cm';

	/**
	 * Renders the form in the kickstarter; this was add_cat_cm()
	 *
	 * @return	HTML code
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0]=='edit')	{
			$this->regNewEntry($this->sectionID,$action[1]);
			$lines = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],'&nbsp;',$action[1]);
			$piConf = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix='['.$this->sectionID.']['.$action[1].']';

				// Enter title of the module function
			$subContent='<strong>Title of the ClickMenu element:</strong><br />'.
				$this->renderStringBox_lang('title',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Position
			$optValues = array(
				'bottom' => 'Insert in bottom',
				'top' => 'Insert in top',
				'before_delete' => 'Insert before the \'Delete\' item',
			);
			$subContent='<strong>Options</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[options]',$piConf['options'],$optValues);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Admin only
			$subContent =$this->resImg('cm.png');
			$subContent.= $this->renderCheckBox($ffPrefix.'[second_level]',$piConf['second_level']).'Activate a second-level menu.<br />';
			$subContent.= $this->renderCheckBox($ffPrefix.'[only_page]',$piConf['only_page']).'Add only if the click menu is on a \'Page\' (example)<br />';
			$subContent.= $this->renderCheckBox($ffPrefix.'[only_if_edit]',$piConf['only_if_edit']).'Only active if item is editable.<br />';
			$subContent.= $this->renderCheckBox($ffPrefix.'[remove_view]',$piConf['remove_view']).'Remove \'Show\' element (example)<br />';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}


		$this->process_hook('render_wizard', $lines);

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Renders the extension PHP code; this was
	 *
	 * @param	string		$k: section key (???)
	 * @param	array		$config: module configuration
	 * @param	string		$extKey: extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP = '[' . $this->sectionID . '][' . $k .']';
		$cN = $this->returnName($extKey,'class', $this->sectionID . $k);
		$filename = 'class.'.$cN.'.php';
		$pathSuffix = $this->sectionID . $k .'/';

			// This will make sure our item is inserted in the clickmenu!
		$this->wizard->ext_tables[]=$this->sPS('
			'.$this->WOPcomment('WOP:'.$WOP.':').'
			if (TYPO3_MODE === \'BE\')	{
				$GLOBALS[\'TBE_MODULES_EXT\'][\'xMOD_alt_clickmenu\'][\'extendCMclasses\'][] = array(
					\'name\' => \''.$cN.'\',
					\'path\' => t3lib_extMgm::extPath($_EXTKEY).\''.$filename.'\'
				);
			}
		');
			// Add title to the locallang file.
		$this->addLocalConf($this->wizard->ext_locallang,$config,'title','cm',$k);

			// Add icon
		$this->addFileToFileArray($pathSuffix.'cm_icon.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/notfound_module.gif'));

			// 	Building class:
		$content = '';
		$content.=$this->sPS('
				// Adds the regular item:
			$LL = $this->includeLL();

				// Repeat this (below) for as many items you want to add!
				// Remember to add entries in the localconf.php file for additional titles.
			$url = t3lib_extMgm::extRelPath(\''.$extKey.'\').\''.$pathSuffix.'index.php?id=\'.$uid;
			$localItems[] = $backRef->linkItem(
				$GLOBALS["LANG"]->getLLL("cm'.$k.'_title",$LL),
				$backRef->excludeIcon(\'<img src="\'.t3lib_extMgm::extRelPath("'.$extKey.'").\''.$pathSuffix.'cm_icon.gif" width="15" height="12" border="0" align="top" />\'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);
		');
		if ($config['second_level'])	{
			$secondContent = $content;
			$secondContent.=chr(10).'$menuItems=array_merge($menuItems,$localItems);';

			$content = '';
			$content.=$this->sPS('
				$LL = $this->includeLL();

				$localItems[]="spacer";
				$localItems["moreoptions_'.$cN.'"]=$backRef->linkItem(
					$GLOBALS["LANG"]->getLLL("cm'.$k.'_title_activate",$LL),
					$backRef->excludeIcon(\'<img src="\'.t3lib_extMgm::extRelPath("'.$extKey.'").\''.$pathSuffix.'cm_icon_activate.gif" width="15" height="12" border="0" align="top" />\'),
					"top.loadTopMenu(\'".t3lib_div::linkThisScript()."&cmLevel=1&subname=moreoptions_'.$cN.'\');return false;",
					0,
					1
				);
			');

				// Add activate title to the locallang file.
			$this->addLocalConf($this->wizard->ext_locallang,array('title_activate'=>'...Second level ->'),'title_activate','cm',$k,0,1);
				// Add activate icon
			$this->addFileToFileArray($pathSuffix.'cm_icon_activate.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/notfound_module.gif'));
		}

		if ($config['only_page'])	$content=$this->sPS('
				// Returns directly, because the clicked item was not from the pages table '.$this->WOPcomment('(WOP:'.$WOP.'[only_page])').'
			if ($table!="pages")	return $menuItems;
		').$content;

		$content.=$this->sPS('
			'.$this->WOPcomment('(WOP:'.$WOP.'[options] BEGIN) Inserts the item at the chosen location').'
		');
		if ($config['options']=='top')	{	// In top:
			$content.=$this->sPS('
				$menuItems=array_merge($localItems,$menuItems);
			');
		} elseif ($config['options']=='before_delete')	{	// Just before "Delete" and its preceding divider line:
			$content.=$this->sPS('
					// Find position of "delete" element:
				reset($menuItems);
				$c=0;
				while(list($k)=each($menuItems))	{
					$c++;
					if (!strcmp($k,"delete"))	break;
				}
					// .. subtract two (delete item + divider line)
				$c-=2;
					// ... and insert the items just before the delete element.
				array_splice(
					$menuItems,
					$c,
					0,
					$localItems
				);
			');
		} else	{	// In bottom (default):
			$content.=$this->sPS('
				// Simply merges the two arrays together and returns ...
				$menuItems=array_merge($menuItems,$localItems);
			');
		}
		$content.=$this->sPS('
			'.$this->WOPcomment('(WOP:'.$WOP.'[options] END)').'
		');

		if ($config['only_if_edit'])	$content=$this->wrapBody('
			if ($backRef->editOK)	{
			',$content,'
			}
		');


		if ($config['remove_view'])	$content.=$this->sPS('
				// Removes the view-item from clickmenu  '.$this->WOPcomment('(WOP:'.$WOP.'[remove_view])').'
			unset($menuItems["view"]);
		');

		$content=$this->wrapBody('
			if (!$backRef->cmLevel)	{
			',$content,'
			}
		');

		if ($config['second_level'])	{
			$content.=$this->wrapBody('
				else {
				',$secondContent,'
				}
			');
		}

			// Now wrap the function body around this:
		$content=$this->wrapBody('
			function main(&$backRef,$menuItems,$table,$uid)	{
				global $BE_USER,$TCA,$LANG;

				$localItems = Array();
				',$content,'
				return $menuItems;
			}
		');
			// Add include locallanguage function:
		$content.=$this->addLLFunc($extKey);

			// Now wrap the function body around this:
		$content=$this->wrapBody(
			'class '.$cN.' {
				',$content,'
			}
		');

		$this->addFileToFileArray(
			$filename,
			$this->PHPclassFile(
				$extKey,
				$filename,
				$content,
				'Addition of an item to the clickmenu'
			)
		);

		$cN = $this->returnName($extKey,'class','cm'.$k);
		$this->writeStandardBE_xMod($extKey,$config,$pathSuffix,$cN,$k,'cm');

	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_cm.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_cm.php']);
}


?>