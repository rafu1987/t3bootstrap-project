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
 * Page Footer
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.pagefooter.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class user_tqseo_pagefooter {

	/**
	 * Add Page Footer
	 *
	 * @param	string	$title	Default page title (rendered by TYPO3)
	 * @return	string			Modified page title
	 */
	public function main($title) {
		global $TSFE;

		// INIT
		$ret				= array();
		$tsSetup			= $TSFE->tmpl->setup;
		$tsServices			= array();

		$beLoggedIn			= isset($GLOBALS['BE_USER']->user['username']);

		$disabledHeaderCode = false;
		if( !empty($tsSetup['config.']['disableAllHeaderCode']) ) {
			$disabledHeaderCode = true;
		}

		if( !empty($tsSetup['plugin.']['tq_seo.']['services.']) ) {
			$tsServices = $tsSetup['plugin.']['tq_seo.']['services.'];
		}

		// Call hook
		tx_tqseo_tools::callHook('pagefooter-setup', $this, $tsServices);

		#########################################
		# GOOGLE ANALYTICS
		#########################################

		if( !empty($tsServices['googleAnalytics']) ) {
			$gaConf = $tsServices['googleAnalytics.'];

			$gaEnabled = true;

			if( $disabledHeaderCode && empty($gaConf['enableIfHeaderIsDisabled']) ) {
				$gaEnabled = false;
			}

			if( $gaEnabled && !(empty($gaConf['showIfBeLogin']) && $beLoggedIn) ) {
				$tmp = '';

				$customCode = '';
				if( !empty($gaConf['customizationCode']) ) {
					$customCode .= "\n".$this->cObj->cObjGetSingle($gaConf['customizationCode'], $gaConf['customizationCode.']);
				}

				$this->cObj->data['gaCode']					= $tsServices['googleAnalytics'];
				$this->cObj->data['gaIsAnonymize']			= (int)!empty($gaConf['anonymizeIp']);
				$this->cObj->data['gaDomainName']			= $gaConf['domainName'];
				$this->cObj->data['gaCustomizationCode']	= $customCode;

				// Build code
				$ret['ga'] = $this->cObj->cObjGetSingle($gaConf['template'], $gaConf['template.']);

				if( !empty($gaConf['trackDownloads']) && !empty($gaConf['trackDownloadsScript']) ) {
					$jsFile = t3lib_div::getFileAbsFileName($gaConf['trackDownloadsScript']);
					$jsfile = preg_replace('/^'.preg_quote(PATH_site,'/').'/i','',$jsFile);
					$ret['ga.trackdownload'] = '<script type="text/javascript" src="'.htmlspecialchars($jsfile).'"></script>';
				}
			} elseif($gaEnabled && $beLoggedIn) {
				// Backend login detected, disable cache because this page is viewed by BE-users
				$ret['ga.disabled'] = '<!-- Google Analytics disabled - Backend-Login detected -->';
			}
		}


		#########################################
		# PIWIK
		#########################################
		if( !empty($tsServices['piwik.']) && !empty($tsServices['piwik.']['url']) && !empty($tsServices['piwik.']['id']) ) {
			$piwikConf = $tsServices['piwik.'];

			$piwikEnabled = true;

			if( $disabledHeaderCode && empty($piwikConf['enableIfHeaderIsDisabled']) ) {
				$piwikEnabled = false;
			}

			if( $piwikEnabled && !(empty($piwikConf['showIfBeLogin']) && $beLoggedIn) ) {
				$tmp = '';

				$customCode = '';
				if( !empty($piwikConf['customizationCode']) ) {
					$customCode .= "\n".$this->cObj->cObjGetSingle($piwikConf['customizationCode'], $piwikConf['customizationCode.']);
				}

				// remove last slash
				$piwikConf['url'] = rtrim($piwikConf['url'], '/');

				$this->cObj->data['piwikUrl']					= $piwikConf['url'];
				$this->cObj->data['piwikId']					= $piwikConf['id'];
				$this->cObj->data['piwikDomainName']			= $piwikConf['domainName'];
				$this->cObj->data['piwikCookieDomainName']		= $piwikConf['cookieDomainName'];
				$this->cObj->data['piwikDoNotTrack']			= $piwikConf['doNotTrack'];
				$this->cObj->data['piwikCustomizationCode']		= $customCode;

				// Build code
				$ret['piwik'] = $this->cObj->cObjGetSingle($piwikConf['template'], $piwikConf['template.']);

			} elseif($piwikEnabled && $beLoggedIn) {
				// Backend login detected, disable cache because this page is viewed by BE-users
				$ret['piwik.disabled'] = '<!-- Piwik disabled - Backend-Login detected -->';
			}
		}

		// Call hook
		tx_tqseo_tools::callHook('pagefooter-output', $this, $ret);

		return implode("\n", $ret);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.pagefooter.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.pagefooter.php']);
}
?>