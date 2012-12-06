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
 * translationData: encapsulates the data which are needed for saving a new translation.
 *
 * @author	Daniel Poetzinger <development@aoemedia.de>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_translationData {

	var $data=array();
	var $sysLang;

	function setTranslationData($data) {
		$this->data=$data;
	}

	function setLanguage($sysLang) {
		$this->sysLang=$sysLang;
	}

	function &getTranslationData() {
		return $this->data;
	}

	function getLanguage() {
		return $this->sysLang;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/class.tx_l10nmgr_translationData.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/models/class.tx_l10nmgr_translationData.php']);
}


?>
