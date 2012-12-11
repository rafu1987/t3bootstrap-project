<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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
 * 'tceFunc' for the 'jfmulticontent' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_tceFunc
{
	/**
	 * This will render a selectorbox instead of a checkbox, so its possible to select "from TS".
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	public function getCheckbox($PA, &$fObj)
	{
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
		if ($confArr['useSelectInsteadCheckbox']) {
			$PA['fieldConf']['config'] = array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.pi_flexform.from_ts', 2),
					array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.pi_flexform.yes',1),
					array('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.pi_flexform.no',0),
				),
			);
		} else {
			$conf = $PA['fieldConf']['config'];
			$PA['itemFormElValue'] = (is_numeric($PA['itemFormElValue']) && $PA['itemFormElValue'] != 2 ? $PA['itemFormElValue'] : $conf['checked']);
			$PA['fieldConf']['config'] = array(
				'type' => 'check',
			);
		}
		$tceforms = &$PA['pObj'];
		return $tceforms->getSingleField_SW($PA['table'], $PA['field'], $PA['row'], $PA);
	}
	
	/**
	 * This will render the info text for the typoscript-style
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	public function getTypoScriptInfo($PA, &$fObj)
	{
		return '
<div class="typo3-message message-information">
	<div class="message-body">
		' . $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:tt_content.tx_jfmulticontent.typoscriptInfo') . '
	</div>
</div>';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tceFunc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tceFunc.php']);
}
?>