<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Tim Lochmueller (webmaster@fruit-lab.de)
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
 * ************************************************************* */

class Tx_FlRealurlImage_CleanTask extends tx_scheduler_Task {

	/**
	 * Run the Scheduler
	 *
	 * @return boolean
	 */
	public function execute() {
		$this->removeDoubleEntries();
		#$this->checkImagePath();

		return true;
	}

	/**
	 * Remove double entries
	 */
	protected function removeDoubleEntries() {
		$sql = "SELECT COUNT( * ) c, image_path FROM  tx_flrealurlimage_cache GROUP BY image_path ORDER BY c DESC LIMIT 0, 20";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['c'] > 1) {
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_flrealurlimage_cache', 'image_path="' . $row['image_path'] . '"', '', 'crdate ASC', $row['c']-1);
				
				$ids = array();
				foreach($rows as $r)
					$ids[] = $r['uid'];
				
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_flrealurlimage_cache', 'uid IN ('.implode(',', $ids).')'); 
				
				$msg = 'Found '.$row['c'].' of "'.$row['image_path'].'"-path and delete '.sizeof($ids).' entries.';
				t3lib_FlashMessageQueue::addMessage(
					t3lib_div::makeInstance('t3lib_FlashMessage', '', $msg, t3lib_FlashMessage::INFO)
				);
			}
		}
	}

	/**
	 * Chek the Image Path
	 */
	protected function checkImagePath() {
		$sql = "SELECT uid,image_path FROM tx_flrealurlimage_cache";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if (!file_exists(PATH_site . $row['image_path'])) {
				echo $row['image_path'] . ' -- ' . $row['uid'] . '<br />';
			}
		}

		die();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/Service/CleanTask.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fl_realurl_image/Classes/Service/CleanTask.php']);
}
?>