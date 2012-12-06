<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Stanislas Rolland <stanislas.rolland(arobas)sjbr.ca>
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
 *
 * Class for handling static info tables: countries, and subdivisions, currencies, languages and taxes
 *
 * $Id: class.tx_staticinfotables_pi1.php 32736 2010-04-29 10:21:33Z franzholz $
 *
 * @author	Stanislas Rolland <stanislas.rolland(arobas)sjbr.ca>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   66: class tx_staticinfotables_pi1 extends tslib_pibase
 *   97:     function needsInit ()
 *  108:     function init ($conf=array())
 *  166:     function getStaticInfoName ($type='COUNTRIES', $code, $country='', $countrySubdivision='', $local=FALSE)
 *  238:     function buildStaticInfoSelector ($type='COUNTRIES', $name='', $class='', $selectedArray=array(), $country='', $submit=0, $id='', $title='', $addWhere='', $lang='', $local=FALSE, $mergeArray=array(), $size=1, &$outSelectedArray=array())
 *  324:     function initCountries ($param='UN', $lang='', $local=FALSE, $addWhere='')
 *  382:     function initCountrySubdivisions ($param, $addWhere='')
 *  429:     function initCurrencies ($addWhere='')
 *  470:     function initLanguages ($addWhere='')
 *  512:     function optionsConstructor ($nameArray, $selectedArray=array(), &$outSelectedArray=array())
 *  539:     function loadCurrencyInfo ($currencyCode)
 *  585:     function formatAmount ($amount, $displayCurrencyCode='')
 *  614:     function formatAddress ($delim, $streetAddress, $city, $zip, $subdivisionCode='', $countryCode='')
 *  659:     function getCurrentLanguage ()
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('static_info_tables').'class.tx_staticinfotables_div.php');

class tx_staticinfotables_pi1 extends tslib_pibase {

