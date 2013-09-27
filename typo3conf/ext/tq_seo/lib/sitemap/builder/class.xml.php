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
 * Sitemap XML
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.xml.php 62829 2012-05-25 08:18:37Z mblaschke $
 */
class tx_tqseo_sitemap_builder_xml extends tx_tqseo_sitemap_builder_base {

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Create sitemap index
	 *
	 * @return	string
	 */
	public function sitemapIndex() {
		global $TSFE;

		$pageLimit		= 10000;

		if( isset($this->tsSetup['pageLimit']) && $this->tsSetup['pageLimit'] != '' ) {
			$pageLimit = (int)$this->tsSetup['pageLimit'];
		}

		$sitemaps		= array();
		$pageItems		= count($this->sitemapPages);
		$pageCount		= ceil($pageItems/$pageLimit);

		$linkConf = array(
			'parameter'			=> tx_tqseo_tools::getCurrentPid().','.$TSFE->type,
			'additionalParams'	=> '',
			'useCacheHash'		=> 1,
		);

		for($i=0; $i < $pageCount; $i++) {
			if( $this->indexPathTemplate ) {
				$link = str_replace('###PAGE###', $i, $this->indexPathTemplate);
				$sitemaps[] = $link;
			} else {
				$linkConf['additionalParams'] = '&page='.($i+1);
				$sitemaps[] = tx_tqseo_tools::fullUrl( $TSFE->cObj->typoLink_URL($linkConf) );
			}
		}

		$ret = '<?xml version="1.0" encoding="UTF-8"?>';
		$ret .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$ret .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';


		foreach($sitemaps as $sitemapPage) {
			$ret .= '<sitemap><loc>'.htmlspecialchars($sitemapPage).'</loc></sitemap>';
		}

		$ret .= '</sitemapindex>';

		// Call hook
		tx_tqseo_tools::callHook('sitemap-xml-index-output', $this, $ret);

		return $ret;
	}

	/**
	 * Create sitemap (for page)
	 *
	 * @param	integer	$page	Page
	 * @return	string
	 */
	public function sitemap($page = null) {
		$ret = '';

		$pageLimit		= 10000;

		if( isset($this->tsSetup['pageLimit']) && $this->tsSetup['pageLimit'] != '' ) {
			$pageLimit = (int)$this->tsSetup['pageLimit'];
		}

		$pageItems		= count($this->sitemapPages);
		$pageItemBegin	= $pageLimit * ($page-1);
		$pageCount		= ceil($pageItems/$pageLimit);


		if( $pageItemBegin <= $pageItems) {
			$this->sitemapPages = array_slice($this->sitemapPages, $pageItemBegin, $pageLimit);
			$ret = $this->createSitemapPage( $page );
		}

		return $ret;
	}

	/**
	 * Create Sitemap Page
	 *
	 * @return string 		XML Sitemap
	 */
	protected function createSitemapPage() {
		$ret = '<?xml version="1.0" encoding="UTF-8"?>';
		$ret .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
		$ret .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$ret .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9';
		$ret .= ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

		$pagePriorityDefaultValue		= (float)tx_tqseo_tools::getRootSettingValue('sitemap_priorty', 0);
		$pagePriorityDepthMultiplier	= (float)tx_tqseo_tools::getRootSettingValue('sitemap_priorty_depth_multiplier', 0);
		$pagePriorityDepthModificator	= (float)tx_tqseo_tools::getRootSettingValue('sitemap_priorty_depth_modificator', 0);

		if($pagePriorityDefaultValue == 0) {
			$pagePriorityDefaultValue = 1;
		}

		if($pagePriorityDepthMultiplier == 0) {
			$pagePriorityDepthMultiplier = 1;
		}

		if($pagePriorityDepthModificator == 0) {
			$pagePriorityDepthModificator = 1;
		}


		#####################
		# SetupTS conf
		#####################

		foreach($this->sitemapPages as $sitemapPage) {
			if(empty($this->pages[ $sitemapPage['page_uid'] ])) {
				// invalid page
				continue;
			}

			$page = $this->pages[ $sitemapPage['page_uid'] ];

			#####################################
			# Page priority
			#####################################
			$pageDepth = $sitemapPage['page_depth'];
			$pageDepthBase = 1;

			if(!empty($sitemapPage['page_hash'])) {
				// page has module-content - trade as subpage
				++$pageDepth;
			}

			$pageDepth -= $pagePriorityDepthModificator;


			if($pageDepth > 0.1) {
				$pageDepthBase = 1/$pageDepth;
			}

			$pagePriority = $pagePriorityDefaultValue * ( $pageDepthBase * $pagePriorityDepthMultiplier );
			if(!empty($page['tx_tqseo_priority'])) {
				$pagePriority = $page['tx_tqseo_priority'] / 100;
			}

			$pagePriority = number_format($pagePriority, 2);

			if($pagePriority > 1) {
				$pagePriority = '1.00';
			} elseif($pagePriority <= 0) {
				$pagePriority = '0.00';
			}

			#####################################
			# Page informations
			#####################################

			// page Url
			$pageUrl = tx_tqseo_tools::fullUrl( $sitemapPage['page_url'] );

			// Page modification date
			$pageModifictionDate = date('c', $sitemapPage['tstamp']);

			// Page change frequency
			$pageChangeFrequency = NULL;
			if( !empty($page['tx_tqseo_change_frequency']) ) {
				$pageChangeFrequency = (int)$page['tx_tqseo_change_frequency'];
			} elseif( !empty($sitemapPage['page_change_frequency']) ) {
				$pageChangeFrequency = (int)$sitemapPage['page_change_frequency'];
			}

			if( !empty($pageChangeFrequency) && !empty( $this->pageChangeFrequency[$pageChangeFrequency] ) ) {
				$pageChangeFrequency = $this->pageChangeFrequency[$pageChangeFrequency];
			} else {
				$pageChangeFrequency = NULL;
			}


			#####################################
			# Sitemal page output
			#####################################
			$ret .= '<url>';
			$ret .= '<loc>'.htmlspecialchars($pageUrl).'</loc>';
			$ret .= '<lastmod>'.$pageModifictionDate.'</lastmod>';

			if( !empty($pageChangeFrequency) ) {
				$ret .= '<changefreq>'.htmlspecialchars($pageChangeFrequency).'</changefreq>';
			}

			$ret .= '<priority>'.$pagePriority.'</priority>';

			$ret .= '</url>';
		}


		$ret .= '</urlset>';

		// Call hook
		tx_tqseo_tools::callHook('sitemap-xml-page-output', $this, $ret);

		return $ret;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/builder/class.xml.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/sitemap/builder/class.xml.php']);
}
?>