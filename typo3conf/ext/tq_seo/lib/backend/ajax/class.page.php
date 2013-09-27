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
require_once PATH_t3lib.'class.t3lib_pagetree.php';

/**
 * TYPO3 Backend ajax module page
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class tx_tqseo_backend_ajax_page extends tx_tqseo_backend_ajax_base {

	###########################################################################
	# Attributes
	###########################################################################

	/**
	 * List of page uids which have templates
	 *
	 * @var	array
	 */
	protected $_templatePidList = array();

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Return overview entry list for root tree
	 *
	 * @return	array
	 */
	protected function _executeGetList() {
		global $TYPO3_DB, $BE_USER;

		// Init
		$list = array();

		$pid			= (int)$this->_postVar['pid'];
		$offset			= (int)$this->_postVar['start'];
		$limit			= (int)$this->_postVar['limit'];
		$itemsPerPage	= (int)$this->_postVar['pagingSize'];
		$depth			= (int)$this->_postVar['depth'];
		$listType		= (string)$this->_postVar['listType'];

		if( !empty($pid) ) {
			$page = t3lib_BEfunc::getRecord('pages', $pid);

			$fieldList = array();

			switch($listType) {
				case 'metadata':
					$fieldList = array_merge($fieldList, array(
						'keywords',
						'description',
						'abstract',
						'author',
						'author_email',
						'lastupdated',
					));

					$list = $this->_listDefaultTree($page, $depth, $fieldList);

					unset($row);
					foreach($list as &$row) {
						if( !empty($row['lastupdated']) ) {
							$row['lastupdated'] = date('Y-m-d', $row['lastupdated']);
						} else {
							$row['lastupdated'] = '';
						}
					}
					unset($row);

					break;

				case 'searchengines':
					$fieldList = array_merge($fieldList, array(
						'tx_tqseo_canonicalurl',
						'tx_tqseo_is_exclude',
						'tx_tqseo_priority',
					));

					$list = $this->_listDefaultTree($page, $depth, $fieldList);
					break;

				case 'url':
					$fieldList = array_merge($fieldList, array(
						'title',
						'url_scheme',
						'alias',
						'tx_realurl_pathsegment',
						'tx_realurl_pathoverride',
						'tx_realurl_exclude',
					));

					$list = $this->_listDefaultTree($page, $depth, $fieldList);
					break;

				case 'pagetitle':
					$fieldList = array_merge($fieldList, array(
						'tx_tqseo_pagetitle',
						'tx_tqseo_pagetitle_rel',
						'tx_tqseo_pagetitle_prefix',
						'tx_tqseo_pagetitle_suffix',
					));

					$list = $this->_listDefaultTree($page, $depth, $fieldList);
					break;

				case 'pagetitlesim':
					$buildTree = false;
					$list = $this->_listPageTitleSim($page, $depth);
					break;

				default:
					// Not defined
					return;
					break;
			}
		}

		$ret = array(
			'results'	=> count($list),
			'rows'		=> array_values($list),
		);

		return $ret;
	}

	/**
	 * Generate simulated title for one page
	 *
	 * @return	string
	 */
	protected function _executeGenerateSimulatedTitle() {
		global $TYPO3_DB, $BE_USER;

		// Init
		$ret = '';

		$pid = (int)$this->_postVar['pid'];

		if( !empty($pid) ) {
			$page = t3lib_BEfunc::getRecord('pages', $pid);

			if( !empty($page) ) {
				// Load TYPO3 classes
				// TODO: check if this is needed anymore with autoloading
				require_once PATH_t3lib.'class.t3lib_page.php';
				require_once PATH_t3lib.'class.t3lib_tstemplate.php';
				require_once PATH_t3lib.'class.t3lib_tsparser_ext.php';
				require_once dirname(__FILE__).'/../../class.pagetitle.php';

				$this->_initTsfe($page, null, $page, null);

				$pagetitle = new user_tqseo_pagetitle();
				$ret = $pagetitle->main($page['title']);
			}
		}

		$ret = array(
			'title' => $ret,
		);

		return $ret;
	}

	/**
	 * Generate simulated title for one page
	 *
	 * @return	string
	 */
	protected function _executeGenerateSimulatedUrl() {
		global $TYPO3_DB, $BE_USER, $TSFE, $TYPO3_CONF_VARS, $LANG;

		// Init
		$ret = '';

		$pid = (int)$this->_postVar['pid'];

		if( !empty($pid) ) {
			$page = t3lib_BEfunc::getRecord('pages', $pid);

			if( !empty($page) ) {

				if( t3lib_extMgm::isLoaded('realurl') ) {
					// Disable caching for url
					$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['enableUrlDecodeCache'] = 0;
					$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['enableUrlEncodeCache'] = 0;
					$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['disablePathCache'] = 1;
				}

				// TODO: check if this is needed anymore with autoloading
				require_once PATH_t3lib.'class.t3lib_page.php';
				require_once PATH_t3lib.'class.t3lib_tstemplate.php';
				require_once PATH_t3lib.'class.t3lib_tsparser_ext.php';

				$this->_initTsfe($page, null, $page, null);

				$ret = $TSFE->cObj->typolink_URL( array('parameter' => $page['uid']) );

				if( !empty($ret) ) {
					$ret = tx_tqseo_tools::fullUrl($ret);
				}
			}
		}

		if( !empty($ret) ) {
			$ret = array(
				'url' => $ret,
			);
		} else {
			$ret = array(
				'error' => $LANG->getLL('error_url_generation_failed'),
			);
		}

		return $ret;
	}


	/**
	 * Return default tree
	 *
	 * @param	array	$page		Root page
	 * @param	integer	$depth		Depth
	 * @param	array	$fieldList	Field list
	 * @return	array
	 */
	protected function _listDefaultTree($page, $depth, $fieldList) {
		global $BE_USER;

		$rootPid = $page['uid'];

		$list = array();

		$fieldList[] = 'pid';

		// Init tree
		$tree = t3lib_div::makeInstance('t3lib_pageTree');
		foreach($fieldList as $field) {
			$tree->addField($field, true);
		}
		$tree->init('AND doktype IN (1,4) AND '.$BE_USER->getPagePermsClause(1));

		$tree->tree[] = array(
			'row'			=> $page,
			'invertedDepth'	=> 0,
		);

		$tree->getTree($rootPid, $depth, '');


		// Build tree list
		foreach($tree->tree as $row) {
			$tmp = $row['row'];
			$list[ $tmp['uid'] ] = $tmp;
		}

		// Calc depth

		$rootLineRaw = array();
		foreach($list as $row) {
			$rootLineRaw[ $row['uid'] ] = $row['pid'];
		}

		$rootLineRaw[$rootPid] = null;

		unset($row);
		foreach($list as &$row) {
			$row['_depth'] = $this->_listCalcDepth($row['uid'], $rootLineRaw);
		}
		unset($row);


		return $list;
	}

	/**
	 * Calculate the depth of a page
	 *
	 * @param	integer	$pageUid		Page UID
	 * @param	array	$rootLineRaw	Root line (raw list)
	 * @param	integer	$depth			Current depth
	 * @return	integer
	 */
	protected function _listCalcDepth($pageUid, $rootLineRaw, $depth = null) {

		if( $depth === null ) {
			$depth = 1;
		}

		if( empty($rootLineRaw[$pageUid]) ) {
			// found root page
			return $depth;
		}

		// we must be at least in the first depth
		++$depth;

		$pagePid = $rootLineRaw[$pageUid];

		if( !empty($pagePid) ) {
			// recursive
			$depth = $this->_listCalcDepth($pagePid, $rootLineRaw, $depth);
		}


		return $depth;
	}


	/**
	 * Return simulated page title
	 *
	 * @param	array	$page		Root page
	 * @param	integer	$depth		Depth
	 * @return	array
	 */
	protected function _listPageTitleSim($page, $depth) {
		global $TYPO3_DB, $BE_USER;

		// Init
		$list = array();

		$pid = $page['uid'];

		$fieldList = array(
			'title',
			'tx_tqseo_pagetitle',
			'tx_tqseo_pagetitle_rel',
			'tx_tqseo_pagetitle_prefix',
			'tx_tqseo_pagetitle_suffix',
		);

		$list = $this->_listDefaultTree($page, $depth, $fieldList);

		// Load TYPO3 classes
		// TODO: check if this is needed anymore with autoloading
		require_once PATH_t3lib.'class.t3lib_page.php';
		require_once PATH_t3lib.'class.t3lib_tstemplate.php';
		require_once PATH_t3lib.'class.t3lib_tsparser_ext.php';
		require_once dirname(__FILE__).'/../../class.pagetitle.php';

		$uidList = array_keys($list);

		if( !empty($uidList) ) {
			// Check which pages have templates (for caching and faster building)
			$this->_templatePidList = array();

			$query = 'SELECT pid
						FROM sys_template
					   WHERE pid IN ('.implode(',', $uidList).')
					     AND deleted = 0
					     AND hidden = 0';
			$res = $TYPO3_DB->sql_query($query);
			while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
				$this->_templatePidList[ $row['pid'] ] = $row['pid'];
			}

			// Build simulated title
			foreach($list as &$row) {
				$row['title_simulated'] = $this->_simulateTitle($row);
			}
		}

		return $list;
	}

	/**
	 * Generate simluated page title
	 *
	 * @param	array	$page	Page
	 * @return	string
	 */
	protected function _simulateTitle($page) {
		$this->_initTsfe($page, null, $page, null);

		$pagetitle = new user_tqseo_pagetitle();
		$ret = $pagetitle->main($page['title']);

		return $ret;
	}


	/**
	 * Init TSFE (for simulated pagetitle)
	 *
	 * @param	array		$page		Page
	 * @param	null|array	$rootLine	Rootline
	 * @param	null|array	$pageData	Page data (recursive generated)
	 * @return	void
	 */
	protected function _initTsfe($page, $rootLine = null, $pageData = null, $rootlineFull = null) {
		global $TYPO3_CONF_VARS;

		static $cacheTSFE		= array();
		static $lastTsSetupPid	= null;

		$pageUid = (int)$page['uid'];

		// create time tracker if needed
		if( empty($GLOBALS['TT']) ) {
			$GLOBALS['TT'] = t3lib_div::makeInstance('t3lib_timeTrack');
			$GLOBALS['TT']->start();
		}

		if($rootLine === null) {
			$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
			$rootLine = $sysPageObj->getRootLine( $pageUid );

			// save full rootline, we need it in TSFE
			$rootlineFull = $rootLine;
		}

		// check if current page has a ts-setup-template
		// if not, we go down the tree to the parent page
		if( count($rootLine) >= 2 && !empty($this->_templatePidList) && empty($this->_templatePidList[$pageUid]) ) {
			// go to parent page in rootline
			reset($rootLine);
			next($rootLine);
			$prevPage = current($rootLine);

			// strip current page from rootline
			reset($rootLine);
			$currPageIndex = key($rootLine);
			unset( $rootLine[$currPageIndex] );

			return $this->_initTsfe($prevPage, $rootLine, $pageData, $rootlineFull);
		}

		// Only setup tsfe if current instance must be changed
		if( $lastTsSetupPid !== $pageUid ) {

			// Cache TSFE if possible to prevent reinit (is still slow but we need the TSFE)
			if( empty($cacheTSFE[$pageUid]) ) {
				$TSFE = t3lib_div::makeInstance('tslib_fe',  $TYPO3_CONF_VARS);
				$TSFE->cObj = t3lib_div::makeInstance('tslib_cObj');

				$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
				$TSObj->tt_track = 0;
				$TSObj->init();
				$TSObj->runThroughTemplates($rootLine);
				$TSObj->generateConfig();

				$_GET['id'] = $page['uid'];
				$TSFE->initFEuser();
				$TSFE->determineId();

				$TSFE->tmpl->setup = $TSObj->setup;
				$TSFE->initTemplate();
				$TSFE->getConfigArray();

				$TSFE->baseUrl = $TSFE->config['config']['baseURL'];

				$cacheTSFE[$pageUid] = $TSFE;
			}

			$GLOBALS['TSFE'] = $cacheTSFE[$pageUid];

			$lastTsSetupPid = $pageUid;
		}

		$GLOBALS['TSFE']->page = $pageData;
		$GLOBALS['TSFE']->rootLine = $rootlineFull;
		$GLOBALS['TSFE']->cObj->data = $pageData;
	}

	/**
	 * Update page field
	 */
	protected function _executeUpdatePageField() {
		global $TYPO3_DB, $BE_USER, $LANG;

		if( empty($this->_postVar['pid'])
			|| empty($this->_postVar['field']) ) {
			return;
		}

		$pid			= (int)$this->_postVar['pid'];
		$fieldName		= strtolower( (string)$this->_postVar['field'] );
		$fieldValue		= (string)$this->_postVar['value'];

		// validate field name
		$fieldName = preg_replace('/[^-_a-zA-Z0-9]/i', '', $fieldName);

		if( empty($fieldName) ) {
			return;
		}

		###############################
		# Security checks
		###############################


		// check if user is able to modify pages
		if( !$BE_USER->check('tables_modify','pages') ) {
			// No access
			return array(
				'error'	=> $LANG->getLL('error_access_denied').' [0x4FBF3BE2]',
			);
		}

		// check if user is able to modify the field of pages
		if( !$BE_USER->check('non_exclude_fields', 'pages:'.$fieldName) ) {
			// No access
			return array(
				'error'	=> $LANG->getLL('error_access_denied').' [0x4FBF3BD9]',
			);
		}

		$page = t3lib_BEfunc::getRecord('pages', $pid);

		// check if page exists and user can edit this specific record
		if( empty($page) || !$BE_USER->doesUserHaveAccess($page,2) ) {
			// No access
			return array(
				'error'	=> $LANG->getLL('error_access_denied').' [0x4FBF3BCF]',
			);
		}

		###############################
		# Transformations
		###############################

		switch($fieldName) {
			case 'lastupdated':
				// transform to unix timestamp
				$fieldValue = strtotime($fieldValue);
				break;
		}


		###############################
		# Update
		###############################

		// Update field in page (also logs update event and clear cache for this page)
		$this->_tce()->updateDB(
			'pages',
			(int)$pid,
			array(
				$fieldName => $fieldValue
			)
		);
	}

}

?>