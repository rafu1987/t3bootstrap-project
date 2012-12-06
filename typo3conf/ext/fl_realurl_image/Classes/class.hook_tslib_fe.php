<?php

/* * *************************************************************
 *  Copyright notice
 *  
 *  (c) 2008 Tim Lochmueller
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
 * ************************************************************* */

require_once (t3lib_extMgm::extPath('fl_realurl_image') . 'Classes/class.tx_flrealurlimage.php');  # the main class of fl_realurl_image

class hook_tslib_fe {
	/*
	 * Hook method for realurl image decode
	 *
	 * @param	
	 * @param	 	 
	 * @return 	nothing
	 */

	function checkImageDecode(&$params, &$ref) {
		// call fl_realurl_image
		$tx_flrealurlimage = new tx_flrealurlimage();
		#$tx_flrealurlimage->init($conf,$info);
		$tx_flrealurlimage->showImage();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/class.hook_tslib_fe.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/class.hook_tslib_fe.php']);
}
?>