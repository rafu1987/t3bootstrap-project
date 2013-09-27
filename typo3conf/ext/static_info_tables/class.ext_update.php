<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2008 René Fritz (r.fritz@colorcube.de)
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
 * Class for updating the db
 *
 * $Id: class.ext_update.php 71336 2013-02-20 04:30:42Z stan $
 *
 * @author	 René Fritz <r.fritz@colorcube.de>
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		require_once(t3lib_extMgm::extPath(STATIC_INFO_TABLES_EXTkey).'class.tx_staticinfotables_encoding.php');

		$tableArray = array ('static_countries', 'static_country_zones', 'static_languages', 'static_currencies');

		$content = '';
		$content.= '<br />Convert character encoding of the static info tables.';
		$content.= '<br />The default encoding is UTF-8.';
		$destEncoding = htmlspecialchars(t3lib_div::_GP('dest_encoding'));

		if(t3lib_div::_GP('convert') AND ($destEncoding != '')) {
			foreach ($tableArray as $table) {
				$content .= '<p>'.htmlspecialchars($table.' > '.$destEncoding).'</p>';
				tx_staticinfotables_encoding::convertEncodingTable($table, 'utf-8', $destEncoding);
			}
			$content .= '<p>You must enter the charset \''.$destEncoding.'\' now manually in the EM for static_info_tables!</p>';
			$content .= '<p>Done</p>';
		} else {
			$content .= '<form name="static_info_tables_form" action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">';
			$linkScript = t3lib_div::slashJS(t3lib_div::linkThisScript());
			$content .= '<br /><br />';
			$content .= 'This conversion works only once. When you converted the tables and you want to do it again to another encoding you have to reinstall the tables with the Extension Manager or select \'UPDATE!\'.';
			$content .= '<br /><br />';
			$content .= 'Destination character encoding:';
			$content .= '<br />'.tx_staticinfotables_encoding::getEncodingSelect('dest_encoding', '', '', $TYPO3_CONF_VARS['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['charset']);
			$content .= '<br /><br />';
			$content .= '<input type="submit" name="convert" value="Convert"  onclick="this.form.action=\''.$linkScript.'\';submit();" />';
			$content .= '</form>';
		}

		return $content;

	}

	/**
	 * access is always allowed
	 *
	 * @return	boolean		Always returns true
	 */
	function access() {
		$typo3Version = class_exists('t3lib_utility_VersionNumber') ? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) : t3lib_div::int_from_ver(TYPO3_version);
		return ($typo3Version < 4007000);
	}


}

// Include extension?
if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.ext_update.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.ext_update.php']);
}


?>
