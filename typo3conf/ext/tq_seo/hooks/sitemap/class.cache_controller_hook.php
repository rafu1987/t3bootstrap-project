<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
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

// Load interface (WHY do we need this? sometimes it will break the BE)
require_once PATH_typo3.'interfaces/interface.backend_cacheActionsHook.php';

/**
 * Seo Sitemapo backend cache action hook
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.cache_controller_hook.php 49810 2011-07-14 14:24:09Z mblaschke $
 */
class tx_tqseo_sitemap_cache_controller_hook implements backend_cacheActionsHook {
	public function manipulateCacheActions(&$cacheActions, &$optionValues) {
		if( $GLOBALS['BE_USER']->isAdmin() ) {
			// Add new cache menu item
			$title = $GLOBALS['LANG']->sL('LLL:EXT:tq_seo/hooks/sitemap/locallang.xml:clearSeoSitemap');
			$cacheActions[] = array(
					'id'    => 'clearRTECache',
					'title' => $title,
					'href'  => $GLOBALS['BACK_PATH'] . 'ajax.php?ajaxID=tx_tqseo_sitemap::clearSeoSitemap',
					'icon'  => '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,t3lib_extMgm::extRelPath('tq_seo').'res/images/clear-sitemap.png', 'width="16" height="16"').' title="'.$title.'" alt="'.$title.'" />'
			);
			$optionValues[] = 'clearSeoSitemap';
		}
	}
}

?>