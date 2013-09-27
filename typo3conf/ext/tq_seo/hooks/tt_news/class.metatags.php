<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
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
 * TT_NEWS hook for metatags
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.metatags.php 55805 2011-12-22 12:57:10Z mblaschke $
 */
class tx_tqseo_hook_ttnews_metatags {

	/**
	 * Extra item marker hook for metatag fetching
	 *
	 * @param	array	$markerArray		Marker array
	 * @param	array	$row				Current tt_news row
	 * @param	array	$lConf				Local configuration
	 * @param	object	$ttnewsObj			Pi-object from tt_news
	 * @return	array						Marker array (not changed)
	 */
	public function extraItemMarkerProcessor($markerArray, $row, $lConf, $ttnewsObj) {
		global $TSFE;

		$theCode = (string)strtoupper(trim($ttnewsObj->theCode));

		switch($theCode) {
			case 'SINGLE':
			case 'SINGLE2':
				// Keywords
				if( !empty($row['keywords']) ) {
					$TSFE->page['keywords']			= $row['keywords'];
					$TSFE->cObj->data['keywords']	= $row['keywords'];
				}

				// Short/Description
				if( !empty($row['short']) ) {
					$TSFE->page['description']			= $row['short'];
					$TSFE->cObj->data['description']	= $row['short'];
				}

				// Author
				if( !empty($row['author']) ) {
					$TSFE->page['author']		= $row['author'];
					$TSFE->cObj->data['author']	= $row['author'];
				}

				// E-Mail
				if( !empty($row['author_email']) ) {
					$TSFE->page['email']		= $row['author_email'];
					$TSFE->cObj->data['email']	= $row['author_email'];
				}
				break;
		}

		return $markerArray;
	}

}

?>