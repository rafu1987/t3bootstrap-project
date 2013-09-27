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
 * Cache access
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.cache.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class tx_tqseo_cache {

	/**
	 * Get cache entry
	 *
	 * @param	integer		$pageId		Page UID
	 * @param	string		$section	Cache section
	 * @param	string		$identifier	Cache identifier
	 * @return	string
	 */
	static public function get($pageId, $section, $identifier) {
		global $TYPO3_DB;
		$ret = null;

		$query = 'SELECT cache_content FROM tx_tqseo_cache
					WHERE page_uid = '.(int)$pageId.'
					  AND cache_section = '.$TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache').'
					  AND cache_identifier = '.$TYPO3_DB->fullQuoteStr($identifier, 'tx_tqseo_cache');
		$res = $TYPO3_DB->sql_query($query);

		if( $row = $TYPO3_DB->sql_fetch_assoc($result) ) {
			$ret = $row['cache_content'];
		}

		return $ret;
	}

	/**
	 * Set cache entry
	 *
	 * @param	integer		$pageId		Page UID
	 * @param	string		$section	Cache section
	 * @param	string		$identifier	Cache identifier
	 * @param	string		$value		Cache content
	 * @return	boolean					Success
	 */
	static public function set($pageId, $section, $identifier, $value) {
		global $TYPO3_DB;

		$query = 'INSERT INTO tx_tqseo_cache (page_uid, cache_section, cache_identifier, cache_content)
					VALUES(
						'.(int)$pageId.',
						'.$TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache').',
						'.$TYPO3_DB->fullQuoteStr($identifier, 'tx_tqseo_cache').',
						'.$TYPO3_DB->fullQuoteStr($value, 'tx_tqseo_cache').'
					) ON DUPLICATE KEY UPDATE cache_content = '.$TYPO3_DB->fullQuoteStr($value, 'tx_tqseo_cache');
		$res = $TYPO3_DB->sql_query($query);

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get cache list
	 *
	 * @param	string		$section		Cache section
	 * @param	string		$identifier		Cache identifier
	 * @return	array
	 */
	static public function getList($section, $identifier) {
		global $TYPO3_DB;
		$ret = array();

		$query = 'SELECT page_uid, cache_content FROM tx_tqseo_cache
					WHERE cache_section = '.$TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache').'
					  AND cache_identifier = '.$TYPO3_DB->fullQuoteStr($identifier, 'tx_tqseo_cache');
		$res = $TYPO3_DB->sql_query($query);

		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$ret[ $row['page_uid'] ] = $row['cache_content'];
		}

		return $ret;
	}

	/**
	 * Clear cache entry
	 *
	 * @param	integer		$pageId		Page UID
	 * @param	string		$section	Cache section
	 * @param	string		$identifier	Cache identifier
	 * @return	boolean					Success
	 */
	static public function remove($pageId, $section, $identifier) {
		global $TYPO3_DB;

		$pageId			= (int)$pageId;
		$section		= $TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache');
		$identifier		= $TYPO3_DB->fullQuoteStr($identifier, 'tx_tqseo_cache');

		$query = 'DELETE FROM tx_tqseo_cache
					WHERE page_uid = '.(int)$pageId.'
					  AND cache_section = '.$TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache').'
					  AND cache_identifier = '.$TYPO3_DB->fullQuoteStr($identifier, 'tx_tqseo_cache');
		$res = $TYPO3_DB->sql_query($query);

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Clear cache by page
	 *
	 * @param	integer		$pageId		Page UID
	 * @return	boolean				Success
	 */
	static public function clearByPage($pageId) {
		global $TYPO3_DB;

		$query = 'DELETE FROM tx_tqseo_cache
					WHERE page_uid = '.(int)$pageId;
		$res = $TYPO3_DB->sql_query($query);

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Clear cache by section
	 *
	 * @param	string		$section	Cache section
	 * @return	boolean					Success
	 */
	static public function clearBySection($section) {
		global $TYPO3_DB;

		$query = 'DELETE FROM tx_tqseo_cache
					WHERE cache_section = '.$TYPO3_DB->fullQuoteStr($section, 'tx_tqseo_cache');
		$res = $TYPO3_DB->sql_query($query);

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Clear all cache
	 * 
	 * @return	boolean		Success
	 */
	static public function clearAll() {
		global $TYPO3_DB;

		$query = 'TRUNCATE tx_tqseo_cache';
		$res = $TYPO3_DB->sql_query($query);

		if( $res ) {
			return true;
		} else {
			return false;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.cache.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.cache.php']);
}
?>