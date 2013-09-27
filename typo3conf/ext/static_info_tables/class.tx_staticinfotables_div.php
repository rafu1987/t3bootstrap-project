<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 René Fritz (r.fritz@colorcube.de)
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
 * Misc functions to access the static info tables
 *
 * $Id: class.tx_staticinfotables_div.php 55590 2011-12-17 01:13:30Z stan $
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   57: class tx_staticinfotables_div
 *   69:     function getTCAlabelField ($table, $loadTCA=TRUE, $lang='', $local=FALSE)
 *  119:     function isoCodeType ($isoCode)
 *  143:     function getIsoCodeField ($table, $isoCode, $bLoadTCA=TRUE, $index=0)
 *  169:     function getTCAsortField ($table, $loadTCA=TRUE)
 *  181:     function getCurrentLanguage ()
 *  215:     function getCurrentSystemLanguage ($where='')
 *  249:     function getCollateLocale ()
 *  282:     function getTitleFromIsoCode ($table, $isoCode, $lang='', $local=FALSE)
 *  341:     function replaceMarkersInSQL ($sql, $table, $row)
 *  383:     function selectItemsTCA ($params)
 *  480:     function updateHotlist ($table, $indexValue, $indexField='', $app='')
 *  542:     function &fetchCountries ($country, $iso2='', $iso3='', $isonr='')
 *  587:     function quoteJSvalue ($value, $inScriptTags=FALSE)
 *  609:     function loadTcaAdditions ($ext_keys)
 *
 * TOTAL FUNCTIONS: 14
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_staticinfotables_div {

	private static $cache = array();

	/**
	 * Returns a label field for the current language
	 *
	 * @param	string		table name
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded with t3lib_div::loadTCA(). It will be needed to set it to false if you call this function from inside of tca.php
	 * @param	string		language to be used
	 * @param	boolean		If set, we are looking for the "local" title field
	 * @return	string		field name
	 */
	function getTCAlabelField ($table, $loadTCA=TRUE, $lang='', $local=FALSE) {
		global $TCA, $LANG, $TSFE;

		if (is_object($LANG)) {
			$csConvObj = $LANG->csConvObj;
		} elseif (is_object($TSFE)) {
			$csConvObj = $TSFE->csConvObj;
		}

		if (!is_object($csConvObj))       {
			include_once(PATH_t3lib.'class.t3lib_cs.php');
			// The object may not exist yet, so we need to create it now.
			$csConvObj = &t3lib_div::makeInstance('t3lib_cs');
		}

		$labelFields = array();
		if($table && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'][$table]['label_fields'])) {
			if ($loadTCA)	{
				t3lib_div::loadTCA($table);

					// get all extending TCAs
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['extendingTCA']))	{
					tx_staticinfotables_div::loadTcaAdditions($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['extendingTCA']);
				}
			}

			$lang = $lang ? $lang : tx_staticinfotables_div::getCurrentLanguage();
			$lang = isset($csConvObj->isoArray[$lang]) ? $csConvObj->isoArray[$lang] : $lang;

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'][$table]['label_fields'] as $field) {
				if ($local) {
					$labelField = str_replace ('##', 'local', $field);
				} else {
					$labelField = str_replace ('##', $csConvObj->conv_case('utf-8',$lang,'toLower'), $field);
				}
				if (is_array($TCA[$table]['columns'][$labelField])) {
					$labelFields[] = $labelField;
				}
			}
		}
		return $labelFields;
	}


	/**
	 * Returns the type of an iso code: nr, 2, 3
	 *
	 * @param	string		iso code
	 * @return	string		iso code type
	 */
	function isoCodeType ($isoCode) {
		$type = '';
			// t3lib_utility_Math was introduced in TYPO3 4.6
		$isoCodeAsInteger = class_exists('t3lib_utility_Math')
			? t3lib_utility_Math::canBeInterpretedAsInteger($isoCode)
			: t3lib_div::testInt($isoCode);
		if ($isoCodeAsInteger) {
			$type = 'nr';
		} elseif (strlen($isoCode) == 2) {
			$type = '2';
		} elseif (strlen($isoCode) == 3) {
			$type = '3';
		}
		return $type;
	}


	/**
	 * Returns a iso code field for the passed table and iso code
	 *
	 *                                 $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables']
	 *
	 * @param	string		table name
	 * @param	string		iso code
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded with t3lib_div::loadTCA(). It will be needed to set it to FALSE if you call this function from inside of tca.php
	 * @param	integer		index in the table's isocode_field array in the global variable
	 * @return	string		field name
	 */
	function getIsoCodeField ($table, $isoCode, $bLoadTCA=TRUE, $index=0) {
		global $TCA;
		$rc = FALSE;

		if ($isoCode && $table && (($isoCodeField = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'][$table]['isocode_field'][$index]) != '')) {
			if ($bLoadTCA) {
				t3lib_div::loadTCA($table);
			}
			$type = tx_staticinfotables_div::isoCodeType($isoCode);
			$isoCodeField = str_replace ('##', $type, $isoCodeField);

			if (is_array($TCA[$table]['columns'][$isoCodeField])) {
				$rc = $isoCodeField;
			}
		}
		return $rc;
	}


	/**
	 * Returns a sort field for the current language
	 *
	 * @param	string		table name
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded
	 * @return	string		field name
	 */
	function getTCAsortField ($table, $loadTCA=TRUE) {
		$labelFields = tx_staticinfotables_div::getTCAlabelField($table, $loadTCA);

		return $labelFields[0];
	}


	/**
	 * Returns the current language as iso-2-alpha code
	 *
	 * @return	string		'DE', 'EN', 'DK', ...
	 */
	function getCurrentLanguage () {

 		if (is_object($GLOBALS['TSFE'])) {
			$langCodeT3 = $GLOBALS['TSFE']->lang;
			$csConvObj = $GLOBALS['TSFE']->csConvObj;
 		} elseif (is_object($GLOBALS['LANG'])) {
 			$langCodeT3 = $GLOBALS['LANG']->lang;
 			$csConvObj = $GLOBALS['LANG']->csConvObj;
		} else {
			return 'EN';
		}
		if ($langCodeT3 === 'default') {
			return 'EN';
		}
			// Return cached value if any
		if (isset(self::$cache['getCurrentLanguage'][$langCodeT3])) {	
			return self::$cache['getCurrentLanguage'][$langCodeT3];
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'lg_iso_2,lg_country_iso_2',
			'static_languages',
			'lg_typo3=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($langCodeT3, 'static_languages')
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$lang = $row['lg_iso_2'] . ($row['lg_country_iso_2'] ? '_' . $row['lg_country_iso_2'] : '');
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		$lang = $lang ? $lang : $csConvObj->conv_case('utf-8', $langCodeT3, 'toUpper');

			// Initialize cache array
		if (!is_array(self::$cache['getCurrentLanguage'])) {
			self::$cache['getCurrentLanguage'] = array();
		}
			// Cache retrieved value
		self::$cache['getCurrentLanguage'][$langCodeT3] = $lang;

		return $lang;
	}


	/**
	 * Returns the row of the current system language
	 *
	 * @param	[type]		$where: ...
	 * @return	array		row in the sys_language table
	 */
	function getCurrentSystemLanguage ($where='') {
		global $LANG, $TSFE, $TYPO3_DB;

		$rc = array();

		if (is_object($LANG)) {
			$langCodeT3 = $LANG->lang;
		} elseif (is_object($TSFE)) {
			$langCodeT3 = $TSFE->lang;
		} else {
			return $rc;
		}

		$res = $TYPO3_DB->exec_SELECTquery(
			'sys_language.uid',
			'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode=static_languages.uid',
			'static_languages.lg_typo3='.$TYPO3_DB->fullQuoteStr($langCodeT3,'static_languages').
				$where
			);
		while($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$rc[$row['uid']] = $row;
		}

		$TYPO3_DB->sql_free_result($res);
		return $rc;
	}


	/*
	 *
	 * Returns the locale to used when sorting labels
	 *
	 * @return	string	locale
	 */
	function getCollateLocale () {
		global $LANG, $TSFE, $TYPO3_DB;

		if (is_object($LANG)) {
			$langCodeT3 = $LANG->lang;
		} elseif (is_object($TSFE)) {
			$langCodeT3 = $TSFE->lang;
		} else {
			return 'C';
		}

		$res = $TYPO3_DB->exec_SELECTquery(
			'lg_collate_locale',
			'static_languages',
			'lg_typo3='.$TYPO3_DB->fullQuoteStr($langCodeT3,'static_languages')
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$locale = $row['lg_collate_locale'];
		}
		$TYPO3_DB->sql_free_result($res);
		return $locale ? $locale : 'C';
	}


	/**
	 * Fetches short title from an iso code
	 *
	 * @param	string		table name
	 * @param	string		iso code
	 * @param	string		language code - if not set current default language is used
	 * @param	boolean		local name only - if set local title is returned
	 * @return	string		short title
	 */
	function getTitleFromIsoCode ($table, $isoCode, $lang='', $local=FALSE) {
		global $TSFE, $TYPO3_DB;

		$title = '';
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang, $local);
		if (count ($titleFields))	{
			$prefixedTitleFields = array();
			foreach ($titleFields as $titleField) {
				$prefixedTitleFields[] = $table.'.'.$titleField;
			}
			$fields = implode(',', $prefixedTitleFields);
			$whereClause = '1=1';
			if (!is_array($isoCode)) {
				$isoCode = array($isoCode);
			}
			$index = 0;
			foreach ($isoCode as $index => $code) {
				if ($code != '')	{
					$tmpField = tx_staticinfotables_div::getIsoCodeField($table, $code, TRUE, $index);
					$tmpValue = $TYPO3_DB->fullQuoteStr($code,$table);
					if ($tmpField && $tmpValue)	{
						$whereClause .= ' AND ' . $table . '.' . $tmpField . ' = ' . $tmpValue;
					}
				}
			}
			if (is_object($TSFE)) {
				$enableFields = $TSFE->sys_page->enableFields($table);
			} else {
				$enableFields = t3lib_BEfunc::deleteClause($table);
			}

			$res = $TYPO3_DB->exec_SELECTquery(
				$fields,
				$table,
				$whereClause.$enableFields
			);
			if ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
				foreach ($titleFields as $titleField) {
					if ($row[$titleField]) {
						$title = $row[$titleField];
						break;
					}
				}
			}
			$TYPO3_DB->sql_free_result($res);
		}

		return $title;
	}


	/**
	 * Replaces any dynamic markers in a SQL statement.
	 *
	 * @param	string		The SQL statement with dynamic markers.
	 * @param	string		Name of the table.
	 * @param	array		row from table.
	 * @return	string		SQL query with dynamic markers subsituted.
	 */
	function replaceMarkersInSQL ($sql, $table, $row)	{

		$TSconfig = t3lib_BEfunc::getTCEFORM_TSconfig($table, $row);

		/* Replace references to specific fields with value of that field */
		if (strstr($sql,'###REC_FIELD_'))	{
			$sql_parts = explode('###REC_FIELD_',$sql);
			while(list($kk,$vv)=each($sql_parts))	{
				if ($kk)	{
					$sql_subpart = explode('###',$vv,2);
					$sql_parts[$kk]=$TSconfig['_THIS_ROW'][$sql_subpart[0]].$sql_subpart[1];
				}
			}
			$sql = implode('',$sql_parts);
		}

		/* Replace markers with TSConfig values */
		$sql = str_replace('###THIS_UID###',intval($TSconfig['_THIS_UID']),$sql);
		$sql = str_replace('###THIS_CID###',intval($TSconfig['_THIS_CID']),$sql);
		$sql = str_replace('###SITEROOT###',intval($TSconfig['_SITEROOT']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_ID###',intval($TSconfig[$field]['PAGE_TSCONFIG_ID']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_IDLIST###',$GLOBALS['TYPO3_DB']->cleanIntList($TSconfig[$field]['PAGE_TSCONFIG_IDLIST']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_STR###',$GLOBALS['TYPO3_DB']->quoteStr($TSconfig[$field]['PAGE_TSCONFIG_STR'], $table),$sql);

		return $sql;
	}


	/**
	 * Function to use in own TCA definitions
	 * Adds additional select items
	 *
	 * 			items		reference to the array of items (label,value,icon)
	 * 			config		The config array for the field.
	 * 			TSconfig	The "itemsProcFunc." from fieldTSconfig of the field.
	 * 			table		Table name
	 * 			row		Record row
	 * 			field		Field name
	 *
	 * @param	array		itemsProcFunc data array:
	 * @return	void		The $items array may have been modified
	 */
	function selectItemsTCA ($params) {
		global $TCA;

		$where = '';
		$config = &$params['config'];
		$table = $config['itemsProcFunc_config']['table'];
		$tcaWhere = $config['itemsProcFunc_config']['where'];
		if ($tcaWhere)	{
			$where = tx_staticinfotables_div::replaceMarkersInSQL($tcaWhere, $params['table'], $params['row']);
		}

		if ($table) {
			$indexField = $config['itemsProcFunc_config']['indexField'];
			$indexField = $indexField ? $indexField : 'uid';

			$lang = strtolower(tx_staticinfotables_div::getCurrentLanguage());
			$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
			$prefixedTitleFields = array();
			foreach ($titleFields as $titleField) {
				$prefixedTitleFields[] = $table.'.'.$titleField;
			}
			$fields = $table.'.'.$indexField.','.implode(',', $prefixedTitleFields);

			if ($config['itemsProcFunc_config']['prependHotlist']) {

				$limit = $config['itemsProcFunc_config']['hotlistLimit'];
				$limit = $limit ? $limit : '8';
				$app = $config['itemsProcFunc_config']['hotlistApp'];
				$app = $app ? $app : TYPO3_MODE;

				$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						$fields,
						$table,
						'tx_staticinfotables_hotlist',
						'',	// $foreign_table
						'AND tx_staticinfotables_hotlist.tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').' AND tx_staticinfotables_hotlist.application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist'),
						'',
						'tx_staticinfotables_hotlist.sorting DESC',	// $orderBy
						$limit
					);

				$cnt = 0;
				$rows = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{

					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$rows[$row[$indexField]] = $row[$titleField];
							break;
						}
					}
					$cnt++;
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);

				if (!isset($config['itemsProcFunc_config']['hotlistSort']) || $config['itemsProcFunc_config']['hotlistSort']) {
					asort ($rows);
				}

				foreach ($rows as $index => $title)	{
					$params['items'][] = array($title, $index, '');
					$cnt++;
				}
				if($cnt && !$config['itemsProcFunc_config']['hotlistOnly']) {
					$params['items'][] = array('--------------', '', '');
				}
			}

				// Set ORDER BY:
			$orderBy = $titleFields[0];

			if(!$config['itemsProcFunc_config']['hotlistOnly']) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, '1=1'.$where.t3lib_BEfunc::deleteClause($table), '', $orderBy);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$params['items'][] = array($row[$titleField], $row[$indexField], '');
							break;
						}
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
		}
	}


	/**
	 * Updates the hotlist table.
	 * This means that a hotlist entry will be created or the counter of an existing entry will be increased
	 *
	 * @param	string		table name: static_countries, ...
	 * @param	string		value of the following index field
	 * @param	string		the field which holds the value and is an index field: uid (default) or one of the iso code fields which are also unique
	 * @param	string		This indicates a counter group. Default is TYPO3_MOD (BE or FE). If you want a unique hotlist for your application you can provide here a name (e.g. extension key)
	 * @return	void
	 */
	function updateHotlist ($table, $indexValue, $indexField='', $app='') {

		if ($table && $indexValue) {
			$indexField = $indexField ? $indexField : 'uid';
			$app = $app ? $app : TYPO3_MODE;

			if ($indexField=='uid') {
				$uid = $indexValue;

			} else {
					// fetch original record
				$fields = array();
				$fields[$indexField] = $indexField;
				$fields['uid'] = 'uid';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$fields), $table, $indexField.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($indexValue,$table).t3lib_BEfunc::deleteClause($table));
				if ($res !== FALSE)	{
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
						$uid = $row['uid'];
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($res);
				}
			}

			if ($uid) {
					// update record from hotlist table
				$newRow = array('sorting' => 'sorting+1');
				// the dumb update function does not allow to use sorting+1 - that's why this trick is necessary

				$GLOBALS['TYPO3_DB']->sql_query(str_replace('"sorting+1"', 'sorting+1', $GLOBALS['TYPO3_DB']->UPDATEquery(
						'tx_staticinfotables_hotlist',
						'uid_local='.$uid.
							' AND application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist').
							' AND tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').
							t3lib_BEfunc::deleteClause('tx_staticinfotables_hotlist'),
						$newRow)));

				if (!$GLOBALS['TYPO3_DB']->sql_affected_rows())	{
						// insert new hotlist entry
					$row = array(
						'uid_local' => $uid,
						'tablenames' => $table,
						'application' => $app,
						'sorting' => 1,
					);
					$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_staticinfotables_hotlist', $row);
				}
			}
		}
	}


	/**
	 * Get a list of countries by specific parameters or parts of names of countries
	 * in different languages. Parameters might be left empty.
	 *
	 * @param	string		a name of the country or a part of it in any language
	 * @param	string		ISO alpha-2 code of the country
	 * @param	string		ISO alpha-3 code of the country
	 * @param	array		Database row.
	 * @return	array		Array of rows of country records
	 */
	function &fetchCountries ($country, $iso2='', $iso3='', $isonr='')	{
		global $TYPO3_DB;

		$rcArray = array();
		$where = '';

		$table = 'static_countries';
		if ($country != '')	{
			$value = $TYPO3_DB->fullQuoteStr(trim('%'.$country.'%'),$table);
			$where = 'cn_official_name_local LIKE '.$value.' OR cn_official_name_en LIKE '.$value.' OR cn_short_local LIKE '.$value;
		}

		if ($isonr != '')	{
			$where = 'cn_iso_nr='.$TYPO3_DB->fullQuoteStr(trim($isonr),$table);
		}

		if ($iso2 != '')	{
			$where = 'cn_iso_2='.$TYPO3_DB->fullQuoteStr(trim($iso2),$table);
		}

		if ($iso3 !='')	{
			$where = 'cn_iso_3='.$TYPO3_DB->fullQuoteStr(trim($iso3),$table);
		}

		if ($where != '')	{
			$res = $TYPO3_DB->exec_SELECTquery('*', $table, $where);

			if ($res)	{
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$rcArray[] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $rcArray;
	}


	/**
	 * Quotes a string for usage as JS parameter. Depends wheter the value is used in script tags (it must not get `htmlspecialchar'ed in this case because this is done in this function)
	 *
	 * @param	string		The string to encode.
	 * @param	boolean		If the values are used inside of <script> tags.
	 * @return	string		The encoded value already quoted
	 */
	function quoteJSvalue ($value, $inScriptTags=FALSE)	{
		global $TSFE;

		$value = addcslashes($value, '"'.chr(10).chr(13));
		if (!$inScriptTags)	{

			$charset = $TSFE->renderCharset;
			$value = htmlspecialchars($value,ENT_COMPAT,$charset);
		}
		return '"'.$value.'"';
	}


	/**
	 * loadTcaAdditions($ext_keys)
	 *
	 * It has been copied here in order not to depend on the div extension only for this function.
	 *
	 * @param	array		extension keys which have TCA additions to load
	 * @return	void
	 * @see div extension
	 */
	function loadTcaAdditions ($ext_keys){
		global $_EXTKEY, $TCA;

		//Merge all ext_keys
		if (is_array($ext_keys)) {
			foreach ($ext_keys as $_EXTKEY)	{
				if (t3lib_extMgm::isLoaded($_EXTKEY))	{
					//Include the ext_table
					include(t3lib_extMgm::extPath($_EXTKEY).'ext_tables.php');
				}
			}
		}
	}
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_div.php'])    {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_div.php']);
}
?>