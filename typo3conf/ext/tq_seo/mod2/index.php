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

$LANG->includeLLFile('EXT:tq_seo/mod2/locallang.xml');
$BE_USER->modAccess($MCONF,1);    // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

/**
 * Module 'SEO' for the 'tq_seo' extension.
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class tx_tqseo_module_controlcenter extends tx_tqseo_module_standalone {
	###########################################################################
	# Attributes
	###########################################################################


	###########################################################################
	# Methods
	###########################################################################
	/**
	 * Website rootpage list
	 *
	 * @return string
	 */
	public function executeMain() {
		global $TYPO3_DB, $LANG, $BE_USER;

		$rootPageList		= tx_tqseo_backend_tools::getRootPageList();
		$rootIdList			= array_keys($rootPageList);

		// check which root lages have no root settings
		$query = 'SELECT p.uid
					FROM pages p
						 LEFT JOIN tx_tqseo_setting_root seosr
							ON   seosr.pid = p.uid
							 AND seosr.deleted = 0
					WHERE p.uid IN ('.implode(',', $rootIdList).')
					  AND seosr.uid IS NULL';
		$res = $TYPO3_DB->sql_query($query);
		while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
			$tmpUid = $row['uid'];
			$query = 'INSERT INTO tx_tqseo_setting_root (pid, tstamp, crdate, cruser_id)
							VALUES ('.(int)$tmpUid.',
									'.(int)time().',
									'.(int)time().',
									'.(int)$BE_USER->user['uid'].')';
			$TYPO3_DB->sql_query($query);
		}

		$rootSettingList	= tx_tqseo_backend_tools::getRootPageSettingList();


		$tableRowList = array();
		$i = 0;
		foreach($rootPageList as $pageId => $page) {
			// Init
			$domainCell		= '';
			$sitemapCell	= '';
			$robotsTxtCell	= '';
			$rowClass = $i % 2 ? 'db_list_alt' : 'db_list_normal';

			$settingRow = array();
			if( !empty($rootSettingList[$pageId]) ) {
				$settingRow = $rootSettingList[$pageId];
			}

			// Settings available
			if( !empty($settingRow) ) {
				$settingsLink = t3lib_BEfunc::editOnClick('&edit[tx_tqseo_setting_root]['.$rootSettingList[$pageId]['uid'].']=edit',$this->doc->backPath);
			} else {
				$args = array(
					'pid'	=> $pageId,
				);
				$settingsLink = $this->_moduleLinkOnClick('createSettingForPage', $args);
			}

			$res = $TYPO3_DB->exec_SELECTquery(
				'domainName, forced',
				'sys_domain',
				'pid = '.(int)$pageId.' AND hidden = 0',
				'',
				'forced DESC, sorting'
			);
			$domainList = array();
			while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
				if( !empty($row['forced']) ) {
					$domainName = '<strong>'.htmlspecialchars($row['domainName']).'</strong>';
				} else {
					$domainName = htmlspecialchars($row['domainName']);
				}

				$domainList[] = '<div style="white-space: nowrap">'.$domainName.'</div>';
			}
			$domainCell = implode('', $domainList);


			// Sitemap support
			if( !empty($settingRow['is_sitemap']) ) {
				$sitemapCell = '<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-permission-granted"></span>';
			} else {
				$sitemapCell = '<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-permission-denied"></span>';
			}

			if( !empty($settingRow['is_sitemap_language_lock']) ) {
				$sitemapCell .= '<br /><i>'.$LANG->getLL('is_sitemap_language_lock_active').'</i>';
			}

			// Robots.txt support
			if( !empty($settingRow['is_robotstxt']) ) {
				$robotsTxtCell = '<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-permission-granted"></span>';
			} else {
				$robotsTxtCell = '<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-permission-denied"></span>';
			}

			// Build row
			$tableRowList[] = '
				<tr class="'.$rowClass.'">
					<td>
						'.htmlspecialchars($page['title']).'
					</td>
					<td>'.$domainCell.'</td>
					<td class="center nowrap">'.$sitemapCell.'</td>
					<td class="center nowrap">'.$robotsTxtCell.'</td>
					<td>
						<a href="#" onclick="'.htmlspecialchars($settingsLink).'">'.t3lib_iconWorks::getSpriteIcon('actions-document-open').'</a>
					</td>
				</tr>';
			$i++;
		}

		$ret = '
			<table border="0" cellpadding="0" cellspacing="1" width="100%" class="typo3-dblist">
				<colgroup>
					<col width="*" />
					<col width="200" />
					<col width="80" />
					<col width="80" />
					<col width="20" />
				</colgroup>
				<thead>
					<tr>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_rootpage',1).'</strong></td>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_domain',1).'</strong></td>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_sitemap',1).'</strong></td>
						<td class="t3-row-header"><strong>'.$LANG->getLL('header_robotstxt',1).'</strong></td>
						<td class="t3-row-header">&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					'.implode('',$tableRowList).'
				</tbody>
			</table>
		';



		return $ret;
	}

	/**
	 * Create setting (root) entry for website (website-rootpage)
	 *
	 * @return	string
	 */
	public function executeCreateSettingForPage() {
		global $BE_USER;
		global $TYPO3_DB;

		$rootPageList		= tx_tqseo_backend_tools::getRootPageList();
		$rootSettingList	= tx_tqseo_backend_tools::getRootPageSettingList();

		$pid = null;
		if( !empty($this->_moduleArgs['pid']) ) {
			$pid = (int)$this->_moduleArgs['pid'];
		}

		if( empty($rootPageList[$pid]) ) {
			return $this->executeMain();
		}

		if( empty($rootSettingList[$pid]) ) {
			$data = array(
				'pid'		=> $pid,
				'crdate'	=> time(),
				'tstamp'	=> time(),
				'cruser_id'	=> $BE_USER->user['uid']
			);
			$TYPO3_DB->exec_INSERTquery('tx_tqseo_setting_root', $data);
			$uid = $TYPO3_DB->sql_insert_id();
		} else {
			$uid = $rootSettingList[$pid];
		}

		// Maybe there is a better solution than this js-redirect!?
		$link = t3lib_BEfunc::editOnClick('&edit[tx_tqseo_setting_root]['.$uid.']=edit',$this->doc->backPath);
		return '<script type="text/javascript">
			function tq_redirect() {
				'.$link.'
			}
			tq_redirect();
			</script>';
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod2/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/mod2/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_tqseo_module_controlcenter');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>