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
 * @author	Ingo Renner	<ingo@typo3.org>
 */

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');

class tx_kickstarter_section_ts extends tx_kickstarter_sectionbase {
  var $sectionID = 'ts';

	/**
	 * Renders the form in the kickstarter
	 *
	 * @return	string		wizard
	 */
	function render_wizard() {
		$lines = array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0] == 'edit')	{
			$this->regNewEntry($this->sectionID, $action[1]);
			$lines = $this->catHeaderLines(
				$lines,
				$this->sectionID,
				$this->wizard->options[$this->sectionID],
				'&nbsp;',
				$action[1]
			);
			$piConf   = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix ='['.$this->sectionID.']['.$action[1].']';

				// Enter title of the static extension template
			$subContent='<strong>Enter a title for the static extension template:</strong><br />'.
			$this->renderStringBox($ffPrefix.'[title]',$piConf['title']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Enter constants
			$subContent='<strong>Constants:</strong><br />'.
				$this->renderTextareaBox(
					$ffPrefix.'[constants]',
					$piConf['constants']
				);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Enter setup
			$subContent='<strong>Setup:</strong><br />'.
				$this->renderTextareaBox(
					$ffPrefix.'[setup]',
					$piConf['setup']
				);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_ts'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_ts'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Renders the extension PHP code; this was
	 *
	 * @param	string		$k: module name key
	 * @param	array		$config: module configuration
	 * @param	string		$extKey: extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {

		$WOP = '[ts]['.$k.']';
		$tsPath = strtolower(t3lib_basicFileFunctions::cleanFileName($config['title']));
		$pathSuffix = 'static/'.$tsPath.'/';

		$this->addFileToFileArray(
			$pathSuffix.'constants.txt',
			$config['constants']
		);
		$this->addFileToFileArray(
			$pathSuffix.'setup.txt',
			$config['setup']
		);

		// add TS definition to ext_tables.php
		$this->wizard->ext_tables[] = $this->sPS(
			$this->WOPcomment('WOP:'.$WOP).chr(10).
			't3lib_extMgm::addStaticFile($_EXTKEY,\''.$pathSuffix.'\', \''.$config['title'].'\');',
			0
		);
	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_ts.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_ts.php']);
}


?>