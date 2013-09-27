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

$LANG->includeLLFile('EXT:tq_seo/mod3/locallang.xml');
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
class tx_tqseo_module_sitemap extends tx_tqseo_module_standalone {
	###########################################################################
	# Attributes
	###########################################################################


	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Main
	 *
	 * @return	string
	 */
	public function executeMain() {
		return $this->executeSitemap();
	}

	/**
	 * Sitemap Root pages
	 *
	 * @return	string
	 */
	public function executeSitemap() {
		global $LANG, $TYPO3_DB;

		// Init
		$rootPageList = tx_tqseo_backend_tools::getRootPageList();

		###############################
		# Fetch
		###############################
		$statsList['sum_total'] = $TYPO3_DB->exec_SELECTgetRows(
			'page_rootpid, COUNT(*) as count',
			'tx_tqseo_sitemap',
			'',
			'page_rootpid',
			'',
			'',
			'page_rootpid'
		);

		###############################
		# Build rows
		###############################
		$tableRowList = array();
		$i = 0;
		foreach($rootPageList as $pageId => $page) {
			$rowClass = $i % 2 ? 'db_list_alt' : 'db_list_normal';

			$stats = array(
				'sum_pages'	=> 0,
				'sum_total' => 0,
			);

			foreach($statsList as $statsKey => $statsTmpList) {
				if( !empty($statsTmpList[$pageId]) ) {
					$stats[$statsKey] = $statsTmpList[$pageId]['count'];
				}
			}

			// Root statistics
			$tmp = $TYPO3_DB->exec_SELECTgetRows(
				'DISTINCT page_uid',
				'tx_tqseo_sitemap',
				'page_rootpid = '.(int)$pageId
			);
			if( !empty($tmp) ) {
				$stats['sum_pages'] = count($tmp);
			}

			$args = array(
				'rootPid'	=> $pageId
			);
			$showLink = $this->_moduleLinkOnClick('sitemapList', $args);


			$tmpRow = '
				<tr class="'.$rowClass.'" onclick="'.htmlspecialchars($showLink).'">
					<td>
						'.htmlspecialchars($page['title']).'
					</td>';

			foreach($stats as $key => $value) {
				$tmpRow .= '<td>'.$value.'</td>';
			}

			$tmpRow .= '
					<td>
						<a href="#" onclick="'.htmlspecialchars($showLink).'">'.t3lib_iconWorks::getSpriteIcon('actions-system-list-open').'</a>
					</td>
				</tr>';

			$tableRowList[] = $tmpRow;

			$i++;
		}


		###############################
		# Build HTML
		###############################

		$ret = '
			<table border="0" cellpadding="0" cellspacing="1" width="100%" class="typo3-dblist">
				<colgroup>
					<col width="*" />
					<col width="150" />
					<col width="150" />
					<col width="20" />
				</colgroup>
				<thead>
					<tr>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_rootpage',1).'</strong></td>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_sitemap_pages',1).'</strong></td>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_sitemap_total',1).'</strong></td>
						<td class="t3-row-header"></td>
					</tr>
				</thead>
				<tbody>
					'.implode('',$tableRowList).'
				</tbody>
			</table>';

		return $ret;
	}

	/**
	 * Sitemap list module
	 *
	 * @return	string
	 */
	public function executeSitemapList() {
		global $TYPO3_DB, $LANG,$BACK_PATH;

		if( empty($this->_moduleArgs['rootPid']) ) {
			return $this->executeMain();
		}

		$this->_menuBackLink = $this->_moduleLink('sitemap');

		$rootPageList = tx_tqseo_backend_tools::getRootPageList();

		$rootPid	= (int)$this->_moduleArgs['rootPid'];
		$rootPage	= $rootPageList[$rootPid];

		###############################
		# Fetch
		###############################
		$pageTsConf = t3lib_BEfunc::getPagesTSconfig($rootPid);

		$languageFullList = array(
			0 => array(
				'label'	=> $LANG->getLL('default_language'),
				'flag'	=> '',
			),
		);

		if( !empty($pageTsConf['mod.']['SHARED.']['defaultLanguageFlag']) ) {
			$languageFullList[0]['flag'] = $pageTsConf['mod.']['SHARED.']['defaultLanguageFlag'];
		}

		if( !empty($pageTsConf['mod.']['SHARED.']['defaultLanguageLabel']) ) {
			$languageFullList[0]['label'] = $pageTsConf['mod.']['SHARED.']['defaultLanguageLabel'];
		}

		// Fetch other flags
		$res = $TYPO3_DB->exec_SELECTquery(
			'uid, title, flag',
			'sys_language',
			'hidden = 0'
		);
		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$languageFullList[ $row['uid'] ] = array(
				'label'	=> htmlspecialchars($row['title']),
				'flag'	=> htmlspecialchars($row['flag']),
			);
		}

		// Langauges
		$languageList = array();
		$languageList[] =	array(
			-1,
			 $LANG->getLL('empty_search_page_language'),
		);

