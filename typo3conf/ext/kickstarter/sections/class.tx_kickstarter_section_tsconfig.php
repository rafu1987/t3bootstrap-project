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
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');

class tx_kickstarter_section_tsconfig extends tx_kickstarter_sectionbase {
  var $sectionID = 'tsconfig';

	/**
	 * Renders the form in the kickstarter; this was add_cat_()
	 *
	 * @return	string		...
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0]=='edit')	{
			$action[1]=1;
			$this->regNewEntry($this->sectionID,$action[1]);

			$lines = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],'&nbsp;',$action[1]);
			$piConf = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix='['.$this->sectionID.']['.$action[1].']';

				// Enter Page TSconfige
			$subContent='<strong>Default Page TSconfig:</strong><br />'.
				$this->renderTextareaBox($ffPrefix.'[page_TSconfig]',$piConf['page_TSconfig']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Enter User TSconfig
			$subContent='<strong>Default User TSconfig:</strong><br />'.
				$this->renderTextareaBox($ffPrefix.'[user_TSconfig]',$piConf['user_TSconfig']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_tsconfig'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_tsconfig'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Renders the extension PHP codee; this was
	 *
	 * @param	[type]		$k: ...
	 * @param	[type]		$config: ...
	 * @param	[type]		$extKey: ...
	 * @return	[type]		...
	 */
	function render_extPart($k,$config,$extKey) {

	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_tsconfig.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_tsconfig.php']);
}
?>