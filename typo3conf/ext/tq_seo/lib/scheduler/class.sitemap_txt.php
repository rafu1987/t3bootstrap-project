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
 * Scheduler Task Sitemap TXT
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id: class.sitemap_txt.php 62700 2012-05-22 15:53:22Z mblaschke $
 */
class tx_tqseo_scheduler_task_sitemap_txt extends tx_tqseo_scheduler_task_sitemap_base {

	###########################################################################
	# Attributes
	###########################################################################

	/**
	 * Sitemap base directory
	 *
	 * @var string
	 */
	protected $_sitemapDir = 'uploads/tx_tqseo/sitemap_txt';

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Build sitemap
	 *
	 * @param	integer	$rootPageId	Root page id
	 * @param	integer	$languageId	Language id
	 */
	protected function _buildSitemap($rootPageId, $languageId) {

		if( $languageId !== null ) {
			// Language lock enabled
			$sitemapFileName	= 'sitemap-r%s-l%s.txt.gz';
		} else {
			$sitemapFileName	= 'sitemap-r%s.txt.gz';
		}

		$builder = new tx_tqseo_sitemap_builder_txt();
		$content = $builder->sitemap();

		$fileName = sprintf($sitemapFileName, $rootPageId, $languageId);
		$this->_writeToFile(PATH_site.'/'.$this->_sitemapDir.'/'.$fileName, $content);

		return true;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/scheduler/class.sitemap_txt.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/scheduler/class.sitemap_txt.php']);
}
?>
