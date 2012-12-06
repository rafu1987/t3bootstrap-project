<?php
/***************************************************************
 *  Copyright notice
 *
 *  Based on t3mootools from Peter Klein <peter@umloud.dk>
 *  (c) 2007-2012 Juergen Furrer (juergen.furrer@gmail.com)
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


$GLOBALS['LANG']->includeLLFile('EXT:t3jquery/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$GLOBALS['BE_USER']->modAccess($MCONF,1);
// DEFAULT initialization of a module [END]


if (intval(PHP_VERSION) < 5) {
	require_once('class.JavaScriptPacker.php4');
} else {
	require_once('class.JavaScriptPacker.php');
}
require_once('class.analyzeJqJS.php');
if (t3lib_extMgm::isLoaded('extdeveval')) {
	require_once(t3lib_extMgm::extPath('extdeveval') . 'mod1/class.tx_extdeveval_apidisplay.php');
}

require_once(t3lib_extMgm::extPath('t3jquery') . 'class.tx_t3jquery.php');

/**
 * Module 'jQuery Config' for the 't3jquery' extension.
 *
 * @author     Juergen Furrer (juergen.furrer@gmail.com)
 * @package    TYPO3
 * @subpackage tx_t3jquery
 */
class  tx_t3jquery_module1 extends t3lib_SCbase
{
	var $pageinfo;
	var $extKey = 't3jquery';
	var $jQueryVersionOrig          = '1.8.x';
	var $jQueryUiVersionOrig        = '1.9.x';
	var $jQueryTOOLSVersionOrig     = '1.2.x';
	var $jQueryBootstrapVersionOrig = '2.1.x';
	var $jQueryOriginalVersions = array();
	var $jQueryConfig      = array();
	var $jQueryUiConfig    = array();
	var $jQueryTOOLSConfig = array();
	var $LANG = NULL;
	var $confArray = array();
	var $configDir = NULL;
	var $configXML = array();
	var $missingComponents = array();

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()
	{
		// get extension configuration
		$this->confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		$this->configXML['groups'] = array();
		$this->configXML['groups_missing'] = array();
		// get the XML-Config from jQuery
		if ($this->confArray['jQueryVersion']) {
			$version = $this->confArray['jQueryVersion'];
			$array_name = 'groups';
		} else {
			$version = $this->jQueryVersionOrig;
			$array_name = 'groups_missing';
		}
		$this->jQueryConfig = tx_t3jquery::getJqueryConfiguration($version);
		if (count($this->jQueryConfig['groups']) > 0) {
			$this->configXML[$array_name] = $this->jQueryConfig['groups'];
		}
		// Get the XML-Config from jQuery UI
		if ($this->confArray['jQueryUiVersion']) {
			$version = $this->confArray['jQueryUiVersion'];
			$array_name = 'groups';
		} else {
			$version = $this->jQueryUiVersionOrig;
			$array_name = 'groups_missing';
		}
		$this->jQueryUiConfig = tx_t3jquery::getJqueryUiConfiguration($version);
		if (count($this->jQueryUiConfig['groups']) > 0) {
			foreach ($this->jQueryUiConfig['groups'] as $group) {
				$this->configXML[$array_name][] = $group;
			}
		}
		// Get the XML-Config from jQuery TOOLS
		if ($this->confArray['jQueryTOOLSVersion']) {
			$version = $this->confArray['jQueryTOOLSVersion'];
			$array_name = 'groups';
		} else {
			$version = $this->jQueryTOOLSVersionOrig;
			$array_name = 'groups_missing';
		}
		$this->jQueryTOOLSConfig = tx_t3jquery::getJqueryToolsConfiguration($version);
		if (count($this->jQueryTOOLSConfig['groups']) > 0) {
			foreach ($this->jQueryTOOLSConfig['groups'] as $group) {
				$this->configXML[$array_name][] = $group;
			}
		}
		// Get the XML-Config from jQuery Bootstrap
		if ($this->confArray['jQueryBootstrapVersion']) {
			$version = $this->confArray['jQueryBootstrapVersion'];
			$array_name = 'groups';
		} else {
			$version = $this->jQueryBootstrapVersionOrig;
			$array_name = 'groups_missing';
		}
		$this->jQueryBootstrapConfig = tx_t3jquery::getJqueryBootstrapConfiguration($version);
		if (count($this->jQueryBootstrapConfig['groups']) > 0) {
			foreach ($this->jQueryBootstrapConfig['groups'] as $group) {
				$this->configXML[$array_name][] = $group;
			}
		}
		// Define the language object
		$this->LANG = $GLOBALS['LANG'];
		// Define the used file directory
		$this->configDir = PATH_site . tx_t3jquery::getJqPath();
		if (! is_dir($this->configDir)) {
			$this->configDir = PATH_site . 'uploads/tx_t3jquery/';
		}
		$this->createFolder();
		$this->initConfig();
		parent::init();
	}

