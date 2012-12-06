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
 * @author	Ingo Renner <ingo@typo3.org>
 */

require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');

class tx_kickstarter_section_sv extends tx_kickstarter_sectionbase {
  var $sectionID = 'sv';

	/**
	 * Renders the form in the kickstarter; this was add_cat_()
	 *
	 * @return	string		wizard
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0]=='edit')	{
			$this->regNewEntry($this->sectionID,$action[1]);

			$lines = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],'<strong>Edit Service #'.$action[1].'</strong>',$action[1]);
			$piConf = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix='['.$this->sectionID.']['.$action[1].']';

			if (!$this->wizard->EMmode && $this->wizard->saveKey)	{
				$extKeyRec = $this->wizard->pObj->getExtKeyRecord($this->saveKey);
			}

				// Title
			$subContent='<strong>Title:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[title]',$piConf['title']?$piConf['title']:$extKeyRec['title']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Description
			$subContent='<strong>Description:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[description]',$piConf['description']?$piConf['description']:$extKeyRec['description']);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

			$lines[]='<tr><td>&nbsp;</td><td></td></tr>';

				// Type
			$subContent='<strong>Service type:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[type]',$piConf['type']?$piConf['type']:$extKeyRec['type']).'<br />'.
				'Enter here the key to define which type of service this should be.<br />Examples: "textExtract", "metaExtract".';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// SubType
			$subContent='<strong>Sub type(s) (comma list):</strong><br />'.
				$this->renderStringBox($ffPrefix.'[subtype]',$piConf['subtype']?$piConf['subtype']:$extKeyRec['subtype']).'<br />'.
				'Possible subtypes are defined by the service type.<br />You have read the service type documentation.<br />Example: using subtypes for file types (doc, txt, pdf, ...) the service might work for.';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

			$lines[]='<tr><td>&nbsp;</td><td></td></tr>';

				// Priority
			$optValues = Array(
				'50' => 'default (50)',
				'10' => 'very low (10)',
				'20' => 'low (20)',
				'40' => 'bit lower (40)',
				'60' => 'bit higher (60)',
				'80' => 'high (80)',
				'100' => 'Very high (100)',
			);
			$subContent='<strong>Priority:</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[priority]',$piConf['priority'],$optValues).'<br />'.
				'50 = medium priority. <br />The priority of services can be changed by admin configuration.';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Quality
			$quality = $piConf['quality']?$piConf['quality']:$extKeyRec['quality'];
			$quality = $quality ? $quality : '50';
			$subContent='<strong>Quality:</strong><br />'.
				$this->renderStringBox($ffPrefix.'[quality]',$quality).'<br />'.
				'The numbering of the quality is defined by the service type.<br />You have read the service type documentation.<br />The default quality range is 0-100.';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


				// OS Dependencies
			$optValues = Array(
				''     => 'no special dependency',
				'unix' => 'Unix only',
				'win'  => 'Windows only',
			);

			$lines[]='<tr><td>&nbsp;</td><td></td></tr>';

			$subContent='<strong>Operating System dependency:</strong><br />'.
				$this->renderSelectBox($ffPrefix.'[os]',$piConf['os'],$optValues);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Exec dependencies
			$subContent='<strong>External program(s) (comma list):</strong><br />'.
				$this->renderStringBox($ffPrefix.'[exec]',$piConf['exec']).'<br />'.
				'Program(s) needed to run this service (eg. "perl").';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_services'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_services'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Renders the extension PHP codee; this was
	 *
	 * @param	integer		key
	 * @param	array		table configuration
	 * @param	string		extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP ='[sv]['.$k.']';
		$cN  = $this->returnName($extKey,'class','sv'.$k);
		$pathSuffix = 'sv'.$k.'/';

		$this->wizard->ext_localconf[]=$this->sPS('
			'.$this->WOPcomment('WOP:'.$WOP.'[type]').'
			t3lib_extMgm::addService($_EXTKEY, \''.$config['type'].'\',  \''.$cN.'\',
				array(
					\'title\' => \''.addslashes($config['title']).'\','.$this->WOPcomment('	WOP:'.$WOP.'[title]').'
					\'description\' => \''.addslashes($config['description']).'\','.$this->WOPcomment('	WOP:'.$WOP.'[description]').'

					\'subtype\' => \''.$config['subtype'].'\','.$this->WOPcomment('	WOP:'.$WOP.'[subtype]').'

					\'available\' => TRUE,
					\'priority\' => '.$config['priority'].','.$this->WOPcomment('	WOP:'.$WOP.'[priority]').'
					\'quality\' => '.$config['quality'].','.$this->WOPcomment('	WOP:'.$WOP.'[quality]').'

					\'os\' => \''.$config['os'].'\','.$this->WOPcomment('	WOP:'.$WOP.'[os]').'
					\'exec\' => \''.$config['exec'].'\','.$this->WOPcomment('	WOP:'.$WOP.'[exec]').'

					\'classFile\' => t3lib_extMgm::extPath($_EXTKEY).\'sv'.$k.'/class.'.$cN.'.php\',
					\'className\' => \''.$cN.'\',
				)
			);
		');

		$innerMainContent = $this->sPS('

			/**
 * [Put your description here]
 *
 * @return	[type]		...
 */
			function init()	{
				$available = parent::init();

				// Here you can initialize your class.

				// The class have to do a strict check if the service is available.
				// The needed external programs are already checked in the parent class.

				// If there\'s no reason for initialization you can remove this function.

				return $available;
			}

			/**
 * [Put your description here]
 * performs the service processing
 *
 * @param	string		Content which should be processed.
 * @param	string		Content type
 * @param	array		Configuration array
 * @return	boolean
 */
			function process($content=\'\', $type=\'\', $conf=array())	{

				// Depending on the service type there\'s not a process() function.
				// You have to implement the API of that service type.

				return FALSE;
			}
		');

		$indexRequire = 'require_once(PATH_t3lib.\'class.t3lib_svbase.php\');';
		$indexContent = $this->wrapBody(
			'class '.$cN.' extends t3lib_svbase {
				var $prefixId = \''.$cN.'\';		// Same as class name
				var $scriptRelPath = \''.($pathSuffix.'class.'.$cN.'.php').'\';	// Path to this script relative to the extension dir.
				var $extKey = \''.$extKey.'\';	// The extension key.

				',$innerMainContent,'
			}
		');
		$this->addFileToFileArray(
			$pathSuffix.'class.'.$cN.'.php',
			$this->PHPclassFile(
				$extKey,
				$pathSuffix.'class.'.$cN.'.php',
				$indexContent,
				'Service "'.$config['title'].'" for the "'.$extKey.'" extension.',
				'',
				'',
				$indexRequire
			)
		);
	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_sv.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_sv.php']);
}


?>