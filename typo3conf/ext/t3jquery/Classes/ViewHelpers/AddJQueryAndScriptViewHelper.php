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
 * ViewHelper which allows to jquery and additional files
 *
 * = Examples =
 *
 * <code title="Simple">
 *   {namespace t3jquery=Tx_T3jquery_ViewHelpers}
 *   
 *   <t3jquery:AddJQueryAndScript jsfile="EXT:example/main.js"/>
 * </code>
 * <output>
 *
 * See paramlist for more options, these are oriented on typoscript options
 *
 * </output>
 *
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 * @license http://www.gnu.org/copyleft/gpl.html
 */

class Tx_T3jquery_ViewHelpers_AddJQueryAndScriptViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	/**
	 * @param string $jsfile
	 * @param string $jsurl
	 * @param string $jsdata
	 * @param string $jsready
	 * @param boolean $forceOnTop
	 * @param string $compress
	 * @param string $type
	 * @param boolean $tofooter
	 * @param boolean $renderChildrenToData
	 * @return string
	 */

	public function render($jsfile = NULL, $jsurl = NULL, $jsdata = NULL, $jsready = NULL, $forceOnTop = NULL, $compress = NULL, $type = "text/javascript", $tofooter = null, $renderChildrenToData = false) {
		$buffer_data = NULL;
		$buffer_ready = NULL;
		if ($renderChildrenToData === true ) {
			$buffer_data = $this->renderChildren();
		} else {
			$buffer_ready = $this->renderChildren();
		}
			// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			$config = array();
			if($jsfile !== NULL) {
				$config['jsfile'] = $jsfile;
			}
			if($jsurl !== NULL) {
				$config['jsurl'] = $jsurl;
			}
			if($jsdata !== NULL) {
				$config['jsdata'] = $buffer_data . "\n" . $jsdata;
			} else {
				$config['jsdata'] = $buffer_data;
			}
			if($jsready !== NULL) {
				$config['jsready'] = $buffer_ready . "\n" . $jsready;
			} else {
				$config['jsready'] = $buffer_ready;
			}
			if($forceOnTop !== NULL) {
				$config['forceOnTop'] = $forceOnTop;
			}
			if($compress !== NULL) {
				$config['compress'] = $compress;
			}
			if($type !== NULL) {
				$config['type'] = $type;
			}
			if($tofooter !== NULL) {
				$config['tofooter'] = $tofooter;
			}
			tx_t3jquery::addJS('', $config);
		}
		return '';
	}
}
?>