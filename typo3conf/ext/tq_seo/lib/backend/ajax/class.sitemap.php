<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 TEQneers GmbH & Co. KG <info@teqneers.de>
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

/**
 * TYPO3 Backend ajax module sitemap
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class tx_tqseo_backend_ajax_sitemap extends tx_tqseo_backend_ajax_base {

	/**
	 * Return sitemap entry list for root tree
	 *
	 * @return	array
	 */
	protected function _executeGetList() {
		global $TYPO3_DB;

		// Init
		$rootPageList = tx_tqseo_backend_tools::getRootPageList();

		$rootPid		= (int)$this->_postVar['pid'];
		$offset			= (int)$this->_postVar['start'];
		$limit			= (int)$this->_postVar['limit'];
		$itemsPerPage	= (int)$this->_postVar['pagingSize'];

		$searchFulltext			= trim( (string)$this->_postVar['criteriaFulltext'] );
		$searchPageUid			= trim( (int)$this->_postVar['criteriaPageUid'] );
		$searchPageLanguage		= trim( (string)$this->_postVar['criteriaPageLanguage'] );
		$searchPageDepth		= trim( (string)$this->_postVar['criteriaPageDepth'] );

		###############################
		# Critera
		###############################
		$where = array();

		// Root pid limit
		$where[] = 'page_rootpid = '.(int)$rootPid;

		// Fulltext
		if( !empty($searchFulltext) ) {
			$where[] = 'page_url LIKE '.$TYPO3_DB->fullQuoteStr('%'.$searchFulltext.'%', 'tx_tqseo_sitemap');
		}

		// Page id
		if( !empty($searchPageUid) ) {
			$where[] = 'page_uid = '.(int)$searchPageUid;
		}

		// Lannguage
		if( $searchPageLanguage != -1 && strlen($searchPageLanguage) >= 1 ) {
			$where[] = 'page_language = '.(int)$searchPageLanguage;
		}

		// Depth
		if( $searchPageDepth != -1 && strlen($searchPageDepth) >= 1 ) {
			$where[] = 'page_depth = '.(int)$searchPageDepth;
		}

		// Build where
		$where = '( '.implode(' ) AND ( ', $where).' )';

		###############################
		# Pager
		###############################

		// Fetch total count of items with this filter settings
		$res = $TYPO3_DB->exec_SELECTquery(
			'COUNT(*) as count',
			'tx_tqseo_sitemap',
			$where
		);
		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$itemCount = $row['count'];

		###############################
		# Sort
		###############################
		// default sort
		$sort = 'page_depth ASC, page_uid ASC';

		if( !empty($this->_sortField) && !empty($this->_sortDir) ) {
			// already filered
			$sort = $this->_sortField.' '.$this->_sortDir;
		}

		###############################
		# Fetch sitemap
		###############################
		$list = $TYPO3_DB->exec_SELECTgetRows(
			'uid,
			 page_rootpid,
			 page_uid,
			 page_language,
			 page_url,
			 page_depth,
			 FROM_UNIXTIME(tstamp) as tstamp,
			 FROM_UNIXTIME(crdate) as crdate',
			'tx_tqseo_sitemap',
			$where,
			'',
			$sort,
			$offset.', '.$itemsPerPage
		);

		$ret = array(
			'results'	=> $itemCount,
			'rows'		=> $list,
		);

		return $ret;
	}

	/**
	 * Delete sitemap entries
	 *
	 * @return	array
	 */
	protected function _executeDelete() {
		global $TYPO3_DB;

		$ret = false;

		$uidList		= $this->_postVar['uidList'];
		$rootPid		= (int)$this->_postVar['pid'];

		$uidList		= $TYPO3_DB->cleanIntArray($uidList);

		if( empty($uidList) || empty($rootPid) ) {
			return false;
		}

		$where = array();
		$where[] = 'page_rootpid = '.(int)$rootPid;
		$where[] = 'uid IN ('.implode(',',$uidList).')';
		$where = '( '.implode(' ) AND ( ', $where).' )';

		$res = $TYPO3_DB->exec_DELETEquery(
			'tx_tqseo_sitemap',
			$where
		);

		if( $res ) {
			$ret = true;
		}

		return $ret;
	}

}

?>