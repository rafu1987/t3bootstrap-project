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

class tx_kickstarter_section_emconf extends tx_kickstarter_sectionbase {
  var $sectionID = 'emconf';

	/**
	 * Renders the form in the kickstarter; this was add_cat_emconf()
	 *
	 * @return	[type]		...
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

			if (!$this->wizard->EMmode && $this->wizard->saveKey)	{
				$extKeyRec = $this->wizard->pObj->getExtKeyRecord($this->wizard->saveKey);
			}

				// Title
			$subContent='<strong>Title:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[title]',$piConf['title']?$piConf['title']:$extKeyRec['title']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Description
			$subContent='<strong>Description:</strong><br />' .
				$this->renderTextareaBox(
					$ffPrefix.'[description]',
					$piConf['description'] ? $piConf['description'] : $extKeyRec['description']
				);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Sub-position
			$optValues = Array(
				'' => '',
				'fe' => 'Frontend',
				'plugin' => 'Frontend Plugins',
				'be' => 'Backend',
				'module' => 'Backend Modules',
				'services' => 'Services',
				'example' => 'Examples',
				'misc' => 'Miscellaneous',
				'templates' => 'Templates',
				'doc' => 'Documentation',
			);
			$subContent='<strong>Category:</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[category]',$piConf['category'],$optValues);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';




				// State
			$optValues = Array(
				'alpha' => 'Alpha (Very initial development)',
				'beta' => 'Beta (Under current development, should work partly)',
				'stable' => 'Stable (Stable and used in production)',
				'experimental' => 'Experimental (Nobody knows if this is going anywhere yet...)',
				'test' => 'Test (Test extension, demonstrates concepts etc.)',
			);
			$subContent='<strong>State</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[state]',$piConf['state'],$optValues);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Dependencies
			$subContent='<strong>Dependencies (comma list of extkeys):</strong><br />'.
				$this->renderStringBox($ffPrefix.'[dependencies]',$piConf['dependencies']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';




				// Author
			$subContent='<strong>Author Name:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[author]',$piConf['author']?$piConf['author']:$GLOBALS['BE_USER']->user['realName']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Author/Email
			$subContent='<strong>Author email:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[author_email]',$piConf['author_email']?$piConf['author_email']:$GLOBALS['BE_USER']->user['email']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_emconf'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_emconf'] as $_funcRef) {
				$lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
			}
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Do nothing !?
	 *
	 * @param	mixed		$k: ???
	 * @param	mixed		$config: ???
	 * @param	mixed		$extKey: ???
	 * @return	mixed
	 */
	function render_extPart($k,$config,$extKey) {

	}
}

// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_emconf.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_emconf.php']);
}

?>