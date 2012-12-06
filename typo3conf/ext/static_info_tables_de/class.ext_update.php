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



require_once (t3lib_extMgm::extPath('static_info_tables').'class.tx_staticinfotables_encoding.php');

/**
 * Class for updating the db
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

		$content = '';

		$content.= '<br />Update the Static Info Tables with new language labels.';
		$content .= '<br />';
		$import = t3lib_div::_GP('import');

		if ($import == 'Import') {

			$destEncoding = t3lib_div::_GP('dest_encoding');
			$extPath = t3lib_extMgm::extPath('static_info_tables_de');
			$fileContent = explode("\n", t3lib_div::getUrl($extPath.'ext_tables_static_update.sql'));

			foreach($fileContent as $line)	{
				$line=trim($line);
				if ($line AND preg_match('#^UPDATE#i', $line))	{
					$query = $this->getUpdateEncoded ($line, $destEncoding);
					$res = $GLOBALS['TYPO3_DB']->admin_query($query);
				}
			}
			$content .= '<br />';
			$content .= '<p>Encoding: '.htmlspecialchars($destEncoding).'</p>';
			$content .= '<p>Done.</p>';
		} elseif (t3lib_extMgm::isLoaded('static_info_tables')) {

			$content .= '</form>';
			$content .= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">';
			$content .= '<br />Destination character encoding:';
			$content .= '<br />'.tx_staticinfotables_encoding::getEncodingSelect('dest_encoding', '', 'utf-8');
			$content .= '<br />(The character encoding must match the encoding of the existing tables data. By default this is UTF-8.)';
			$content .= '<br /><br />';
			$content .= '<input type="submit" name="import" value="Import" />';
			$content .= '</form>';
		} else {
			$content .= '<br /><strong>The extension static_info_tables needs to be installed first!</strong>';
		}

		return $content;
	}


	/**
	 * Convert the values of a SQL update statement to a different encoding than UTF-8.
	 *
	 * @param string $query Update statement like: UPDATE static_countries SET cn_short_de='XXX' WHERE cn_iso_2='DE';
	 * @param string $destEncoding Destination encoding
	 * @return string Converted update statement
	 */
	function getUpdateEncoded ($query, $destEncoding) {
		static $csconv;

		if (!($destEncoding==='utf-8')) {
			if(!is_object($csconv)) {
				$csconv = t3lib_div::makeInstance('t3lib_cs');
			}

			$queryElements = explode('WHERE', $query);
			$where = preg_replace('#;$#', '', trim($queryElements[1]));

			$queryElements = explode('SET', $queryElements[0]);
			$queryFields = $queryElements[1];

			$queryElements = t3lib_div::trimExplode('UPDATE', $queryElements[0], 1);
			$table = $queryElements[0];

			$fields_values = array();
			$queryFieldsArray = preg_split('/[,]/', $queryFields, 1);
			foreach ($queryFieldsArray as $fieldsSet) {
				$col = t3lib_div::trimExplode('=', $fieldsSet, 1);
				$value = stripslashes(substr($col[1], 1, strlen($col[1])-2));
				$value = $csconv->conv($value, 'utf-8', $destEncoding);
				$fields_values[$col[0]] = $value;
			}

			$query = $GLOBALS['TYPO3_DB']->UPDATEquery($table,$where,$fields_values);
		}
		return $query;
	}


	function access() {
		return TRUE;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables_de/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables_de/class.ext_update.php']);
}


?>