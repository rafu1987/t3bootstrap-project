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

/**
 * SeoSitemap access
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.cache_controller.php 49810 2011-07-14 14:24:09Z mblaschke $
 */
class tx_tqseo_sitemap_cache_controller {
	public function clearSeoSitemap() {
		global $TYPO3_DB;
		
		$ret = true;

		// Call hook
		tx_tqseo_tools::callHook('sitemap-clear', $this, $ret);
		
		$query = 'TRUNCATE tx_tqseo_sitemap';
		$TYPO3_DB->sql_query($query);
		
		return $ret;
	}
}

?>