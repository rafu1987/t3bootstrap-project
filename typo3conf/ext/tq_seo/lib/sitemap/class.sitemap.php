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
 * Sitemap
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.sitemap.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class tx_tqseo_sitemap {

	###########################################################################
	# Public methods
	###########################################################################

	/**
	 * Insert into sitemap
	 *
	 * @param	array	$pageData	Page informations
	 * @param	string	$type		Parser type (page/link)
	 */
	public static function index($pageData, $type) {
		global $TYPO3_DB;
		static $cache = array();

		$pageHash = $pageData['page_hash'];

		// Escape/Quote data
		unset($pageDataValue);
		foreach($pageData as &$pageDataValue) {
			if($pageDataValue === NULL) {
				$pageDataValue = 'NULL';
			} elseif( is_int($pageDataValue) || is_numeric($pageDataValue) ) {
				// Don't quote numeric/integers
				$pageDataValue = (int)$pageDataValue;
			} else {
				// String
				$pageDataValue = $TYPO3_DB->fullQuoteStr($pageDataValue, 'tx_tqseo_sitemap');
			}
		}
		unset($pageDataValue);

		// only process each page once to keep sql-statements at a normal level
		if( empty($cache[$pageHash]) ) {

			// $pageData is already quoted

			$query = 'SELECT
							uid
						FROM
							tx_tqseo_sitemap
						WHERE
								page_uid = '.$pageData['page_uid'].'
							AND	page_language = '.$pageData['page_language'].'
							AND page_hash = '.$pageData['page_hash'];
			$res = $TYPO3_DB->sql_query($query);

			if( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
				$query = 'UPDATE
								tx_tqseo_sitemap
							SET
								tstamp					= '.$pageData['tstamp'].',
								page_rootpid			= '.$pageData['page_rootpid'].',
								page_language			= '.$pageData['page_language'].',
								page_url				= '.$pageData['page_url'].',
								page_depth				= '.$pageData['page_depth'].',
								page_change_frequency	= '.$pageData['page_change_frequency'].'
							WHERE
								uid = '.(int)$row['uid'];
				$TYPO3_DB->sql_query($query);
			} else {
				#####################################
				# INSERT
				#####################################
				$ret = $TYPO3_DB->exec_INSERTquery(
					'tx_tqseo_sitemap',
					$pageData,
					array_keys($pageData)
				);
			}

			$cache[$pageHash] = 1;
		}
	}

	/**
	 * Clear outdated and invalid pages from sitemap table
	 */
	public static function expire() {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;

		#####################
		# Expired pages
		#####################
		$expireDays = (int)tx_tqseo_tools::getExtConf('sitemap_pageSitemapExpireDays', 60);

		$tstamp = time() - $expireDays*24*60*60;

		$query = 'DELETE FROM tx_tqseo_sitemap WHERE tstamp <= '.(int)$tstamp;
		$res = $TYPO3_DB->sql_query($query);

		#####################
		# Deleted or
		# excluded pages
		#####################
		$deletedSitemapPages = array();

		$query = 'SELECT
						ts.uid
					FROM
						tx_tqseo_sitemap ts
						LEFT JOIN pages p
							ON		p.uid = ts.page_uid
								AND	p.deleted = 0
								AND p.hidden = 0
					WHERE
						p.uid IS NULL';
		$res = $TYPO3_DB->sql_query($query);

		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$deletedSitemapPages[ $row['uid'] ] = $row['uid'];
		}

		// delete pages
		if(!empty($deletedSitemapPages)) {
			$query = 'DELETE FROM tx_tqseo_sitemap WHERE uid IN ('.implode(',', $deletedSitemapPages).')';
			$TYPO3_DB->sql_query($query);
		}
	}


	/**
	 * Return list of sitemap pages
	 *
	 * @param	integer			$rootPid		Root page id of tree
	 * @param	integer			$languageId		Limit to language id
	 * @return	boolean|array					Array with table list
	 */
	public static function getList($rootPid, $languageId = null) {
		global $TYPO3_DB;

		$sitemapList	= array();
		$pageList		= array();

		$typo3Pids		= array();
		$sitemapPageId	= array();

		$query = 'SELECT ts.*
					FROM tx_tqseo_sitemap ts
							INNER JOIN pages p
							  ON	p.uid = ts.page_uid
								AND	p.deleted = 0
								AND	p.hidden = 0
								AND	p.tx_tqseo_is_exclude = 0
				   WHERE ts.page_rootpid = '.(int)$rootPid;

		if( $languageId !== null ) {
			$query .= ' AND ts.page_language = '.(int)$languageId;
		}
		$query .= ' ORDER BY
						ts.page_depth ASC,
						p.pid ASC,
						p.sorting ASC';

		$res = $TYPO3_DB->sql_query($query);

		if( !$res ) {
			return false;
		}

		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$sitemapList[] = $row;

			$sitemapPageId = $row['page_uid'];
			$typo3Pids[$sitemapPageId] = (int)$sitemapPageId;
		}

		if(!empty($typo3Pids)) {
			$query = 'SELECT *
						FROM pages
					   WHERE uid IN ('.implode(',', $typo3Pids).')';
			$res = $TYPO3_DB->sql_query($query);

			if( !$res ) {
				return false;
			}

			while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
				$pageList[ $row['uid'] ] = $row;
			}
		}


		$ret = array(
			'tx_tqseo_sitemap'	=> $sitemapList,
			'pages'				=> $pageList
		);

		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/class.sitemap.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/class.sitemap.php']);
}
?>