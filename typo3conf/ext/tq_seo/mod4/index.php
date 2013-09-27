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

$LANG->includeLLFile('EXT:tq_seo/mod4/locallang.xml');
require_once t3lib_extMgm::extPath('tq_seo').'lib/backend/class.base.php';
$BE_USER->modAccess($MCONF,1);    // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

/**
 * Module 'SEO' for the 'tq_seo' extension.
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class  tx_tqseo_module_overview extends tx_tqseo_module_tree {
	###########################################################################
	# Attributes
	###########################################################################


	###########################################################################
	# Methods
	###########################################################################
	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return    void
	 */
	public function menuConfig()    {
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'metadata'		=> $LANG->getLL('function_metadata'),
				'searchengines'	=> $LANG->getLL('function_searchengines'),
				'url'			=> $LANG->getLL('function_url'),
				'pagetitle'		=> $LANG->getLL('function_pagetitle'),
				'pagetitlesim'	=> $LANG->getLL('function_pagetitle_simulator'),
			)
		);
		parent::menuConfig();
	}

	public function executeMain() {
		return $this->executeMetadata();
	}


	public function executeMetadata() {
		return $this->_handleList('metadata');
	}

	public function executeSearchengines() {
		return $this->_handleList('searchengines');
	}

	public function executeUrl() {
		return $this->_handleList('url');
	}

	public function executePagetitle() {
		return $this->_handleList('pagetitle');
	}

	public function executePagetitleSim() {
		return $this->_handleList('pagetitlesim');
	}

	protected function _handleList($type) {
		global $TYPO3_DB, $LANG, $BACK_PATH;

		$pageId		= (int)$this->id;

		if( empty($pageId) ) {
			return '<div class="typo3-message message-warning">'.htmlspecialchars($LANG->getLL('message_no_valid_page')).'</div>';
		}

		###############################
		# HTML
		###############################

		$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') . 'res/backend/js/Ext.ux.plugin.FitToParent.js');
		$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') . 'res/backend/js/TQSeo.overview.js');

		$realUrlAvailable = t3lib_extMgm::isLoaded('realurl');

			// Include Ext JS inline code
		$this->pageRenderer->addJsInlineCode(
			'TQSeo.overview',

			'Ext.namespace("TQSeo.overview");

			TQSeo.overview.conf = {
				sessionToken			: '.json_encode($this->_sessionToken('tx_tqseo_backend_ajax_page')).',
				ajaxController			: '. json_encode($this->doc->backPath. 'ajax.php?ajaxID=tx_tqseo_backend_ajax::page').',
				pid						: '. (int)$pageId .',
				renderTo				: "tx-tqseo-sitemap-grid",

				pagingSize				: 50,

				sortField				: "crdate",
				sortDir					: "DESC",

				filterIcon				: '. json_encode(t3lib_iconWorks::getSpriteIcon('actions-system-tree-search-open')) .',

				depth					: 2,

				languageFullList		: '. json_encode($languageFullList) .',

				listType				: '. json_encode($type) .',

				criteriaFulltext		: "",

				realurlAvailable		: '. json_encode($realUrlAvailable) .',

				sprite : {
					edit	: '.json_encode( t3lib_iconWorks::getSpriteIcon('actions-document-open') ).',
					info	: '.json_encode( t3lib_iconWorks::getSpriteIcon('actions-document-info') ).'
				}
			};

			// Localisation:
			TQSeo.overview.conf.lang = {
				title					: '. json_encode( '' ) .',
				pagingMessage			: '. json_encode( $LANG->getLL('pager_results') ) .',
				pagingEmpty				: '. json_encode( $LANG->getLL('pager_noresults') ) .',

				boolean_yes				: '. json_encode( $LANG->getLL('boolean_yes') ) .',
				boolean_no				: '. json_encode( $LANG->getLL('boolean_no') ) .',

				labelDepth				: '. json_encode( $LANG->getLL('label_depth') ) .',

				labelSearchFulltext		: '. json_encode( $LANG->getLL('label_search_fulltext') ) .',
				emptySearchFulltext		: '. json_encode( $LANG->getLL('empty_search_fulltext') ) .',

				page_uid				: '. json_encode( $LANG->getLL('header_sitemap_page_uid') ) .',
				page_title				: '. json_encode( $LANG->getLL('header_sitemap_page_title') ) .',
				page_keywords			: '. json_encode( $LANG->getLL('header_sitemap_page_keywords') ) .',
				page_description		: '. json_encode( $LANG->getLL('header_sitemap_page_description') ) .',
				page_abstract			: '. json_encode( $LANG->getLL('header_sitemap_page_abstract') ) .',
				page_author				: '. json_encode( $LANG->getLL('header_sitemap_page_author') ) .',
				page_author_email		: '. json_encode( $LANG->getLL('header_sitemap_page_author_email') ) .',
				page_lastupdated		: '. json_encode( $LANG->getLL('header_sitemap_page_lastupdated') ) .',

				page_tx_tqseo_pagetitle			: '. json_encode( $LANG->getLL('header_sitemap_page_tx_tqseo_pagetitle') ) .',
				page_tx_tqseo_pagetitle_rel		: '. json_encode( $LANG->getLL('header_sitemap_page_tx_tqseo_pagetitle_rel') ) .',
				page_tx_tqseo_pagetitle_prefix	: '. json_encode( $LANG->getLL('header_sitemap_page_tx_tqseo_pagetitle_prefix') ) .',
				page_tx_tqseo_pagetitle_suffix	: '. json_encode( $LANG->getLL('header_sitemap_page_tx_tqseo_pagetitle_suffix') ) .',

				page_title_simulated			: '. json_encode( $LANG->getLL('header_pagetitlesim_title_simulated') ) .',

				page_searchengine_canonicalurl	: '. json_encode( $LANG->getLL('header_searchengine_canonicalurl') ) .',
				page_searchengine_is_exclude	: '. json_encode( $LANG->getLL('header_searchengine_is_excluded') ) .',
				searchengine_is_exclude_disabled	: '. json_encode( $LANG->getLL('searchengine_is_exclude_disabled') ) .',
				searchengine_is_exclude_enabled	: '. json_encode( $LANG->getLL('searchengine_is_exclude_enabled') ) .',

				page_sitemap_priority			: '. json_encode( $LANG->getLL('header_sitemap_priority') ) .',

				page_url_scheme					: '. json_encode( $LANG->getLL('header_url_scheme') ) .',
				page_url_scheme_default			: '. json_encode( $LANG->getLL('page_url_scheme_default') ) .',
				page_url_scheme_http			: '. json_encode( $LANG->getLL('page_url_scheme_http') ) .',
				page_url_scheme_https			: '. json_encode( $LANG->getLL('page_url_scheme_https') ) .',
				page_url_alias					: '. json_encode( $LANG->getLL('header_url_alias') ) .',
				page_url_realurl_pathsegment	: '. json_encode( $LANG->getLL('header_url_realurl_pathsegment') ) .',
				page_url_realurl_pathoverride	: '. json_encode( $LANG->getLL('header_url_realurl_pathoverride') ) .',
				page_url_realurl_exclude		: '. json_encode( $LANG->getLL('header_url_realurl_exclude') ) .',

				qtip_pagetitle_simulate			: '. json_encode( $LANG->getLL('qtip_pagetitle_simulate') ) .',
				qtip_url_simulate				: '. json_encode( $LANG->getLL('qtip_url_simulate') ) .',

				value_default					: '. json_encode( $LANG->getLL('value_default') ) .'
			};
			');

		###############################
		# Build HTML
		###############################
		$ret = '<div id="tx-tqseo-sitemap-grid"></div>';

		return $ret;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod4/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod4/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_tqseo_module_overview');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>