<?php
/***************************************************************
*	Copyright notice
*
*	(c)	 2001-2008 Kasper Skaarhoj (kasperYYYY@typo3.com) 	All rights reserved
*
*	This script is part of the TYPO3 project. The TYPO3 project is
*	free software; you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation; either version 2 of the License, or
*	(at your option) any later version.
*
*	The GNU General Public License can be found at
*	http://www.gnu.org/copyleft/gpl.html.
*	A copy is found in the textfile GPL.txt and important notices to the license
*	from the author is found in LICENSE.txt distributed with these scripts.
*
*
*	This script is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
*	GNU General Public License for more details.
*
*	This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

$pathKickstarter = t3lib_extMgm::extPath('kickstarter');
require_once($pathKickstarter.'class.tx_kickstarter_compilefiles.php');
require_once($pathKickstarter.'class.tx_kickstarter_reservedwords.php');

/**
 * TYPO3 Extension Kickstarter
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Ingo Renner <ingo@typo3.org>
 */
class tx_kickstarter_wizard extends tx_kickstarter_compilefiles {
	var $varPrefix = 'kickstarter';		// redundant from 'extrep'
	var $siteBackPath = '';
	var $EMmode=1;	// If run from Extension Manager, set to 1.

	var $wizArray = array();

	var $extKey_nusc = 'myext';
	var $extKey      = 'my_ext';
	var $printWOP    = 0;
	var $outputWOP   = 0;
	var $saveKey     = '';
	var $pObj;

	var $afterContent;

	var $languages     = array();
	var $reservedWords = array();

		// Internal:
	var $selectedLanguages = array();
	var $usedNames         = array();
	var $fileArray         = array();
	var $ext_tables        = array();
	var $ext_localconf     = array();
	var $ext_locallang     = array();

	var $color = array('#C8D0B3','#FEE7B5','#EEEEEE');

	var $modData;

	/**
	 * Constructor
	 */
	function tx_kickstarter_wizard() {
		$this->modData = t3lib_div::_POST($this->varPrefix);

		$version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
		if ($version < 4006000) {
			$LOCAL_LANG = t3lib_div::readLLXMLfile(t3lib_extMgm::extPath('setup') . '/mod/locallang.xml', 'default');
				// Getting the available languages
			$theLanguages = t3lib_div::trimExplode('|', TYPO3_languages);
		} else {
			/** @var $xliffParser t3lib_l10n_parser_Xliff */
			$xliffParser = t3lib_div::makeInstance('t3lib_l10n_parser_Xliff');
			$LOCAL_LANG = $xliffParser->getParsedData(t3lib_extMgm::extPath('setup') . '/mod/locallang.xlf', 'default');

			/** @var $locales t3lib_l10n_Locales */
			$locales = t3lib_div::makeInstance('t3lib_l10n_Locales');
				// Getting the available languages
			$theLanguages = $locales->getLocales();
		}
		foreach ($theLanguages as $val) {
			if ($val !== 'default') {
				if ($version < 4006000) {
					$localLabel = htmlspecialchars($LOCAL_LANG['default']['lang_' . $val]);
				} else {
					$localLabel = htmlspecialchars($LOCAL_LANG['default']['lang_' . $val][0]['target']);
				}
				$this->languages[$val] = $localLabel;
			}
		}
		asort($this->languages);

		// init reserved words
		$resWords = t3lib_div::makeInstance('tx_kickstarter_reservedWords');
		$this->reservedWords = $resWords->getReservedWords();
	}

	/**
	 * Initializing the wizard.
	 *
	 * @return	void
	 */
	function initWizArray()	{
		$inArray = unserialize(base64_decode($this->modData['wizArray_ser']));
		$this->wizArray = is_array($inArray) ? $inArray : array();
		if (is_array($this->modData['wizArray_upd']))	{
			$this->wizArray = t3lib_div::array_merge_recursive_overrule($this->wizArray,$this->modData['wizArray_upd']);

				// Use "overwrite_files" from uploaded data always. This prevents recreation of removed files.
			if (isset($this->modData['wizArray_upd']['save']['overwrite_files']))	{
				$this->wizArray['save']['overwrite_files'] = $this->modData['wizArray_upd']['save']['overwrite_files'];
			}
		}

		$lA = is_array($this->wizArray['languages']) ? current($this->wizArray['languages']) : '';
		if (is_array($lA))	{
			foreach($lA as $k => $v)	{
				if ($v && isset($this->languages[$k]))	{
					$this->selectedLanguages[$k]=$this->languages[$k];
				}
			}
		}
	}

