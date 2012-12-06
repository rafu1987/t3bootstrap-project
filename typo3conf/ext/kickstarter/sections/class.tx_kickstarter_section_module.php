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

class tx_kickstarter_section_module extends tx_kickstarter_sectionbase {
  var $sectionID = 'module';
	/**
	 * Renders the form in the kickstarter; this was add_cat_module()
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

				// Enter title of the module
			$subContent='<strong>Enter a title for the module:</strong><br />'.
				$this->renderStringBox_lang('title',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Description
			$subContent='<strong>Enter a description:</strong><br />'.
				$this->renderStringBox_lang('description',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Description
			$subContent='<strong>Enter a tab label (shorter description):</strong><br />'.
				$this->renderStringBox_lang('tablabel',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Position
			$optValues = array(
				'web' => 'Sub in Web-module',
				'file' => 'Sub in File-module',
				'user' => 'Sub in User Tools-module',
				'tools' => 'Sub in Admin Tools-module',
				'help' => 'Sub in Help-module',
				'_MAIN' => 'New main module'
			);
			$subContent='<strong>Sub- or main module?</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[position]',$piConf['position'],$optValues).
				$this->resImg('module.png');
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Sub-position
			$optValues = array(
				'0' => 'Bottom (default)',
				'top' => 'Top',
				'web_after_page' => 'If in Web-module, after Web>Page',
				'web_before_info' => 'If in Web-module, before Web>Info',
			);
			$subContent='<strong>Position in module menu?</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[subpos]',$piConf['subpos'],$optValues);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


				// docheaders
			$subContent = '<strong>Backend Module with docheaders</strong>' .
				$this->resImg('docheader.png') .
				$this->renderCheckBox($ffPrefix.'[docheader]',$piConf['docheader']).'Use docheader<br />';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Admin only
			$subContent = '<strong>Other settings</strong><br />' .
				$this->renderCheckBox($ffPrefix.'[admin_only]',$piConf['admin_only']).'Admin-only access!<br />';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Options
			$subContent = $this->renderCheckBox($ffPrefix.'[interface]',$piConf['interface']).'Allow other extensions to interface with function menu<br />';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_module'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_module'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode('',$lines).'</table>';
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
		$WOP='[module]['.$k.']';
		$mN = ($config['position']!='_MAIN'?$config['position'].'_':'').$this->returnName($extKey,'module','M'.$k);
		$cN = $this->returnName($extKey,'class','module'.$k);
		$pathSuffix = 'mod'.$k.'/';

			// Insert module:
		switch($config['subpos'])	{
			case 'top':
				$subPos='top';
			break;
			case 'web_after_page':
				$subPos='after:layout';
			break;
			case 'web_before_info':
				$subPos='before:info';
			break;
		}
		$this->wizard->ext_tables[] = $this->sPS('
			'.$this->WOPcomment('WOP:'.$WOP).'
			if (TYPO3_MODE === \'BE\') {
				t3lib_extMgm::addModulePath(\'' . $mN . '\', t3lib_extMgm::extPath($_EXTKEY) . \'' .$pathSuffix . '\');' . '
					'.$this->WOPcomment('1. and 2. parameter is WOP:'.$WOP.'[position] , 3. parameter is WOP:'.$WOP.'[subpos]').'
				t3lib_extMgm::addModule(\''.
					($config['position']!='_MAIN'?$config['position']:$this->returnName($extKey,'module','M'.$k)).
					'\', \''.
					($config['position']!='_MAIN'?$this->returnName($extKey,'module','M'.$k):'').
					'\', \''.
					$subPos.
					'\', t3lib_extMgm::extPath($_EXTKEY) . \'' . $pathSuffix . '\');
			}
		');

			// Make conf.php file:
		$content = $this->sPS('
				// DO NOT REMOVE OR CHANGE THESE 2 LINES:
			$MCONF[\'name\'] = \''.$mN.'\';
			$MCONF[\'script\'] = \'_DISPATCH\';
				' . $this->WOPcomment('WOP:' .$WOP . '[admin_only]: If the flag was set the value is "admin", otherwise "user,group"') . '
			$MCONF[\'access\'] = \'' . ($config['admin_only'] ? 'admin' : 'user,group') . '\';

			$MLANG[\'default\'][\'tabs_images\'][\'tab\'] = \'moduleicon.gif\';
			$MLANG[\'default\'][\'ll_ref\'] = \'LLL:EXT:' . $extKey . '/' . $pathSuffix . 'locallang_mod.xml\';
		');

		$ll=array();
		$this->addLocalConf($ll,$config,'title','module',$k,1,0,'mlang_tabs_tab');
		$this->addLocalConf($ll,$config,'description','module',$k,1,0,'mlang_labels_tabdescr');
		$this->addLocalConf($ll,$config,'tablabel','module',$k,1,0,'mlang_labels_tablabel');
		$this->addLocalLangFile($ll,$pathSuffix.'locallang_mod.xml','Language labels for module "'.$mN.'" - header, description');

		$content=$this->wrapBody('
			<?php
			',$content,'
			?>
		',0);

		$this->addFileToFileArray($pathSuffix . 'conf.php', trim($content));

			// add the template for docheader
		if ($config['docheader']) {
			$mod_template = '<!-- ###FULLDOC### begin -->
<div class="typo3-fullDoc">
	<!-- Page header with buttons, path details and csh -->
	<div id="typo3-docheader">
		<div id="typo3-docheader-row1">
			<div class="buttonsleft">###BUTTONLIST_LEFT###</div>
			<div class="buttonsright">###BUTTONLIST_RIGHT###</div>
		</div>
		<div id="typo3-docheader-row2">
			<div class="docheader-row2-left"><div class="docheader-funcmenu">###FUNC_MENU###</div></div>
			<div class="docheader-row2-right">###PAGEPATH######PAGEINFO###</div>
		</div>
	</div>
	<!-- Content of module, for instance listing, info or editing -->
	<div id="typo3-docbody">
		<div id="typo3-inner-docbody">
			###CONTENT###
		</div>
	</div>
</div>
<!-- ###FULLDOC### end -->

<!-- Grouping the icons on top -->

<!-- ###BUTTON_GROUP_WRAP### -->
	<div class="buttongroup">###BUTTONS###</div>
<!-- ###BUTTON_GROUP_WRAP### -->

<!-- ###BUTTON_GROUPS_LEFT### -->
<!-- ###BUTTON_GROUP1### -->###SAVE###<!-- ###BUTTON_GROUP1### -->
<!-- ###BUTTON_GROUPS_LEFT### -->

<!-- ###BUTTON_GROUPS_RIGHT### -->
<!-- ###BUTTON_GROUP1### -->###SHORTCUT###<!-- ###BUTTON_GROUP1### -->
<!-- ###BUTTON_GROUPS_RIGHT### -->';
			$this->addFileToFileArray($pathSuffix . 'mod_template.html', $mod_template);
		}

			// Add title to local lang file
		$ll=array();
		$this->addLocalConf($ll,$config,'title','module',$k,1);
		$this->addLocalConf($ll,array('function1'=>'Function #1'),'function1','module',$k,1,1);
		$this->addLocalConf($ll,array('function2'=>'Function #2'),'function2','module',$k,1,1);
		$this->addLocalConf($ll,array('function3'=>'Function #3'),'function3','module',$k,1,1);
		$this->addLocalLangFile($ll,$pathSuffix.'locallang.xml','Language labels for module "'.$mN.'"');

			// Add default module icon
		$this->addFileToFileArray($pathSuffix.'moduleicon.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/notfound_module.gif'));


		$indexRequire = $this->sPS('
			$GLOBALS[\'LANG\']->includeLLFile(\'EXT:' . $extKey . '/' . $pathSuffix . 'locallang.xml\');
			//require_once(PATH_t3lib . \'class.t3lib_scbase.php\');
			$GLOBALS[\'BE_USER\']->modAccess($MCONF, 1);	// This checks permissions and exits if the users has no permission for entry.
				// DEFAULT initialization of a module [END]
		');

			// Make module index.php file:
		$indexContent = $this->sPS(
				'class ' . $cN . ' extends t3lib_SCbase {
	protected $pageinfo;

	/**
	 * Initializes the module.
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		/*
		if (t3lib_div::_GP(\'clear_all_cache\'))	{
			$this->include_once[] = PATH_t3lib . \'class.t3lib_tcemain.php\';
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	public function menuConfig() {
		$this->MOD_MENU = array(
			\'function\' => array(
				\'1\' => $GLOBALS[\'LANG\']->getLL(\'function1\'),
				\'2\' => $GLOBALS[\'LANG\']->getLL(\'function2\'),
				\'3\' => $GLOBALS[\'LANG\']->getLL(\'function3\'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return void
	 */
	public function main() {
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
	' . ($config['docheader'] ? '
			// Initialize doc
		$this->doc = t3lib_div::makeInstance(\'template\');
		$this->doc->setModuleTemplate(t3lib_extMgm::extPath(\'' . $extKey . '\') . \'' . $pathSuffix . 'mod_template.html\');
		$this->doc->backPath = $GLOBALS[\'BACK_PATH\'];
		$docHeaderButtons = $this->getButtons();

		if (($this->id && $access) || ($GLOBALS[\'BE_USER\']->user[\'admin\'] && !$this->id)) {

				// Draw the form
			$this->doc->form = \'<form action="" method="post" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '">\';

				// JavaScript
			$this->doc->JScode = \'
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			\';
			$this->doc->postCode=\'
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			\';
				// Render content:
			$this->moduleContent();
		} else {
				// If no access or if ID == zero
			$docHeaderButtons[\'save\'] = \'\';
			$this->content .= $this->doc->spacer(10);
		}

			// Compile document
		$markers[\'FUNC_MENU\'] = t3lib_BEfunc::getFuncMenu(0, \'SET[function]\', $this->MOD_SETTINGS[\'function\'], $this->MOD_MENU[\'function\']);
		$markers[\'CONTENT\'] = $this->content;

			// Build the <body> for the module
		$this->content .= $this->doc->startPage($GLOBALS[\'LANG\']->getLL(\'title\'));
		$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		$this->content .= $this->doc->endPage();
		$this->content .= $this->doc->insertStylesAndJS($this->content);
	' : '
		if (($this->id && $access) || ($GLOBALS[\'BE_USER\']->user[\'admin\'] && !$this->id)) {

				// Draw the header.
			$this->doc = t3lib_div::makeInstance(\'mediumDoc\');
			$this->doc->backPath = $GLOBALS[\'BACK_PATH\'];
			$this->doc->form = \'<form action="" method="post" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '">\';

				// JavaScript
			$this->doc->JScode = \'
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			\';
			$this->doc->postCode = \'
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			\';

			$headerSection = $this->doc->getHeader(\'pages\', $this->pageinfo, $this->pageinfo[\'_thePath\']) . \'<br />\'
				. $GLOBALS[\'LANG\']->sL(\'LLL:EXT:lang/locallang_core.xml:labels.path\') . \': \' . t3lib_div::fixed_lgd_cs($this->pageinfo[\'_thePath\'], -50);

			$this->content .= $this->doc->startPage($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content .= $this->doc->header($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->section(\'\',$this->doc->funcMenu($headerSection, t3lib_BEfunc::getFuncMenu($this->id, \'SET[function]\', $this->MOD_SETTINGS[\'function\'], $this->MOD_MENU[\'function\'])));
			$this->content .= $this->doc->divider(5);

				// Render content:
			$this->moduleContent();

				// Shortcut
			if ($GLOBALS[\'BE_USER\']->mayMakeShortcut()) {
				$this->content .= $this->doc->spacer(20) . $this->doc->section(\'\', $this->doc->makeShortcutIcon(\'id\', implode(\',\', array_keys($this->MOD_MENU)), $this->MCONF[\'name\']));
			}

			$this->content .= $this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance(\'mediumDoc\');
			$this->doc->backPath = $GLOBALS[\'BACK_PATH\'];

			$this->content .= $this->doc->startPage($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content .= $this->doc->header($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->spacer(10);
		}
	') . '
	}

	/**
	 * Prints out the module HTML.
	 *
	 * @return void
	 */
	public function printContent() {
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content.
	 *
	 * @return void
	 */
	protected function moduleContent() {
		switch ((string)$this->MOD_SETTINGS[\'function\']) {
			case 1:
				$content = \'<div align="center"><strong>Hello World!</strong></div><br />
					The "Kickstarter" has made this module automatically, it contains a default framework for a backend module but apart from that it does nothing useful until you open the script \'.substr(t3lib_extMgm::extPath(\''.$extKey.'\'),strlen(PATH_site)).\''.$pathSuffix.'index.php and edit it!
					<hr />
					<br />This is the GET/POST vars sent to the script:<br />\'.
					\'GET:\' . t3lib_div::view_array($_GET) . \'<br />\'.
					\'POST:\' . t3lib_div::view_array($_POST) . \'<br />\'.
					\'\';
				$this->content .= $this->doc->section(\'Message #1:\', $content, 0, 1);
				break;
			case 2:
				$content = \'<div align=center><strong>Menu item #2...</strong></div>\';
				$this->content .= $this->doc->section(\'Message #2:\', $content, 0, 1);
				break;
			case 3:
				$content = \'<div align=center><strong>Menu item #3...</strong></div>\';
				$this->content .= $this->doc->section(\'Message #3:\', $content, 0, 1);
				break;
		}
	}
	' . ($config['docheader'] ? '

	/**
	 * Creates the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return array All available buttons as an assoc.
	 */
	protected function getButtons()	{
		$buttons = array(
			\'csh\' => \'\',
			\'shortcut\' => \'\',
			\'save\' => \'\'
		);

			// CSH
		$buttons[\'csh\'] = t3lib_BEfunc::cshItem(\'_MOD_web_func\', \'\', $GLOBALS[\'BACK_PATH\']);

			// SAVE button
		$buttons[\'save\'] = \'<input type="image" class="c-inputButton" name="submit" value="Update"\' . t3lib_iconWorks::skinImg($GLOBALS[\'BACK_PATH\'], \'gfx/savedok.gif\', \'\') . \' title="\' . $GLOBALS[\'LANG\']->sL(\'LLL:EXT:lang/locallang_core.php:rm.saveDoc\', 1) . \'" />\';

			// Shortcut
		if ($GLOBALS[\'BE_USER\']->mayMakeShortcut())	{
			$buttons[\'shortcut\'] = $this->doc->makeShortcutIcon(\'\', \'function\', $this->MCONF[\'name\']);
		}

		return $buttons;
	}
	' : '') . '
}
		',
		0);

		$SOBE_extras['firstLevel']=0;
		$SOBE_extras['include']=1;
		$this->addFileToFileArray(
			$pathSuffix.'index.php',
			$this->PHPclassFile(
				$extKey,
				$pathSuffix.'index.php',
				$indexContent,
				"Module '".$config["title"]."' for the '".$extKey."' extension.",
				$cN,
				$SOBE_extras,
				$indexRequire
			)
		);
	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_module.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_module.php']);
}


?>
