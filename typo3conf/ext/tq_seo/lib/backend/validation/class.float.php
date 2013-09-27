<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 TEQneers GmbH & Co. KG <info@teqneers.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * TYPO3 Backend field validation: float
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class  tx_tqseo_backend_validation_float {

	/**
	 * Returns Javscript validation function body
	 *
	 * @return string
	 */
	public function returnFieldJS() {
		return '
value = value.replace(/[^-0-9,.]/g,\'\');

var ret = 0;
try {
	if( isNaN(value) ) {
		value = 0;
	}

	ret = parseFloat(value);
} catch(e) {}

if( isNaN(ret) ) {
	ret = 0;
}

return ret;
';
	}

	/**
	 * Validate number on serverside
	 *
	 * @param	string	$value	Value
	 * @param	mixed	$is_in	Is in value (config)
	 * @param	mixed	$set	Set
	 * @return	float
	 */
	function evaluateFieldValue($value, $is_in, &$set) {
		return (float)$value;
	}
}

?>