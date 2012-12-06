<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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

require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');

/**
 * Class that renders fields for the extensionmanager configuration
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_t3jquery
 */
class tx_t3jquery_tsparserext
{
	/**
	 * Supported jQuery UI-Versions
	 * @var array
	 */
	private $supportedUiVersion = array();

	/**
	 * Supported jQuery Tools-Versions
	 * @var array
	 */
	private $supportedToolsVersion = array();
	
	/**
	 * Configuratio array
	 * @var array
	 */
	private $confArr = array();

	/**
	 * Shows the update Message
	 * @return	string
	 */
	public function displayMessage(&$params, &$tsObj)
	{
		$out = '';
		if (tx_t3jquery::getIntFromVersion(TYPO3_version) < 4003000) {
			// 4.3.0 comes with flashmessages styles. For older versions we include the needed styles here
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('t3jquery');
			$out .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'compat/flashmessages.css" media="screen" />';
		}
		// get all supported UI-Versions from folder
		$supportedUiVersions = t3lib_div::get_dirs(t3lib_div::getFileAbsFileName("EXT:t3jquery/res/jquery/ui/"));
		if (is_array($supportedUiVersions)) {
			foreach ($supportedUiVersions as $supportedUiVersion) {
				if (file_exists(t3lib_div::getFileAbsFileName("EXT:t3jquery/res/jquery/ui/").$supportedUiVersion.'/jquery.xml')) {
					$this->supportedUiVersion[] = $supportedUiVersion;
				}
			}
		}

		// get all supported TOOLS-Versions from folder
		$supportedToolsVersions = t3lib_div::get_dirs(t3lib_div::getFileAbsFileName("EXT:t3jquery/res/jquery/tools/"));
		if (is_array($supportedToolsVersions)) {
			foreach ($supportedToolsVersions as $supportedToolsVersion) {
				if (file_exists(t3lib_div::getFileAbsFileName("EXT:t3jquery/res/jquery/tools/").$supportedToolsVersion.'/jquery.xml')) {
					$this->supportedToolsVersion[] = $supportedToolsVersion;
				}
			}
		}

		// get the conf array
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);
		// if form is submited, the POST values are taken
		$post = t3lib_div::_POST();
		if (count($post) > 0) {
			$jQueryUiVersion    = $post['data']['jQueryUiVersion'];
			$jQueryToolsVersion = $post['data']['jQueryTOOLSVersion'];
			$jQueryVersion      = $post['data']['jQueryVersion']."-".$post['data']['jQueryUiVersion'].($post['data']['jQueryTOOLSVersion'] ? "-".$post['data']['jQueryTOOLSVersion'] : "");
			$configDir          = $post['data']['configDir'] . (preg_match("/\/$/", $configDir) ? "" : "/");
		} else {
			$jQueryUiVersion    = $this->confArr['jQueryUiVersion'];
			$jQueryToolsVersion = $this->confArr['jQueryTOOLSVersion'];
			$jQueryVersion      = T3JQUERYVERSION;
			$configDir          = tx_t3jquery::getJqPath();
		}
		if ($this->checkConfig() === FALSE) {
			$out .= '
	<div class="typo3-message message-warning">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.checkConfigHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.checkConfig') . '
		</div>
	</div>';
		} elseif ($this->confArr['integrateFromCDN'] || $post['data']['integrateFromCDN']) {
			// Nothing to check
		} else {
			// check the actual version
			if (
				$jQueryUiVersion && ! in_array($jQueryUiVersion, $this->supportedUiVersion) ||
				$jQueryToolsVersion && ! in_array($jQueryToolsVersion, $this->supportedToolsVersion)
			) {
				$out .= '
	<div class="typo3-message message-information">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.updatermsgHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.updatermsg') . '
		</div>
	</div>';
			}
			// Check if the library exists
			if (! file_exists(PATH_site . $configDir . tx_t3jquery::getJqName())) {
				$out .= '
	<a href="javascript:void();" onclick="top.goToModule(\'tools_txt3jqueryM1\',\'\',\'createLib=1\');this.blur();return false;">
		<div class="typo3-message message-warning">
			<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.updatermsgHeader2') . '</div>
			<div class="message-body">
				' . sprintf($GLOBALS['LANG']->sL('LLL:EXT:t3jquery/locallang.xml:extmng.updatermsg2'), $configDir . tx_t3jquery::getJqName()) . '
			</div>
		</div>
	</a>';
			}
		}

		$out = '<div style="position:absolute;top:10px;right:10px; width:300px;">' . $out . '</div>';
		return $out;
	}


	/**
	 * Check the config for a gifen feature
	 * 
	 * @return boolean
	 */
	private function checkConfig()
	{
		$confDefault = array(
			'alwaysIntegrate',
			'integrateToFooter',
			'enableStyleStatic',
			'dontIntegrateOnUID',
			'dontIntegrateInRootline',
			'jqLibFilename',
			'integrateFromCDN',
			'locationCDN',
			'configDir',
			'jQueryVersion',
			'jQueryUiVersion',
			'jQueryTOOLSVersion',
		);
		foreach ($confDefault as $val) {
			if (! isset($this->confArr[$val]) && ! isset($_POST['data'][$val])) {
				return FALSE;
			}
		}
		return true;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/lib/class.tx_t3jquery_tsparserext.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/lib/class.tx_t3jquery_tsparserext.php']);
}
?>