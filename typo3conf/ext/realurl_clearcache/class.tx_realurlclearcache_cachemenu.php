<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Sebastiaan de Jonge (SebastiaanDeJonge.com) <szebi.eger@gmail.com>
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
 *
 *
 *   45: class tx_realurlclearcache_cachemenu implements backend_cacheActionsHook
 *   54:     public function manipulateCacheActions(&$a_cacheActions, &$a_optionValues)
 *
 * TOTAL FUNCTIONS: 1
 */

require_once(PATH_typo3.'interfaces/interface.backend_cacheActionsHook.php');
require_once(t3lib_extMgm::extPath('realurl_clearcache').'class.tx_realurlclearcache.php');

/**
 * Main class file for the 'RealURL: Clear cache' extension
 *
 * @author Sebastiaan de Jonge (SebastiaanDeJonge.com) <szebi.eger@gmail.com>
 * @package TYPO3
 * @subpackage tx_realurlclearcache
 */
class tx_realurlclearcache_cachemenu implements backend_cacheActionsHook {
	/**
	 * Adds the option to clear the RealURL cache in the back-end clear cache menu.
	 *
	 * @param array $a_cacheActions
	 * @param array $a_optionValues
	 * @return void
	 * @see typo3/interfaces/backend_cacheActionsHook#manipulateCacheActions($cacheActions, $optionValues)
	 */
	public function manipulateCacheActions(&$a_cacheActions, &$a_optionValues) {
		if(($GLOBALS['BE_USER']->isAdmin() || $GLOBALS['BE_USER']->getTSConfigVal('options.clearCache.realurl')) && $GLOBALS['TYPO3_CONF_VARS']['EXT']['extCache']) {
			$s_title = $GLOBALS['LANG']->sL('LLL:EXT:realurl_clearcache/locallang.xml:rm.clearCacheMenu_realUrlClearCache', true);
			$s_imagePath = t3lib_extMgm::extRelPath('realurl_clearcache').'res/';
			if(strpos($s_imagePath,'typo3conf') !== false) $s_imagePath = '../'.$s_imagePath;
			$a_cacheActions[] = array(
				'id'    => 'realurl_cache',
				'title' => $s_title,
				'href' => 'ajax.php?ajaxID=tx_realurlclearcache::clear',
				'icon'  => '<img src="'.$s_imagePath.'be_icon.gif" title="'.$s_title.'" alt="'.$s_title.'" />',
			);
			$a_optionValues[] = 'clearCacheRealURL';
		}
	}
}

/**
 * XCLASS Inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl_clearcache/class.tx_realurlclearcache_cachemenu.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl_clearcache/class.tx_realurlclearcache_cachemenu.php']);
}
?>