	/**
	 * Creates all needed folders and files if not exist
	 * 
	 * @return	boolean
	 */
	function createFolder()
	{
		// create the config folder
		if (! is_dir($this->configDir)) {
			if (! t3lib_div::mkdir($this->configDir)) {
				t3lib_div::devLog("Could not create config path '{$this->configDir}'!", 't3jquery', 3);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Create the config-file if not exist
	 * 
	 * @return	boolean
	 */
	function initConfig() {
		if (! file_exists($this->configDir.'t3jquery.cfg')) {
			if (! t3lib_div::writeFile($this->configDir.'t3jquery.cfg', '')) {
				t3lib_div::devLog("Could not create config file '{$this->configDir}t3jquery.cfg'!", 't3jquery', 3);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()
	{
		$this->MOD_MENU = array(
			'function' => array(
				'1' => $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:function1'),
				'2' => $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:function2'),
				'3' => $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:function3'),
				'4' => $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:function4'),
			)
		);
		if (t3lib_extMgm::isLoaded('extdeveval')) {
			$this->MOD_MENU['function'][5] = $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:function5');
		}
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	void
	 */
	function main()
	{
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if ($_GET['createLib'] == 1) {
			$this->MOD_SETTINGS['function'] = 1;
		}

		// If CDN is used, there are no settings.
		if ($this->confArray['integrateFromCDN'] && isset($this->confArray['locationCDN'])) {
			// Draw the header.
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];

			$this->content .= $this->doc->startPage($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->header($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->spacer(5);

			// output the used versions
			$temp_version = array();
			if ($this->confArray['jQueryVersion']) {
				$temp_version[] = "jQuery {$this->confArray['jQueryVersion']}";
			}
			if ($this->confArray['jQueryUiVersion']) {
				$temp_version[] = "UI {$this->confArray['jQueryUiVersion']}";
			}
			if ($this->confArray['jQueryTOOLSVersion']) {
				$temp_version[] = "Tools {$this->confArray['jQueryTOOLSVersion']}";
			}
			if ($this->confArray['jQueryBootstrapVersion']) {
				$temp_version[] = "Bootstrap {$this->confArray['jQueryBootstrapVersion']}";
			}
			$this->content .= $this->doc->section(
				sprintf($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:version_in_use'), implode(" / ", $temp_version))
			);
			$this->content .= $this->doc->section('', $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:integrate_from_cdn'));

		} else if (($this->id && $access) || ($GLOBALS['BE_USER']->user['admin'] && !$this->id)) {
			// Draw the header.
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$this->doc->form = '<form action="'.t3lib_div::linkThisScript().'#jqbuttons" method="post" enctype="multipart/form-data" name="jq" id="jq">';

			// JavaScript (jQuery subscripts is used, as no compressed lib exists yet or might not include the supparts needed.)
			$this->doc->JScode = '
<script type="text/javascript" src="../'.t3lib_extMgm::siteRelPath('t3jquery').'res/jquery/core/' . $this->confArray['jQueryVersion'] . '/jquery.js"></script>
<script type="text/javascript" src="../'.t3lib_extMgm::siteRelPath('t3jquery').'res/jqconfig.js"></script>
<script language="javascript" type="text/javascript">
	script_ended = 0;
	function jumpToUrl(URL)	{
		document.location = URL;
	}
</script>';
			$this->doc->postCode = '
<script language="javascript" type="text/javascript">
	script_ended = 1;
	if (top.fsMod) top.fsMod.recentIds["web"] = 0;
</script>
';

			$this->content .= $this->doc->startPage($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->header($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->spacer(5);

			// Use button from the Analyzer has been pressed
			if ($_POST['usejq'] && $_POST['dependencies']) {
				$temp = array();
				$temp['files'] = $this->safe_unserialize(urldecode($_POST['dependencies']));
				$this->saveJqConf($temp);
				$this->MOD_SETTINGS['function'] = 1;
			}
			// Merge&Use button from the Analyzer has been pressed
			if ($_POST['mergejq'] && $_POST['dependencies']) {
				$temp = array();
				$temp['files'] = $this->mergeJqConf($this->safe_unserialize(urldecode($_POST['dependencies'])));
				$this->saveJqConf($temp);
				$this->MOD_SETTINGS['function'] = 1;
			}
			$this->compressed = '';
			// Compress button from the Compress own script has been pressed
			if ($_POST['compress'] && $_POST['compressdata'] != '') {
				$this->compressed = $this->compressJSFile($_POST['compressdata']);
			}

			// output the used versions
			$temp_version = array();
			if ($this->confArray['jQueryVersion']) {
				$temp_version[] = "jQuery {$this->jQueryConfig['version']['act']}";
			}
			if ($this->confArray['jQueryUiVersion']) {
				$temp_version[] = "UI {$this->jQueryUiConfig['version']['act']}";
			}
			if ($this->confArray['jQueryTOOLSVersion']) {
				$temp_version[] = "Tools {$this->jQueryTOOLSConfig['version']['act']}";
			}
			if ($this->confArray['jQueryBootstrapVersion']) {
				$temp_version[] = "Bootstrap {$this->jQueryBootstrapConfig['version']['act']}";
			}
			$this->content .= $this->doc->section(
				'',
				$this->doc->funcMenu(
					sprintf($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:version_in_use'), implode(" / ", $temp_version)),
					t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function'])
				)
			);
			$this->content .= $this->doc->divider(5);

			// Render content:
			$this->moduleContent();

			// ShortCut
			if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
				$this->content .= $this->doc->spacer(20).$this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
			}

			$this->content .= $this->doc->spacer(10);

		} else {
			// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];

			$this->content .= $this->doc->startPage($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->header($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:title'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()
	{
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()
	{
		$this->jQueryConfig = array();
		$this->jQueryConfig = $_POST;
		if (!is_array($this->jQueryConfig['files']) || !isset($this->jQueryConfig['files'])) {
			$this->jQueryConfig['files'] = array("jquery.js");
		}

		switch ((string)$this->MOD_SETTINGS['function']) {
			case 1: {
				if (isset($_POST['compression'])) {
					if ($jsAlertData = $this->createJqFile()) {
						$content = '
<script type="text/javascript">
jQuery(document).ready(function() {
	alert("'.t3lib_div::slashJS($jsAlertData).'");
});
</script>';
					}
				}
				$content .= $this->makeJqForm();
				$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.header1'), $content ,0 ,1);
				break;
			}
			case 2: {
				$content = $this->makePackitoForm();
				$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.header2'), $content, 0, 1);
				$file = $_FILES['js_local']['tmp_name'] ? $_FILES['js_local']['tmp_name'] : ($_POST['js_remote'] ? t3lib_div::getFileAbsFileName($_POST['js_remote']) : '');
				// Form has been submitted
				if ($file) {
					$fileName = $_FILES['js_local']['name'] ? $_FILES['js_local']['name'] : ($_POST['js_remote'] ? $_POST['js_remote'] : $file );
					$dep = $this->analyzeJS($file);

					// show the missing dependencies
					$content = $this->displayMissingLibrary();
					if ($content) {
						$this->content .= $this->doc->section('', $content, 0, 1);
					}

					// show the dependencies
					$content = $this->displayDependencies($dep);
					if (count($dep) == 0) {
						$content .= '<p>&nbsp;</p><p>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.analyze.packed').'</p>';
					}
					$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.analyze.dependencies').' "'.basename($fileName).'"', $content, 0, 1);
				}
				break;
			}
			case 3: {
				$content = $this->makeProcessForm();
				$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.header3'), $content, 0, 1);
				// Form has been submitted
				$files = $_POST['ext'];
				if ($files) {
					$dep = Array();
					foreach ($files as $file) {
						$dep = $this->processT3jqueryTxt(t3lib_div::getFileAbsFileName($file), $dep);
					}

					// show the missing dependencies
					$content = $this->displayMissingLibrary();
					if ($content) {
						$this->content .= $this->doc->section('', $content, 0, 1);
					}

					// show the dependencies
					$content = $this->displayDependencies($dep);
					$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.dependencies'), $content, 0, 1);
				}
				break;
			}
			case 4: {
				$content = $this->makeCompressForm($this->compressed);
				$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.header4'), $content, 0, 1);
				break;
			}
			case 5: {
				// Display APIdocs
				if (t3lib_extMgm::isLoaded('extdeveval')) {
					$content = NULL;
					try {
						$inst = t3lib_div::makeInstance('tx_extdeveval_apidisplay');
						$content = '<hr />'.$inst->main(t3lib_div::getUrl('../ext_php_api.dat'),'tx_t3jquery');
					} catch (Exception $e) {
						$content = $e->getMessage();
					}
					$this->content .= $this->doc->section($this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.header5'), $content, 0, 1);
				}
				break;
			}
		}
	}

	/**
	 * Analyze a JS-File and return all depencies
	 * 
	 * @param	string	$file
	 * @return	array
	 */
	function analyzeJS($file)
	{
		$dependencies = array();
		$fileName = $_FILES['js_local']['name'] ? $_FILES['js_local']['name'] : ($_POST['js_remote'] ? $_POST['js_remote'] : $file );
		$path_info = pathinfo(array_shift(explode('?', basename($fileName))));
		if ($path_info['extension'] == 'js') {
			$fileData = t3lib_div::getURL($file);
			if (substr($fileData, 0, 23) != 'eval(function(p,a,c,k,e') {
				$pack = new analyzeJqJS('', $fileData, $this->configXML['groups']);
				$requires = $pack->getDependencies();
				foreach ($requires as $file => $lib) {
					$dependencies[$lib][$file] = 1;
				}
			}
		}
		return $dependencies;
	}


	/**
	 * Shows the missing controlls (if UI or TOOLS not selected but needed)
	 * 
	 * @return string
	 */
	function displayMissingLibrary()
	{
		$content = array();
		$match = array();
		if (count($this->configXML['groups_missing']) > 0) {
			// every group in the config
			foreach ($this->configXML['groups_missing'] as $group) {
				// every file in this group
				if (count($group['files']) > 0) {
					foreach ($group['files'] as $file) {
						if (in_array($file['name'], $this->missingComponents)) {
							if (preg_match("/^ui\//", $file['file'])) {
								$content['UI'] = $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.missingLibrary.ui');
							}
							if (preg_match("/^TOOLS\:/", $file['file'])) {
								$content['TOOLS'] = $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.missingLibrary.tools');
							}
						}
					}
				}
			}
		}
		if (count($content) > 0) {
			return '
			<div class="typo3-message message-warning">
				<div class="message-header">' . $this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.missingLibrary') . '</div>
				<div class="message-body">
					' . implode('<br />', $content) . '<br />' . 
					'<a href="javascript:void();" onclick="top.goToModule(\'tools_em\',\'\',\'\');this.blur();return false;">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.em').'</a>
				</div>
			</div>';
		} else {
			return NULL;
		}
	}

	/**
	 * Shows the depencies of all components
	 * 
	 * @param	array	$requires
	 * @return	string
	 */
	function displayDependencies($requires)
	{
		$dependencies = '';
		$prevlib = '';
		$jqconfig = array();
		$JQconf = $this->loadJqConf();
		if (count($requires) > 0) {
			foreach ($requires as $lib => $files) {
				foreach ($files as $file => $flag) {
					if ($flag) {
						if ($lib != $prevlib) {
							$dependencies .= '<dt><h2>'.$lib.'</h2></dt>';
							$prevlib = $lib;
						}
						$new_file = $this->matchJqFile($file);
						if (! in_array($new_file, $JQconf['files'])) {
							$dependencies .= '<dd><strong>'.$file.'</strong></dd>';
						} else {
							$dependencies .= '<dd>'.$file.'</dd>';
						}
						$jqconfig[] = $new_file;
					}
				}
			}
		}
		if ($dependencies) {
			$content  = '<dl>'. $dependencies.'</dl>';
			$content .= $this->doc->divider(5);
			$content .= '<input type="hidden" name="dependencies" value="'.urlencode(serialize($jqconfig)).'">';
			$content .= '<a name="jqbuttons"></a><input type="submit" name="usejq" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.usejq').'"> ';
			$content .= '<input type="submit" name="mergejq" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.mergejq').'">';
		} else {
			$content = '<strong>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.analyze.none').'</strong>';
		}
		return $content;
	}

	/**
	 * Matchfile to component name
	 * 
	 * @param	string	$component
	 * @return	string
	 */
	function matchJqFile($component="")
	{
		$match = array();
		if (count($this->configXML['groups']) > 0) {
			// every group in the config
			foreach ($this->configXML['groups'] as $group) {
				// every file in this group
				if (count($group['files']) > 0) {
					foreach ($group['files'] as $file) {
						$match[$file['name']] = $file['file'];
						// Every language file
						if (count($file['languages']) > 0) {
							foreach ($file['languages'] as $language) {
								$match[$language['name']] = $language['file'];
							}
						}
					}
				}
			}
		}
		return $match[$component];
	}

	/**
	 * Create the packit form
	 * 
	 * @return	string
	 */
	function makePackitoForm()
	{
		return '
<br /><p>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.packito.description').'</p><br />
<table border="0" cellspacing="1" cellpadding="2">
	<tr>
		<td>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.packito.remote').'</td>
		<td><input type="text" name="js_remote" value="'.$_POST['js_remote'].'" id="js_remote" size="50" /></td>
	</tr>
	<tr>
		<td>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.packito.local').'</td>
		<td><input type="file" name="js_local" size="50" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><p>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.packito.note').'</p></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><p class="submit"><input type="submit" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.check').'" /></p></td>
	</tr>
</table>';
	}

	/**
	 * Create the buttons at the end of the form
	 * 
	 * @return	string
	 */
	function makeProcessForm()
	{
		return '
<br /><p>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.description').'</p><br />
<table border="0" cellspacing="1" cellpadding="2" id="process">
	<tr class="bgColor5">
		<td>&nbsp;</td>
		<td>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.title').'</td>
		<td>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.extkey').'</td>
		<td>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.extension.version').'</td>
	</tr>
	'.$this->makeCheckboxes().'
	<tr>
		<td colspan="4">
			<table>
				<tr>
					<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.selectall').'" /></p></td>
					<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.selectnone').'" /></p></td>
					<td><p class="submit"><input type="submit" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.check').'" /></p></td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
	}

	/**
	 * Create the compression formular
	 * 
	 * @param	string	$compressed
	 * @return	string
	 */
	function makeCompressForm($compressed)
	{
		$out = '
<br /><p>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.description').'</p><br />
<table border="0" cellspacing="1" cellpadding="2">
	<tr>
		<td colspan="3" class="bgColor5">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.compress').'</td>
	</tr>
	<tr>
		<td colspan="3"><textarea cols="80" rows="12" name="compressdata" id="compressdata">'.htmlentities(stripslashes($_POST['compressdata'])).'</textarea></td>
	</tr>
	<tr>
		<td align="right">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.nomunge').'</td>
		<td>
			<select name="compression">
				<option value="1"'.($_POST['compression'] == 1 ? ' selected="selected"' : '').'>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.nomunge.yes').'</option>
				<option value="0"'.($_POST['compression'] != 1 ? ' selected="selected"' : '').'>'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.nomunge.no').'</option>
			</select>
		</td>
		<td>
			<p class="submit">
				<input type="submit" id="compress" name="compress" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.compress').'" />
			</p>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" class="bgColor5">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compress.decompress').'</td>
	</tr>
	<tr>
		<td colspan="3"><textarea cols="80" rows="12" name="decompressdata" id="decompressdata">'.htmlentities($compressed).'</textarea></td>
	</tr>
</table>';
		if ($sizeDiff = strlen(stripslashes($_POST['compressdata']))-strlen($compressed)) {
			$out .= '<p>Compression reduced the size '.$sizeDiff.' bytes.</p>';
		}
		return $out;
	}

	/**
	 * Process a t3jquery.txt and retun all depencies
	 * 
	 * @param	string	$t3jqfile
	 * @param	array	$dep
	 * @return	array
	 */
	function processT3jqueryTxt($t3jqfile, $dep=array())
	{
		$components = array();
		if (count($this->configXML['groups']) > 0) {
			// every group in the config
			foreach ($this->configXML['groups'] as $group) {
				// every file in this group
				if (count($group['files']) > 0) {
					foreach ($group['files'] as $file) {
						$components[$file['name']] = $group['name'];
						// Every language file
						if (count($file['languages']) > 0) {
							foreach ($file['languages'] as $language) {
								$components[$language['name']] = $group['name'];
							}
						}
					}
				}
			}
		}

		$lines = file($t3jqfile);
		$path = dirname($t3jqfile);

		foreach ($lines as $line) {
			$tmp = explode('=', $line);
			$option = strtolower(trim($tmp[0]));
			$params = explode(',', $tmp[1]);
			switch ($option) {
				case 'script' : {
					foreach ($params as $file) {
						if (is_file($path.'/'.trim($file))) {
							$dep = $this->array_merge_recursive_unique($dep, $this->analyzeJS($path.'/'.trim($file)));
						}
					}
					break;
				}
				case 'components' : {
					foreach ($params as $component) {
						if (array_key_exists(trim($component), $components)) {
							$dep[$components[trim($component)]][trim($component)] = 1;
						} else {
							$this->missingComponents[] = $component;
						}
						if (preg_match("/^Datepicker\-(.*)/i", $component, $preg)) {
							$dep[$components['Datepicker']]['Datepicker'] = 1;
							$dep[$components['Datepicker']][$preg[0]] = 1;
						}
					}
					break;
				}
			}
		}
		return $dep;
	}

	/**
	 * Create a checkbox imput field
	 * 
	 * @return	string
	 */
	function makeCheckboxes()
	{
		$out = $this->makeCheckboxesForLocalExtensions('typo3conf/ext/'); // Local extensions
		$out.= $this->makeCheckboxesForLocalExtensions('typo3/ext/'); // Global extensions
		$out.= $this->makeCheckboxesForLocalExtensions('typo3/sysext/'); // System extensions
		return $out;
	}

	/**
	 * Generates checkboxes with the extension keys locally available for this install.
	 *
	 * @return	string		list of checkboxes for selecting the local extension to work on (or error message)
	 */
	function makeCheckboxesForLocalExtensions($localExtensionDir)
	{
		$path = PATH_site.$localExtensionDir;
		if (@is_dir($path))	{
			$dirs = $this->extensionList = t3lib_div::get_dirs($path);
			if (is_array($dirs)) {
				sort($dirs);
				$c = 0;
				$opt = array();
				foreach ($dirs as $dirName) {
					// only display loaded extensions
					if (t3lib_extMgm::isLoaded($dirName)) {
						if (@file_exists($path.$dirName.'/t3jquery.txt')) {
							// Get extension info from ext_emconf.php
							$extInfo = $this->includeEMCONF($path.$dirName.'/ext_emconf.php', $dirName);
							if (is_array($_POST['ext'])) {
								$selVal = in_array($path.$dirName.'/t3jquery.txt',$_POST['ext']) ? ' checked="checked"' : '';
							}
							$c++;
							$opt[] = '
	<tr class="bgColor4" valign="top">
		<td><input name="ext[]" type="checkbox" id="ext'.$c.'" class="extkey" value="'.htmlspecialchars($path.$dirName.'/t3jquery.txt').'"'.$selVal.' /></td>
		<td title="'.htmlspecialchars($extInfo['description']).'" nowrap><label for="ext'.$c.'">'.htmlspecialchars($extInfo['title']).'</label></td>
		<td nowrap>'.htmlspecialchars($dirName).'</td>
		<td nowrap>'.htmlspecialchars($extInfo['version']).'</td>
	</tr>';
						}
					}
				}
				return implode(' ',$opt);
			}
		} else {
			return '
	<tr><td>ERROR: Extensions path: "'.$path.'" not found!</td></tr>';
		}
	}

	/**
	 * Compress a JS-File
	 * 
	 * @param	string	$script
	 * @return	string
	 */
	function compressJSFile($script)
	{
		$out = array();
		switch((integer)$_POST['compression'])	{
			case 0 : {
				$t1 = microtime(TRUE);

				$packer = new JavaScriptPacker($script, 'None', TRUE, FALSE);
				$script = $packer->pack();

				$t2 = microtime(TRUE);
				$time = sprintf('%.4f', ($t2 - $t1) );
				$out[] = 'jQuery script packed in '.$time.' s';
				break;
			}
			case 1 : {
				$t1 = microtime(TRUE);
				$script = stripslashes($script);

				try {
					$error = '';
					$script = t3lib_div::minifyJavaScript($script, $error);
					if ($error != '') {
						throw new Exception($error);
					}
				} catch(Exception $e) {
					$out[] = $e->getMessage();
				}

				$t2 = microtime(TRUE);
				$time = sprintf('%.4f', ($t2 - $t1) );
				$out[] = 'jQuery script packed in '.$time.' s';
				break;
			}
		}
		$out = implode('\n', $out);
		return $script;
	}

	/**
	 * Create the JS File
	 * 
	 * @return string
	 */
	function createJqFile()
	{
		$out = array();
		$script = '';
		$script_bs = NULL;
		$compression = $_POST['compression'];
		foreach ($this->jQueryConfig['files'] as $scriptPart) {
			$temp_script = NULL;
			if ($scriptPart == 'jquery.js') { // add core
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/core/{$this->confArray['jQueryVersion']}/jquery.js";
			} elseif ($scriptPart == 'jquery.noConflict.js') { // add noConflict mode
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/plugins/jquery.noConflict.js";
			} elseif ($scriptPart == 'jquery-easing.js') { // Easing is in effects.core.js, nothing to do
				if ($this->confArray['jQueryUiVersion'] == '1.9.x') {
					$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/plugins/jquery.easing.js";
				} else {
					$temp_script = NULL;
				}
			} elseif (in_array($scriptPart, array('jquery.mousewheel.js', 'jquery.lint.js', 'jquery.mobile.js', 'jquery.cookie.js'))) { // add plugins
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/plugins/".$scriptPart;
			} elseif (preg_match("/^TOOLS\:(.*)/", $scriptPart, $reg)) { // add TOOLS
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/tools/{$this->confArray['jQueryTOOLSVersion']}/ui/{$reg[1]}";
			} elseif (preg_match("/^Bootstrap\:(.*)/", $scriptPart, $reg)) { // add Bootstrap
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/bootstrap/{$this->confArray['jQueryBootstrapVersion']}/ui/{$reg[1]}";
				if ($compression == 'jsmin') {
					if (file_exists($temp_script)) {
						$script_bs .= t3lib_div::getURL($temp_script);
					} else {
						if ($temp_script) {
							t3lib_div::devLog("File '{$temp_script}' not found!", 't3jquery', 3);
						}
					}
					$temp_script = NULL;
				}
			} else { // add UI
				$temp_script = t3lib_extMgm::extPath($this->extKey)."res/jquery/ui/{$this->confArray['jQueryUiVersion']}/{$scriptPart}";
			}
			if (file_exists($temp_script)) {
				$script .= t3lib_div::getURL($temp_script);
			} else {
				if ($temp_script) {
					t3lib_div::devLog("File '{$temp_script}' not found!", 't3jquery', 3);
				}
			}
		}
		$sizeBefore = strlen($script);
		// get the license from script
		$license = $this->getLicense($script);
		switch((string)$compression) {
			case 'packer' : {
				$t1 = microtime(TRUE);

				$packer = new JavaScriptPacker($script, 'None', TRUE, FALSE);
				$script = $packer->pack();

				$t2 = microtime(TRUE);
				$time = sprintf('%.4f', ($t2 - $t1) );
				$out[] = 'jQuery script packed in '.$time.' s';
				break;
			}
			case 'jsmin' : {
				$t1 = microtime(TRUE);

				try {
					$error = '';
					$script_bs = $this->getNoDocs($script_bs, TRUE);
					$script = t3lib_div::minifyJavaScript($script, $error).$script_bs;
					if ($error != '') {
						throw new Exception($error);
					}
				} catch(Exception $e) {
					$out[] = $e->getMessage();
				}

				$t2 = microtime(TRUE);
				$time = sprintf('%.4f', ($t2 - $t1) );
				$out[] = 'jQuery script minimized in '.$time.' s';
				break;
			}
			case 'nodocs' : {
				$t1 = microtime(TRUE);

				$script = $this->getNoDocs($script);

				$t2 = microtime(TRUE);
				$time = sprintf('%.4f', ($t2 - $t1) );
				$out[] = 'jQuery script stripped of comments in '.$time.' s';
				break;
			}
			default : {
				$out[] = 'jQuery script created.';
				$out[] = 'Size: '.$sizeBefore.' bytes';
				break;
			}
		}

		if ($_POST['compression'] != 'none') {
			$sizeAfter = strlen($script);
			$out[] = 'Compression ratio: '.sprintf('%01.2f', ($sizeAfter && $sizeBefore ? $sizeAfter/$sizeBefore : 0));
			$out[] = 'Size reduced '.($sizeBefore - $sizeAfter).' bytes from '.$sizeBefore.' bytes to '.$sizeAfter.' bytes';
			$script = $license . "\n" . $script;
		}
		$this->safeJqFile($script);
		$this->saveJqConf($this->jQueryConfig);
		$out = implode('\n', $out);
		$GLOBALS['BE_USER']->simplelog("jQuery library created", $this->extKey, 0);
		return $out;
	}

	/**
	 * Returns the license to add when compression is activated
	 * 
	 * @param $script string
	 * @return string
	 */
	function getLicense($script='')
	{
		$license = array();
		preg_match_all("#/\*!(?:[^*]*(?:\*(?!/))*)*\*/#", $script, $license);
		return implode("\n", $license[0]);
	}

	/**
	 * Removes all Documentation
	 * 
	 * @param $script string
	 * @return string
	 */
	function getNoDocs($script=NULL, $removeWhitespaces=FALSE)
	{
		// workaround for "*/*" in jQuery 1.4.4
		$script = str_replace("*/*", "*|/|*", $script);
		// Workaround for /^\/\// in jQuery 1.5.0
		$script = str_replace("/^\\/\\//", "/^\\/\\/|/", $script);
		// Workaround for "//" in jQuery 1.5.0
		$script = str_replace('"//"', '"|/|/"', $script);
		/* Workaround for "http://" in flashembed */
		$script = str_replace("URL = 'http://", "URL = 'http:\/\/", $script);
		$script = str_replace('expressInstall:"http://', 'expressInstall:"http:\/\/', $script);
		$script = str_replace('document.all,j="http://', 'document.all,j="http:\/\/', $script);
	
		/* Workaround internal for jQuery 1.5.0 */
		$script = str_replace("/* internal */", "", $script);
	
		// Remove comments
		$script = preg_replace('#/\*.*?/\*#',                    "", $script); // remove "/* SINGLE LINE */" comments
		$script = preg_replace('#(\/\/.*)#',                     "", $script); // remove "//" comments
		$script = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', "", $script); // remove "/* MULTI LINE */" comments
	
		// Remove empty lines
		$new_script = array();
		$lines = explode(LF, $script);
		if (count($lines) > 0) {
			foreach ($lines as $line) {
				if (! preg_match('/^[ \t]*$/', $line)) {
					if ($removeWhitespaces === TRUE) {
						$line = trim($line);
					}
					$new_script[] = str_replace(array("\n", "\r"), "", $line);
				}
			}
		}
		$script = implode(LF, $new_script);
	
		// restore "*/*" for jQuery 1.4.4
		$script = str_replace("*|/|*", "*/*", $script);
		// restore "/^\/\//" for jQuery 1.5.0
		$script = str_replace("/^\\/\\/|/", "/^\\/\\//", $script);
		// restore "//" in jQuery 1.5.0
		$script = str_replace('"|/|/"', '"//"', $script);
		/* restore "http://" in flashembed */
		$script = str_replace("URL = 'http://", "URL = 'http:\/\/", $script);
		$script = str_replace('expressInstall:"http:\/\/', 'expressInstall:"http://', $script);
		$script = str_replace('document.all,j="http:\/\/', 'document.all,j="http://', $script);
	
		return $script;
	}

	/**
	 * Safe the file to disc
	 * 
	 * @param $block string
	 * @return void
	 */
	function safeJqFile($block='')
	{
		t3lib_div::writeFile($this->configDir . tx_t3jquery::getJqName(), $block);
	}

	/**
	 * Load the config from file
	 * 
	 * @return array
	 */
	function loadJqConf()
	{
		if ($formVars = t3lib_div::getURL($this->configDir.'t3jquery.cfg')) {
			$return = unserialize($formVars);
			if (! is_array($return['files'])) {
				// fallback for old config files
				$temp = array();
				$temp['files'] = $return;
				$return = $temp;
			}
			// if no files are in config, empty array
			if (! $return['files']) {
				$return['files'] = array();
			}
			return $return;
		} else {
			return array(
				'files' => array()
			);
		}
	}

	/**
	 * Save the config to file
	 * 
	 * @param array $formVars
	 * @return void
	 */
	function saveJqConf($formVars)
	{
		if ($this->createFolder()) {
			t3lib_div::writeFile($this->configDir.'t3jquery.cfg', serialize($formVars));
		}
	}

	/**
	 * Merg the existing config with the config from file
	 * 
	 * @param array $formVars
	 * @return array
	 */
	function mergeJqConf($formVars)
	{
		$config = $this->loadJqConf();
		return array_keys(array_count_values(array_merge($config['files'], $formVars)));
	}

	/**
	 * Make the form to define the jQuery Library
	 * 
	 * @return string
	 */
	function makeJqForm()
	{
		$formVars = $this->loadJqConf();
		$out = '
<div class="">
	<table id="download">';
		if (count($this->configXML['groups']) > 0) {
			// every group in the config
			foreach ($this->configXML['groups'] as $group) {
				$out .= '
		<tr><th colspan="3"><h3>'.$group['name'].'</h3></th></tr>';
				// every file in this group
				if (count($group['files']) > 0) {
					foreach ($group['files'] as $file) {
						// if UI Tab and the Tools Tab is selected, the UI Tabs will win...
						$notChecked = FALSE;
						if (preg_match("/^ToolsTabs/", $file['name']) && in_array('ui/jquery.ui.tabs.js', $formVars['files'])) {
							$notChecked = TRUE;
						}
						if (preg_match("/^BootstrapButton/", $file['name']) && in_array('ui/jquery.ui.button.js', $formVars['files'])) {
							$notChecked = TRUE;
						}
						$out .= '
		<tr class="check">
			<td class="check">
				<input type="checkbox" id="'.$file['name'].'" deps="'.$file['depends'].'" dist="'.$file['disturbing'].'" name="files[]" value="'.$file['file'].'"'.(in_array($file['file'], $formVars['files']) && !$notChecked ? ' checked="checked"' : '').' />
			</td>
			<td class="name" style="width:100px;"><label for="'.$file['name'].'">'.$file['label'].'</label></td>
			<td class="description">
				<p>'.$this->LANG->sL($file['detail']).'</p>
			</td>
		</tr>';
						// Every language file
						if (count($file['languages']) > 0) {
							$datepicker_langs = NULL;
							foreach ($file['languages'] as $language) {
								$datepicker_langs .= '
				<div style="width:150px;float:left;">
					<input type="checkbox" id="'.$language["name"].'" deps="'.$file['name'].'" name="files[]" value="'.$language["file"].'"'.(in_array($language["file"], $formVars['files'])?' checked="1"':'').' />
					<label for="'.$language["name"].'">'.$language["label"].'</label>
				</div>';
							}
							$out .= '
		<tr class="check">
			<td class="check"></td>
			<td class="name">Languages</td>
			<td class="description">'.$datepicker_langs.'</td>
		</tr>';
						}
					}
				}
			}
		}
		$out .= '
	</table>
</div>
<h2 class="options compression-options">
	<a href="#" id="compression-tog">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression').'</a>
</h2>
<table id="download-options">
	<tr class="radio">
		<td class="check">
			<input type="radio" id="compression_jsmin" name="compression" value="jsmin" '.($formVars['compression'] == 'jsmin' || ! $formVars['compression'] ? 'checked="checked"' : '').' />
		</td>
		<td class="name"><label for="compression_jsmin">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.jsmin.name').'</label></td>
		<td class="description">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.jsmin.description').'</td>
	</tr>
	<tr class="radio">
		<td class="check">
			<input type="radio" id="compression_packer" name="compression" value="packer" '.($formVars['compression'] == 'packer' ? 'checked="checked"' : '').' />
		</td>
		<td class="name"><label for="compression_packer">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.packer.name').'</label></td>
		<td class="description">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.packer.description').'</td>
	</tr>
	<tr class="radio">
		<td class="check">
			<input type="radio" id="compression_nodocs" name="compression" value="nodocs" '.($formVars['compression'] == 'nodocs' ? 'checked="checked"' : '').' />
		</td>
		<td class="name"><label for="compression_nodocs">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.nodocs.name').'</label></td>
		<td class="description">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.nodocs.description').'</td>
	</tr>
	<tr class="radio last">
		<td class="check">
			<input type="radio" id="compression_none" name="compression" value="none" '.($formVars['compression'] == 'none' ? 'checked="checked"' : '').' />
		</td>
		<td class="name"><label for="compression_none">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.none.name').'</label></td>
		<td class="description">'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.compression.none.description').'</td>
	</tr>
</table>
<input type="hidden" name="version" value="'.$this->jQueryUiVersion.'" />
<table>
	<tr>
		<td><p class="submit"><input type="button" id="select_all" name="select_all" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.selectall').'" /></p></td>
		<td><p class="submit"><input type="button" id="select_none" name="select_none" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.selectnone').'" /></p></td>
		<td><p class="submit"><input type="submit" value="'.$this->LANG->sL('LLL:EXT:t3jquery/mod1/locallang.xml:jquery.button.create').'" /></p></td>
	</tr>
</table>';
		if ($_GET['createLib'] == 1 && !$_POST) {
			$out .= '
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#jq").submit();
});
</script>';
		}
		return $out;
	}

	/**
	 * Returns the $EM_CONF array from an extensions ext_emconf.php file
	 *
	 * @param	string		Absolute path to EMCONF file.
	 * @param	string		Extension key.
	 * @return	array		EMconf array values.
	 */
	function includeEMCONF($path, $_EXTKEY)
	{
		@include($path);
		if (is_array($EM_CONF[$_EXTKEY])) {
			return $EM_CONF[$_EXTKEY];
		}
		return FALSE;
	}

	/**
	 * Merge two arrays to one with unique keys
	 *
	 * @param	array	$array0
	 * @param	array	$array1
	 * @return	array
	 */
	function array_merge_recursive_unique($array0, $array1)
	{
		$arrays = func_get_args();
		$remains = $arrays;

		// We walk through each arrays and put value in the results (without
		// considering previous value).
		$result = array();

		// loop available array
		foreach ($arrays as $array) {

			// The first remaining array is $array. We are processing it. So
			// we remove it from remaing arrays.
			array_shift($remains);

			// We don't care non array param, like array_merge since PHP 5.0.
			if (is_array($array)) {
				// Loop values
				foreach ($array as $key => $value) {
					if (is_array($value)) {
						// we gather all remaining arrays that have such key available
						$args = array();
						foreach ($remains as $remain) {
							if (array_key_exists($key, $remain)) {
								array_push($args, $remain[$key]);
							}
						}
						if (count($args) > 2) {
							// put the recursion
							$result[$key] = call_user_func_array(__FUNCTION__, $args);
						} else {
							foreach ($value as $vkey => $vval) {
								$result[$key][$vkey] = $vval;
							}
						}
					} else {
						// simply put the value
						$result[$key] = $value;
					}
				}
			}
		}
		return $result;
	}

	/**
	* mixed safe_unserialize(string $serialized)
	* Safely unserialize, that is only unserialize string, numbers and arrays, not objects
	*
	* @license Public Domain
	* @author dcz (at) phpbb-seo (dot) com
	*/
	function safe_unserialize($serialized) {
		// unserialize will return false for object declared with small cap o
		// as well as if there is any ws between O and :
		if (is_string($serialized) && strpos($serialized, "\0") === false) {
			if (strpos($serialized, 'O:') === false) {
				// the easy case, nothing to worry about
				// let unserialize do the job
				return @unserialize($serialized);
			} else if (!preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) {
				// in case we did have a string with O: in it,
				// but it was not a true serialized object
				return @unserialize($serialized);
			}
		}
		return false;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/index.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3jquery_module1');
$SOBE->init();


// Include files?
foreach ($SOBE->include_once as $INC_FILE) {
	include_once($INC_FILE);
}


$SOBE->main();
$SOBE->printContent();

?>