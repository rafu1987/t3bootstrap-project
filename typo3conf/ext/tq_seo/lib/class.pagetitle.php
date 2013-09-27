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
 * Page Title Changer
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.pagetitle.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class user_tqseo_pagetitle {

	/**
	 * Add SEO-Page Title
	 *
	 * @param	string	$title	Default page title (rendered by TYPO3)
	 * @return	string			Modified page title
	 */
	public function main($title) {
		global $TSFE;

		// INIT
		$ret				= $title;
		$rawTitel			= $TSFE->page['title'];
		$tsSetup			= $TSFE->tmpl->setup;
		$tsSeoSetup			= array();
		$rootLine			= $TSFE->rootLine;
		$currentPid			= $TSFE->id;
		$skipPrefixSuffix	= false;
		$applySitetitle		= false;

		$stdWrapList		= array();

		// get configuration
		if( !empty($tsSetup['plugin.']['tq_seo.']) ) {
			$tsSeoSetup = $tsSetup['plugin.']['tq_seo.'];
		}

		// Use browsertitle if available
		if( !empty($TSFE->page['tx_tqseo_pagetitle_rel']) ) {
			$rawTitel	= $TSFE->page['tx_tqseo_pagetitle_rel'];
		}

		// Call hook
		tx_tqseo_tools::callHook('pagetitle-setup', $this, $tsSeoSetup);

		// get stdwrap list
		if( !empty($tsSeoSetup['pageTitle.']['stdWrap.']) ) {
			$stdWrapList = $tsSeoSetup['pageTitle.']['stdWrap.'];
		}

		// Apply stdWrap before
		if( !empty($stdWrapList['before.']) ) {
			$rawTitel = $this->cObj->stdWrap($rawTitel, $stdWrapList['before.']);
		}

		#######################################################################
		# RAW PAGE TITEL
		#######################################################################
		if(!empty($TSFE->page['tx_tqseo_pagetitle'])) {
			$ret = $TSFE->page['tx_tqseo_pagetitle'];

			// Add template prefix/suffix
			if(!empty($tsSeoSetup['pageTitle.']['applySitetitleToPagetitle'])) {
				$applySitetitle = true;
			}

			$skipPrefixSuffix = true;
		}


		#######################################################################
		# PAGE TITEL PREFIX/SUFFIX
		#######################################################################
		 if(!$skipPrefixSuffix) {
			$pageTitelPrefix = false;
			$pageTitelSuffix = false;

			foreach($rootLine as $page) {
				switch( (int)$page['tx_tqseo_inheritance'] ) {
					case 0:
						###################################
						# Normal
						###################################
						if( !empty($page['tx_tqseo_pagetitle_prefix']) ) {
							$pageTitelPrefix = $page['tx_tqseo_pagetitle_prefix'];
						}

						if( !empty($page['tx_tqseo_pagetitle_suffix']) ) {
							$pageTitelSuffix = $page['tx_tqseo_pagetitle_suffix'];
						}

						if($pageTitelPrefix !== FALSE || $pageTitelSuffix !== FALSE) {
							// pagetitle found - break foreach
							break 2;
						}
						break;

					case 1:
						###################################
						# Skip
						# (don't herit from this page)
						###################################
						if( (int)$page['uid'] != $currentPid ) {
							continue 2;
						}

						if( !empty($page['tx_tqseo_pagetitle_prefix']) ) {
							$pageTitelPrefix = $page['tx_tqseo_pagetitle_prefix'];
						}

						if( !empty($page['tx_tqseo_pagetitle_suffix']) ) {
							$pageTitelSuffix = $page['tx_tqseo_pagetitle_suffix'];
						}

						break 2;
						break;
				}
			}

			// Apply prefix and suffix
			if($pageTitelPrefix !== FALSE || $pageTitelSuffix !== FALSE) {
				$ret = $rawTitel;

				if($pageTitelPrefix !== FALSE) {
					$ret = $pageTitelPrefix.' '.$ret;
				}

				if($pageTitelSuffix !== FALSE) {
					$ret .= ' '.$pageTitelSuffix;
				}

				if(!empty($tsSeoSetup['pageTitle.']['applySitetitleToPrefixSuffix'])) {
					$applySitetitle = true;
				}
			} else {
				$ret = $rawTitel;
				$applySitetitle = true;
			}
		}

		#######################################################################
		# APPLY SITETITLE (from setup)
		#######################################################################
		if($applySitetitle) {
			$pageTitleGlue		= ':';
			$glueSpacerBefore	= '';
			$glueSpacerAfter	= '';
			$sitetitle = $tsSetup['sitetitle'];

			// Overwrite sitetitle with the one from ts-setup (if available)
			if( !empty($tsSeoSetup['pageTitle.']['sitetitle']) ) {
				$sitetitle = $tsSeoSetup['pageTitle.']['sitetitle'];
			}

			// Apply stdWrap after
			if( !empty($stdWrapList['sitetitle.']) ) {
				$sitetitle = $this->cObj->stdWrap($sitetitle, $stdWrapList['sitetitle.']);
			}


			if( isset($tsSeoSetup['pageTitle.']['sitetitleGlue']) ) {
				$pageTitleGlue = $tsSeoSetup['pageTitle.']['sitetitleGlue'];
			}

			if( !empty($tsSeoSetup['pageTitle.']['sitetitleGlueSpaceBefore']) ) {
				$glueSpacerBefore = ' ';
			}

			if( !empty($tsSeoSetup['pageTitle.']['sitetitleGlueSpaceAfter']) ) {
				$glueSpacerAfter = ' ';
			}

			$sitetitlePosition = 0;
			if( isset($tsSeoSetup['pageTitle.']['sitetitlePosition']) ) {
				$sitetitlePosition = (int)$tsSeoSetup['pageTitle.']['sitetitlePosition'];
			} elseif(isset($tsSetup['config.']['pageTitleFirst'])) {
				$sitetitlePosition = (int)$tsSetup['config.']['pageTitleFirst'];
			}

			// add overall pagetitel from template/ts-setup
			if($sitetitlePosition) {
				// suffix
				$ret .= $glueSpacerBefore.$pageTitleGlue.$glueSpacerAfter.$sitetitle;
			} else {
				// prefix (default)
				$ret = $sitetitle.$glueSpacerBefore.$pageTitleGlue.$glueSpacerAfter.$ret;
			}
		}


		// Apply stdWrap after
		if( !empty($stdWrapList['after.']) ) {
			$ret = $this->cObj->stdWrap($ret, $stdWrapList['after.']);
		}

		// Call hook
		tx_tqseo_tools::callHook('pagetitle-output', $this, $ret);

		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.pagetitle.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.pagetitle.php']);
}
?>