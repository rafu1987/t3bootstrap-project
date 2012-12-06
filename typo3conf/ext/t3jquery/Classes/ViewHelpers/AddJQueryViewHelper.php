<?php
/*                                                                        *
 * This script belongs to the fluid_css extension.                        *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * ViewHelper which allows to jquery
 *
 * = Examples =
 *
 * <code title="Simple">
 *   {namespace t3jquery=Tx_T3jquery_ViewHelpers}
 *   
 *   <t3jquery:AddJQuery />
 * </code>
 * <output>
 *
 * </output>
 *
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 * @license http://www.gnu.org/copyleft/gpl.html
 */

class Tx_T3jquery_ViewHelpers_AddJQueryViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {
	/**
	* Adds T3Jquery as Lib
	* @param string color 
	* @param string delta	
	*/
	public function render() {
			// checks if t3jquery is loaded
		if (t3lib_extMgm::isLoaded('t3jquery')) {
			require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
		}
			// if t3jquery is loaded and the custom Library had been created
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		}
		return '';
	}
}
?>