	/**
	 * Switch between the basic operations. Calls the different modules and puts
	 * their content into a basic framework.
	 *
	 * @return	HTML code for the kickstarter containing the module content
	 */
	function mgm_wizard()	{
		$this->wizard =& $this;
		$this->initWizArray();
		$this->sections = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['sections'];


		/* HOOK: Place a hook here, so additional things can be done */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['wizard_beforeSectionsHook'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['wizard_beforeSectionsHook'] as $_funcRef) {
				$conf = array('pObj' => $this);
				t3lib_div::callUserFunction($_funcRef, $conf, $this);
			}
		}

		foreach($this->sections as $k => $v) {
			$this->options[$k] = array($v['title'],$v['description']);
		}

		$this->wizArray['save']['extension_key'] = str_replace('-','_',$this->wizArray['save']['extension_key']);
		$saveKey = $this->saveKey = $this->wizArray['save']['extension_key']
			= substr(
				strtolower(trim($this->wizArray['save']['extension_key'])),
				0,
				30
			);
		$this->outputWOP = $this->wizArray['save']['print_wop_comments'] ? 1 : 0;

		if ($saveKey)	{
			$this->extKey=$saveKey;
			$this->extKey_nusc=str_replace('_','',$saveKey);
		}

		if ($this->modData['viewResult'] || $this->modData['updateResult'])	{
			$this->modData['wizAction']='';
			$this->modData['wizSubCmd']='';
			if ($saveKey) {
				$content = $this->view_result();
			} else {
				$content = $this->fw('<strong>Error:</strong> Please enter an extension key first!<br /><br />');
			}
		} elseif ($this->modData['WRITE'])	{
			$this->modData['wizAction']='';
			$this->modData['wizSubCmd']='';
			if ($saveKey) {
				$this->makeFilesArray($this->saveKey);
				$uploadArray = $this->makeUploadArray($this->saveKey,$this->fileArray);
				$version = class_exists('t3lib_utility_VersionNumber')
						? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
						: t3lib_div::int_from_ver(TYPO3_version);
				if ($version < 4000000) {
						// Syntax for TYPO3 3.8 and older
					$this->pObj->importExtFromRep(0,$this->modData['loc'],0,$uploadArray,0,0,1);
				} else {
						// TYPO3 4.0+ syntax
					$this->pObj->importExtFromRep('','',$this->modData['loc'],0,1,$uploadArray);
				}
			} else {
				$content = $this->fw('<strong>Error:</strong> Please enter an extension key first!<br /><br />');
			}
		} elseif ($this->modData['totalForm'])	{
			$content = $this->totalForm();
		} elseif ($this->modData['downloadAsFile'])	{
			if ($saveKey)	{
				$this->makeFilesArray($this->saveKey);
				$uploadArray = $this->makeUploadArray($this->saveKey,$this->fileArray);
				$backUpData = $this->makeUploadDataFromArray($uploadArray);
				$filename='T3X_'.$saveKey.'-'.str_replace('.','_','0.0.0').'.t3x';
				$mimeType = 'application/octet-stream';
				Header('Content-Type: '.$mimeType);
				Header('Content-Disposition: attachment; filename='.$filename);
				echo $backUpData;
				exit;
			} else {
				$content = $this->fw('<strong>Error:</strong> Please enter an extension key first!<br /><br />');
			}
		} else {
			$action = explode(':',$this->modData['wizAction']);
			if ((string)$action[0]=='deleteEl')	{
				unset($this->wizArray[$action[1]][$action[2]]);
			}

			$content = $this->getFormContent();
		}
		$wasContent = $content?1:0;
		$content = '
		<script language="javascript" type="text/javascript">
			function setFormAnchorPoint(anchor)	{
				document.'.$this->varPrefix.'_wizard.action = unescape("'.rawurlencode($this->linkThisCmd()).'")+"#"+anchor;
			}
		</script>
		<table border="0" cellpadding="0" cellspacing="0">
			<form action="' . $this->linkThisCmd() . '" method="POST" name="' . $this->varPrefix . '_wizard">
			<tr>
				<td valign="top">'.$this->sidemenu().'</td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				<td valign="top">'.$content.'
					<input type="hidden" name="'.$this->piFieldName("wizArray_ser").'" value="'.htmlspecialchars(base64_encode(serialize($this->wizArray))).'" /><br />';

