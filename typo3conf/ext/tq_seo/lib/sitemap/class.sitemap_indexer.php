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
 * Sitemap Indexer
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.sitemap_indexer.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class user_tqseo_sitemap_indexer {

	###########################################################################
	# Attributes
	###########################################################################

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Add Page to sitemap table
	 */
	public function addPageToSitemapIndex() {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;

		// check if sitemap is enabled in root
		if( !tx_tqseo_tools::getRootSettingValue('is_sitemap', true)
			|| !tx_tqseo_tools::getRootSettingValue('is_sitemap_page_indexer', true) ) {
			return true;
		}

		// Skip non-seo-pages
		if( 	$_SERVER['REQUEST_METHOD'] !== 'GET'
			|| !empty($TSFE->fe_user->user['uid']) )  {
			return true;
		}

		// Skip own sitemap tools
		if( $TSFE->type == 841131 || $TSFE->type == 841132) {
			return true;
		}

		// Skip no_cache-pages
		if( !empty($TSFE->no_cache) ) {
			return true;
		}

		// Fetch chash
		$pageHash = NULL;
		if(!empty($TSFE->cHash)) {
			$pageHash = $TSFE->cHash;
		}

		// Fetch sysLanguage
		$pageLanguage = tx_tqseo_tools::getLanguageId();

		// Fetch page changeFrequency
		$pageChangeFrequency = 0;
		if( !empty($TSFE->page['tx_tqseo_change_frequency']) ) {
			$pageChangeFrequency = (int)$TSFE->page['tx_tqseo_change_frequency'];
		} elseif(!empty($TSFE->tmpl->setup['plugin.']['tq_seo.']['sitemap.']['changeFrequency'])) {
			$pageChangeFrequency = (int)$TSFE->tmpl->setup['plugin.']['tq_seo.']['sitemap.']['changeFrequency'];
		}

		if( empty($pageChangeFrequency) ) {
			$pageChangeFrequency = 0;
		}

		// Fetch pageUrl
		if( $pageHash !== NULL ) {
			$pageUrl = $TSFE->anchorPrefix;
		} else {
			$linkConf = array(
				'parameter'	=> $TSFE->id,
			);

			$pageUrl = $TSFE->cObj->typoLink_URL($linkConf);
			$pageUrl = self::_processLinkUrl($pageUrl);
		}

		$tstamp = $_SERVER['REQUEST_TIME'];

		$pageData = array(
			'tstamp'				=> $tstamp,
			'crdate'				=> $tstamp,
			'page_rootpid'			=> tx_tqseo_tools::getRootPid(),
			'page_uid'				=> $TSFE->id,
			'page_language'			=> $pageLanguage,
			'page_url'				=> $pageUrl,
			'page_hash'				=> md5($pageUrl),
			'page_depth'			=> count($TSFE->rootLine),
			'page_change_frequency'	=> $pageChangeFrequency,
		);

		// Call hook
		tx_tqseo_tools::callHook('sitemap-index-page', null, $pageData);

		if( !empty($pageData) ) {
			tx_tqseo_sitemap::index($pageData, 'page');
		}

		return true;
	}

	/**
	 * Insert page into sitemap
	 *
	 * @param	array	$pageData	Page informations
	 */
	protected static function _processLinkUrl($linkUrl) {
		global $TSFE;
		static $absRefPrefix		= null;
		static $absRefPrefixLength	= 0;
		$ret = $linkUrl;

		// Fetch abs ref prefix if available/set
		if( $absRefPrefix === null ) {
			if( !empty($TSFE->tmpl->setup['config.']['absRefPrefix']) ) {
				$absRefPrefix		= $TSFE->tmpl->setup['config.']['absRefPrefix'];
				$absRefPrefixLength	= strlen($absRefPrefix);
			} else {
				$absRefPrefix = false;
			}
		}

		// remove abs ref prefix
		if( $absRefPrefix !== false && strpos($ret, $absRefPrefix) === 0) {
			$ret = substr($ret, $absRefPrefixLength);
		}

		return $ret;
	}

	###########################################################################
	# HOOKS
	###########################################################################

	/**
	 * Hook: Index Page Content
	 *
	 * @param	object	$pObj	Object
	 */
	public function hook_indexContent(&$pObj) {
		$this->addPageToSitemapIndex();

		$possibility = (int)tx_tqseo_tools::getExtConf('sitemap_clearCachePossibility', 0);

		if( $possibility > 0 ) {

			$clearCacheChance = ceil(mt_rand(0, $possibility));
			if( $clearCacheChance == 1 ) {
				tx_tqseo_sitemap::expire();
			}
		}
	}


	/**
	 * Hook: Link Parser
	 *
	 * @param	object	$pObj	Object
	 */
	public static function hook_linkParse(&$pObj) {
		global $TSFE;

		// check if sitemap is enabled in root
		if( !tx_tqseo_tools::getRootSettingValue('is_sitemap', true)
			|| !tx_tqseo_tools::getRootSettingValue('is_sitemap_typolink_indexer', true) ) {
			return true;
		}

		// skip POST-calls and feuser login
		if( 	$_SERVER['REQUEST_METHOD'] !== 'GET'
			|| !empty($TSFE->fe_user->user['uid']) )  {
			return;
		}

		// Skip own sitemap tools
		if( $TSFE->type == 841131 || $TSFE->type == 841132) {
			return true;
		}

		// dont parse if page is not cacheable
		if(!$TSFE->isStaticCacheble()) {
			return;
		}

		// Check
		if(    empty($pObj['finalTagParts'])
			|| empty($pObj['conf'])
			|| empty($pObj['finalTagParts']['url']) ) {
			// no valid link
			return;
		}

		// Init link informations
		$linkConf	= $pObj['conf'];
		$linkUrl	= $pObj['finalTagParts']['url'];
		$linkUrl	= self::_processLinkUrl($linkUrl);

		if( !is_numeric($linkConf['parameter']) ) {
			// not valid internal link
			return;
		}

		#####################################
		# Init
		#####################################
		$uid = $linkConf['parameter'];

		$addParameters = array();
		if( !empty($linkConf['additionalParams']) ) {
			parse_str($linkConf['additionalParams'], $addParameters);
		}

		#####################################
		# Check if link is cacheable
		#####################################
		$isValid = false;

		// check if conf is valid
		if( !empty($linkConf['useCacheHash']) ) {
			$isValid = true;
		}

		// check for typical typo3 params
		$addParamsCache = $addParameters;
		unset($addParamsCache['L']);
		unset($addParamsCache['type']);

		if( empty($addParamsCache) ) {
			$isValid = true;
		}

		if(!$isValid) {
			// page is not cacheable, skip it
			return;
		}

		#####################################
		# Rootline
		#####################################
		$rootline = tx_tqseo_tools::getRootLine($uid);

		if( empty($rootline) ) {
			return;
		}

		$page = reset($rootline);

		#####################################
		# Build relative url
		#####################################
		$linkParts = parse_url($linkUrl);
		$pageUrl = ltrim($linkParts['path'], '/');
		if( !empty($linkParts['query']) ) {
			$pageUrl .= '?'.$linkParts['query'];
		}

		#####################################
		# Page settings
		#####################################
		// Fetch page changeFrequency
		$pageChangeFrequency = 0;
		if( !empty($page['tx_tqseo_change_frequency']) ) {
			$pageChangeFrequency = (int)$page['tx_tqseo_change_frequency'];
		} elseif(!empty($TSFE->tmpl->setup['plugin.']['tq_seo.']['sitemap.']['changeFrequency'])) {
			$pageChangeFrequency = (int)$TSFE->tmpl->setup['plugin.']['tq_seo.']['sitemap.']['changeFrequency'];
		}

		// Fetch sysLanguage
		$pageLanguage = 0;
		if(isset($addParameters['L'])) {
			$pageLanguage = (int)$addParameters['L'];
		} elseif(!empty($TSFE->tmpl->setup['config.']['sys_language_uid'])) {
			$pageLanguage = (int)$TSFE->tmpl->setup['config.']['sys_language_uid'];
		}

		#####################################
		# Indexing
		#####################################
		$tstamp = $_SERVER['REQUEST_TIME'];

		$pageData = array(
			'tstamp'				=> $tstamp,
			'crdate'				=> $tstamp,
			'page_rootpid'			=> $rootline[0]['uid'],
			'page_uid'				=> $linkConf['parameter'],
			'page_language'			=> $pageLanguage,
			'page_url'				=> $pageUrl,
			'page_hash'				=> md5($pageUrl),
			'page_depth'			=> count($rootline),
			'page_change_frequency'	=> $pageChangeFrequency,
		);

		// Call hook
		tx_tqseo_tools::callHook('sitemap-index-link', null, $pageData);

		if( !empty($pageData) ) {
			tx_tqseo_sitemap::index($pageData, 'link');
		}

		return true;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/class.sitemap_indexer.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/class.sitemap_indexer.php']);
}
?>