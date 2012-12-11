<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Juergen Furrer <juergen.furrer@gmail.com>
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
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_jfmulticontent
 */
class tx_jfmulticontent
{
	public $cObj;

	public function getContentFromTemplavoilaField($content, $conf)
	{
		$pageID = $this->cObj->stdWrap($conf['pageID'], $conf['pageID.']);
		$field = $this->cObj->stdWrap($conf['field'], $conf['field.']);

		$row = NULL;
		if ($GLOBALS['TSFE']->sys_language_content) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages_language_overlay', 'deleted=0 AND hidden=0 AND pid='.intval($pageID).' AND sys_language_uid='.$GLOBALS['TSFE']->sys_language_content, '', '', 1);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		if (! is_array($row)) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', 'deleted=0 AND hidden=0 AND uid='.intval($pageID), '', '', 1);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		if (is_array($row)) {
			foreach ($row as $key => $val) {
				$GLOBALS['TSFE']->register['page_'.$key] = $val;
			}
		}

		$page_flex_array = t3lib_div::xml2array($row['tx_templavoila_flex']);

		$content_ids = array();
		if (isset($page_flex_array['data'])) {
			if (isset($page_flex_array['data']['sDEF'])) {
				if (count($page_flex_array['data']['sDEF']['lDEF']) > 0) {
					foreach ($page_flex_array['data']['sDEF']['lDEF'] as $key => $fields) {
						if ($key == $field) {
							$content_ids = array_merge($content_ids, t3lib_div::trimExplode(',', $fields['vDEF']));
						}
					}
				}
			}
		}

		$content = NULL;
		foreach ($content_ids as $content_id) {
			$GLOBALS['TSFE']->register['uid'] = $content_id;
			$content .= $this->cObj->cObjGetSingle($conf['contentRender'], $conf['contentRender.']);
		}

		return $content;
	}
}


// XCLASS inclusion code
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/class.tx_jfmulticontent.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/class.tx_jfmulticontent.php']);
}
?>