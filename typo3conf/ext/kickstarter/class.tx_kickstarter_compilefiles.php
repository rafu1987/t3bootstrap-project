<?php
/***************************************************************
*  Copyright notice
*
*  (c)  2001-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)  All rights reserved
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
 * TYPO3 Extension Kickstarter
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Ingo Renner <ingo@typo3.org>
 */

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');

class tx_kickstarter_compilefiles extends tx_kickstarter_sectionbase {

		// Internal:
	var $fileArray=array();
	var $ext_tables=array();
	var $ext_tca=array();
	var $ext_tables_sql=array();
	var $ext_localconf=array();
	var $ext_locallang=array();
	var $ext_locallang_db=array();

	var $extKey='';

	var $charMaxLng = 2;	// Varchars are created instead of chars when over this length.

	/**
	 * Creates all files that are necessary for an extension
	 * 	- ext_localconf.php
	 * 	- ext_tables.php
	 * 	- tca.php
	 * 	- ext_tables.sql
	 * 	- locallang.xml
	 * 	- locallang_db.xml
	 * 	- doc/wizard_form.html
	 * 	- doc/wizard_form.dat
	 * 	- ChangeLog
	 * 	- README.txt
	 * 	- ext_icon.gif
	 *
	 * @param	string		$extKey: the extension key
	 * @return	void
	 */
	function makeFilesArray($extKey)	{

		$this->ext_localconf=array();
		$this->ext_tables=array();
		$this->fileArray=array();

		foreach($this->wizArray as $catID=>$catData)	{
			if($this->sections[$catID]) {
				$path = t3lib_div::getFileAbsFileName($this->sections[$catID]['filepath']);
				if(is_file($path)) {
					require_once($path);
					$section = t3lib_div::makeInstance($this->sections[$catID]['classname']);
					$section->wizard = &$this;

					foreach($catData as $k=>$config) {
						$section->render_extPart($k,$config,$extKey);
					}
				}
			}
		}

		if (is_array($this->wizArray['sv']))	{
			reset($this->wizArray['sv']);
			while(list($k,$config)=each($this->wizArray['sv']))	{
				$this->EM_CONF_presets['clearCacheOnLoad']=1;
			}
		}

			// Write the ext_localconf.php file:
		if (count($this->ext_localconf))	{
			$this->addFileToFileArray('ext_localconf.php',trim($this->wrapBody('
				<?php
				if (!defined(\'TYPO3_MODE\')) {
					die (\'Access denied.\');
				}

					',
				implode(chr(10),$this->ext_localconf),
				'?>
			',0)));
		}
			// Write the ext_tables.php file:
		if (count($this->ext_tables))	{
			$this->addFileToFileArray('ext_tables.php',trim($this->wrapBody('
				<?php
				if (!defined(\'TYPO3_MODE\')) {
					die (\'Access denied.\');
				}

				',implode(chr(10),$this->ext_tables),'
				?>
			',0)));
		}
			// Write the tca.php file:
		if (count($this->ext_tca))	{
			$this->addFileToFileArray('tca.php',trim($this->wrapBody('
				<?php
				if (!defined(\'TYPO3_MODE\')) {
					die (\'Access denied.\');
				}

				',implode(chr(10),$this->ext_tca),'
				?>
			',0)));
		}
			// Write the ext_tables.sql file:
		if (count($this->ext_tables_sql))	{
			$this->addFileToFileArray('ext_tables.sql',trim($this->sPS(implode(chr(10),$this->ext_tables_sql))));
		}
			// Local lang file:
		if (count($this->ext_locallang))	{
			$this->addLocalLangFile($this->ext_locallang,'locallang.xml','Language labels for extension \''.$extKey.'\'');
		}
			// Local lang DB file:
		if (count($this->ext_locallang_db))	{
			$this->addLocalLangFile($this->ext_locallang_db,'locallang_db.xml','Language labels for database tables/fields belonging to extension \''.$extKey.'\'','database');
		}

			// The form used to generate the extension:
		$this->dontPrintImages = 1;
		$this->addFileToFileArray('doc/wizard_form.html',trim($this->sPS('
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

			<html>
			<head>
				<title>Untitled</title>
			</head>

			<body>

				'.$this->totalForm().'
			</body>
			</html>
		')));
		$this->addFileToFileArray('doc/wizard_form.dat',serialize($this->wizArray));

		$this->addFileToFileArray('ChangeLog',
date('Y-m-d') . '  ' . $this->userField('name') . '  <' . $this->userField('email') . '>

	* Initial code generated with kickstarter
'		);

		$this->addFileToFileArray('README.txt','
Feel free to add some documentation or simply add a link to the online manual.
'		);

			// icon:
		$this->addFileToFileArray('ext_icon.gif', t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter') . 'res/notfound.gif'));
	}



}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_compilefiles.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_compilefiles.php']);
}

?>