	var $cObj;		// The backReference to the mother cObj object set at call time
	var $prefixId = 'tx_staticinfotables_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_staticinfotables_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'static_info_tables';		 // The extension key.
	var $conf = array();
	var $currency;		// default currency
	var $currencyInfo = array();
	var $defaultCountry;
	var $defaultCountryZone;
	var $defaultLanguage;
	var $types = array('TERRITORIES', 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES', 'LANGUAGES', 'TAXES', 'SUBTAXES');
	var $tables = array(
		'TERRITORIES' 	=> 'static_territories',
		'COUNTRIES' 	=> 'static_countries',
		'SUBDIVISIONS' 	=> 'static_country_zones',
		'CURRENCIES' 	=> 'static_currencies',
		'LANGUAGES' 	=> 'static_languages',
		'TAXES' 	=> 'static_taxes',
		'SUBTAXES' 	=> 'static_taxes'
	);
	var $bHasBeenInitialised = FALSE;


	/**
	 * Returns info if the tx_staticinfotables_pi1 object has already been initialised.
	 * You need to initialise this object only once.
	 *
	 * @return	boolean		Always returns true
	 */
	function needsInit ()	{
		return !$this->bHasBeenInitialised;
	}


	/**
	 * Initializing the class: sets the language based on the TS configuration language property
	 *
	 * @param	array		$conf ... overwriting setup of extension
	 * @return	boolean		Always returns true
	 */
	function init ($conf=array())	{
		global $TSFE;

		$this->conf = $TSFE->tmpl->setup['plugin.'][$this->prefixId.'.'];

			//Get the default currency and make sure it does exist in table static_currencies
		$this->currency = $conf['currencyCode'];
		if (!$this->currency)	{
			$this->currency = (trim($this->conf['currencyCode'])) ? trim($this->conf['currencyCode']) : 'EUR';
		}
			//If nothing is set, we use the Euro because TYPO3 is spread more in this area
		if (!$this->getStaticInfoName('CURRENCIES', $this->currency)) {
			$this->currency = 'EUR';
		}
		$this->currencyInfo = $this->loadCurrencyInfo($this->currency);
		$this->defaultCountry = $conf['countryCode'];

		if (!$this->defaultCountry)	{
			$this->defaultCountry = trim($this->conf['countryCode']);
		}
		if (!$this->getStaticInfoName('COUNTRIES', $this->defaultCountry)) {
			$this->defaultCountry = 'DEU';
		}

		$this->defaultCountryZone = $conf['countryZoneCode'];
		if (!$this->defaultCountryZone)	{
			$this->defaultCountryZone = trim($this->conf['countryZoneCode']);
		}
		if (!$this->getStaticInfoName('SUBDIVISIONS', $this->defaultCountryZone, $this->defaultCountry))	{
			if ($this->defaultCountry == 'DEU') {
				$this->defaultCountryZone = 'NW';
			} else {
				$this->defaultCountryZone = '';
			}
		}

		$this->defaultLanguage = $conf['languageCode'];
		if (!$this->defaultLanguage)	{
			$this->defaultLanguage = trim($this->conf['languageCode']);
		}
		if (!$this->getStaticInfoName('LANGUAGES', $this->defaultLanguage)) {
			$this->defaultLanguage = 'EN';
		}
		$this->bHasBeenInitialised = TRUE;
		return TRUE;
	}


	/**
	 * Getting the name of a country, country subdivision, currency, language, tax
	 *
	 * @param	string		Defines the type of entry of the requested name: 'TERRIRORIES', 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES', 'LANGUAGES', 'TAXES', 'SUBTAXES'
	 * @param	string		The ISO alpha-3 code of a territory, country or currency, or the ISO alpha-2 code of a language or the code of a country subdivision, can be a comma ',' separated string, then all the single items are looked up and returned
	 * @param	string		The value of the country code (cn_iso_3) for which a name of type 'SUBDIVISIONS', 'TAXES' or 'SUBTAXES' is requested (meaningful only in these cases)
	 * @param	string		The value of the country subdivision code for which a name of type 'SUBTAXES' is requested (meaningful only in this case)
	 * @param	boolean		local name only - if set local title is returned
	 * @return	string		The name of the object in the current language
	 */
	function getStaticInfoName ($type='COUNTRIES', $code, $country='', $countrySubdivision='', $local=FALSE) {
		global $TYPO3_DB, $TSFE;

		if (in_array($type, $this->types) && trim($code)) {
			$codeArray = t3lib_div::trimExplode(',',($code));
			$table = $this->tables[$type];
			if (!$table)	{
				return FALSE;
			}
			$lang = $this->getCurrentLanguage();
			if (!t3lib_extMgm::isLoaded(STATIC_INFO_TABLES_EXTkey.'_'.strtolower($lang)))	{
				$lang = '';
			}
			$nameArray = array();
			foreach ($codeArray as $tmpisoCode)	{
				$isoCodeArray = array();
				$isoCodeArray[] = $tmpisoCode;

				switch($type)	{
					case 'TERRITORIES':
					case 'COUNTRIES':
					case 'CURRENCIES':
						$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCodeArray, $lang, $local);
						break;
					case 'SUBDIVISIONS':
					case 'TAXES':
						$isoCodeArray[] = trim($country) ? trim($country) : $this->defaultCountry;
						$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCodeArray, $lang, $local);
						break;
					case 'SUBTAXES':
						$isoCodeArray[] = trim($country) ? trim($country) : $this->defaultCountry;
						$isoCodeArray[] = trim($countrySubdivision) ? trim($countrySubdivision) : $this->defaultCountryZone;
						$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCodeArray, $lang, $local);
						break;
					case 'LANGUAGES':
						$isoCodeArray = t3lib_div::trimExplode( '_', $code, 1);
						$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCodeArray, $lang, $local);
						break;
				}
				if (!$name && $lang != 'EN')	{
					// use the default English name if there is not text in another language
					$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCodeArray, '', $local);
				}
				$nameArray[] = $TSFE->csConv($name, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['charset']);
			}
			$rc = implode(',',$nameArray);
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	 * Buils a HTML drop-down selector of countries, country subdivisions, currencies or languages
	 *
	 * @param	string		Defines the type of entries to be presented in the drop-down selector: 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES' or 'LANGUAGES'
	 * @param	string		A value for the name attribute of the <select> tag
	 * @param	string		A value for the class attribute of the <select> tag
	 * @param	array		The values of the code of the entries to be pre-selected in the drop-down selector: value of cn_iso_3, zn_code, cu_iso_3 or lg_iso_2
	 * @param	string		The value of the country code (cn_iso_3) for which a drop-down selector of type 'SUBDIVISIONS' is requested (meaningful only in this case)
	 * @param	boolean/string		If set to 1, an onchange attribute will be added to the <select> tag for immediate submit of the changed value; if set to other than 1, overrides the onchange script
	 * @param	string		A value for the id attribute of the <select> tag
	 * @param	string		A value for the title attribute of the <select> tag
	 * @param	string		A where clause for the records
	 * @param	string		language to be used
	 * @param	boolean		$local: If set, we are looking for the "local" title field
	 * @param	array		additional array to be merged as key => value pair
	 * @param	int		max elements that can be selected. Default: 1
	 * @param	array		out: resulting selected array with the ISO alpha-3 code of the countries
	 * @return	string		A set of HTML <select> and <option> tags
	 */
	function buildStaticInfoSelector ($type='COUNTRIES', $name='', $class='', $selectedArray=array(), $country='', $submit=0, $id='', $title='', $addWhere='', $lang='', $local=FALSE, $mergeArray=array(), $size=1, &$outSelectedArray=array())	{
		global $TSFE;

		if ($size > 1) {
			$multiple = ' multiple="multiple"';
			$name .= '[]';
		} else {
			$multiple="";
		}
		if (isset($selectedArray) && !is_array($selectedArray)) {
			$selectedArray = t3lib_div::trimExplode (',',$selectedArray);
		}

		$charset = $TSFE->renderCharset;
		$country = trim($country);
		$nameAttribute = (trim($name)) ? 'name="'.htmlspecialchars(trim($name),ENT_COMPAT,$charset).'" ' : '';
		$classAttribute = (trim($class)) ? 'class="'.htmlspecialchars(trim($class),ENT_COMPAT,$charset).'" ' : '';
		$idAttribute = (trim($id)) ? 'id="'.htmlspecialchars(trim($id),ENT_COMPAT,$charset).'" ' : '';
		$titleAttribute = (trim($title)) ? 'title="'.htmlspecialchars(trim($title),ENT_COMPAT,$charset).'" ' : '';
		$onchangeAttribute = '';
		if ($submit) {
			if ($submit == 1) {
				$onchangeAttribute = $this->conf['onChangeAttribute'];
			} else {
				$onchangeAttribute = $submit;
			}
			$onchangeAttribute = str_replace('"', '\'', $onchangeAttribute);
			$onchangeAttribute = tx_staticinfotables_div::quoteJSvalue($onchangeAttribute);
			$onchangeAttribute = 'onchange='.$onchangeAttribute;
		}
		$selector = '<select size="'.$size.'" '.$idAttribute.$nameAttribute.$titleAttribute.$classAttribute.$onchangeAttribute.$multiple.'>'.chr(10);

		switch($type)	{
			case 'COUNTRIES':
				$nameArray = $this->initCountries('ALL',$lang,$local,$addWhere);
				$defaultSelectedArray = array($this->defaultCountry);
				break;
			case 'SUBDIVISIONS':
				$param = (trim($country) ? trim($country) : $this->defaultCountry);
				$nameArray = $this->initCountrySubdivisions($param,$addWhere);
				if($param == $this->defaultCountry) {
					$defaultSelectedArray = array($this->defaultCountryZone);
				}
				break;
			case 'CURRENCIES':
				$nameArray = $this->initCurrencies($addWhere);
				$defaultSelectedArray = array($this->currency);
				break;
			case 'LANGUAGES':
				$nameArray = $this->initLanguages($addWhere);
				$defaultSelectedArray = array($this->defaultLanguage);
				break;
		}

		if (!$defaultSelectedArray)	{
			reset($nameArray);
			$defaultSelectedArray = array(key($nameArray));
		}
		$bEmptySelected = (empty($selectedArray) || ((count($selectedArray) == 1) && empty($selectedArray[0])));
		$selectedArray = ((!$bEmptySelected || count($mergeArray)) ? $selectedArray : $defaultSelectedArray);

		if (count($mergeArray))	{
			$nameArray = array_merge($nameArray, $mergeArray);
			uasort($nameArray, 'strcoll');
		}

		if(count($nameArray) > 0)	{
			$selector .= $this->optionsConstructor($nameArray, $selectedArray, $outSelectedArray);
			$selector .= '</select>'.chr(10);
		} else {
			$selector = '';
		}
		return $selector;
	}


	/**
	 * Getting all countries into an array
	 * 	where the key is the ISO alpha-3 code of the country
	 * 	and where the value is the name of the country in the current language
	 *
	 * @param	string		It defines a selection: 'ALL', 'UN', 'EU'
	 * @param	string		language to be used
	 * @param	boolean		If set, we are looking for the "local" title field
	 * @param	string		additional WHERE clause
	 * @return	array		An array of names of countries
	 */
	function initCountries ($param='UN', $lang='', $local=FALSE, $addWhere='') {
		global $TYPO3_DB, $TSFE;

		$table = $this->tables['COUNTRIES'];
		if (!$lang)	{
			$lang = $this->getCurrentLanguage();
		}
		$nameArray = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang, $local);
		$prefixedTitleFields = array();
		$prefixedTitleFields[] = $table.'.cn_iso_3';
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}

		array_unique($prefixedTitleFields);
		$labelFields = implode(',', $prefixedTitleFields);
		if ($param == 'UN') {
			$where = 'cn_uno_member=1';
		} elseif ($param == 'EU') {
			$where = 'cn_eu_member=1';
		} elseif ($param == 'ALL')	{
			$where = '1=1';
		} else {
			$where = '1=1';
		}

		$where .= ($addWhere ? ' AND '.$addWhere : '');
		$res = $TYPO3_DB->exec_SELECTquery(
			$labelFields,
			$table,
			$where.$TSFE->sys_page->enableFields($table)
		);

		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{

			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$nameArray[$row['cn_iso_3']] = $TSFE->csConv($row[$titleField], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		$TYPO3_DB->sql_free_result($res);

		if ($this->conf['countriesAllowed'] != '')	{
			$countriesAllowedArray = t3lib_div::trimExplode(',', $this->conf['countriesAllowed']);
			$newNameArray = array();
			foreach ($countriesAllowedArray as $iso3)	{
				if (isset($nameArray[$iso3]))	{
					$newNameArray[$iso3] = $nameArray[$iso3];
				}
			}
			$nameArray = $newNameArray;
		} else {
			uasort($nameArray, 'strcoll');
		}
		return $nameArray;
	}


	/**
	 * Getting all country subdivisions of a given country into an array
	 * 	where the key is the code of the subdivision
	 * 	and where the value is the name of the country subdivision in the current language
	 * You can leave the ISO code empty and use the additional WHERE clause instead of it.
	 *
	 * @param	string		The ISO alpha-3 code of a country
	 * @param	string		additional WHERE clause
	 * @return	array		An array of names of country subdivisions
	 */
	function initCountrySubdivisions ($param, $addWhere='')	{
		global $TYPO3_DB, $TSFE;

		$table = $this->tables['SUBDIVISIONS'];
		if (strlen($param) == 3)	{
			$country = $param;
			$where = 'zn_country_iso_3='.$TYPO3_DB->fullQuoteStr($country,$table);
		} else {
			$where = '1=1';
		}
		$where .= ($addWhere ? ' AND '.$addWhere : '');
		$lang = $this->getCurrentLanguage();
		$nameArray = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.zn_code,'.$labelFields,
			$table,
			$where.
				$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$nameArray[$row['zn_code']] = $TSFE->csConv($row[$titleField], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		$TYPO3_DB->sql_free_result($res);
		uasort($nameArray, 'strcoll');
		return $nameArray;
	}


	/**
	 * Getting all currencies into an array
	 * 	where the key is the ISO alpha-3 code of the currency
	 * 	and where the value are the name of the currency in the current language
	 *
	 * @param	string		additional WHERE clause
	 * @return	array		An array of names of currencies
	 */
	function initCurrencies ($addWhere='') {
		global $TYPO3_DB, $TSFE;

		$where = '1=1'.($addWhere ? ' AND '.$addWhere : '');
		$table = $this->tables['CURRENCIES'];
		$lang = $this->getCurrentLanguage();
		$nameArray = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.cu_iso_3,'.$labelFields,
			$table,
			$where.$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$nameArray[$row['cu_iso_3']] = $TSFE->csConv($row[$titleField], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		$TYPO3_DB->sql_free_result($res);
		uasort($nameArray, 'strcoll');
		return $nameArray;
	}


	/**
	 * Getting all languages into an array
	 * 	where the key is the ISO alpha-2 code of the language
	 * 	and where the value are the name of the language in the current language
	 * 	Note: we exclude sacred and constructed languages
	 *
	 * @param	string		additional WHERE clause
	 * @return	array		An array of names of languages
	 */
	function initLanguages ($addWhere='') {
		global $TYPO3_DB, $TSFE;

		$where = '1=1'.($addWhere ? ' AND '.$addWhere : '');
		$table = $this->tables['LANGUAGES'];
		$lang = $this->getCurrentLanguage();
		$nameArray = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.lg_iso_2,'.$table.'.lg_country_iso_2,'.$labelFields,
			$table,
			$where.' AND lg_sacred = 0 AND lg_constructed = 0 '.
				$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			$code = $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$nameArray[$code] = $TSFE->csConv($row[$titleField], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		$TYPO3_DB->sql_free_result($res);
		uasort($nameArray, 'strcoll');
		return $nameArray;
	}


	/**
	 * Builds a list of <option> tags
	 *
	 * @param	array		An array where the values will be the texts of an <option> tags and keys will be the values of the tags
	 * @param	string		A pre-selected value: if the value appears as a key, the <option> tag will bear a 'selected' attribute
	 * @param	array		out: resulting selected array with the ISO alpha-3 code of the countries
	 * @return	string		A string of HTML <option> tags
	 */
	function optionsConstructor ($nameArray, $selectedArray=array(), &$outSelectedArray=array()) {
		global $TSFE;

		$options = '';

		foreach($nameArray as $value => $name)	{

			$options  .= '<option value="'.$value.'"';
			if (in_array($value, $selectedArray))	{
				$options  .= ' selected="selected"';
				$outSelectedArray[] = $value;
			}
			$options  .= '>'.$name.'</option>'.chr(10);
		}
		if (!isset($outSelectedArray) || count($outSelectedArray) == 0)	{
			reset ($nameArray);
			$outSelectedArray = array(key($nameArray));
		}
		return $options;
	}


	/**
	 * Loading currency display parameters from Static Info Tables
	 *
	 * @param	string		An ISO alpha-3 currency code
	 * @return	array		An array of information regarding the currrency
	 */
	function loadCurrencyInfo ($currencyCode)	{
		global $TYPO3_DB;

			// Fetching the currency record
		$this->currencyInfo['cu_iso_3'] = trim($currencyCode);
		$this->currencyInfo['cu_iso_3'] = ($this->currencyInfo['cu_iso_3']) ? $this->currencyInfo['cu_iso_3'] : $this->currency;
		$res = $TYPO3_DB->exec_SELECTquery(
			'*',
			'static_currencies',
			'cu_iso_3='.$TYPO3_DB->fullQuoteStr($this->currencyInfo['cu_iso_3'],'static_currencies')
		);
			// If not found we fetch the default currency!
		if (!$TYPO3_DB->sql_num_rows($res)) {
			$TYPO3_DB->sql_free_result($res);
		 	$this->currencyInfo['cu_iso_3'] = $this->currency;
			$res = $TYPO3_DB->exec_SELECTquery(
				'*',
				'static_currencies',
				'cu_iso_3='.$TYPO3_DB->fullQuoteStr($this->currencyInfo['cu_iso_3'],'static_currencies')
			);
		}
		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$TYPO3_DB->sql_free_result($res);

		$this->currencyInfo['cu_name'] = $this->getStaticInfoName('CURRENCIES', $this->currencyInfo['cu_iso_3']);
		$this->currencyInfo['cu_symbol_left'] = $row['cu_symbol_left'];
		$this->currencyInfo['cu_symbol_right'] = $row['cu_symbol_right'];
		$this->currencyInfo['cu_decimal_digits'] = $row['cu_decimal_digits'];
		$this->currencyInfo['cu_decimal_point'] = $row['cu_decimal_point'];
		$this->currencyInfo['cu_thousands_point'] = $row['cu_thousands_point'];

		return $this->currencyInfo;
	}


	/**
	 * Formatting an amount in the currency loaded by loadCurrencyInfo($currencyCode)
	 *
	 * 	 '' - the currency code is not displayed
	 * 	 'RIGHT' - the code is displayed at the right of the amount
	 * 	 'LEFT' - the code is displayed at the left of the amount
	 *
	 * @param	float		An amount to be displayed in the loaded currency
	 * @param	string		A flag specifying if the the currency code should be displayed:
	 * @return	string		The formated amounted
	 */
	function formatAmount ($amount, $displayCurrencyCode='')	{

		$formatedAmount = '';

		if( $displayCurrencyCode == 'LEFT' )	{
			$formatedAmount .= $this->currencyInfo['cu_iso_3'].chr(32);
		}
		$formatedAmount .= $this->currencyInfo['cu_symbol_left'];
		$formatedAmount .= number_format($amount, intval($this->currencyInfo['cu_decimal_digits']), $this->currencyInfo['cu_decimal_point'], (($this->currencyInfo['cu_thousands_point'])?$this->currencyInfo['cu_thousands_point']:chr(32)));
		$formatedAmount .= (($this->currencyInfo['cu_symbol_right'])?chr(32):'').$this->currencyInfo['cu_symbol_right'];
		if( $displayCurrencyCode == 'RIGHT' )	{
			$formatedAmount .= chr(32).$this->currencyInfo['cu_iso_3'];
		}

		return $formatedAmount;
	}


	/**
	 * Formatting an address in the format specified
	 *
	 * @param	string		A delimiter for the fields of the returned address
	 * @param	string		A street address
	 * @param	string		A city
	 * @param	string		A country subdivision code (zn_code)
	 * @param	string		A ISO alpha-3 country code (cn_iso_3)
	 * @param	string		A zip code
	 * @return	string		The formated address using the country address format (cn_address_format)
	 */
	function formatAddress ($delim, $streetAddress, $city, $zip, $subdivisionCode='', $countryCode='')	{
		global $TYPO3_DB;

		$formatedAddress = '';
		$countryCode = ($countryCode ? trim($countryCode) : $this->defaultCountry);
		$subdivisionCode = ($subdivisionCode ? trim($subdivisionCode) : ($countryCode == $this->defaultCountry ? $this->defaultCountryZone : ''));

			// Get country name
		$countryName = $this->getStaticInfoName('COUNTRIES', $countryCode);
		if (!$countryName) {
			return $formatedAddress;
		}

			// Get address format
		$res = $TYPO3_DB->exec_SELECTquery(
			'cn_address_format',
			'static_countries',
			'cn_iso_3='.$TYPO3_DB->fullQuoteStr($countryCode,'static_countries')
		);
		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$TYPO3_DB->sql_free_result($res);
		$addressFormat = $row['cn_address_format'];

			// Get country subdivision name
		$countrySubdivisionName = $this->getStaticInfoName('SUBDIVISIONS', $subdivisionCode, $countryCode);

			// Format the address
		$formatedAddress = $this->conf['addressFormat.'][$addressFormat];
		$formatedAddress = str_replace('%street', $streetAddress, $formatedAddress);
		$formatedAddress = str_replace('%city', $city, $formatedAddress);
		$formatedAddress = str_replace('%zip', $zip, $formatedAddress);
		$formatedAddress = str_replace('%countrySubdivisionCode', $subdivisionCode, $formatedAddress);
		$formatedAddress = str_replace('%countrySubdivisionName', $countrySubdivisionName, $formatedAddress);
		$formatedAddress = str_replace('%countryName', strtoupper($countryName), $formatedAddress);
		$formatedAddress = implode($delim, t3lib_div::trimExplode(';', $formatedAddress, 1));

		return $formatedAddress;
	}


	/**
	 * Returns the current language as iso-2-alpha code
	 *
	 * @return	string		'DE', 'EN', 'DK', ...
	 */
	function getCurrentLanguage () {
		global $TSFE, $TYPO3_DB;

		$rc = tx_staticinfotables_div::getCurrentLanguage();
		return $rc;
	}
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/pi1/class.tx_staticinfotables_pi1.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/pi1/class.tx_staticinfotables_pi1.php']);
}

?>