		if ((string)$this->modData['wizSubCmd'])	{
			if ($wasContent)	$content.='<input name="update2" type="submit" value="Update..." /> ';
		}
		$content.='
					<input type="hidden" name="'.$this->piFieldName("wizAction").'" value="'.$this->modData["wizAction"].'" />
					<input type="hidden" name="'.$this->piFieldName("wizSubCmd").'" value="'.$this->modData["wizSubCmd"].'" />
					'.$this->cmdHiddenField().'
				</td>
			</tr>
			</form>
		</table>' . $this->afterContent;

		return $content;
	}

	/**
	 * Get form content
	 *
	 * @return	HTML code of special section
	 */
	function getFormContent()	{

		if($this->sections[$this->modData['wizSubCmd']]) {
			$path = t3lib_div::getFileAbsFileName($this->sections[$this->modData['wizSubCmd']]['filepath']);
			require_once($path);
			$section = t3lib_div::makeInstance($this->sections[$this->modData['wizSubCmd']]['classname']);
			$section->wizard = &$this;
			return $section->render_wizard();
		}
	}

	/**
	 * Total form
	 *
	 * @return	HTML
	 */
	function totalForm()	{
		$buf = array($this->printWOP,$this->dontPrintImages);
		$this->printWOP = 1;

		$lines=array();
		foreach($this->options as $k => $v)	{
			// Add items:
			$items = $this->wizArray[$k];
			if (is_array($items))	{
				foreach($items as $k2 => $conf)	{
					$this->modData['wizSubCmd']=$k;
					$this->modData['wizAction']='edit:'.$k2;
					$lines[]=$this->getFormContent();
				}
			}
		}

		$this->modData['wizSubCmd']='';
		$this->modData['wizAction']='';
		list($this->printWOP,$this->dontPrintImages) = $buf;

		$content = implode('<hr />',$lines);
		return $content;
	}

	/**
	 * Side menu
	 *
	 * @return	HTML code of the side menu
	 */
	function sidemenu()	{
		$actionType = $this->modData['wizSubCmd'].':'.$this->modData['wizAction'];
		$singles    = $this->getSingles();
		$lines      = array();
		foreach($this->options as $k => $v)	{
			// Add items:
			$items = $this->wizArray[$k];
			$c = 0;
			$iLines = array();
			if (is_array($items))	{
				foreach($items as $k2=>$conf)	{
					$dummyTitle = t3lib_div::inList($singles, $k) ? '[Click to Edit]' : '<em>Item '.$k2.'</em>';
					$isActive   = !strcmp($k.':edit:'.$k2, $actionType);
					$delIcon    = $this->linkStr('<img src="'.$this->siteBackPath.TYPO3_mainDir.'gfx/garbage.gif" width="11" height="12" border="0" title="Remove item" />','','deleteEl:'.$k.':'.$k2);
					$iLines[]   = '<tr'.($isActive?$this->bgCol(2,-30):$this->bgCol(2)).'><td>'.$this->fw($this->linkStr($this->bwWithFlag($conf['title']?$conf['title']:$dummyTitle,$isActive),$k,'edit:'.$k2)).'</td><td>'.$delIcon.'</td></tr>';
					$c = $k2;
				}
			}
			if (!t3lib_div::inList($singles, $k) || !count($iLines))	{
				$c++;
				$addIcon = $this->linkStr('<img src="'.$this->siteBackPath.TYPO3_mainDir.'gfx/add.gif" width="12" height="12" border="0" title="Add item" />',$k,'edit:'.$c);
			} else {$addIcon = '';}

			$lines[]='<tr'.$this->bgCol(1).'><td nowrap="nowrap"><strong>'.$this->fw($v[0]).'</strong></td><td>'.$addIcon.'</td></tr>';
			$lines = array_merge($lines,$iLines);
		}

		$lines[]='<tr><td>&nbsp;</td><td></td></tr>';

		$lines[]='<tr><td width="150">
		'.$this->fw('Enter extension key:').'<br />
		<input type="text" name="'.$this->piFieldName('wizArray_upd').'[save][extension_key]" value="' . htmlspecialchars($this->wizArray['save']['extension_key']) . '" maxlength="30" />
		'.($this->wizArray['save']['extension_key']?'':'<br /><a href="http://typo3.org/1382.0.html" target="_blank"><font color="red">Make sure to enter the right extension key from the beginning here!</font> You can register one here.</a>').'
		</td><td></td></tr>';

		$lines[]='<tr><td><input type="submit" value="Update..." /></td><td></td></tr>';
		$lines[]='<tr><td><input type="submit" name="'.$this->piFieldName('totalForm').'" value="Total form" /></td><td></td></tr>';

		if ($this->saveKey)	{
			$lines[]='<tr><td><input type="submit" name="'.$this->piFieldName('viewResult').'" value="View result" /></td><td></td></tr>';
			$lines[]='<tr><td><input type="submit" name="'.$this->piFieldName('downloadAsFile').'" value="D/L as file" /></td><td></td></tr>';
			$lines[]='<tr><td>
			<input type="hidden" name="'.$this->piFieldName('wizArray_upd').'[save][print_wop_comments]" value="0" /><input type="checkbox" name="'.$this->piFieldName('wizArray_upd').'[save][print_wop_comments]" value="1" '.($this->wizArray['save']['print_wop_comments']?' checked="checked"':'').' />'.$this->fw('Print WOP comments').'
			</td><td></td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['sidemenu'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['sidemenu'] as $_funcRef) {
				$lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
			}
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * gets single items as a comma separated list
	 *
	 * @return string comma separated list of single items
	 */
	function getSingles() {
		$singles = array('save');

		foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['sections'] as $sectionName => $sectionConf) {
			if($sectionConf['singleItem']) {
				$singles[] = $sectionName;
			}
		}

		return implode(',', $singles);
	}

	/**
	 * View result
	 *
	 * @return	HTML with filelist and fileview
	 */
	function view_result()	{
		$this->makeFilesArray($this->saveKey);

		$keyA = array_keys($this->fileArray);
		asort($keyA);

		$filesOverview1 = array();
		$filesOverview2 = array();
		$filesContent   = array();

		$filesOverview1[]= '<tr'.$this->bgCol(1).'>
			<td><strong>' . $this->fw('Filename:') . '</strong></td>
			<td><strong>' . $this->fw('Size:') . '</strong></td>
			<td><strong>' . $this->fw('&nbsp;') . '</strong></td>
			<td><strong>' . $this->fw('Overwrite:') . '</strong></td>
		</tr>';

		foreach($keyA as $fileName)	{
			$data = $this->fileArray[$fileName];

			$fI = pathinfo($fileName);
			if (t3lib_div::inList('php,sql,txt,xml',strtolower($fI['extension'])))	{
				$linkToFile='<strong><a href="#'.md5($fileName).'">'.$this->fw("&nbsp;View&nbsp;").'</a></strong>';

				if($fI['extension'] == 'xml') {
					$data['content'] = $GLOBALS['LANG']->csConvObj->utf8_decode(
						$data['content'],
						$GLOBALS['LANG']->charSet
					);
				}

				$filesContent[]='<tr' .$this->bgCol(1) .'>
				<td><a name="' . md5($fileName) . '"></a><strong>' . $this->fw($fileName) . '</strong></td>
				</tr>
				<tr>
					<td>' . $this->preWrap($data['content'], $fI['extension']) . '</td>
				</tr>';
			} else $linkToFile=$this->fw('&nbsp;');

			$line = '<tr' . $this->bgCol(2) . '>
				<td>' . $this->fw($fileName) . '</td>
				<td>' . $this->fw(t3lib_div::formatSize($data['size'])) . '</td>
				<td>' . $linkToFile . '</td>
				<td>';

			if($fileName == 'doc/wizard_form.dat'
			|| $fileName == 'doc/wizard_form.html') {
				$line .= '<input type="hidden" name="' . $this->piFieldName('wizArray_upd') . '[save][overwrite_files]['.$fileName.']" value="1" />';
			} else {
				$checked = '';

				if(!is_array($this->wizArray['save']['overwrite_files']) // check for first time call of "View Result"
				|| (isset($this->wizArray['save']['overwrite_files'][$fileName]) && $this->wizArray['save']['overwrite_files'][$fileName] == '1') // if selected
				|| !isset($this->wizArray['save']['overwrite_files'][$fileName]) // if new
				) {
					$checked = ' checked="checked"';
				}

				$line .= '<input type="hidden" name="' . $this->piFieldName('wizArray_upd') . '[save][overwrite_files]['.$fileName.']" value="0" />';
				$line .= '<input type="checkbox" name="' . $this->piFieldName('wizArray_upd') . '[save][overwrite_files]['.$fileName.']" value="1"'.$checked.' />';
			}

			$line .= '</td>
			</tr>';
			if (strstr($fileName,'/'))	{
				$filesOverview2[]=$line;
			} else {
				$filesOverview1[]=$line;
			}
		}

		$content  = '<table border="0" cellpadding="1" cellspacing="2">'.implode('',$filesOverview1).implode('',$filesOverview2).'</table>';
		$content .= '<br /><input type="submit" name="'.$this->piFieldName('updateResult').'" value="Update result" /><br />';
		$content .= $this->fw('<br /><strong>Author name:</strong> '.$this->wizArray['emconf'][1]['author'].'
							<br /><strong>Author email:</strong> '.$this->wizArray['emconf'][1]['author_email']);


		$content.= '<br /><br />';
		if (!$this->EMmode)	{
			$content.='<input type="submit" name="'.$this->piFieldName('WRITE').'" value="WRITE to \''.$this->saveKey.'\'" />';
		} else {
			$content.='
				<strong>'.$this->fw('Write to location:').'</strong><br />
				<select name="'.$this->piFieldName('loc').'">'.
					($this->pObj->importAsType('G')?'<option value="G">Global: '.$this->pObj->typePaths['G'].$this->saveKey.'/'.(@is_dir(PATH_site.$this->pObj->typePaths['G'].$this->saveKey)?' (OVERWRITE)':' (empty)').'</option>':'').
					($this->pObj->importAsType('L')?'<option value="L" selected="selected">Local: '.$this->pObj->typePaths['L'].$this->saveKey.'/'.(@is_dir(PATH_site.$this->pObj->typePaths['L'].$this->saveKey)?' (OVERWRITE)':' (empty)').'</option>':'').
				'</select>
				<input type="submit" name="'.$this->piFieldName('WRITE').'" value="WRITE" onclick="return confirm(\'If the setting in the selectorbox says OVERWRITE\nthen the marked files of the current extension in that location will be OVERRIDDEN! \nPlease decide if you want to continue.\n\n(Remember, this is a *kickstarter* - NOT AN editor!)\');" />
			';
		}


		$this->afterContent= '<br /><table border="0" cellpadding="1" cellspacing="2">'.implode('',$filesContent).'</table>';
		return $content;
	}



	/**
	 * Encodes extension upload array
	 *
	 * @param	array		$uploadArray: The data array that should be serialized
	 * @return	string		serialized data prepended with md5 checksum
	 */
	function makeUploadDataFromArray($uploadArray)	{
		if (is_array($uploadArray))	{
			$serialized = serialize($uploadArray);
			$md5 = md5($serialized);

			$content  = $md5.':';
			$content .= ':';
			$content .= $serialized;
		}
		return $content;
	}
	/**
	 * Make upload array out of extension
	 *
	 * @param	string		$extKey: extension key
	 * @param	array		$files: array with filedata
	 * @return	array of extension files
	 */
	function makeUploadArray($extKey,$files)	{
		$uploadArray=array();
		$uploadArray['extKey']=$extKey;
		$uploadArray['EM_CONF']=Array(
			'title' => '[No title]',
			'description' => '[Enter description of extension]',
			'category' => 'example',
			'author' => $this->userfield('name'),
			'author_email' => $this->userfield('email'),

		);

		$uploadArray['EM_CONF'] = array_merge($uploadArray['EM_CONF'],$this->makeEMCONFpreset(''));

		if (is_array($this->_addArray))	{
			$uploadArray['EM_CONF'] = array_merge($uploadArray['EM_CONF'],$this->_addArray);
		}
		$uploadArray['misc']['codelines']=0;
		$uploadArray['misc']['codebytes']=0;
		$uploadArray['techInfo'] = '';

			// Go through overwrite-files list to determine which files are to be written to disk
			// This allows to change only certain files on disk while keeping all others
		if(is_array($this->wizArray['save']['overwrite_files'])) {
			foreach($this->wizArray['save']['overwrite_files'] as $fileName => $overwrite) {
				if($overwrite) {
					$uploadArray['FILES'][$fileName] = $files[$fileName];
				}
			}
		}

		return $uploadArray;
	}


}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_wizard.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_wizard.php']);
}

?>