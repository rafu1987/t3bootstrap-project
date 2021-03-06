<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>
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
 * Plugin 'Powermail' for the 'powermail' extension.
 *
 * @author	2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage	tx_powermail
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_powermail_pi1_wizicon
 *   57:     function proc($wizardItems)
 *   80:     function includeLocalLang()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_powermail_pi1_wizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param	array		$wizardItems: The wizard items
	 * @return	array		array with wizard items
	 */
	function proc($wizardItems)	{
		global $LANG;

		$LL = $this->includeLocalLang();

		$wizardItems['plugins_tx_powermail_pi1'] = array(
			'icon' => t3lib_extMgm::extRelPath('powermail') . 'Resources/Public/Icons/ce_wiz.gif',
			'title' => $LANG->getLLL('pi1_title', $LL),
			'description' => $LANG->getLLL('pi1_plus_wiz_description', $LL),
			'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=powermail_pi1',
			'tt_content_defValues' => array(
				'CType' => 'list',
			),
		);

		return $wizardItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the \$LOCAL_LANG array found in that file.
	 *
	 * @return	The array with language labels
	 */
	function includeLocalLang() {
		$localizationParser = t3lib_div::makeInstance('t3lib_l10n_parser_Llxml');
		$LOCAL_LANG = $localizationParser->getParsedData(
			t3lib_extMgm::extPath('powermail') . 'Resources/Private/Language/locallang.xml',
			$GLOBALS['LANG']->lang
		);

		return $LOCAL_LANG;
	}
}
?>