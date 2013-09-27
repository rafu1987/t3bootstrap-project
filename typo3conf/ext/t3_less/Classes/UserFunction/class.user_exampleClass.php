<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013 David Greiner <hallo@davidgreiner.de>
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
 * ************************************************************* */

/**
 *
 *
 * @package TYPO3
 * @subpackage t3_less
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author  David Greiner <hallo@davidgreiner.de>
 */

/**
 * Example function to register custom function
 * http://leafo.net/lessphp/docs/#custom_functions
 *
 * Does anyone know why the original example with "return array ($ type, $ value * 2)" does not work?
 * This function works only if a var_dump or echo is executed. very strange...
 * Please give me a hint on hallo@davidgreiner.de
 *
 */
class user_exampleClass
{

	public function exampleFunction( $arg )
	{

		list($type, $value, $dimension) = $arg;
		//spooky workaround
		return $value * 2 . $dimension;
	}

}

?>
