<?php
/***************************************************************
*  Copyright notice
*
*  (c) Stanislas Rolland <typo3(arobas)sjbr.ca>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
	// Make sure that we are executed only in TYPO3 context
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
/**
 * Class providing configuration help for Static Info Tables
 *
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class tx_staticinfotables_emconfhelper {

	/**
	 * Renders an input element that allows to enter the charset to be used.
	 *
	 * @param	array				$params: Field information to be rendered
	 * @param	t3lib_tsStyleConfig		$pObj: The calling parent object.
	 * @return	string				The HTML input field
	 */
	public function buildCharsetField(array $params, t3lib_tsStyleConfig $pObj) {
		$fieldName = substr($params['fieldName'], 5, -1);

		$typo3Version = class_exists('t3lib_utility_VersionNumber') ? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) : t3lib_div::int_from_ver(TYPO3_version);
		$readonly = ($typo3Version < 4007000) ? '' : ' readonly="readonly"';
		$field = '<input id="' . $fieldName . '" name="' . $params['fieldName'] . '" value="utf-8"' . $readonly . ' />';

		return $field;
	}
}
?>