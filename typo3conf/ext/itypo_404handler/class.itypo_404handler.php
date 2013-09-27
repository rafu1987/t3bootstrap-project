<?php

class user_itypo_404handler {
	function handleError($params, tslib_fe $pObj) {
		// create regular FE environment so we can link, access setup, etc
		$this->createTSFE();
		
		// get constants/setup
		$setup = $GLOBALS['TSFE']->tmpl->setup['plugin.']['itypo_404handler.'];

		// support for itypo_securedfiles
		if (t3lib_extMgm::isLoaded('itypo_securedfiles') && $params['reasonText'] == 'No permission to access this file') $throw403 = true;
		
		if (isset($params['pageAccessFailureReasons']['fe_group']) && !isset($params['pageAccessFailureReasons']['hidden'])
		&& current($params['pageAccessFailureReasons']['fe_group']) !== 0 && !is_array($pObj->fe_user->user)) {	// redirect to login page, 303
			if (is_numeric($setup['loginPage'])) {
				$loginPage = $this->cObj->getTypoLink_URL(intval($setup['loginPage']));
			} else {
				$loginPage = $setup['loginPage'];
			}	
			// if the url ends with a /, add a ? for the parameters, this happens when realurl is used for example
			if (substr($loginPage,-1,1) == '/') $loginPage .= '?';
			
			$url = 'REDIRECT:'.$loginPage.'&redirect_url='.rawurlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		} elseif ((isset($params['pageAccessFailureReasons']['fe_group']) && !isset($params['pageAccessFailureReasons']['hidden'])
		&& current($params['pageAccessFailureReasons']['fe_group']) !== 0 && is_array($pObj->fe_user->user)) || $throw403) {	// logged in but no access, 403
			$header = 'HTTP/1.0 403 Forbidden';
			
			$forbiddenPage = $this->cObj->getTypoLink_URL(intval($setup['forbiddenPage']));			
			// if the url ends with a /, add a ? for the parameters, this happens when realurl is used for example
			if (substr($forbiddenPage,-1,1) == '/') $forbiddenPage .= '?';
			
			$url = $forbiddenPage;
			if (is_array($pObj->fe_user->user)) {
				$urlParams = '&tx_itypo404handler[ses_id]='.$pObj->fe_user->user['ses_id'];
				$url .= $urlParams;
				$url .= '&cHash='.t3lib_div::generateCHash($urlParams);
			}
		} else {																								// item not found, 404
			$header = $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling_statheader'];
			
			$notfoundPage = $this->cObj->getTypoLink_URL(intval($setup['notfoundPage']));			
			// if the url ends with a /, add a ? for the parameters, this happens when realurl is used for example
			if (substr($notfoundPage,-1,1) == '/') $notfoundPage .= '?';
			
			$url = $notfoundPage;
			if (is_array($pObj->fe_user->user)) {
				$urlParams = '&tx_itypo404handler[ses_id]='.$pObj->fe_user->user['ses_id'];
				$url .= $urlParams;
				$url .= '&cHash='.t3lib_div::generateCHash($urlParams);
			}
		}

		// output the page
		$pObj->pageErrorHandler($url, $header, $params['reasonText']);
	}
	
	/**
	 * Initializes TSFE. This is necessary to have proper environment for typoLink.
	 * This function is based on the one from extension 'pagepath' by Dmitry Dulepov.
	 *
	 * @return	void
	 */
	protected function createTSFE() {
		require_once(PATH_tslib . 'class.tslib_fe.php');
		require_once(PATH_t3lib . 'class.t3lib_page.php');
		require_once(PATH_tslib . 'class.tslib_content.php');
		require_once(PATH_t3lib . 'class.t3lib_userauth.php' );
		require_once(PATH_tslib . 'class.tslib_feuserauth.php');
		require_once(PATH_t3lib . 'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib . 'class.t3lib_cs.php');

		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $this->pageId, '');
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->getCompressedTCarray();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
		
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');

		// Set linkVars, absRefPrefix, etc
		require_once(PATH_tslib . 'class.tslib_pagegen.php');
		TSpagegen::pagegenInit();
	}
}

?>