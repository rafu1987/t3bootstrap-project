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
 * Sitemap Output Base
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.base.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
abstract class tx_tqseo_sitemap_output_base {

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Output sitemap
	 *
	 * @return	string
	 */
	public function main() {
		global $TSFE, $TYPO3_DB, $TYPO3_CONF_VARS;

		// INIT
		$this->tsSetup		= $TSFE->tmpl->setup['plugin.']['tq_seo.']['sitemap.'];

		// check if sitemap is enabled in root
		if( !tx_tqseo_tools::getRootSettingValue('is_sitemap', true) ) {
			$this->showError('Sitemap is not available, please check your configuration [control-center]');
		}

		$ret .= $this->_build();

		return $ret;
	}

	/**
	 * Show error
	 *
	 * @param	string	$msg			Message
	 */
	protected function showError($msg = null) {
		global $TSFE;

		if( $msg === null ) {
			$msg = 'Sitemap is not available, please check your configuration';
		}

		header('HTTP/1.0 503 Service Unavailable');
		$TSFE->pageErrorHandler( true, NULL, $msg );
		exit;
	}

	###########################################################################
	# Abstract methods
	###########################################################################
	/*
	 * Build
	 *
	 * @return string
	 */
	abstract protected function _build();

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/output/class.base.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/output/class.base.php']);
}
?>