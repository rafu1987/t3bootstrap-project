<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
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
 * Tools
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.tools.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class tx_tqseo_backend_tools {

	/**
	 * Fetch list of root pages (is_siteroot) in TYPO3 (cached)
	 *
	 * @return	array
	 */
	public static function getRootPageList() {
		global $TYPO3_DB;

		static $cache = null;

		if( $cache === null ) {
			$tmp = $TYPO3_DB->exec_SELECTgetRows(
				'uid, pid, title',
				'pages',
				'is_siteroot = 1 AND deleted = 0'
			);

			$cache = array();
			if( is_array($tmp) ) {
				foreach($tmp as $row) {
					$cache[ $row['uid'] ] = $row;
				}
			}
		}

		return $cache;
	}

	/**
	 * Fetch list of setting entries
	 *
	 * @return	array
	 */
	public static function getRootPageSettingList() {
		global $TYPO3_DB;

		static $cache = null;

		if( $cache === null ) {
			$cache = array();

			$query = 'SELECT seosr.*
						FROM tx_tqseo_setting_root seosr
							 INNER JOIN pages p
								ON  p.uid = seosr.pid
								AND p.is_siteroot = 1
								AND p.deleted = 0
						WHERE seosr.deleted = 0';
			$res = $TYPO3_DB->sql_query($query);

			while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
				$cache[ $row['pid'] ] = $row;
			}
		}

		return $cache;
	}

}

?>