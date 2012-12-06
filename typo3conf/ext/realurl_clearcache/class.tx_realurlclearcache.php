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
 *   44: class tx_realurlclearcache
 *   50:     public static function clear()
 *
 * TOTAL FUNCTIONS: 1
 */

/**
 * Main class file for the 'RealURL: Clear cache' extension
 *
 * @author Sebastiaan de Jonge (SebastiaanDeJonge.com) <szebi.eger@gmail.com>
 * @package TYPO3
 * @subpackage tx_realurlclearcache
 */
class tx_realurlclearcache {
	/**
	 * Clears the actual RealURL cache tables inside the database (doesn't clear loggin tables)
	 *
	 * @return	void
	 */
	public static function clear() {
		$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE tx_realurl_chashcache;');
		$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE tx_realurl_pathcache;');
		$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE tx_realurl_uniqalias;');
		$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE tx_realurl_urldecodecache;');
		$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE tx_realurl_urlencodecache;');
	}
	
	/**
	 * Checks if this code is executed on the DB list page, returns false otherwise.
	 * 
	 * @return bool
	 */
	public static function isInsideDbList() {
		$s_scriptName = t3lib_div::getIndpEnv('SCRIPT_NAME');
		$i_pathLenght = strlen($s_scriptName);
		if(substr($s_scriptName,$i_pathLength-11) == 'db_list.php') return true;
		return false;
	}
	
	/**
	 * Adds the icons for clearing a single pages' RealURL caches
	 * 
	 * @param array $a_params
	 * @param object $o_parent
	 * @return void
	 */
	public function pageIcon(&$a_params,$o_parent) {
		// Check if we are actually inside the list view, we don't want the icon to end 
		// up inside the page tree list
		if(!self::isInsideDbList()) return;
		
		// Clear cache on command 
		if(t3lib_div::_GET('realurl_clearcache') == 'page') {
			$this->clearPageCache();
		}
		
		// Seperate all links
		$s_buttonsMarker = str_replace(
			array(
				'</a>',
				'<a ',
			),
			array(
				'</a>|',
				'|<a ',
			),
			$a_params['markers']['BUTTONLIST_RIGHT']
		);
		$a_seperatedItems = t3lib_div::trimExplode('|',$s_buttonsMarker);
		
		// Check if there are any link items, if not we will not add ours either
		if(count($a_seperatedItems) <= 1) return;
		
		// Generate cache clearing URL
		$s_clearCacheCmdUri = t3lib_div::getIndpEnv('SCRIPT_NAME');
		$a_queryStringParts = t3lib_div::trimExplode('&',t3lib_div::getIndpEnv('QUERY_STRING'));
		if(t3lib_div::_GET('realurl_clearcache') != 'page') $a_queryStringParts[] = 'realurl_clearcache=page';
		$s_clearCacheCmdUri .= '?'.implode('&',$a_queryStringParts);
		
		// Generate our own link
		$s_title = $GLOBALS['LANG']->sL('LLL:EXT:realurl_clearcache/locallang.xml:rm.clearRealUrlPageCache', true);
		$s_imagePath = t3lib_extMgm::extRelPath('realurl_clearcache').'res/';
		if(strpos($s_imagePath,'typo3conf') !== false) $s_imagePath = '../'.$s_imagePath;
		$s_image = '<img src="'.$s_imagePath.'be_page_icon.gif" title="'.$s_title.'" alt="'.$s_title.'" />';
		$s_pageIconLink = '<a href="'.$s_clearCacheCmdUri.'">'.$s_image.'</a>';
		$a_seperatedItems[2] = $s_pageIconLink;
		
		$a_params['markers']['BUTTONLIST_RIGHT'] = implode('',$a_seperatedItems);
	}
	
	/**
	 * This will clear only the page related RealURL cache, unique aliasses will not
	 * be clear, since is there no way to absolutely determine what is exactly displayed on a 
	 * page. Instead, when a SysFolder is selected, the unique aliasses of the records found 
	 * in that SysFolder will be removed.
	 * 
	 * @return void
	 * @todo Add cache clearing of unique aliasses
	 */
	public function clearPageCache() {
		// Initialize
		$i_pid = intval(t3lib_div::_GET('id'));
		
		// Clear RealURL cache tables
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_realurl_pathcache',
			'page_id = '.$i_pid
		);
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_realurl_urldecodecache',
			'page_id = '.$i_pid
		);
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_realurl_urlencodecache',
			'page_id = '.$i_pid
		);
		
		// Get page properties
		$a_pageData = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'doktype',
			'pages',
			'uid = '.$i_pid
		);
		
		// Process page, if page is found and if it's a SysFolder
		if($a_pageData[0]['doktype'] == 254) {
			// First we need to get all tables that have records stored on this page, and 
			// that could unique aliasses. Since looping through all tables in the TCA 
			// takes simply too much time, we will use a more clever aproach. We will first 
			// select the tables that are stored inside the tx_realurl_uniqalias table.
			$a_availableTables = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'DISTINCT tablename, field_id',
				'tx_realurl_uniqalias',
				'1=1'
			);
			
			// Find all records for this table that match our criteria
			foreach($a_availableTables as $a_tableData) {
				// First we get all the records found in tx_realurl_uniqalias matching $a_tableData[tablename]
				$a_dataRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					$a_tableData['field_id'],
					$a_tableData['tablename'],
					'pid = '.$i_pid
				);
				
				// To keep some speed, limit each delete query to have 10 list items
				$i_itemLimit = 10;
				$i_totalItems = count($a_dataRecords);
				$a_deleteBatches = array();
				$a_batchData = array();
				
				// Create the actual batches
				foreach($a_dataRecords as $a_dataRecord) {
					$a_batchData[] = $a_dataRecord['uid'];
					
					// If the batch is full, add it and create a new one
					if(count($a_batchData) == $i_itemLimit) {
						$a_deleteBatches[] = "'".implode("','",$a_batchData)."'";
						$a_batchData = array();
					}
				}
				$a_deleteBatches[] = "'".implode("','",$a_batchData)."'";
				
				// Loop through the batches to delete the actual data
				foreach($a_deleteBatches as $s_recordList) {
					$GLOBALS['TYPO3_DB']->exec_DELETEquery(
						'tx_realurl_uniqalias',
						'value_id IN ('.$s_recordList.') AND tablename = \''.$a_tableData['tablename'].'\' AND field_id = \''.$a_tableData['field_id'].'\''
					);
				}
			}
		}
	}
}

/**
 * XCLASS Inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl_clearcache/class.tx_realurlclearcache.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl_clearcache/class.tx_realurlclearcache.php']);
}
?>