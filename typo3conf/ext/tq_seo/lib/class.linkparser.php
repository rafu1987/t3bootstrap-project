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
 * LinkParser
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.linkparser.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class user_tqseo_linkparser {

	/**
	 * Add MetaTags
	 *
	 * @return	string			XHTML Code with metatags
	 */
	public function main( &$param, $pObj ) {
		global $TSFE;

		$pageUid = NULL;

		// Try to find pageUid
		if(!empty($param['conf']['parameter'])) {
			$pageUid = $param['conf']['parameter'];
		} elseif( !empty($pObj->parameters['allParams']) ) {
			$parameters = explode(' ', $pObj->parameters['allParams']);
			$pageUid = reset($parameters);
		}

		if(!empty($pageUid)) {
			$pageInfo = $GLOBALS['TSFE']->sys_page->getPage($pageUid);

			if( !empty($pageInfo['tx_tqseo_is_nofollow']) || !empty($pageInfo['tx_tqseo_is_exclude']) ) {
				$param['finalTag'] = str_replace('<a ', '<a rel="nofollow" ', $param['finalTag'] );
				$param['finalTagParts']['aTagParams'] .= 'rel="nofollow" ';
			}
		}

	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.linkparser.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.linkparser.php']);
}
?>