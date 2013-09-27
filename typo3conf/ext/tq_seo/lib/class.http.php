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
 * Http Header generator
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.http.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class user_tqseo_http {

	/**
	 * Add HTTP Headers
	 */
	public function main() {
		global $TSFE;

		// INIT
		$ret		= array();
		$tsSetup	= $TSFE->tmpl->setup;
		$cObj		= $TSFE->cObj;
		$pageMeta	= array();
		$tsfePage	= $TSFE->page;
		$headers = array();

		// dont send any headers if headers are already sent
		if( headers_sent() ) {
			return;
		}

		if( !empty($TSFE->tmpl->loaded) ) {
			#####################################
			# Non-Cached page
			#####################################

			if(!empty($tsSetup['plugin.']['tq_seo.']['metaTags.'])) {
				$tsSetupSeo = $tsSetup['plugin.']['tq_seo.']['metaTags.'];

				#####################################
				# W3C P3P Tags
				#####################################
				$p3pCP			= null;
				$p3pPolicyUrl	= null;

				if( !empty($tsSetupSeo['p3pCP']) ) {
					$p3pCP = $tsSetupSeo['p3pCP'];
				}

				if( !empty($tsSetupSeo['p3pPolicyUrl']) ) {
					$p3pPolicyUrl = $tsSetupSeo['p3pPolicyUrl'];
				}

				if( !empty($p3pCP) || !empty($p3pPolicyUrl) ) {
					$p3pHeaders = array();

					if( !empty($p3pCP) ) {
						$p3pHeader[] = 'CP="'.$p3pCP.'"';
					}

					if( !empty($p3pPolicyUrl) ) {
						$p3pHeader[] = 'policyref="'.$p3pPolicyUrl.'"';
					}

					$headers['P3P'] = implode(' ', $p3pHeader);

					// cache informations
					$curentTemplate = end( $TSFE->tmpl->hierarchyInfo );
					$currentTemplatePid	= $curentTemplate['pid'];
					tx_tqseo_cache::set($currentTemplatePid, 'http', 'p3p', $headers['P3P']);
				}
			}

		} else {
			#####################################
			# Cached page
			#####################################
			// build root pid list
			$rootPidList = array();
			foreach($TSFE->rootLine as $pageRow) {
				$rootPidList[ $pageRow['uid'] ] = $pageRow['uid'];
			}

			// fetch from cache
			$cacheList = tx_tqseo_cache::getList('http', 'p3p');
			foreach($rootPidList as $pageId) {
				if( !empty($cacheList[$pageId]) ) {
					$headers['P3P'] = $cacheList[$pageId];
					break;
				}
			}
		}
		
		// Call hook
		tx_tqseo_tools::callHook('httpheader-output', $this, $headers);

		#####################################
		# Sender headers
		#####################################
		if( !empty($headers['P3P']) ) {
			header('P3P: '.$headers['P3P']);
		}

	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.http.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.http.php']);
}
?>