		foreach($languageFullList as $langId => $langRow) {
			$flag = '';

			// Flag (if available)
			if( !empty($langRow['flag']) ) {
				$flag .= '<span class="t3-icon t3-icon-flags t3-icon-flags-'.$langRow['flag'].' t3-icon-'.$langRow['flag'].'"></span>';
				$flag .= '&nbsp;';
			}

			// label
			$label = $langRow['label'];

			$languageList[] = array(
				$langId,
				$label,
				$flag
			);
		}

		// Depth
		$depthList = array();
		$depthList[] =	array(
			-1,
			 $LANG->getLL('empty_search_page_depth'),
		);

		$res = $TYPO3_DB->exec_SELECTquery(
			'DISTINCT page_depth',
			'tx_tqseo_sitemap',
			'page_rootpid = '.(int)$rootPid
		);
		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$depth = $row['page_depth'];
			$depthList[] = array(
				$depth,
				$depth,
			);
		}


		###############################
		# HTML
		###############################

		$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') . 'res/backend/js/Ext.ux.plugin.FitToParent.js');
		$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') . 'res/backend/js/TQSeo.sitemap.js');

		// Include Ext JS inline code
		$this->pageRenderer->addJsInlineCode(
			'TQSeo.sitemap',

			'Ext.namespace("TQSeo.sitemap");

			TQSeo.sitemap.conf = {
				sessionToken			: '.json_encode($this->_sessionToken('tx_tqseo_backend_ajax_sitemap')).',
				ajaxController			: '.json_encode($this->doc->backPath. 'ajax.php?ajaxID=tx_tqseo_backend_ajax::sitemap').',
				pid						: '.(int)$rootPid .',
				renderTo				: "tx-tqseo-sitemap-grid",

				pagingSize				: 50,

				sortField				: "crdate",
				sortDir					: "DESC",

				filterIcon				: '. json_encode(t3lib_iconWorks::getSpriteIcon('actions-system-tree-search-open')) .',

				dataLanguage			: '. json_encode($languageList) .',
				dataDepth				: '. json_encode($depthList) .',

				criteriaFulltext		: "",
				criteriaPageUid			: "",
				criteriaPageLanguage	: "",
				criteriaPageDepth		: "",

				languageFullList		: '. json_encode($languageFullList) .',
			};

			// Localisation:
			TQSeo.sitemap.conf.lang = {
				title					: '. json_encode( sprintf($LANG->getLL('title_sitemap_list'), $rootPage['title'], $rootPid ) ) .',
				pagingMessage			: '. json_encode( $LANG->getLL('pager_results') ) .',
				pagingEmpty				: '. json_encode( $LANG->getLL('pager_noresults') ) .',
				sitemap_page_uid		: '. json_encode( $LANG->getLL('header_sitemap_page_uid') ) .',
				sitemap_page_url		: '. json_encode( $LANG->getLL('header_sitemap_page_url') ) .',
				sitemap_page_depth		: '. json_encode( $LANG->getLL('header_sitemap_page_depth') ) .',
				sitemap_page_language	: '. json_encode( $LANG->getLL('header_sitemap_page_language') ) .',
				sitemap_tstamp			: '. json_encode( $LANG->getLL('header_sitemap_tstamp') ) .',
				sitemap_crdate			: '. json_encode( $LANG->getLL('header_sitemap_crdate') ) .',

				labelSearchFulltext		: '. json_encode( $LANG->getLL('label_search_fulltext') ) .',
				emptySearchFulltext		: '. json_encode( $LANG->getLL('empty_search_fulltext') ) .',

				labelSearchPageUid		: '. json_encode( $LANG->getLL('label_search_page_uid') ) .',
				emptySearchPageUid		: '. json_encode( $LANG->getLL('empty_search_page_uid') ) .',

				labelSearchPageLanguage	: '. json_encode( $LANG->getLL('label_search_page_language') ) .',
				emptySearchPageLanguage	: '. json_encode( $LANG->getLL('empty_search_page_language') ) .',

				labelSearchPageDepth	: '. json_encode( $LANG->getLL('label_search_page_depth') ) .',
				emptySearchPageDepth	: '. json_encode( $LANG->getLL('empty_search_page_depth') ) .',

				buttonYes				: '. json_encode( $LANG->getLL('button_yes') ) .',
				buttonNo				: '. json_encode( $LANG->getLL('button_no') ) .',


				buttonDelete			: '. json_encode( $LANG->getLL('button_delete') ) .',
				buttonDeleteHint		: '. json_encode( $LANG->getLL('button_delete_hint') ) .',

				messageDeleteTitle			: '. json_encode( $LANG->getLL('message_delete_title') ) .',
				messageDeleteQuestion		: '. json_encode( $LANG->getLL('message_delete_question') ) .',
				errorDeleteFailedMessage	: '. json_encode( $LANG->getLL('message_delete_failed_body') ) .',

				errorNoSelectedItemsBody	: '. json_encode( $LANG->getLL('message_no_selected_items') ) .',

				today						: '. json_encode( $LANG->getLL('today') ) .',
				yesterday					: '. json_encode( $LANG->getLL('yesterday') ) .'
			};
		');

		###############################
		# Build HTML
		###############################
		$ret = '<div id="tx-tqseo-sitemap-grid"></div>';

		return $ret;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod3/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod3/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_tqseo_module_sitemap');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>