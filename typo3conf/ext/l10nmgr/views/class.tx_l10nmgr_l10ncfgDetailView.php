<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Kasper Skårhøj <kasperYYYY@typo3.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
***************************************************************/



/**
 * l10nmgr detail view:
 * 	renders information for a l10ncfg record.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @author	Daniel Pötzinger <development@aoemedia.de>
 *
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_l10ncfgDetailView {

	/**
	 * @var tx_l10nmgr_l10nConfiguration
	 */
	var $l10ncfgObj;	// Internal array (=datarow of config record)

	/**
	 * @var $doc
	 */
	var $doc = null;

	/**
	 * constructor. Set the internal required objects as parameter in constructor (kind of dependency injection, and communicate the dependencies)
	 * @param tx_l10nmgr_l10nConfiguration $l10ncfgObj
	 * @param template $doc Reference to the calling template object
	 * @return void
	 **/
	function tx_l10nmgr_l10ncfgDetailView($l10ncfgObj, $doc) {
		$this->l10ncfgObj = $l10ncfgObj;
		$this->doc = $doc;
	}

	/**
	 * checks if the internal tx_l10nmgr_l10nConfiguration object is valid
	 *
	 * @return void
	 **/
	function _hasValidConfig() {
		if (is_object($this->l10ncfgObj) && $this->l10ncfgObj->isLoaded()) {
			return true;
		}
		else  {
			return false;
		}
	}
	/**
	 * returns HTML table with infos for the l10nmgr config.
	 *	(needs valid configuration to be set)
	 *
	 * @return string HTML to display
	 **/
	function render()	{
		global $LANG;
		$content = '';

		if (!$this->_hasValidConfig()) {
			return $LANG->getLL('general.export.configuration.error.title');
		}

		$configurationSettings = '
				<table border="0" cellpadding="0" cellspacing="0" class="typo3-dblist">
					<tr class="t3-row-header">
						<td colspan="4"><strong>'.htmlspecialchars($this->l10ncfgObj->getData('title')).' ['.$this->l10ncfgObj->getData('uid').']</strong></td>
					</tr>
					<tr class="db_list_normal">
						<td><strong>'.$LANG->getLL('general.list.headline.depth.title').':</strong></td>
						<td>'.htmlspecialchars($this->l10ncfgObj->getData('depth')).'&nbsp;</td>
						<td><strong>'.$LANG->getLL('general.list.headline.tables').':</strong></td>
						<td>'.htmlspecialchars($this->l10ncfgObj->getData('tablelist')).'&nbsp;</td>
					</tr>
					<tr class="db_list_normal">
						<td><strong>'.$LANG->getLL('general.list.headline.exclude.title').':</strong></td>
						<td>'.htmlspecialchars($this->l10ncfgObj->getData('exclude')).'&nbsp;</td>
						<td><strong>'.$LANG->getLL('general.list.headline.include.title').':</strong></td>
						<td>'.htmlspecialchars($this->l10ncfgObj->getData('include')).'&nbsp;</td>
					</tr>
				</table>';

		$content .= $this->doc->section($LANG->getLL('general.export.configuration.title'), $configurationSettings);

		return $content;

	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/class.tx_l10nmgr_l10ncfgDetailView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/class.tx_l10nmgr_l10ncfgDetailView.php']);
}


?>
