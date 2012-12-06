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

#vars that probably still need "wizard->" added: dontPrintImages printWOP


/**
 * TYPO3 Extension Kickstarter
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Ingo Renner	<ingo@typo3.org>
 */
class tx_kickstarter_sectionbase {

	/* instance of the main Kickstarter Wizard class (class.tx_kickstarter_wizard.php) */
	var $wizard;

	/* instance of the Kickstarter Compilefiles class (class.tx_kickstarter_compilefiles.php) */
	var $compilefiles;

	/* Unique ID of this section (used in forms and data processing) */
	var $sectionID = 'uniqueID';

	/* Variable-Prefix used for the generation of input-fields */
	var $varPrefix = 'kickstarter';

	/* renders the wizard for this section */
	function render_wizard() {
	}

	/* renders the code for this section */
	function render_extPart() {
	}

	/**
	 * processes certain hooks for each section
	 *
	 * @param	string		hook name
	 * @param	array		the data to proces by the hook
	 * @return	array		the processed data
	 */
	function &process_hook($hookName, &$data) {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName] as $_funcRef) {
				$data =& t3lib_div::callUserFunction($_funcRef, $data, $this);
			}
		}
		return $data;
	}

	/**
	 * renders a checkbox with optionaly set checkmark
	 *
	 * @param	string		field prefix
	 * @param	boolean		sets the checkmark
	 * @param	boolean		default setting for the checkmark
	 * @return	string		the complete checkbox
	 */
	function renderCheckBox($prefix,$value,$defVal=0)	{
		if (!isset($value))	$value=$defVal;
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="hidden" name="'.$this->piFieldName('wizArray_upd').$prefix.'" value="0"><input type="checkbox" class="checkbox" id="field_'.md5($prefix).'" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="1"'.($value?' checked="checked"':'').' onclick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	/**
	 * renders a textarea with default value
	 *
	 * @param	string		field prefix
	 * @param	string		default value
	 * @return	string		the complete textarea
	 */
	function renderTextareaBox($prefix,$value)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<textarea name="'.$this->piFieldName('wizArray_upd').$prefix.'" style="width:600px;" rows="10" wrap="off" onchange="'.$onCP[1].'" title="'.htmlspecialchars('WOP:'.$prefix).'"'.$this->wop($prefix).'>'.t3lib_div::formatForTextarea($value).'</textarea>';
	}

	/**
	 * renders a regular text input field with default value
	 *
	 * @param	string		field prefix
	 * @param	string		default value
	 * @param	integer		width of the input field in px
	 * @return	string		the complete text input field
	 */
	function renderStringBox($prefix,$value,$width=200)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="text" name="'.$this->piFieldName('wizArray_upd').$prefix.'" value="'.htmlspecialchars($value).'" style="width:'.$width.'px;" onchange="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	/**
	 * renders a radio button
	 *
	 * @param	string		field prefix
	 * @param	string		checks the radio button if equal to $thisValue
	 * @param	string		value for the radio button
	 * @return	string		the complete radio button
	 */
	function renderRadioBox($prefix,$value,$thisValue)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="radio" name="'.$this->piFieldName('wizArray_upd').$prefix.'" value="'.$thisValue.'"'.(!strcmp($value,$thisValue)?' checked="checked"':'').' onclick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	/**
	 * renders a select box
	 *
	 * @param	string		field prefix
	 * @param	string		the value of the preselected option
	 * @param	array		array of string values for the options
	 * @return	string		the complete select box
	 */
	function renderSelectBox($prefix,$value,$optValues)	{
		$onCP = $this->getOnChangeParts($prefix);
		$opt=array();
		$isSelFlag=0;
		foreach($optValues as $k=>$v)	{
			$sel = (!strcmp($k,$value)?' selected="selected"':'');
			if ($sel)	$isSelFlag++;
			$opt[]='<option value="'.htmlspecialchars($k).'"'.$sel.'>'.htmlspecialchars($v).'</option>';
		}
		if (!$isSelFlag && strcmp('',$value))	$opt[]='<option value="'.$value.'" selected="selected">'.htmlspecialchars("CURRENT VALUE '".$value."' DID NOT EXIST AMONG THE OPTIONS").'</option>';
		return $this->wopText($prefix).$onCP[0].'<select name="'.$this->piFieldName("wizArray_upd").$prefix.'" onchange="'.$onCP[1].'"'.$this->wop($prefix).'>'.implode('',$opt).'</select>';
	}

	/**
	 * renders the "What is this" help link
	 *
	 * @param	string		title attribute for the link
	 * @return	string		the help link
	 */
	function whatIsThis($str)	{
		return ' <a href="#" title="'.htmlspecialchars($str).'" style="cursor:help" onclick="alert('.$GLOBALS['LANG']->JScharCode($str).');return false;">(What is this?)</a>';
	}

	/**
	 * renders the textbox for the localized label of a field
	 *
	 * @param	string		field name the localization is for
	 * @param	string		field prefix
	 * @param	array		$piConf: ...
	 * @return	string		the complete localization field
	 */
	function renderStringBox_lang($fieldName,$ffPrefix,$piConf)	{
		$content = $this->renderStringBox($ffPrefix.'['.$fieldName.']',$piConf[$fieldName]).' [English]';
		if (count($this->wizard->selectedLanguages))	{
			$lines=array();
			foreach($this->wizard->selectedLanguages as $k=>$v) {
				$lines[]=$this->renderStringBox($ffPrefix.'['.$fieldName.'_'.$k.']',$piConf[$fieldName.'_'.$k]).' ['.$v.']';
			}
			$content.=$this->textSetup('', implode('<br />', $lines));
		}
		return $content;
	}

	/**
	 * renders a section of text/fields with a header
	 *
	 * @param	string		header
	 * @param	string		content
	 * @return	string		text/field section with header
	 */
	function textSetup($header,$content)	{
		return ($header?'<strong>'.$header.'</strong><br />':'').'<blockquote>'.trim($content).'</blockquote>';
	}

	/**
	 * renders an image tag with content before and after it
	 *
	 * @param	string		file name of the image
	 * @param	string		additional attributes for the img tag
	 * @param	string		content before the img tag
	 * @param	string		content after the img tag
	 * @return	string		the complete image
	 */
	function resImg($name,$p='align="center"',$pre='<br />',$post='<br />')	{
		if ($this->dontPrintImages)	return '<br />';
		$imgRel = $this->path_resources().$name;
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return $pre.'<img src="'.$this->wizard->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?' '.$p:'').' vspace="5" border="1" style="border:solid 1px;" />'.$post;
	}

	/**
	 * renders an icon
	 *
	 * @param	string		file name of the icon
	 * @param	string		additional attributes for the img tag
	 * @return	string		complete icon as img tag
	 */
	function resIcon($name,$p='')	{
		if ($this->dontPrintImages)	return '';
		$imgRel = $this->path_resources('icons/').$name;
		if (!@is_file(PATH_site.$imgRel))	return '';
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return '<img src="'.$this->wizard->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?' '.$p:'').' />';
	}

	/**
	 * returns the path to a subdir of the kickstarter
	 *
	 * @param	string		subdir
	 * @return	stirng		path to the subdir
	 */
	function path_resources($subdir='res/')	{
		return substr(t3lib_extMgm::extPath('kickstarter'),strlen(PATH_site)).$subdir;
	}

	/**
	 * returns the action for onclick events of certain fields
	 *
	 * @param	string		field prefix
	 * @return	array		array with an anchor and a JS function call
	 */
	function getOnChangeParts($prefix)	{
		$md5h=t3lib_div::shortMd5($this->piFieldName('wizArray_upd').$prefix);
		return array('<a name="'.$md5h.'"></a>','setFormAnchorPoint(\''.$md5h.'\');');
	}

	/**
	 * returns a title attribute with the WOP description
	 *
	 * @param	string		field prefix
	 * @return	string		title attribute with WOP comment
	 */
	function wop($prefix)	{
		return ' title="'.htmlspecialchars('WOP: '.$prefix).'"';
	}

	/**
	 * returns the names for classes, tables, fields and modules
	 *
	 * @param	string		extension key
	 * @param	string		type: class/tables/fields/module
	 * @param	string		suffix
	 * @return	string		name for the class/table/field/module
	 */
	function returnName($extKey,$type,$suffix='')	{
		if (substr($extKey,0,5)=='user_')	{
			$extKey = substr($extKey,5);
			switch($type)	{
				case 'class':
					return 'user_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				case 'tables':
				case 'fields':
					return 'user_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				case 'module':
					return 'u'.str_replace('_','',$extKey).$suffix;
			}
		} else {
			switch($type)	{
				case 'class':
					return 'tx_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				case 'tables':
				case 'fields':
					return 'tx_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				case 'module':
					return 'tx'.str_replace('_','',$extKey).$suffix;
			}
		}
	}

	/**
	 * creates the WOP text
	 *
	 * @param	string		WOP prefix
	 * @return	string		WOP text
	 */
	function wopText($prefix)	{
		return $this->printWOP?'<font face="verdana,arial,sans-serif" size=1 color="#999999">'.htmlspecialchars($prefix).':</font><br />':'';
	}

	/**
	 * adds 3 table rows with alternating colors, the first row containing a
	 * header and the third row as spacer
	 *
	 * @param	array		array of lines
	 * @param	string		field key
	 * @param	array		field options
	 * @param	string		alternative header
	 * @param	string		index
	 * @return	array		array with added lines
	 */
	function catHeaderLines($lines,$k,$v,$altHeader='',$index='')	{
					$lines[]='<tr'.$this->bgCol(1).'><td><strong>'.$this->fw($v[0]).'</strong></td></tr>';
					$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw($v[1]).'</td></tr>';
					$lines[]='<tr><td></td></tr>';
		return $lines;
	}

	/**
	 * creates the link to the currently selected item in the left menu
	 *
	 * @param	string		category like DB tables, frontend plugins, backend modules
	 * @return	string		the linked item
	 */
	function linkCurrentItems($cat)	{
		$items = $this->wizard->wizArray[$cat];
		$lines=array();
		$c=0;
		if (is_array($items))	{
			foreach($items as $k=>$conf)	{
				$lines[]='<strong>'.$this->linkStr($conf['title']?$conf['title']:'<em>Item '.$k.'</em>',$cat,'edit:'.$k).'</strong>';
				$c=$k;
			}
		}
		if (!t3lib_div::inList($this->wizard->getSingles(), $cat) || !count($lines))	{
			$c++;
			if (count($lines))	$lines[]='';
			$lines[]=$this->linkStr('Add new item',$cat,'edit:'.$c);
		}
		return $this->fw(implode('<br />',$lines));
	}

	/**
	 * creates a link for an item in the left menu
	 *
	 * @param	string		link text
	 * @param	string		category
	 * @param	string		action
	 * @return	string		the linked item
	 */
	function linkStr($str,$wizSubCmd,$wizAction)	{
		return '<a href="#" onclick="
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName('wizSubCmd').'\'].value=\''.$wizSubCmd.'\';
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName('wizAction').'\'].value=\''.$wizAction.'\';
			document.'.$this->varPrefix.'_wizard.submit();
			return false;">'.$str.'</a>';
	}

	/**
	 * returns the bgcolor attribute for html tags
	 *
	 * @param	integer		color number
	 * @param	integer		color modifier
	 * @return	string		bgcolor attribute
	 */
	function bgCol($n,$mod=0)	{
		$color = $this->color[$n-1];
		if ($mod)	$color = t3lib_div::modifyHTMLcolor($color,$mod,$mod,$mod);
		return ' bgcolor="'.$color.'"';
	}

	/**
	 * registers a new entry in the wizard array
	 *
	 * @param	string		key
	 * @param	string		index
	 * @return	void
	 */
	function regNewEntry($k,$index)	{
		if (!is_array($this->wizard->wizArray[$k][$index]))	{
			$this->wizard->wizArray[$k][$index]=array();
		}
	}

	/**
	 * makes a text bold if $flag is set
	 *
	 * @param	string		text
	 * @param	boolean		flag to make the text bold
	 * @return	string		text, optionaly made bold
	 */
	function bwWithFlag($str,$flag)	{
		if ($flag)	$str = '<strong>'.$str.'</strong>';
		return $str;
	}

	/**
	 * Getting link to this page + extra parameters, we have specified
	 *
	 * @param	array		Additional parameters specified.
	 * @return	string		The URL
	 */
	function linkThisCmd($uPA=array())	{
		$url = t3lib_div::linkThisScript($uPA);
		return $url;
	}

	/**
	 * Font wrap function; Wrapping input string in a <span> tag with font family and font size set
	 *
	 * @param	string		Input value
	 * @return	string		Wrapped input value.
	 */
	function fw($str)	{
		return '<span style="font-family:verdana,arial,sans-serif; font-size:10px;">'.$str.'</span>';
	}

	/**
	 * returns a field prefix
	 *
	 * @param	string		field key
	 * @return	string		the field prefix
	 */
	function piFieldName($key)	{
		return $this->varPrefix.'['.$key.']';
	}

	/**
	 * returns hidden field containing the current command
	 *
	 * @return	sting		hidden field containing the current command
	 */
	function cmdHiddenField()	{
		return '<input type="hidden" name="'.$this->piFieldName('cmd').'" value="'.htmlspecialchars($this->currentCMD).'">';
	}

	/**
	 * wraps a text in <pre> tags
	 *
	 * @param	string		the string to wrap
	 * @return	string		string wrapped in <pre> tags
	 */
	function preWrap($str, $ext='txt')	{
		if ($ext == 'php') {
			return '<p style="font-size:12px;">' . highlight_string($str, true) . '</p>';
		} else {
			$str = str_replace(chr(9),'&nbsp;&nbsp;&nbsp;&nbsp;',htmlspecialchars($str));
			$str = '<pre>' . $str . '</pre>';
		return $str;
		}
	}

	/**
	 * checks whether a field is a RTE field
	 *
	 * @param	string		field
	 * @return	boolean		true if field si RTE field, false otherwise
	 */
	function fieldIsRTE($fC)	{
		return !strcmp($fC['type'],'textarea_rte') &&
						($fC['conf_rte']=='basic' ||
						(t3lib_div::inList('custom,moderate',$fC['conf_rte']) && $fC['conf_mode_cssOrNot'])
						);
	}

