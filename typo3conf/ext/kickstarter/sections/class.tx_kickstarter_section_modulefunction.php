<?php
/***************************************************************
*  Copyright notice
*
*  (c)  2001-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)  All rights reserved
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
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Ingo Renner <ingo@typo3.org>
 */

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');

class tx_kickstarter_section_modulefunction extends tx_kickstarter_sectionbase {
  var $sectionID = 'modulefunction';
	/**
	 * Renders the form in the kickstarter; this was add_cat_modulefunction()
	 *
	 * @return	HTML
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
			$subContent='<strong>Enter the title of function-menu item:</strong><br />'.
				$this->renderStringBox_lang('title',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Position
			$optValues = array(
				'web_func' => 'Web>Func',
				'web_func_wizards' => 'Web>Func, Wizards',
				'web_info' => 'Web>Info',
				'web_ts' => 'Web>Template',
				'user_task' => 'User>Task Center',
			);
			$subContent='<strong>Sub- or main module?</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[position]',$piConf['position'],$optValues).
				'<br /><br />These images gives you an idea what the options above means:'.
				$this->resImg('modulefunc_task.png').
				$this->resImg('modulefunc_func.png');
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_moduleFunction'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_moduleFunction'] as $_funcRef) {
				$lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
			}
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Renders the extension PHP code; this was
	 *
	 * @param	string		$k: module name key
	 * @param	array		$config: module configuration
	 * @param	string		$extKey: extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP ='[moduleFunction]['.$k.']';
		$cN  = $this->returnName($extKey,'class','modfunc'.$k);
		$pathSuffix = 'modfunc'.$k.'/';

		$position =$config['position'];
		$subPos='';
		switch($config['position'])	{
			case 'user_task';
				$this->wizard->EM_CONF_presets['dependencies'][] = 'taskcenter';
			break;
			case 'web_ts';
				$this->wizard->EM_CONF_presets['dependencies'][] = 'tstemplate';
			break;
			case 'web_func_wizards';
				$this->wizard->EM_CONF_presets['dependencies'][] = 'func_wizards';
				$position='web_func';
				$subPos='wiz';
			break;
		}

		$this->wizard->ext_tables[]=$this->sPS('
			if (TYPO3_MODE === \'BE\')	{
				t3lib_extMgm::insertModuleFunction(
					\''.$position.'\',		'.$this->WOPcomment('WOP:'.$WOP.'[position]').'
					\''.$cN.'\',
					t3lib_extMgm::extPath($_EXTKEY).\''.$pathSuffix.'class.'.$cN.'.php\',
					\''.addslashes($this->getSplitLabels_reference($config,'title','moduleFunction.'.$cN)).'\''.($subPos?',
					\''.$subPos.'\'	'.$this->WOPcomment('WOP:'.$WOP.'[position]'):'').'
				);
			}
		');


			// Add title to local lang file
		$ll=array();
		$this->addLocalConf($ll,$config,'title','module',$k,1);
		$this->addLocalConf($ll,array('checklabel'=>'Check box #1'),'checklabel','modfunc',$k,1,1);
		$this->addLocalLangFile($ll,$pathSuffix.'locallang.xml','Language labels for module "'.$mN.'"');

		if ($position != 'user_task')	{
			$indexRequire = $this->sPS('
				require_once(PATH_t3lib.\'class.t3lib_extobjbase.php\');
			');
			$indexContent = $this->sPS(
				'class '.$cN.' extends t3lib_extobjbase {

					/**
					 * Returns the module menu
					 *
					 * @return array Array with menuitems
					 */
					function modMenu() {
						global $LANG;

						return array(
							"' . $cN . '_check" => "",
						);
					}

					/**
					 * Main method of the module
					 *
					 * @return	HTML
					 */
					public function main() {
							// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
						global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

						$theOutput .= $this->pObj->doc->spacer(5);
						$theOutput .= $this->pObj->doc->section($LANG->getLL("title"),"Dummy content here...",0,1);

						$menu=array();
						$menu[]=t3lib_BEfunc::getFuncCheck($this->pObj->id,"SET['.$cN.'_check]",$this->pObj->MOD_SETTINGS["'.$cN.'_check"]).$LANG->getLL("checklabel");
						$theOutput.=$this->pObj->doc->spacer(5);
						$theOutput.=$this->pObj->doc->section("Menu",implode(" - ",$menu),0,1);

						return $theOutput;
					}
				}
			',
			0);
		} else {
			$indexRequire = '';
			$indexContent = $this->sPS(
				'class '.$cN.' extends mod_user_task {
					/**
					 * Makes the content for the overview frame...
					 *
					 * @return	HTML
					 */
					function overview_main()	{
						$icon = \'<img src="\'.$this->backPath.t3lib_extMgm::extRelPath("'.$extKey.'").\'ext_icon.gif" width=18 height=16 class="absmiddle">\';
						$content = $this->mkMenuConfig($icon.$this->headLink('.$cN.',1),\'\',$this->overviewContent());

						return $content;
					}

					/**
					 * Main method
					 *
					 * @return	HTML
					 */
					function main() {
						global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

						return $this->mainContent();
					}

					/**
					 * Returns content in overview frame
					 *
					 * @return	Content for overview frame
					 */
					function overviewContent()	{
						$content = \'Content in overview frame...\';
						return \'<a href="index.php?SET[function]='.$cN.'"  onClick="this.blur();"><img src="\'.$this->backPath.\'gfx/edit2.gif" style="float: left;"></a><div><a href="index.php?SET[function]='.$cN.'"  onClick="this.blur();">\'.$content.\'</a></div>\';
					}

					/**
					 * Main content method
					 *
					 * @return	Main content for the module
					 */
					function mainContent()	{
						return "Content in main frame...";
					}
				}
			',
			0);
		}

		$this->addFileToFileArray(
			$pathSuffix.'class.'.$cN.'.php',
			$this->PHPclassFile(
				$extKey,
				$pathSuffix.'class.'.$cN.'.php',
				$indexContent,
				'Module extension (addition to function menu) \''.$config['title'].'\' for the \''.$extKey.'\' extension.',
				'',
				'',
				$indexRequire

			)
		);

	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_modulefunction.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_modulefunction.php']);
}


?>