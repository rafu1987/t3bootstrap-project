<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski (stefan.galinski@gmail.com)
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

/**
 * This class contains a hook method for the "clear all cache" action in the TYPO3 backend.
 */
class tx_scriptmerger_cache {
	/**
	 * Contains the temporary directories of this extension.
	 *
	 * @var array
	 */
	protected $tempDirectories = array();

	/**
	 * Initializes some class variables...
	 */
	public function __construct() {
		$this->tempDirectories = array(
			PATH_site . 'typo3temp/scriptmerger/' => 0,
			PATH_site . 'typo3temp/scriptmerger/temp/' => 0,
			PATH_site . 'typo3temp/scriptmerger/uncompressed/' => 1209600,
			PATH_site . 'typo3temp/scriptmerger/compressed/' => 1209600
		);
	}

	/**
	 * Clear cache post processor
	 *
	 * This method deletes all temporary files that are older than one month and
	 * if the deletion of the whole cache is requested.
	 *
	 * @param object $params parameter array
	 * @return void
	 */
	public function clearCachePostProc(&$params) {
		if ($params['cacheCmd'] !== 'all') {
			return;
		}

		$now = time();
		foreach ($this->tempDirectories as $tempDirectory => $maxAge) {
			if (!is_dir($tempDirectory)) {
				continue;
			}

			$handle = opendir($tempDirectory);
			while (FALSE !== ($file = readdir($handle))) {

				if ($file === '.' || $file === '..') {
					continue;
				}

				if (is_file($tempDirectory . $file)) {
					// get last modification time
					$lastAccess = fileatime($tempDirectory . $file);
					$age = $now - $lastAccess;

					if ($age >= $maxAge) {
						unlink($tempDirectory . $file);
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE')
	&& $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/scriptmerger/class.tx_scriptmerger_cache.php']
) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/scriptmerger/class.tx_scriptmerger_cache.php']);
}

?>