######### Functions from compilefiles #########

	/**
	 * [Describe function...]
	 *
	 * @param	string		content
	 * @param	integer		number of lines to add before the content
	 * @return	string		prefixed content
	 */
	function sPS($content,$preLines=1)	{
		$lines = explode(chr(10),str_replace(chr(13),'',$content));
		$lastLineWithContent=0;
		$firstLineWithContent=-1;
		$min=array();
		reset($lines);
		while(list($k,$v)=each($lines))	{
			if (trim($v))	{
				if ($firstLineWithContent==-1) {
					$firstLineWithContent=$k;
				}
				list($preSpace) = preg_split('/[^[:space:]]/', $v, 2);
				$min[]=count(explode(chr(9),$preSpace));
				$lastLineWithContent=$k;
			}
		}
		$number_of=count($min) ? min($min) : 0;
		$newLines=array();
		if ($firstLineWithContent>=0)	{
			for ($a=$firstLineWithContent;$a<=$lastLineWithContent;$a++)	{
				$parts = explode(chr(9),$lines[$a],$number_of);
				$newLines[]=end($parts);
			}
		}
		return str_pad('',$preLines,chr(10)).implode(chr(10),$newLines).chr(10);
	}

	/**
	 * gets local lang key reference
	 *
	 * @param	array		$config: ...
	 * @param	string		$key: ...
	 * @param	string		label key
	 * @return	string		reference to label
	 */
	function getSplitLabels_reference($config,$key,$LLkey)	{
		$this->wizard->ext_locallang_db['default'][$LLkey]=array(trim($config[$key]));
		if (count($this->wizard->languages))	{
			reset($this->wizard->languages);
			while(list($lk,$lv)=each($this->wizard->languages))	{
				if (isset($this->wizard->selectedLanguages[$lk]))	{
					$this->wizard->ext_locallang_db[$lk][$LLkey]=array(trim($config[$key.'_'.$lk]));
				}
			}
		}
		return 'LLL:EXT:'.$this->wizard->extKey.'/locallang_db.xml:'.$LLkey;
	}

	/**
	 * return the WOP comment if activated
	 *
	 * @param	string		WOP comment
	 * @return	string		WOP comment if activated, empty string otherwise
	 */
	function WOPcomment($str)	{
		return $str&&$this->wizard->outputWOP ? '## '.$str : '';
	}

	/**
	 * prepands $content with $number of tab characters
	 *
	 * @param	string		content to indent
	 * @param	integer		level of indention
	 * @return	string		indented string
	 */
	function indentLines($content,$number=1)	{
		$preTab = str_pad('',$number,chr(9));
		$lines = explode(chr(10),str_replace(chr(13),'',$content));
		while(list($k,$v)=each($lines))	{
			$lines[$k]=$preTab.$v;
		}
		return implode(chr(10),$lines);
	}

	/**
	 * prints a string preformatted by wrapping in <pre> tags
	 *
	 * @param	string		string to wrap in <pre> tags
	 * @return	string		preformatted string
	 */
	function printPre($content)	{
		echo '<pre>'.htmlspecialchars(str_replace(chr(9),'    ',$content)).'</pre>';
	}

	/**
	 * wraps and indents a string
	 *
	 * @param	string		string to put before $content
	 * @param	string		content to wrap and indent
	 * @param	string		string to put after $content
	 * @param	integer		level of indention
	 * @return	string		wrapped and indeted string
	 */
	function wrapBody($before, $content, $after, $indent=1)	{
		$parts   = array();
		$parts[] = $this->sPS($before,0);
		$parts[] = $this->indentLines(rtrim($content),$indent);
		$parts[] = chr(10).$this->sPS($after,0);

		return implode('',$parts);
	}

	/**
	 * replaces markers
	 *
	 * @param	string		content with markers
	 * @param	array		array of markers with key as marker and value as replacement
	 * @return	string		content with replaced markers
	 */
	function replaceMarkers($content,$markers)	{
		reset($markers);
		while(list($k,$v)=each($markers))	{
			$content = str_replace($k,$v,$content);
		}
		return $content;
	}

	/**
	 * makes array of file meta data
	 *
	 * @param	string		file name
	 * @param	string		file content
	 * @return	array		meta data array
	 */
	function makeFileArray($name,$content)	{

		return array(
			'name'          => $name,
			'size'          => strlen($content),
			'mtime'         => time(),
			'is_executable' => 0,
			'content'       => $content,
			'content_md5'   => md5($content)
		);
	}

	/**
	 * protects \ and ' in $value
	 *
	 * @param	string		value
	 * @return	string		string with protected \ and '
	 */
	function slashValueForSingleDashes($value)	{
		return str_replace("'","\'",str_replace('\\','\\\\',$value));
	}

	/**
	 * Gets localization labels separated with |.
	 *
	 * @param array $config
	 * @param string $key
	 * @return string labels separated with |
	 */
	function getSplitLabels(array $config, $key) {
		$language = array();
		$language[]=str_replace('|', '', $config[$key]);
		if (count($this->wizard->languages)) {
			reset($this->wizard->languages);
			while (list($lk, $lv) = each($this->wizard->languages)) {
				if (isset($this->wizard->selectedLanguages[$lk])) {
					$language[] = str_replace('|', '', $config[$key . '_' . $lk]);
				} else $language[] = '';
			}
		}
		$out = implode('|', $language);
		$out = str_replace(chr(10), '', $out);
		$out = rtrim(str_replace('|', chr(10), $out));
		$out = str_replace(chr(10), '|', $out);
		return $out;
	}

	/**
	 * Generates locallang file.
	 *
	 * @param array array with locallang labels
	 * @param string locallang filename
	 * @param string description for the locallang file
	 * @param string llXML file type. Default is "module". Use "database" if its a locallang_db file!
	 * @return void
	 */
	function addLocalLangFile(array $arr, $filename, $description, $fileType = 'module') {
		$outputArray = array();

		$outputArray['meta'] = array(
			'type' => $fileType,
			'description' => $description
		);

		$outputArray['data'] = array();
		while (list($lK, $labels) = each($arr)) {
			if (is_array($labels)) {
				while (list($l, $v) = each($labels)) {
					if (strcmp($v[0], '')) {
						$outputArray['data'][$lK][$l] =
							$GLOBALS['LANG']->csConvObj->utf8_encode(
								$v[0],
								$GLOBALS['LANG']->charSet
							);
					}
				}
			}
		}

		$this->addFileToFileArray($filename,$this->createXML($outputArray));
	}

	/**
	 * Creates llXML string from input array
	 *
	 * @param array locallang-XML array
	 * @return string XML content
	 */
	function createXML(array $outputArray) {

			// Options:
		$options = array(
			'parentTagMap' => array(
				'data'         => 'languageKey',
				'orig_hash'    => 'languageKey',
				'orig_text'    => 'languageKey',
				'labelContext' => 'label',
				'languageKey'  => 'label'
			)
		);

			// Creating XML file from $outputArray:
		$XML = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . chr(10);
		$XML.= t3lib_div::array2xml($outputArray, '', 0, 'T3locallang', 0, $options);

		return $XML;
	}

	/**
	 * Generates conf.php for BE modules
	 *
	 * @param string $extKey Extension key
	 * @param array $config Configuration
	 * @param string $pathSuffix TYPO3_MOD_PATH suffix
	 * @param string $cN class name
	 * @param string $k
	 * @param string $k_prefix
	 * @return	void
	 */
	function writeStandardBE_xMod($extKey, array $config, $pathSuffix, $cN, $k, $k_prefix) {
			// Make conf.php file:
		$content = $this->sPS('
				// DO NOT REMOVE OR CHANGE THESE 3 LINES:
			define(\'TYPO3_MOD_PATH\', \'ext/' . $extKey . '/' . $pathSuffix . '\');
			$BACK_PATH = \'../../../\';
			$MCONF[\'name\'] = \'xMOD_' . $cN . '\';
		');
		$content = $this->wrapBody('
			<?php
			', $content, '
			?>
		', 0);
		$this->addFileToFileArray($pathSuffix . 'conf.php', trim($content));
		$this->wizard->EM_CONF_presets['module'][] = preg_replace('/\/$/', '', $pathSuffix);

			// Add title to local lang file
		$ll = array();
		$this->addLocalConf($ll, $config, 'title', $k_prefix, $k, 1);
		$this->addLocalConf($ll, array('function1' => 'Function #1'), 'function1', $k_prefix, $k, 1, 1);
		$this->addLocalConf($ll, array('function2' => 'Function #2'), 'function2', $k_prefix, $k, 1, 1);
		$this->addLocalConf($ll, array('function3' => 'Function #3'), 'function3', $k_prefix, $k, 1, 1);
		$this->addLocalLangFile($ll, $pathSuffix . 'locallang.xml', 'Language labels for ' . $extKey . ' module ' . $k_prefix . $k);

			// Add clear.gif
		$this->addFileToFileArray($pathSuffix.'clear.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/clear.gif'));

		$indexRequire = $this->sPS('
				// DEFAULT initialization of a module [BEGIN]
			unset($MCONF);
			require_once(\'conf.php\');
			require_once($BACK_PATH.\'init.php\');
			require_once($BACK_PATH.\'template.php\');
			$GLOBALS[\'LANG\']->includeLLFile(\'EXT:' . $extKey . '/' . $pathSuffix . 'locallang.xml\');
			//require_once(PATH_t3lib . \'class.t3lib_scbase.php\');
				// ....(But no access check here...)
				// DEFAULT initialization of a module [END]
		');
			// Make module index.php file:
		$indexContent = $this->sPS(
				'class ' . $cN . ' extends t3lib_SCbase {
	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	[type]		...
	 */
	public function menuConfig() {
		$this->MOD_MENU = array(
			\'function\' => array(
				\'1\' => $GLOBALS[\'LANG\']->getLL(\'function1\'),
				\'2\' => $GLOBALS[\'LANG\']->getLL(\'function2\'),
				\'3\' => $GLOBALS[\'LANG\']->getLL(\'function3\'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return void
	 */
	public function main() {

			// Draw the header.
		$this->doc = t3lib_div::makeInstance(\'mediumDoc\');
		$this->doc->backPath = $GLOBALS[\'BACK_PATH\'];
		$this->doc->form = \'<form action="" method="post">\';

			// JavaScript
		$this->doc->JScode = \'
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		\';

		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($GLOBALS[\'BE_USER\']->user[\'admin\'] && !$this->id))	{
			if ($GLOBALS[\'BE_USER\']->user[\'admin\'] && !$this->id)	{
				$this->pageinfo = array(
						\'title\' => \'[root-level]\',
						\'uid\'   => 0,
						\'pid\'   => 0
				);
			}

			$headerSection = $this->doc->getHeader(\'pages\', $this->pageinfo, $this->pageinfo[\'_thePath\']) . \'<br />\'
					. $GLOBALS[\'LANG\']->sL(\'LLL:EXT:lang/locallang_core.xml:labels.path\') . \': \' . t3lib_div::fixed_lgd_cs($this->pageinfo[\'_thePath\'], -50);

			$this->content.=$this->doc->startPage($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content.=$this->doc->header($GLOBALS[\'LANG\']->getLL(\'title\'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section(\'\',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,\'SET[function]\',$this->MOD_SETTINGS[\'function\'],$this->MOD_MENU[\'function\'])));
			$this->content.=$this->doc->divider(5);

				// Render content
			$this->moduleContent();

				// Shortcut
			if ($GLOBALS[\'BE_USER\']->mayMakeShortcut()) {
				$this->content.=$this->doc->spacer(20).$this->doc->section(\'\',$this->doc->makeShortcutIcon(\'id\',implode(\',\',array_keys($this->MOD_MENU)),$this->MCONF[\'name\']));
			}
		}
		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function printContent()	{
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function moduleContent()	{
		switch((string) $this->MOD_SETTINGS[\'function\'])	{
			case 1:
				$content = \'<div align=center><strong>Hello World!</strong></div><br />
					The "Kickstarter" has made this module automatically, it contains a default framework for a backend module but apart from that it does nothing useful until you open the script "\'.substr(t3lib_extMgm::extPath(\''.$extKey.'\'),strlen(PATH_site)).$pathSuffix.index.php.\'" and edit it!
					<hr />
					<br />This is the GET/POST vars sent to the script:<br />\'.
					\'GET:\'.t3lib_div::view_array($_GET).\'<br />\'.
					\'POST:\'.t3lib_div::view_array($_POST).\'<br />\'.
					\'\';
				$this->content.=$this->doc->section(\'Message #1:\',$content,0,1);
			break;
			case 2:
				$content=\'<div align=center><strong>Menu item #2...</strong></div>\';
				$this->content.=$this->doc->section(\'Message #2:\',$content,0,1);
			break;
			case 3:
				$content=\'<div align=center><strong>Menu item #3...</strong></div>\';
				$this->content.=$this->doc->section(\'Message #3:\',$content,0,1);
			break;
		}
	}
}
		');

		$this->addFileToFileArray(
			$pathSuffix.'index.php',
			$this->PHPclassFile(
				$extKey,
				$pathSuffix.'index.php',
				$indexContent,
				$extKey.' module '.$k_prefix.$k,
				$cN,
				'',
				$indexRequire
			)
		);
	}

	/**
	 * generates function to get locallang.xml
	 *
	 * @param	string		extension key
	 * @return	string		function to get locallang.xml
	 */
	function addLLfunc($extKey)	{
		return $this->sPS('
			/**
			 * Reads the [extDir]/locallang.xml and returns the \$LOCAL_LANG array found in that file.
			 *
			 * @return	[type]		...
			 */
			function includeLL()	{
				return $GLOBALS[\'LANG\']->includeLLFile(\'EXT:'.$extKey.'/locallang.xml\', false);
			}
		');
	}

	/**
	 * adds standard locallang configuration
	 *
	 * @param	array		locallang array
	 * @param	string		subprefix
	 * @param	boolean		$onlyMode: ...
	 * @return	array		locallang array with standard locallang configuration
	 */
	function addStdLocalLangConf($ll,$k,$onlyMode=0)	{
		$this->addLocalConf($ll,
			array(
				'list_mode_1'=>'Mode 1',
			),
			'list_mode_1','pi',$k,1,1
		);
		$this->addLocalConf($ll,
			array(
				'list_mode_2'=>'Mode 2',
			),
			'list_mode_2','pi',$k,1,1
		);
		$this->addLocalConf($ll,
			array(
				'list_mode_3'=>'Mode 3',
			),
			'list_mode_3','pi',$k,1,1
		);
		$this->addLocalConf($ll,
			array(
				'back'=>'Back',
			),
			'back','pi',$k,1,1
		);

		if (!$onlyMode)	{
			$this->addLocalConf($ll,
				array(
					'pi_list_browseresults_prev'=>'< Previous',
				),
				'pi_list_browseresults_prev','pi',$k,1,1
			);
			$this->addLocalConf($ll,
				array(
					'pi_list_browseresults_page'=>'Page',
				),
				'pi_list_browseresults_page','pi',$k,1,1
			);
			$this->addLocalConf($ll,
				array(
					'pi_list_browseresults_next'=>'Next >',
				),
				'pi_list_browseresults_next','pi',$k,1,1
			);
			$this->addLocalConf($ll,
				array(
					'pi_list_browseresults_displays'=>'Displaying results ###SPAN_BEGIN###%s to %s</span> out of ###SPAN_BEGIN###%s</span>',
				),
				'pi_list_browseresults_displays','pi',$k,1,1
			);

			$this->addLocalConf($ll,
				array(
					'pi_list_searchBox_search'=>'Search',
				),
				'pi_list_searchBox_search','pi',$k,1,1
			);
		}

		return $ll;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$lArray: ...
	 * @param	[type]		$confArray: ...
	 * @param	[type]		$key: ...
	 * @param	[type]		$prefix: ...
	 * @param	[type]		$subPrefix: ...
	 * @param	[type]		$dontPrefixKey: ...
	 * @param	[type]		$noWOP: ...
	 * @param	[type]		$overruleKey: ...
	 * @return	[type]		...
	 */
	function addLocalConf(&$lArray,$confArray,$key,$prefix,$subPrefix,$dontPrefixKey=0,$noWOP=0,$overruleKey='')	{
		reset($this->wizard->languages);

		$overruleKey = $overruleKey ? $overruleKey : ($dontPrefixKey?'':$prefix.$subPrefix.'_').$key;

		$lArray['default'][$overruleKey] = array($confArray[$key],(!$noWOP?'WOP:['.$prefix.']['.$subPrefix.']['.$key.']':''));
		while(list($k)=each($this->wizard->languages))	{
			$lArray[$k][$overruleKey] = array(trim($confArray[$key.'_'.$k]),(!$noWOP?'WOP:['.$prefix.']['.$subPrefix.']['.$key.'_'.$k.']':''));
		}
		return $lArray;
	}

	/**
	 * generates a php class file
	 *
	 * @param	string		extension key
	 * @param	string		file name
	 * @param	string		file content
	 * @param	string		class description
	 * @param	[type]		$SOBE_class: ...
	 * @param	[type]		$SOBE_extras: ...
	 * @param	string		require and include definitions
	 * @return	string		file content
	 */
	function PHPclassFile($extKey, $filename, $content, $descr, $SOBE_class='', $SOBE_extras='', $require='')	{
		$file = trim($this->sPS('
			<?php
			/***************************************************************
			 *  Copyright notice
			 *
			 *  (c) ' . date('Y') . ' ' . $this->userField('name') . ' <' . $this->userField('email') . '>
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

		'));

		$file .= "\n\n" . $require . "\n\n";

		$file .= trim($this->sPS('
			/**
			 * '.$descr.'
			 *
			 * @author	'.$this->userField('name').' <'.$this->userField('email').'>
			 * @package	TYPO3
			 * @subpackage	'. $this->returnName($extKey, 'class') .'
			 */',
			0
		));

		$file .= "\n".$content."\n\n\n";

		$file .= trim($this->sPS('

			if (defined(\'TYPO3_MODE\') && isset($GLOBALS[\'TYPO3_CONF_VARS\'][TYPO3_MODE][\'XCLASS\'][\'ext/' . $extKey . '/' . $filename . '\'])) {
				include_once($GLOBALS[\'TYPO3_CONF_VARS\'][TYPO3_MODE][\'XCLASS\'][\'ext/' . $extKey . '/' . $filename . '\']);
			}
			' . ($SOBE_class ? '



				// Make instance:
			/** @var $SOBE ' . $SOBE_class . ' */
			$SOBE = t3lib_div::makeInstance(\'' . $SOBE_class . '\');
			$SOBE->init();
			' . ($SOBE_extras['include'] ? '
				// Include files?
			foreach ($SOBE->include_once as $INC_FILE) {
				include_once($INC_FILE);
			}' : '') . '
			' . ($SOBE_extras['firstLevel'] ? '
			$SOBE->checkExtObj();	// Checking for first level external objects' : '') . '
			$SOBE->main();
			$SOBE->printContent();
			' : '') . '
			?>
		'));

		return $file;
	}

	/**
	 * appends, prepends or substitues meta data for a file in the file array
	 *
	 * @param	string		file name
	 * @param	string		file content
	 * @param	integer		mode: 1 append, -1 prepend, 0 (default) substitute
	 * content
	 * @return	void
	 */
	function addFileToFileArray($name, $content, $mode=0)	{
		switch($mode)	{
			case 1:		// Append
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$this->wizard->fileArray[$name]['content'].chr(10).$content);
			break;
			case -1:	// Prepend
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$content.chr(10).$this->wizard->fileArray[$name]['content']);
			break;
			default:	// Substitution:
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$content);
			break;
		}
	}

	/**
	 * generates EMCONF presets
	 *
	 * @param	string		prefix
	 * @return	array		EMCONF
	 */
	function makeEMCONFpreset($prefix = '')	{
		$this->wizard->_addArray = $this->wizard->wizArray['emconf'][1];
		$EM_CONF=array();
		$presetFields = explode(',','title,description,category,shy,dependencies,conflicts,priority,module,state,internal,uploadfolder,createDirs,modify_tables,clearCacheOnLoad,lockType,author,author_email,author_company,private,download_password,version');
		while(list(,$s)=each($presetFields))	{
			$EM_CONF[$prefix.$s]='';
		}


		$EM_CONF[$prefix.'uploadfolder'] = $this->wizard->EM_CONF_presets['uploadfolder']?1:0;
		$EM_CONF[$prefix.'clearCacheOnLoad'] = $this->wizard->EM_CONF_presets['clearCacheOnLoad']?1:0;

		if (is_array($this->wizard->EM_CONF_presets['createDirs']))	{
			$EM_CONF[$prefix.'createDirs'] = implode(',',array_unique($this->wizard->EM_CONF_presets['createDirs']));
		}

		if (is_array($this->wizard->EM_CONF_presets['dependencies']) || $this->wizard->wizArray['emconf'][1]['dependencies'])	{

			if(!is_array($this->wizard->EM_CONF_presets['dependencies'])) {
				// if dependencies haven't been preset
				// in section_fields or section_modulefunction
				$this->wizard->EM_CONF_presets['dependencies'] = array();
			}

			$aa = t3lib_div::trimExplode(
				',',
				strtolower($this->wizard->wizArray['emconf'][1]['dependencies']),
				1
			);
			$deps = array_unique(
					array_merge(
						$this->wizard->EM_CONF_presets['dependencies'],
						$aa
					)
				);
			$EM_CONF[$prefix.'dependencies'] = implode(',', $deps);
			foreach ($deps as $dep) {
				$EM_CONF[$prefix.'constraints']['depends'][$dep] = '';
			}
		}
		unset($this->wizard->_addArray['dependencies']);
		if (is_array($this->wizard->EM_CONF_presets['module']))	{
			$EM_CONF[$prefix.'module'] = implode(',',array_unique($this->wizard->EM_CONF_presets['module']));
		}
		if (is_array($this->wizard->EM_CONF_presets['modify_tables']))	{
			$EM_CONF[$prefix.'modify_tables'] = implode(',',array_unique($this->wizard->EM_CONF_presets['modify_tables']));
		}

		return $EM_CONF;
	}

	/**
	 * gets user data
	 *
	 * @param	string		key (name or email)
	 * @return	string		user data
	 */
	function userField($k)	{
		$v = '';
		if($k == 'name') {
			$v = ($this->wizard->wizArray['emconf'][1]['author'] != '') ? $this->wizard->wizArray['emconf'][1]['author'] : $GLOBALS['BE_USER']->user['realName'];
		} else if ($k == 'email') {
			$v = ($this->wizard->wizArray['emconf'][1]['author_email'] != '') ? $this->wizard->wizArray['emconf'][1]['author_email'] : $GLOBALS['BE_USER']->user['email'];
		}
		return $v;
	}

	/**
	 * Creates a simple flexform datastructure to provide some dummy content.
	 *
	 * @return	string		file content
	 */
	function createFlexForm()	{
		$file =trim($this->sPS('
		<T3DataStructure>
			<meta>
				<langDisable>1</langDisable>
			</meta>
			<ROOT>
				<type>array</type>
				<el>
					<xmlTitle>
						<TCEforms>
							<label>The Title:</label>
							<config>
								<type>input</type>
								<size>30</size>
							</config>
						</TCEforms>
					</xmlTitle>
					<xmlText>
						<TCEforms>
							<label>Bodytext:</label>
							<config>
								<type>text</type>
								<size>48</size>
							</config>
						</TCEforms>
					</xmlText>
				</el>
			</ROOT>
		</T3DataStructure>
		'));

		return $file;
	}

}


// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_sectionbase.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_sectionbase.php']);
}


?>
