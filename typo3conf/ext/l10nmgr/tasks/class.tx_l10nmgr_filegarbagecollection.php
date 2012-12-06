<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Francois Suter <typo3@cobweb.ch>
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
 * L10N Manager file garbage collection task
 *
 * The L10N Manager creates quite a large number of output files. It is necessary
 * to clean them up regularly, lest they accumulate and clog the file system.
 *
 * Credits: some code taken from task tx_scheduler_RecyclerGarbageCollection by Kai Vogel
 *
 * @author Francois Suter <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_FileGarbageCollection extends tx_scheduler_Task {

	/**
	 * @var int Age of files to delete
	 */
	public $age = 30;
	/**
	 * @var string Pattern for files to exclude from clean up
	 */
	public $excludePattern = '(index\.html|\.htaccess)';

	/**
	 * @var array List of directories in which files should be cleaned up
	 */
	protected static $targetDirectories = array(
		'uploads/tx_l10nmgr/saved_files',
		'uploads/tx_l10nmgr/jobs/out',
		'uploads/tx_l10nmgr/jobs/in'
	);

	/**
	 * Removes old files, called by the Scheduler.
	 *
	 * @return boolean TRUE if task run was successful
	 */
	public function execute() {
			// There is no file ctime on windows, so this task disables itself if OS = win
		if (TYPO3_OS == 'WIN') {
			throw new Exception(
				'This task is not reliable on Windows OS',
				1323272367
			);
		}

			// Calculate a reference timestamp, based on age of files to delete
		$seconds = (60 * 60 * 24 * (int)$this->age);
		$timestamp = ($GLOBALS['EXEC_TIME'] - $seconds);

			// Loop on all target directories
		$globalResult = TRUE;
		foreach (self::$targetDirectories as $directory) {
			$result = $this->cleanUpDirectory($directory, $timestamp);
			$globalResult &= $result;
		}
			// Return the global result, which is a success only if all directories could be cleaned up without problem
		return $globalResult;
	}

	/**
	 * Gets a list of all files in a directory recursively and removes
	 * old ones.
	 *
	 * @throws RuntimeException If folders are not found or files can not be deleted
	 * @param string $directory Path to the directory
	 * @param integer $timestamp Timestamp of the last file modification
	 * @return boolean TRUE if success
	 */
	protected function cleanUpDirectory($directory, $timestamp) {
		$fullPathToDirectory = t3lib_div::getFileAbsFileName($directory);

			// Check if given directory exists
		if (!(@is_dir($fullPathToDirectory))) {
			throw new RuntimeException(
				'Given directory "' . $fullPathToDirectory . '" does not exist',
				1323272107
			);
		}

			// Find all files in the directory
		$directoryContent = new DirectoryIterator($fullPathToDirectory);
			/** @var $fileObject SplFileInfo */
		$fileObject = NULL;
		foreach ($directoryContent as $fileObject) {

				// Remove files that are older than given timestamp and don't match the exclude pattern
			if ($fileObject->isFile() && !preg_match('/' . $this->excludePattern . '/i', $fileObject->getFilename()) && $fileObject->getCTime() < $timestamp) {
				if (!(@unlink($fileObject->getRealPath()))) {
					throw new RuntimeException(
						'Could not remove file "' . $fileObject->getRealPath() . '"',
						1323272115
					);
				}
			}
		}

		return TRUE;
	}
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/l10nmgr/tasks/class.tx_l10nmgr_filegarbagecollection.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/l10nmgr/tasks/class.tx_l10nmgr_filegarbagecollection.php']);
}

?>
