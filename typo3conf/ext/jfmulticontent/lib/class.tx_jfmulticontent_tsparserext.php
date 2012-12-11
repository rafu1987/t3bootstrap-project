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

/**
 * Class that renders fields for the extensionmanager configuration
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_tsparserext
{
	/**
	 * Shows the update Message
	 *
	 * @return	string
	 */
	function displayMessage(&$params, &$tsObj)
	{
		$out = '';

		if (class_exists(t3lib_utility_VersionNumber) && t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) < 4003000) {
			// 4.3.0 comes with flashmessages styles. For older versions we include the needed styles here
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('jfmulticontent');
			$out .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'compat/flashmessages.css" media="screen" />';
		}

		$checkConfig = NULL;
		if ($this->checkConfig() === FALSE) {
			$checkConfig = '
	<div class="typo3-message message-warning">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang.xml:extmng.classInnerHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang.xml:extmng.classInner') . '
		</div>
	</div>';
		}

		if (class_exists(t3lib_utility_VersionNumber) && t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) < 4005000) {
			$url = 'index.php?&amp;id=0&amp;CMD[showExt]=jfmulticontent&amp;SET[singleDetails]=updateModule';
		} else {
			$url = 'mod.php?&id=0&M=tools_em&CMD[showExt]=jfmulticontent&SET[singleDetails]=updateModule';
		}

		$out .= '
<div style="position:absolute;top:10px;right:10px; width:300px;">
	<div class="typo3-message message-information">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang.xml:extmng.updatermsgHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang.xml:extmng.updatermsg') . '<br />
			<a style="text-decoration:underline;" href="' . $url . '">
			' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang.xml:extmng.updatermsgLink') . '</a>
		</div>
	</div>
	' . $checkConfig . '
</div>';

		return $out;
	}

	/**
	 * Check the config for a gifen feature
	 * 
	 * @return boolean
	 */
	function checkConfig()
	{
		$confDefault = array(
			'useStoragePidOnly',
			'ttNewsCodes',
			'useSelectInsteadCheckbox',
			'useOwnUserFuncForPages',
			'openExternalLink',
			'showEmptyContent',
			'addBrowseLinks',
			'tabSelectByHash',
			'colPosOfIrreContent',
			'style.',
			'classInner',
			'frontendErrorMsg',
			'anythingSliderThemeFolder',
			'anythingSliderModes',
			'easyAccordionSkinFolder',
		);
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		foreach ($confDefault as $val) {
			if (! isset($confArr[$val]) && ! isset($_POST['data'][$val])) {
				return FALSE;
			}
		}
		return TRUE;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tsparserext.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tsparserext.php']);
}
?>