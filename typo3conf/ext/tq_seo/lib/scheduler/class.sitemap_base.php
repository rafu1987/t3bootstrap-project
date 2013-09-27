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
 * Scheduler Task Sitemap Base
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.sitemap_base.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
abstract class tx_tqseo_scheduler_task_sitemap_base extends tx_scheduler_task {

	###########################################################################
	# Attributes
	###########################################################################

	/**
	 * Sitemap base directory
	 *
	 * @var string
	 */
	protected $_sitemapDir = null;

	/**
	 * Language lock
	 *
	 * @var	integer
	 */
	protected $_languageLock = false;

	/**
	 * Language list
	 *
	 * @var	array
	 */
	protected $_languageIdList = null;

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Execute task
	 */
	public function execute() {
		// Build sitemap

		$rootPageList = $this->_getRootPages();

		$this->_cleanupDirectory();

		$this->_initLanguages();


		foreach($rootPageList as $uid => $page) {
			$this->_initRootPage($uid);


			if( tx_tqseo_tools::getRootSettingValue('is_sitemap_language_lock', false, $uid) ) {
				foreach($this->_languageIdList as $languageId) {
					$this->_setRootPageLanguage($languageId);
					$this->_buildSitemap($uid, $languageId);
				}
			} else {
				$this->_buildSitemap($uid, null);
			}
		}

		return true;
	}

	/**
	 * Get list of root pages in current typo3
	 *
	 * @return	array
	 */
	protected function _getRootPages() {
		global $TYPO3_DB;

		$ret = array();

		$query = 'SELECT uid
					FROM pages
				   WHERE is_siteroot = 1
					  AND deleted = 0';
		$res = $TYPO3_DB->sql_query($query);

		while($row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$uid = $row['uid'];
			$ret[$uid] = $row;
		}

		return $ret;
	}


	/**
	 * Get list of root pages in current typo3
	 *
	 * @return	array
	 */
	protected function _initLanguages() {
		global $TYPO3_DB;

		$this->_languageIdList[0] = 0;

		$query = 'SELECT uid
					FROM sys_language
				   WHERE hidden = 0';
		$res = $TYPO3_DB->sql_query($query);

		while($row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$uid = $row['uid'];
			$this->_languageIdList[$uid] = $uid;
		}

		return $ret;
	}

	/**
	 * Set root page language
	 *
	 */
	protected function _setRootPageLanguage($languageId) {
		global $TSFE;

		$TSFE->tmpl->setup['config.']['sys_language_uid'] = $languageId;
		$this->_languageLock = $languageId;
	}

	/**
	 * Initalize root page (TSFE and stuff)
	 *
	 * @param	integer	$rootPageId	$rootPageId
	 */
	protected function _initRootPage($rootPageId) {
		global $TT, $TSFE;

		$TT		= null;
		$TSFE	= null;

		$TT = new t3lib_timeTrackNull;
		$TSFE = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $rootPageId, 0);
		$TSFE->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$TSFE->sys_page->init(TRUE);
		$TSFE->initTemplate();
		$TSFE->rootLine = $TSFE->sys_page->getRootLine($rootPageId, '');
		$TSFE->getConfigArray();
		$TSFE->cObj = new tslib_cObj();

		// TSFE Init
		if( !empty($TSFE->config['config']['baseURL']) ) {
			$TSFE->baseUrl = $TSFE->config['config']['baseURL'];
		}

		if( !empty($TSFE->config['config']['absRefPrefix']) ) {
			$TSFE->absRefPrefix = $TSFE->config['config']['absRefPrefix'];
		}
	}

	/**
	 * Cleanup sitemap directory
	 */
	protected function _cleanupDirectory() {
		if( empty($this->_sitemapDir) ) {
			throw new Exception('Basedir not set');
		}

		$fullPath = PATH_site.'/'.$this->_sitemapDir;

		if( !is_dir($fullPath) ) {
			t3lib_div::mkdir($fullPath);
		}

		foreach( new DirectoryIterator($fullPath) as $file) {
			if( $file->isFile() && !$file->isDot() ) {
				$fileName = $file->getFilename();
				unlink( $fullPath.'/'.$fileName );
			}
		}
	}

	/**
	 * Generate sitemap link template
	 *
	 * @param	string	$template	File link template
	 * @return	string
	 */
	protected function _generateSitemapLinkTemplate($template) {
		global $TSFE;

		$ret = null;

		// Set link template for index file
		$linkConf = array(
			'parameter'			=> $this->_sitemapDir.'/'.$template,
		);
		if( strlen($TSFE->baseUrl) > 1 ) {
			$ret = $TSFE->baseUrlWrap( $TSFE->cObj->typoLink_URL($linkConf) );
		} elseif( strlen($TSFE->absRefPrefix) > 1 ) {
			$ret = $TSFE->absRefPrefix.$TSFE->cObj->typoLink_URL($linkConf);
		} else {
			$ret = $TSFE->cObj->typoLink_URL($linkConf);
		}

		return $ret;
	}

	/**
	 * Write content to file
	 *
	 * @param	string	$file		Filename/path
	 * @param	string	$content	Content
	 */
	protected function _writeToFile($file, $content) {
		if( !function_exists('gzopen') ) {
			throw new Exception('tq_seo needs zlib support');
		}

		$fp = gzopen($file, 'w');

		if( $fp ) {
			gzwrite($fp,$content);
			gzclose($fp);
		} else {
			throw new Exception('Could not open '.$file.' for writing');
		}

	}

	###########################################################################
	# Abstract Methods
	###########################################################################

	/**
	 * Build sitemap
	 *
	 * @param	integer	$rootPageId	Root page id
	 * @param	integer	$languageId	Language id
	 */
	abstract protected function _buildSitemap($rootPageId, $languageId);

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/scheduler/class.sitemap_xml.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/scheduler/class.sitemap_xml.php']);
}
?>
