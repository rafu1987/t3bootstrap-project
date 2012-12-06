<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2008 René Fritz (r.fritz@colorcube.de)
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
***************************************************************/
/**
 * example for using the hotlist functionality with the sys_language table
 *
 * $Id: class.tx_staticinfotables_syslanguage.php 8613 2008-03-16 07:07:25Z franzholz $
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */



/**
 * example for using the hotlist functionality with the sys_language table
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */
class tx_staticinfotables_syslanguage {

	function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, &$pObj) {
		if($table == 'sys_language' AND $fieldArray['static_lang_isocode']) {
				// the hotlist will be updated only if the field changed, because only then it's in the $fieldArray
			tx_staticinfotables_div::updateHotlist ('static_languages', $fieldArray['static_lang_isocode'], 'uid');
		}
	}
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_syslanguage.php'])    {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_syslanguage.php']);
}
?>