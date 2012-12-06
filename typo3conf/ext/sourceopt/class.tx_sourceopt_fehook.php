<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2003 Boris Nicolai
 *  (c) 2012 Tim Lochmüller
 * 
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
 * ************************************************************* */

require_once (t3lib_extMgm::extPath("sourceopt") . "pi1/class.tx_sourceopt_pi1.php");

/**
 * Base Class for the Hook
 */
class tx_sourceopt_fehook extends tslib_pibase {

	/**
	 *
	 * @param array $params
	 * @param object $that
	 * @return void
	 */
	function intPages(&$params, &$that) {
		if (!$GLOBALS['TSFE']->isINTincScript())
			return;
		$this->optContent($params['pObj']->content);
	}

	/**
	 *
	 * @param array $params
	 * @param object $that
	 * @return void
	 */
	function noIntPages(&$params, &$that) {
		if ($GLOBALS['TSFE']->isINTincScript())
			return;
		$this->optContent($params['pObj']->content);
	}

	/**
	 * Opt the content
	 * 
	 * @param string $content 
	 * @return void
	 */
	protected function optContent(&$content) {
		$tx_sourceopt_pi1 = t3lib_div::makeInstance('tx_sourceopt_pi1');
		$tx_sourceopt_pi1->main($content, $this->getConfiguration());
	}

	/**
	 * Get the Configuration
	 * @return array
	 */
	protected function getConfiguration() {
		return is_array($GLOBALS['TSFE']->config['config']['sourceopt.']) ? $GLOBALS['TSFE']->config['config']['sourceopt.'] : $GLOBALS['TSFE']->tmpl->setup['config.']['sourceopt.'];
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/class.tx_sourceopt_fehook.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sourceopt/class.tx_sourceopt_fehook.php"]);
}
?>