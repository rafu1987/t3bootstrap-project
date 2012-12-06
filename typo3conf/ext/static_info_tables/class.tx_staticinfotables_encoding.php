<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2007 René Fritz (r.fritz@colorcube.de)
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
***************************************************************/
/**
 * Functions to convert the character encoding of the static info tables
 *
 * $Id: class.tx_staticinfotables_encoding.php 8613 2008-03-16 07:07:25Z franzholz $
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_staticinfotables_encoding
 *
 *              SECTION: GUI functions
 *   80:     function getEncodingSelect ($elementName, $currentKey, $firstEntry='', $unsetEntries='')
 *
 *              SECTION: Processing functions
 *  159:     function convertEncodingTable($table, $source_encoding, $dest_encoding='utf-8')
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


global $TYPO3_CONF_VARS;


/**
 * Functions to convert the character encoding of the static info tables
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */
class tx_staticinfotables_encoding {



	/*************************************
	 *
	 * GUI functions
	 *
	 *************************************/


	/**
	 * Returns a selector box with charset encodings
	 *
	 * @param	string		$elementName it the form elements name, probably something like "SET[...]"
	 * @param	string		$currentKey is the key to be selected currently.
	 * @param	string		$firstEntry is the key to be placed on top as first (default) entry.
	 * @param	string		$unsetEntries List of keys that should be removed (comma list).
	 * @return	string		HTML code for selector box
	 */
	function getEncodingSelect ($elementName, $currentKey, $firstEntry='', $unsetEntries='')	{

		$menuItems = array(
			'utf-8' => 'UTF-8',
			'iso-8859-1' => 'ISO-8859-1 (Western Europe)',
			'iso-8859-2' => 'ISO-8859-2 (Central Europe)',
			'iso-8859-3' => 'ISO-8859-3 (Latin 3)',
			'iso-8859-4' => 'ISO-8859-4 (Baltic)',
			'iso-8859-5' => 'ISO-8859-5 (Cyrillic)',
			'iso-8859-6' => 'ISO-8859-6 (Arabic)',
			'iso-8859-7' => 'ISO-8859-7 (Greek)',
			'iso-8859-7' => 'ISO-8859-8 (Hebrew)',
			'iso-8859-9' => 'ISO-8859-9 (Turkish)',
			'iso-8859-14' => 'ISO-8859-14 (Celtic)',
			'iso-8859-15' => 'ISO-8859-15 (Latin 9)',
			'windows-1250' => 'Windows 1250 (ANSI - Central Europe)',
			'windows-1251' => 'Windows 1251 (ANSI - Cyrillic)',
			'windows-1252' => 'Windows 1252 (ANSI - Western Europe)',
			'windows-1253' => 'Windows 1253 (ANSI - Greek)',
			'windows-1254' => 'Windows 1254 (ANSI - Turkish)',
			'windows-1255' => 'Windows 1255 (ANSI - Hebrew)',
			'windows-1256' => 'Windows 1256 (ANSI - Arabic)',
			'windows-1257' => 'Windows 1257 (ANSI - Baltic)',
			'windows-1258' => 'Windows 1258 (ANSI - Vietnamese)',
			'koi-8r' => 'KOI-8R (Russian)',
			'shift_jis' => 'Shift JIS (Japanese)',
			'euc-jp' => 'EUC-JP (Japanese)',
			'gb2312' => 'GB2312 / EUC-CN (Chinese Simplified)',
			'big5' => 'Big5 (Chinese)',
			'ascii' => 'ASCII',
		);

		if($firstEntry AND $menuItems[$firstEntry]) {
			$entry = array($firstEntry => $menuItems[$firstEntry]);
			unset($menuItems[$firstEntry]);
			$menuItems = array_merge($entry, $menuItems);
		}

		$unsetEntries = explode(',', $unsetEntries);
		foreach($unsetEntries as $entry) {
			unset($menuItems[$entry]);
		}

		$options = array();
		foreach($menuItems as $value => $label)	{
			$options[] = '<option value="'.htmlspecialchars($value).'"'.(!strcmp($currentKey,$value)?' selected="selected"':'').'>'.
							t3lib_div::deHSCentities(htmlspecialchars($label)).
							'</option>';
		}
		if (count($options))	{
			return '

					<!-- charset encoding menu -->
					<select name="'.$elementName.'">
						'.implode('
						',$options).'
					</select>
						';
		}
	}



	/*************************************
	 *
	 * Processing functions
	 *
	 *************************************/


	/**
	 * Converts the font encoding of text fields of a selected table
	 * The fields must be marked in TCA...['config']['_is_string'] => 1
	 *
	 * @param	string		$table DB table
	 * @param	string		$source_encoding Current encoding used in DB
	 * @param	string		$dest_encoding Destination encoding
	 * @return	void
	 */
	function convertEncodingTable($table, $source_encoding, $dest_encoding='utf-8') {
		global $TCA;

		if ($source_encoding AND $dest_encoding) {
		 	t3lib_div::loadTCA($table);

		 		// search columns which are text
		 	$stringColumns = array();
		 	foreach ($TCA[$table]['columns'] as $field => $colDef) {
		 		if($colDef['config']['_is_string']) {
		 			$stringColumns[] = $field;
		 		}
		 	}

			$csconv = t3lib_div::makeInstance('t3lib_cs');
		 	$stringColumns[] = 'uid';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', $stringColumns), $table, '');
		 	while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
		 		$uid = $row['uid'];
		 		unset($row['uid']);
		 		foreach ($row as $field => $value) {
		 			$row[$field] = $csconv->conv($value, $source_encoding, $dest_encoding);
		 		}
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid='.intval($uid), $row);
		 	}
		}
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_encoding.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_encoding.php']);
}
?>