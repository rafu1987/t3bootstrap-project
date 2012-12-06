<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch, Tobias Schenk (web@medialis.net)
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

class ux_tx_scriptmerger_cache extends tx_scriptmerger_cache {
	public function __construct() {
		// Get extension config
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['scriptmergercache']);

		$this->tempDirectories = array(
			PATH_site . 'typo3temp/scriptmerger/' => 0,
			PATH_site . 'typo3temp/scriptmerger/temp/' => 0,
			PATH_site . 'typo3temp/scriptmerger/uncompressed/' => $extConf['cacheTime'],
			PATH_site . 'typo3temp/scriptmerger/compressed/' => $extConf['cacheTime']
		);
	}
}

?>