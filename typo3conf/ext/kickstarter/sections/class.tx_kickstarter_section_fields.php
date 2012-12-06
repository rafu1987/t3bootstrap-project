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


require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');


/**
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Ingo Renner <ingo@typo3.org>
 */
class tx_kickstarter_section_fields extends tx_kickstarter_sectionbase {
  var $sectionID = 'fields';

	/**
	 * Renders the form in the kickstarter; this was add_cat_fields()
	 *
	 * @return	string		HTML code
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0]=='edit')	{
			$this->regNewEntry($this->sectionID,$action[1]);
			$lines = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],'&nbsp;',$action[1]);
			$piConf = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix='['.$this->sectionID.']['.$action[1].']';

		}


				// Header field
			$optValues = array(
				'tt_content' => 'tt_content (Content)',
				'fe_users' => 'fe_users (Frontend Users)',
				'fe_groups' => 'fe_groups (Frontend Groups)',
				'be_users' => 'be_users (Backend Users)',
				'be_groups' => 'be_groups (Backend Groups)',
				'pages' => 'pages (Pages)',
			);

			foreach($GLOBALS['TCA'] as $tablename => $tableTCA) {
				if(!$optValues[$tablename]) {
					$optValues[$tablename] = $tablename.' ('.$GLOBALS['LANG']->sL($tableTCA['ctrl']['title']).')';
				}
			}
			asort($optValues);

			$subContent = '<strong>Which table:<br /></strong>'.
					$this->renderSelectBox($ffPrefix.'[which_table]',$piConf['which_table'],$optValues).
					$this->whatIsThis('Select the table which should be extended with these extra fields.');
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).
				'<input type="hidden" name="' . $this->piFieldName('wizArray_upd') . $ffPrefix . '[title]" value="' . ($piConf['which_table']?$optValues[$piConf['which_table']]:'') . '" /></td></tr>';





				// PRESETS:
			$selPresetBox=$this->presetBox($piConf['fields']);

				// Fields
			$c=array(0);
			$this->usedNames=array();
			if (is_array($piConf['fields']))	{
				$piConf['fields'] = $this->cleanFieldsAndDoCommands($piConf['fields'],$this->sectionID,$action[1],$piConf['which_table']?$piConf['which_table']:'');

					// Do it for real...
				reset($piConf['fields']);
				while(list($k,$v)=each($piConf['fields']))	{
					$c[]=$k;
					$subContent=$this->renderField($ffPrefix.'[fields]['.$k.']',$v);
					$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw('<strong>FIELD:</strong> <em>'.$v['fieldname'].'</em>').'</td></tr>';
					$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
				}
			}


				// New field:
			$k=max($c)+1;
			$v=array();
			$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw('<strong>NEW FIELD:</strong>').'</td></tr>';
			$subContent=$this->renderField($ffPrefix.'[fields]['.$k.']',$v,1);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw('<br /><br />Load preset fields: <br />'.$selPresetBox).'</td></tr>';

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_fields'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_fields'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border="0" cellpadding="2" cellspacing="2">'.implode('',$lines).'</table>';
		return $content;
	}

	/**
	 * Creates and returns a dropdown box for selecting presets
	 *
	 * @param	array		$piConfFields: PlugIn Configuration fields (PASSED BY REFERENCE)
	 * @return	string		HTML code for select box
	 */
	function presetBox(&$piConfFields)	{
		$_PRESETS = $this->wizard->modData['_PRESET'];
		$optValues = array();

			// Session presets:
		$ses_optValues=array();
		$sesdat = $GLOBALS['BE_USER']->getSessionData('kickstarter');
		if (is_array($sesdat['presets']))	{
			foreach($sesdat['presets'] as $kk1=>$vv1)	{
				if (is_array($vv1))	{
					foreach($vv1 as $kk2=>$vv2)	{
						$ses_optValues[]='<option value="'.htmlspecialchars($kk1.'.'.$vv2['fieldname']).'">'.htmlspecialchars($kk1.': '.$vv2['title'].' ('.$vv2['fieldname'].', type: '.$vv2['type'].')').'</option>';
						if (is_array($_PRESETS) && in_array($kk1.'.'.$vv2['fieldname'],$_PRESETS))	{
							if (!is_array($piConfFields))	$piConfFields=array();
							$piConfFields[] = $vv2;
						}
					}
				}
			}
		}
		if (count($ses_optValues))	{
			$optValues = array_merge(
				$optValues,
				count($optValues) ?
					array('<option value=""></option>') :
					array(),
				array('<option value="">__Fields picked up in this session__:</option>'),
				$ses_optValues
			);
		}
		if (count($optValues)) {
			$version = class_exists('t3lib_utility_VersionNumber')
					? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
					: t3lib_div::int_from_ver(TYPO3_version);
			$size = ($version < 4006000) ? t3lib_div::intInRange(count($optValues), 1, 10) : t3lib_utility_Math::forceIntegerInRange(count($optValues), 1, 10);

			$selPresetBox = '<select name="' . $this->piFieldName('_PRESET') . '[]" size="' . $size . '" multiple="multiple">' . implode('', $optValues) . '</select>';
		}
		return $selPresetBox;
	}

	/**
	 * Cleans fields and do commands
	 *
	 * @param	array		$fConf: current field configuration
	 * @param	string		$catID: ID of current category
	 * @param	string		$action: the action that should be performed
	 * @param	string		$table: referring table
	 * @return	array		new fieldconfiguration
	 */
	function cleanFieldsAndDoCommands($fConf,$catID,$action,$table)	{
		$newFConf=array();
		$downFlag=0;
		foreach($fConf as $k=>$v)	{
			if ($v['type'] && trim($v['fieldname']))	{
				$v['fieldname'] = $this->cleanUpFieldName($v['fieldname'],$table);

				if (!$v['_DELETE'])	{
					$newFConf[$k]=$v;
					if (t3lib_div::_GP($this->varPrefix.'_CMD_'.$v['fieldname'].'_UP_x') || $downFlag)	{
						if (count($newFConf)>=2)	{
							$lastKeys = array_slice(array_keys($newFConf),-2);

							$buffer = array();
							$buffer[$lastKeys[1]] = $newFConf[$lastKeys[1]];
							$buffer[$lastKeys[0]] = $newFConf[$lastKeys[0]];

							unset($newFConf[$lastKeys[0]]);
							unset($newFConf[$lastKeys[1]]);

							$newFConf[$lastKeys[1]] = $buffer[$lastKeys[1]];
							$newFConf[$lastKeys[0]] = $buffer[$lastKeys[0]];
						}
						$downFlag=0;
					} elseif (t3lib_div::_GP($this->varPrefix.'_CMD_'.$v['fieldname'].'_DOWN_x'))	{
						$downFlag=1;
					}
				}
			}
		}

		$this->wizard->wizArray[$catID][$action]['fields'] = $newFConf;
		$sesdat = $GLOBALS['BE_USER']->getSessionData('kickstarter');
		$sesdat['presets'][$this->wizard->extKey.'-'.$catID.'-'.$action]=$newFConf;
		$GLOBALS['BE_USER']->setAndSaveSessionData('kickstarter',$sesdat);

		return $newFConf;
	}

	/**
	 * Adds a new table to the option values
	 *
	 * @param	array		$optValues: Option values
	 * @return	array		modified option values
	 */
	function addOtherExtensionTables($optValues)	{
		if (is_array($this->wizard->wizArray['tables']))	{
			foreach($this->wizard->wizArray['tables'] as $k=>$info)	{
				if (trim($info['tablename']))	{
					$tableName = $this->returnName($this->wizard->extKey,'tables',trim($info['tablename']));
					$optValues[$tableName]='Extension table: '.$info['title'].' ('.$tableName.')';
				}
			}
		}
		return $optValues;
	}

	/**
	 *
	 */
	function getFieldsFromTable($tableName) {
		$optionValues = array();

		if (is_array($this->wizard->wizArray['tables']))	{
			foreach($this->wizard->wizArray['tables'] as $k=>$info)	{
				$tempTableName = $this->returnName($this->wizard->extKey,'tables',trim($info['tablename']));

				if($tableName == $tempTableName) {
					foreach($info['fields'] as $field) {
						if(!empty($field['fieldname'])) {
							$optionValues[$field['fieldname']] = $tableName.'.'.$field['fieldname'].' ('.$field['title'].')';
						}
					}
				}
			}
		}

		return $optionValues;
	}

	/**
	 * Cleaning up fieldname from invalid characters (only alphanum is allowed)
	 * and chechking for reserved words
	 *
	 * @param	string		$str: orginal fieldname
	 * @param	string		$table: referring table
	 * @return	string		cleaned up fieldname
	 */
	function cleanUpFieldName($str, $table)	{
		$passthrough = 0;
		foreach($GLOBALS['TCA'] as $tablename => $cols){
			if ($tablename == $table) $passthrough = 1;
		}
		$fieldName = preg_replace('/[^[:alnum:]_]/', '', strtolower($str));
		if ((!$fieldName || in_array($fieldName, $this->wizard->reservedWords) || in_array($fieldName, $this->usedNames)) && $passthrough==0)	{
			$fieldName.=($fieldName?'_':'').t3lib_div::shortmd5(microtime());
		}
		$this->usedNames[]=$fieldName;
		return $fieldName;
	}

	/**
	 * Renders a single field
	 *
	 * @param	string		$prefix: The prefix for the fieldname
	 * @param	array		$fConf: field config
	 * @param	boolean		$dontRemove: if true the field can't be removed (option link is not rendered)
	 * @return	string		HTML code of the field
	 */
	function renderField($prefix,$fConf,$dontRemove=0)	{
		global $LANG;

		$version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);

		$onCP = $this->getOnChangeParts($prefix.'[fieldname]');
		$fieldName = $this->renderStringBox($prefix.'[fieldname]',$fConf['fieldname']).
			(!$dontRemove?' (Remove:'.$this->renderCheckBox($prefix.'[_DELETE]',0).')'.
				'<input type="image" hspace="2" src="'.$this->wizard->siteBackPath.TYPO3_mainDir.'gfx/pil2up.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_UP" onclick="'.$onCP[1].'" />'.
				'<input type="image" hspace="2" src="'.$this->wizard->siteBackPath.TYPO3_mainDir.'gfx/pil2down.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_DOWN" onclick="'.$onCP[1].'" />'.
				'<input type="image" hspace="2" src="'.$this->wizard->siteBackPath.TYPO3_mainDir.'gfx/savesnapshot.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_SAVE" onclick="'.$onCP[1].'" title="Save this field setting as a preset." />':'');

		$fieldTitle = ((string)$fConf['type'] != 'passthrough') ? $this->renderStringBox_lang('title',$prefix,$fConf) : '';
		$typeCfg = '';

			// Sorting
		$optValues = array(
			''                => '',
			'input'           => 'String input',
			'input+'          => 'String input, advanced',
			'textarea'        => 'Text area',
			'textarea_rte'    => 'Text area with RTE',
			'textarea_nowrap' => 'Text area, No wrapping',
			'check'           => 'Checkbox, single',
			'check_4'         => 'Checkbox, 4 boxes in a row',
			'check_10'        => 'Checkbox, 10 boxes in two rows (max)',
			'link'            => 'Link',
			'date'            => 'Date',
			'datetime'        => 'Date and time',
			'integer'         => 'Integer, 10-1000',
			'select'          => 'Selectorbox',
			'radio'           => 'Radio buttons',
			'rel'             => 'Database relation',
		//	'inline'          => 'Inline relation',
			'files'           => 'Files',
			'flex'            => 'Flex',
			'none'            => 'Not editable, only displayed',
			'passthrough'     => '[Passthrough]',
		);
		$typeCfg .= $this->renderSelectBox($prefix.'[type]',$fConf['type'],$optValues);
		$typeCfg .= $this->renderCheckBox($prefix.'[excludeField]', isset($fConf['excludeField']) ? $fConf['excludeField'] : 0);
		$typeCfg .= $LANG->getLL('fields.excludeField') . $this->whatIsThis($LANG->getLL('fields.excludeField_description')) . '<br />';

		$fDetails = '';
		switch((string)$fConf['type'])	{
			case 'inline':
				$fDetails .= $this->renderInlineField($prefix,$fConf);
				break;
			case 'input+':
				$typeCfg .= $this->resImg('t_input.png','','');

				$fDetails .= $this->renderStringBox($prefix.'[conf_size]',$fConf['conf_size'],50).' Field width (5-48 relative, 30 default)<br />';
				$fDetails .= $this->renderStringBox($prefix.'[conf_max]',$fConf['conf_max'],50).' Max characters<br />';
				$fDetails .= $this->renderCheckBox($prefix.'[conf_required]',$fConf['conf_required']).'Required<br />';
				$fDetails .= $this->resImg('t_input_required.png','hspace=20','','<br /><br />');

				$fDetails .= $this->renderCheckBox($prefix.'[conf_varchar]',$fConf['conf_varchar']).'Create VARCHAR, not TINYTEXT field (if not forced INT)<br />';

				$fDetails .= $this->renderCheckBox($prefix.'[conf_check]',$fConf['conf_check']).'Apply checkbox<br />';
				$fDetails .= $this->resImg('t_input_check.png','hspace=20','','<br /><br />');

				$optValues = array(
					''         => '',
					'date'     => 'Date (day-month-year)',
					'time'     => 'Time (hours, minutes)',
					'timesec'  => 'Time + seconds',
					'datetime' => 'Date + Time',
					'year'     => 'Year',
					'int'      => 'Integer',
					'int+'     => 'Integer 0-1000',
					'double2'  => 'Floating point, x.xx',
					'alphanum' => 'Alphanumeric only',
					'upper'    => 'Upper case',
					'lower'    => 'Lower case',
				);
				$fDetails.='<br />Evaluate value to:<br />'.$this->renderSelectBox($prefix.'[conf_eval]',$fConf['conf_eval'],$optValues).'<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_stripspace]',$fConf['conf_stripspace']).'Strip space<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_pass]',$fConf['conf_pass']).'Is password field<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_md5]',$fConf['conf_md5']).'Save as MD5<br />';
				$fDetails.=$this->resImg('t_input_password.png','hspace=20','','<br /><br />');

				$fDetails.='<br />';
				$fDetails.=$this->renderRadioBox($prefix.'[conf_unique]',$fConf['conf_unique'],'G').'Unique in whole database<br />';
				$fDetails.=$this->renderRadioBox($prefix.'[conf_unique]',$fConf['conf_unique'],'L').'Unique inside parent page<br />';
				$fDetails.=$this->renderRadioBox($prefix.'[conf_unique]',$fConf['conf_unique'],'').'Not unique (default)<br />';
				$fDetails.='<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_color]',$fConf['conf_wiz_color']).'Add colorpicker wizard<br />';
				$fDetails.=$this->resImg('t_input_colorwiz.png','hspace=20','','<br /><br />');
				$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_link]',$fConf['conf_wiz_link']).'Add link wizard<br />';
				$fDetails.=$this->resImg('t_input_link2.png','hspace=20','','<br /><br />');
				break;
			case 'input':
				$typeCfg.=$this->resImg('t_input.png','','');

				$fDetails.=$this->renderStringBox($prefix.'[conf_size]',$fConf['conf_size'],50).' Field width (5-48 relative, 30 default)<br />';
				$fDetails.=$this->renderStringBox($prefix.'[conf_max]',$fConf['conf_max'],50).' Max characters<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_required]',$fConf['conf_required']).'Required<br />';
				$fDetails.=$this->resImg('t_input_required.png','hspace=20','','<br /><br />');

				$fDetails.=$this->renderCheckBox($prefix.'[conf_varchar]',$fConf['conf_varchar']).'Create VARCHAR, not TINYTEXT field<br />';
				break;
			case 'textarea':
			case 'textarea_nowrap':
				$typeCfg.=$this->resImg('t_textarea.png','','');

				$fDetails.=$this->renderStringBox($prefix.'[conf_cols]',$fConf['conf_cols'],50).' Textarea width (5-48 relative, 30 default)<br />';
				$fDetails.=$this->renderStringBox($prefix.'[conf_rows]',$fConf['conf_rows'],50).' Number of rows (height)<br />';
				$fDetails.='<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_example]',$fConf['conf_wiz_example']).'Add wizard example<br />';
				$fDetails.=$this->resImg('t_textarea_wiz.png','hspace=20','','<br /><br />');
				break;
			case 'textarea_rte':
				$typeCfg.=$this->resImg($fConf['conf_rte']!='tt_content'?'t_rte.png':'t_rte2.png','','');

				$optValues = array(
					'tt_content' => 'Transform content like the Content Element "Bodytext" field (default/old)',
					'basic' => 'Typical basic setup (new "Bodytext" field based on CSS stylesheets)',
					'moderate' => 'Moderate transform of images and links',
					'none' => 'No transformation at all',
					'custom' => 'Custom'
				);
				$fDetails.='<br />Rich Text Editor Mode:<br />'.$this->renderSelectBox($prefix.'[conf_rte]',$fConf['conf_rte'],$optValues).'<br />';
				if ((string)$fConf['conf_rte']=='custom')	{
					$optValues = array(
						'cut' => array('Cut button'),
						'copy' => array('Copy button'),
						'paste' => array('Paste button'),
						'formatblock' => array('Paragraph formatting','<DIV>, <P>'),
						'class' => array('Character formatting','<SPAN>)'),
						'fontstyle' => array('Font face','<FONT face=>)'),
						'fontsize' => array('Font size','<FONT size=>)'),
						'textcolor' => array('Font color','<FONT color=>'),
						'bold' => array('Bold','<STRONG>, <B>'),
						'italic' => array('italic','<EM>, <I>'),
						'underline' => array('Underline','<U>'),
						'left' => array('Left align','<DIV>, <P>'),
						'center' => array('Center align','<DIV>, <P>'),
						'right' => array('Right align','<DIV>, <P>'),
						'orderedlist' => array('Ordered bulletlist','<OL>, <LI>'),
						'unorderedlist' => array('Unordered bulletlist','<UL>, <LI>'),
						'outdent' => array('Outdent block','<BLOCKQUOTE>'),
						'indent' => array('Indent block','<BLOCKQUOTE>'),
						'link' => array('Link','<A>'),
						'table' => array('Table','<TABLE>, <TR>, <TD>'),
						'image' => array('Image','<IMG>'),
						'line' => array('Ruler','<HR>'),
						'user' => array('User defined',''),
						'chMode' => array('Edit source?','')
					);
					$subLines=array();
					$subLines[]='<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><strong>'.$this->fw('Button name:').'</strong></td>
						<td><strong>'.$this->fw('Tags allowed:').'</strong></td>
					</tr>';
					foreach($optValues as $kk=>$vv)	{
						$subLines[]='<tr>
							<td>'.$this->renderCheckBox($prefix.'[conf_rte_b_'.$kk.']',$fConf['conf_rte_b_'.$kk]).'</td>
							<td>'.$this->resIcon($kk.'.png').'</td>
							<td>'.$this->fw($vv[0]).'</td>
							<td>'.$this->fw(htmlspecialchars($vv[1])).'</td>
						</tr>';
					}
					$fDetails.='<table border="0" cellpadding="2" cellspacing="2">'.implode('',$subLines).'</table><br />';

					$fDetails.='<br /><strong>Define specific colors:</strong><br />
						<em>Notice: Use only HEX-values for colors ("blue" should be #0000ff etc.)</em><br />';
					for($a=1;$a<4;$a++)	{
						$fDetails.='Color #'.$a.': '.$this->renderStringBox($prefix.'[conf_rte_color'.$a.']',$fConf['conf_rte_color'.$a],70).'<br />';
					}
					$fDetails.=$this->resImg('t_rte_color.png','','','<br /><br />');

					$fDetails.=$this->renderCheckBox($prefix.'[conf_rte_removecolorpicker]',$fConf['conf_rte_removecolorpicker']).'Hide colorpicker<br />';
					$fDetails.=$this->resImg('t_rte_colorpicker.png','hspace=20','','<br /><br />');

					$fDetails.='<br /><strong>Define classes:</strong><br />';
					for($a=1;$a<7;$a++)	{
						$fDetails.='Class Title:'.$this->renderStringBox($prefix.'[conf_rte_class'.$a.']',$fConf['conf_rte_class'.$a],100).
						  '<br />CSS Style: {'.$this->renderStringBox($prefix.'[conf_rte_class'.$a.'_style]',$fConf['conf_rte_class'.$a.'_style'],250).'}'.
						  '<br />';
					}
					$fDetails.=$this->resImg('t_rte_class.png','','','<br /><br />');

					$optValues = array(
						'0' => '',
						'1' => 'Hide Hx and PRE from Paragraph selector.',
						'H2H3' => 'Hide all, but H2,H3,P,PRE',
					);
					$fDetails.='<br />Hide Paragraph Items:<br />'.$this->renderSelectBox($prefix.'[conf_rte_removePdefaults]',$fConf['conf_rte_removePdefaults'],$optValues).'<br />';
					$fDetails.=$this->resImg('t_rte_hideHx.png','hspace=20','','<br /><br />');

					$fDetails.='<br /><strong>Misc:</strong><br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_rte_div_to_p]',isset($fConf['conf_rte_div_to_p'])?$fConf['conf_rte_div_to_p']:1).htmlspecialchars('Convert all <DIV> to <P>').'<br />';
				}

				$fDetails.='<br />';
				$fDetails.=$this->renderCheckBox($prefix.'[conf_rte_fullscreen]',isset($fConf['conf_rte_fullscreen'])?$fConf['conf_rte_fullscreen']:1).'Fullscreen link<br />';
				$fDetails.=$this->resImg('t_rte_fullscreen.png','hspace=20','','<br /><br />');

				if (t3lib_div::inList('moderate,basic,custom',$fConf['conf_rte']))	{
					$fDetails.='<br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_rte_separateStorageForImages]',isset($fConf['conf_rte_separateStorageForImages'])?$fConf['conf_rte_separateStorageForImages']:1).'Storage of images in separate folder (in uploads/[extfolder]/rte/)<br />';
				}
				if (t3lib_div::inList('moderate,custom',$fConf['conf_rte']))	{
					$fDetails.='<br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_mode_cssOrNot]',isset($fConf['conf_mode_cssOrNot'])?$fConf['conf_mode_cssOrNot']:1) . 'Use "ts_css" transformation instead of "ts_images-ts-reglinks"<br />';
				}
				break;
			case 'check':
				$typeCfg.=$this->resImg('t_input_link.png','','');
				$fDetails.=$this->renderCheckBox($prefix.'[conf_check_default]',$fConf['conf_check_default']).'Checked by default<br />';
				break;
			case 'select':
			case 'radio':
				if ($fConf['type']=='radio')	{
					$typeCfg.=$this->resImg('t_radio.png','','');
				} else	{
					$typeCfg.=$this->resImg('t_sel.png','','');
				}
				$fDetails.='<br /><strong>Define values:</strong><br />';
				$subLines=array();
					$subLines[]='<tr>
						<td valign="top">'.$this->fw('Item label:').'</td>
						<td valign="top">'.$this->fw('Item value:').'</td>
					</tr>';
				if ($version < 4006000) {
					$nItems = $fConf['conf_select_items'] = isset($fConf['conf_select_items']) ? t3lib_div::intInRange(intval($fConf['conf_select_items']), 0, 20) : 4;
				} else {
					$nItems = $fConf['conf_select_items'] = isset($fConf['conf_select_items']) ? t3lib_utility_Math::forceIntegerInRange(intval($fConf['conf_select_items']), 0, 20) : 4;
				}

				for($a=0;$a<$nItems;$a++)	{
					$subLines[]='<tr>
						<td valign="top">'.$this->fw($this->renderStringBox_lang('conf_select_item_'.$a,$prefix,$fConf)).'</td>
						<td valign="top">'.$this->fw($this->renderStringBox($prefix.'[conf_select_itemvalue_'.$a.']',isset($fConf['conf_select_itemvalue_'.$a])?$fConf['conf_select_itemvalue_'.$a]:$a,50)).'</td>
					</tr>';
				}
				$fDetails.='<table border="0" cellpadding="2" cellspacing="2">'.implode('',$subLines).'</table><br />';
				$fDetails.=$this->renderStringBox($prefix.'[conf_select_items]',$fConf['conf_select_items'],50).' Number of values<br />';

				if ($fConf['type']=='select')	{
					$fDetails.=$this->renderCheckBox($prefix.'[conf_select_icons]',$fConf['conf_select_icons']).'Add a dummy set of icons<br />';
					$fDetails.=$this->resImg('t_select_icons.png','hspace="20"','','<br /><br />');

					if ($version < 4006000) {
						$fDetails .= $this->renderStringBox($prefix . '[conf_relations]', t3lib_div::intInRange($fConf['conf_relations'], 1, 1000), 50) . ' Max number of relations<br />';
						$fDetails .= $this->renderStringBox($prefix . '[conf_relations_selsize]', t3lib_div::intInRange($fConf['conf_relations_selsize'], 1, 50), 50) . ' Size of selector box<br />';
					} else {
						$fDetails .= $this->renderStringBox($prefix . '[conf_relations]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 1000), 50) . ' Max number of relations<br />';
						$fDetails .= $this->renderStringBox($prefix . '[conf_relations_selsize]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations_selsize'], 1, 50), 50) . ' Size of selector box<br />';
					}

					$fDetails.=$this->renderCheckBox($prefix.'[conf_select_pro]',$fConf['conf_select_pro']).'Add pre-processing with PHP-function<br />';
				}
				break;
			case 'rel':
				if ($fConf['conf_rel_type']=='group' || !$fConf['conf_rel_type'])	{
					$typeCfg.=$this->resImg('t_rel_group.png','','');
				} elseif(intval($fConf['conf_relations'])>1)	{
					$typeCfg.=$this->resImg('t_rel_selmulti.png','','');
				} elseif(intval($fConf['conf_relations_selsize'])>1)	{
					$typeCfg.=$this->resImg('t_rel_selx.png','','');
				} else {
					$typeCfg.=$this->resImg('t_rel_sel1.png','','');
				}


				$optValues = array(
					'pages'      => 'Pages table, (pages)',
					'fe_users'   => 'Frontend Users, (fe_users)',
					'fe_groups'  => 'Frontend Usergroups, (fe_groups)',
					'tt_content' => 'Content elements, (tt_content)',
					'_CUSTOM'    => 'Custom table (enter name below)',
					'_ALL'       => 'All tables allowed!',
				);
				if ($fConf['conf_rel_type']!='group')	{unset($optValues['_ALL']);}
				$optValues = $this->addOtherExtensionTables($optValues);
				$fDetails.='<br />Create relation to table:<br />'.$this->renderSelectBox($prefix.'[conf_rel_table]',$fConf['conf_rel_table'],$optValues).'<br />';
				if ($fConf['conf_rel_table']=='_CUSTOM')	$fDetails.='Custom table name: '.$this->renderStringBox($prefix.'[conf_custom_table_name]',$fConf['conf_custom_table_name'],200).'<br />';

				$optValues = array(
					'group'          => 'Field with Element Browser',
					'select'         => 'Selectorbox, select global',
					'select_cur'     => 'Selectorbox, select from current page',
					'select_root'    => 'Selectorbox, select from root page',
					'select_storage' => 'Selectorbox, select from storage page',
				);
				$fDetails.='<br />Type:<br />'.$this->renderSelectBox($prefix.'[conf_rel_type]',$fConf['conf_rel_type']?$fConf['conf_rel_type']:'group',$optValues).'<br />';

				$confRelationsInRange = ($version < 4006000) ? t3lib_div::intInRange($fConf['conf_relations'], 1, 1000) : t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 1000);
				if ($confRelationsInRange == 1 && $fConf['conf_rel_type'] !== 'group') {
					$fDetails.=$this->renderCheckBox($prefix.'[conf_rel_dummyitem]',$fConf['conf_rel_dummyitem']).'Add a blank item to the selector<br />';
				}

				if ($version < 4006000) {
					$fDetails .= $this->renderStringBox($prefix . '[conf_relations]', t3lib_div::intInRange($fConf['conf_relations'], 1, 1000), 50) . ' Max number of relations<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_relations_selsize]', t3lib_div::intInRange($fConf['conf_relations_selsize'], 1, 50), 50) . ' Size of selector box<br />';
				} else {
					$fDetails .= $this->renderStringBox($prefix . '[conf_relations]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 1000), 50) . ' Max number of relations<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_relations_selsize]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations_selsize'], 1, 50), 50) . ' Size of selector box<br />';
				}
				$fDetails.=$this->renderCheckBox($prefix.'[conf_relations_mm]',$fConf['conf_relations_mm']).'True M-M relations (otherwise commalist of values)<br />';


				if ($fConf['conf_rel_type']!='group')	{
					$fDetails.='<br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_addrec]',$fConf['conf_wiz_addrec']).'Add "Add record" link<br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_listrec]',$fConf['conf_wiz_listrec']).'Add "List records" link<br />';
					$fDetails.=$this->renderCheckBox($prefix.'[conf_wiz_editrec]',$fConf['conf_wiz_editrec']).'Add "Edit record" link<br />';
					$fDetails.=$this->resImg("t_rel_wizards.png",'hspace="20"','','<br /><br />');
				}
				break;
			case 'files':
				if ($fConf['conf_files_type']=='images')	{
					$typeCfg.=$this->resImg('t_file_img.png','','');
				} elseif ($fConf['conf_files_type']=='webimages')	{
					$typeCfg.=$this->resImg('t_file_web.png','','');
				} else {
					$typeCfg.=$this->resImg('t_file_all.png','','');
				}

				$optValues = array(
					'images'    => 'Imagefiles',
					'webimages' => 'Web-imagefiles (gif,jpg,png)',
					'all'       => 'All files, except php/php3 extensions',
				);
				$fDetails.='<br />Extensions:<br />'.$this->renderSelectBox($prefix.'[conf_files_type]',$fConf['conf_files_type'],$optValues).'<br />';

				if ($version < 4006000) {
					$fDetails .= $this->renderStringBox($prefix . '[conf_files]', t3lib_div::intInRange($fConf['conf_files'], 1, 1000), 50) . ' Max number of files<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_max_filesize]', t3lib_div::intInRange($fConf['conf_max_filesize'], 1, 1000, 500), 50) . ' Max filesize allowed (kb)<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_files_selsize]', t3lib_div::intInRange($fConf['conf_files_selsize'], 1, 50), 50) . ' Size of selector box<br />';
				} else {
					$fDetails .= $this->renderStringBox($prefix . '[conf_files]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_files'], 1, 1000), 50) . ' Max number of files<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_max_filesize]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_max_filesize'], 1, 1000, 500), 50) . ' Max filesize allowed (kb)<br />';
					$fDetails .= $this->renderStringBox($prefix . '[conf_files_selsize]', t3lib_utility_Math::forceIntegerInRange($fConf['conf_files_selsize'], 1, 50), 50) . ' Size of selector box<br />';
				}
				$fDetails.=$this->resImg('t_file_size.png','','','<br /><br />');
				$fDetails.=$this->renderCheckBox($prefix.'[conf_files_thumbs]',$fConf['conf_files_thumbs']).'Show thumbnails<br />';
				$fDetails.=$this->resImg('t_file_thumb.png','hspace="20"','','<br /><br />');
				break;
			case 'integer':
				$typeCfg.=$this->resImg('t_integer.png','','');
				break;
			case 'check_4':
			case 'check_10':
				if ((string)$fConf['type']=='check_4')	{
					$typeCfg.=$this->resImg('t_check4.png','','');
				} else {
					$typeCfg.=$this->resImg('t_check10.png','','');
				}
				if ($version < 4006000) {
					$nItems = t3lib_div::intInRange($fConf['conf_numberBoxes'], 1, 10, (string)$fConf['type'] === 'check_4' ? 4 : 10);
				} else {
					$nItems = t3lib_utility_Math::forceIntegerInRange($fConf['conf_numberBoxes'], 1, 10, (string)$fConf['type'] === 'check_4' ? 4 : 10);
				}

				$fDetails.=$this->renderStringBox($prefix.'[conf_numberBoxes]',$nItems,50).' Number of checkboxes<br />';

				for($a=0;$a<$nItems;$a++)	{
					$fDetails.='<br />Label '.($a+1).':<br />'.$this->renderStringBox_lang('conf_boxLabel_'.$a,$prefix,$fConf);
				}
				break;
			case 'date':
				$typeCfg.=$this->resImg('t_date.png','','');
				break;
			case 'datetime':
				$typeCfg.=$this->resImg('t_datetime.png','','');
				break;
			case 'link':
				$typeCfg.=$this->resImg('t_link.png','','');
				break;
			case 'flex':
				if($this->wizard->wizArray['save']['extension_key']) {
					$extKey = $this->wizard->wizArray['save']['extension_key'];
					$fDetails.='<strong>FILE:EXT:'.$extKey.'/flexform_'.$fConf['fieldname'].'.xml</strong> will be created.';
				}
				break;
		}

		if($fConf['type']) {
			$typeCfg .= $this->textSetup('',$fDetails);
		}

		$content = '<table border="0" cellpadding="0" cellspacing="0">
			<tr><td valign="top">'.$this->fw('Field name:').'</td><td valign="top">'.$this->fw($fieldName).'</td></tr>
			<tr><td valign="top">'.$this->fw('Field title:').'</td><td valign="top">'.$this->fw($fieldTitle).'</td></tr>
			<tr><td valign="top">'.$this->fw('Field type:').'</td><td valign="top">'.$this->fw($typeCfg).'</td></tr>
		</table>';
		return $content;
	}




	/**
	 * Renders the extension PHP code
	 *
	 * @param	string		$k: fieldname (key)
	 * @param	array		$config: pi config
	 * @param	string		$extKey: extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP = '[fields]['.$k.']';
		$tableName = $config['which_table'];
		$prefix = $this->returnName($extKey,'fields').'_';

		$DBfields = array();
		$columns  = array();
		$ctrl     = array();
		$enFields = array();

		if (is_array($config['fields']))	{
			reset($config['fields']);
			while(list($i,$fConf)=each($config['fields']))	{
				$fConf['fieldname'] = $prefix.$fConf['fieldname'];
				$this->makeFieldTCA($DBfields,$columns,$fConf,$WOP.'[fields]['.$i.']',$tableName,$extKey);
			}
		}

		if (t3lib_div::inList('tt_content,fe_users,fe_groups',$tableName)) {
			$this->wizard->EM_CONF_presets['dependencies'][] = 'cms';
		}

		$createTable = $this->wrapBody('
			#
			# Table structure for table \''.$tableName.'\'
			#
			CREATE TABLE '.$tableName.' (
		', preg_replace('/,[[:space:]]*$/','',implode(chr(10),$DBfields)), '

			);
		');
		$this->wizard->ext_tables_sql[]=chr(10).$createTable.chr(10);


			// Finalize ext_tables.php:
		$this->wizard->ext_tables[] = $this->wrapBody('
			$tempColumns = array(
				', implode(chr(10), $columns), '
			);
		');


		list($typeList) = $this->implodeColumns($columns);
		$applyToAll=1;
		if (is_array($this->wizard->wizArray['pi']))	{
			reset($this->wizard->wizArray['pi']);
			while(list(,$fC)=each($this->wizard->wizArray['pi']))	{
				if ($fC['apply_extended']==$k)	{
					$applyToAll=0;
					$this->wizard->_apply_extended_types[$k]=$typeList;
				}
			}
		}
		$this->wizard->ext_tables[]=$this->sPS('
			t3lib_div::loadTCA(\''.$tableName.'\');
			t3lib_extMgm::addTCAcolumns(\''.$tableName.'\',$tempColumns,1);
			'.($applyToAll?'t3lib_extMgm::addToAllTCAtypes(\''.$tableName.'\',\''.$typeList.'\');':'').'
		');
	}

	/**
	 * Implode fields into a string for types -> showItem
	 *
	 * @param	array		$columns: array with fields
	 * @return	string		string with imploded fields
	 */
	function implodeColumns($columns)	{
		reset($columns);
		$outems=array();
		$paltems=array();
		$c=0;
		$hiddenFlag=0;
		$titleDivFlag=0;
		while(list($fN)=each($columns))	{
			if (!$hiddenFlag || !t3lib_div::inList('starttime,endtime,fe_group',$fN))	{
				if($fN == 't3ver_label') {
					continue;
				}
				$outTem = array($fN,'','','','');
				$outTem[3] = $this->wizard->_typeP[$fN];
				if ($c==0)	$outTem[4]='1-1-1';
				if ($fN=='title')	{
					$outTem[4]='2-2-2';
					$titleDivFlag=1;
				} elseif ($titleDivFlag)	{
					$outTem[4]='3-3-3';
					$titleDivFlag=0;
				}
				if ($fN=='hidden')	{
					$outTem[2]='1';
					$hiddenFlag=1;
				}
				$outems[] = str_replace(',','',str_replace(chr(9),';',trim(str_replace(';','',implode(chr(9),$outTem)))));
				$c++;
			} else {
				$paltems[]=$fN;
			}
		}
		return array(implode(', ',$outems), implode(', ',$paltems));
	}

	/**
	 * Creates the TCA for fields
	 *
	 * @param	array		&$DBfields: array of fields (PASSED BY REFERENCE)
	 * @param	array		$columns: $array of fields (PASSED BY REFERENCE)
	 * @param	array		$fConf: field config
	 * @param	string		$WOP: ???
	 * @param	string		$table: tablename
	 * @param	string		$extKey: extensionkey
	 * @return	void
	 */
	function makeFieldTCA(&$DBfields,&$columns,$fConf,$WOP,$table,$extKey)	{
		if (!(string)$fConf['type'])	return;
		$id = $table.'_'.$fConf['fieldname'];

		$version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);

		$configL=array();
		$t = (string)$fConf['type'];
		switch($t)	{
			case 'input':
			case 'input+':
				$isString = true;
				$configL[]='\'type\' => \'input\',	' . $this->WOPcomment('WOP:'.$WOP.'[type]');
				if ($version < 4006000) {
					$configL[] = '\'size\' => \'' . t3lib_div::intInRange($fConf['conf_size'], 5, 48, 30) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_size]');
					if (intval($fConf['conf_max']))	{
						$configL[] = '\'max\' => \'' . t3lib_div::intInRange($fConf['conf_max'], 1, 255) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_max]');
					}
				} else {
					$configL[] = '\'size\' => \'' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_size'], 5, 48, 30) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_size]');
					if (intval($fConf['conf_max']))	{
						$configL[] = '\'max\' => \'' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_max'], 1, 255) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_max]');
					}
				}

				$evalItems=array();
				if ($fConf['conf_required'])	{$evalItems[0][] = 'required';			$evalItems[1][] = $WOP.'[conf_required]';}

				if ($t=='input+')	{
					$isString = (bool) !$fConf['conf_eval'] || t3lib_div::inList('alphanum,upper,lower',$fConf['conf_eval']);
					$isDouble2 = (bool) !$fConf['conf_eval'] || t3lib_div::inList('double2',$fConf['conf_eval']);
					if ($fConf['conf_varchar'] && $isString)		{$evalItems[0][] = 'trim';			$evalItems[1][] = $WOP.'[conf_varchar]';}
					if ($fConf['conf_eval'] === 'int+') {
						$configL[] = '\'range\' => array(\'lower\' => 0, \'upper\' => 1000),	'.$this->WOPcomment('WOP:'.$WOP.'[conf_eval] = int+ results in a range setting');
						$fConf['conf_eval']='int';
					}
					if ($fConf['conf_eval'])		{$evalItems[0][] = $fConf['conf_eval'];			$evalItems[1][] = $WOP.'[conf_eval]';}
					if ($fConf['conf_check'])	$configL[]='\'checkbox\' => \''.($isString?'':'0').'\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_check]');

					if ($fConf['conf_stripspace'])		{$evalItems[0][] = 'nospace';			$evalItems[1][] = $WOP.'[conf_stripspace]';}
					if ($fConf['conf_pass'])		{$evalItems[0][] = 'password';			$evalItems[1][] = $WOP.'[conf_pass]';}
					if ($fConf['conf_md5'])		{$evalItems[0][] = 'md5';			$evalItems[1][] = $WOP.'[conf_md5]';}
 					if ($fConf['conf_unique'])	{
						if ($fConf['conf_unique']=='L')		{$evalItems[0][] = 'uniqueInPid';			$evalItems[1][] = $WOP.'[conf_unique] = Local (unique in this page (PID))';}
						if ($fConf['conf_unique']=='G')		{$evalItems[0][] = 'unique';			$evalItems[1][] = $WOP.'[conf_unique] = Global (unique in whole database)';}
					}

					$wizards =array();
					if ($fConf['conf_wiz_color'])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_color]').'
							\'color\' => array(
								\'title\' => \'Color:\',
								\'type\' => \'colorbox\',
								\'dim\' => \'12x12\',
								\'tableStyle\' => \'border:solid 1px black;\',
								\'script\' => \'wizard_colorpicker.php\',
								\'JSopenParams\' => \'height=300,width=250,status=0,menubar=0,scrollbars=1\',
							),
						'));
					}
					if ($fConf['conf_wiz_link'])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_link]').'
							\'link\' => array(
								\'type\' => \'popup\',
								\'title\' => \'Link\',
								\'icon\' => \'link_popup.gif\',
								\'script\' => \'browse_links.php?mode=wizard\',
								\'JSopenParams\' => \'height=300,width=500,status=0,menubar=0,scrollbars=1\'
							),
						'));
					}
					if (count($wizards))	{
						$configL[]=trim($this->wrapBody('
							\'wizards\' => array(
								\'_PADDING\' => 2,
								',implode(chr(10),$wizards),'
							),
						'));
					}
				} else {
					if ($fConf['conf_varchar'])		{$evalItems[0][] = 'trim';			$evalItems[1][] = $WOP.'[conf_varchar]';}
				}

				if (count($evalItems))	$configL[]='\'eval\' => \''.implode(",",$evalItems[0]).'\',	'.$this->WOPcomment('WOP:'.implode(' / ',$evalItems[1]));

				if (!$isString && !$isDouble2)	{
					$DBfields[] = $fConf['fieldname'] . ' int(11) DEFAULT \'0\' NOT NULL,';
				} elseif (!$isString && $isDouble2) {
					$DBfields[] = $fConf["fieldname"]." double(11,2) DEFAULT '0.00' NOT NULL,";
				} elseif (!$fConf['conf_varchar'])		{
					$DBfields[] = $fConf['fieldname'] . ' tinytext,';
				} else {
					if ($version < 4006000) {
						$varCharLn = (intval($fConf['conf_max']) ? t3lib_div::intInRange($fConf['conf_max'], 1, 255) : 255);
					} else {
						$varCharLn = (intval($fConf['conf_max']) ? t3lib_utility_Math::forceIntegerInRange($fConf['conf_max'], 1, 255) : 255);
					}
					$DBfields[] = $fConf['fieldname'] . ' ' . ($varCharLn>$this->wizard->charMaxLng?'var':'') . 'char(' . $varCharLn .') DEFAULT \'\' NOT NULL,';
				}
			break;
			case 'link':
				$DBfields[] = $fConf['fieldname'].' tinytext,';
				$configL[]  = trim($this->sPS('
					\'type\'     => \'input\',
					\'size\'     => \'15\',
					\'max\'      => \'255\',
					\'checkbox\' => \'\',
					\'eval\'     => \'trim\',
					\'wizards\'  => array(
						\'_PADDING\' => 2,
						\'link\'     => array(
							\'type\'         => \'popup\',
							\'title\'        => \'Link\',
							\'icon\'         => \'link_popup.gif\',
							\'script\'       => \'browse_links.php?mode=wizard\',
							\'JSopenParams\' => \'height=300,width=500,status=0,menubar=0,scrollbars=1\'
						)
					)
				'));
			break;
			case 'datetime':
			case 'date':
				$DBfields[] = $fConf['fieldname'].' int(11) DEFAULT \'0\' NOT NULL,';
				$configL[] = trim($this->sPS('
					\'type\'     => \'input\',
					\'size\'     => \''.($t=="datetime"?12:8).'\',
					\'max\'      => \'20\',
					\'eval\'     => \''.$t.'\',
					\'checkbox\' => \'0\',
					\'default\'  => \'0\'
				'));
			break;
			case 'integer':
				$DBfields[] = $fConf['fieldname'] . ' int(11) DEFAULT \'0\' NOT NULL,';
				$configL[]=trim($this->sPS('
					\'type\'     => \'input\',
					\'size\'     => \'4\',
					\'max\'      => \'4\',
					\'eval\'     => \'int\',
					\'checkbox\' => \'0\',
					\'range\'    => array(
						\'upper\' => \'1000\',
						\'lower\' => \'10\'
					),
					\'default\' => 0
				'));
			break;
			case 'textarea':
			case 'textarea_nowrap':
				$DBfields[] = $fConf['fieldname'].' text,';
				$configL[]='\'type\' => \'text\',';
				if ($t=='textarea_nowrap')	{
					$configL[]='\'wrap\' => \'OFF\',';
				}
				if ($version < 4006000) {
					$configL[] = '\'cols\' => \'' . t3lib_div::intInRange($fConf['conf_cols'], 5, 48, 30) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_cols]');
					$configL[] = '\'rows\' => \'' . t3lib_div::intInRange($fConf['conf_rows'], 1, 20, 5) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_rows]');
				} else {
					$configL[] = '\'cols\' => \'' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_cols'], 5, 48, 30) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_cols]');
					$configL[] = '\'rows\' => \'' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_rows'], 1, 20, 5) . '\',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_rows]');
				}
				if ($fConf["conf_wiz_example"])	{
					$wizards =array();
					$wizards[] = trim($this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_example]').'
						\'example\' => array(
							\'title\'         => \'Example Wizard:\',
							\'type\'          => \'script\',
							\'notNewRecords\' => 1,
							\'icon\'          => t3lib_extMgm::extRelPath(\''.$extKey.'\').\''.$id.'/wizard_icon.gif\',
							\'script\'        => t3lib_extMgm::extRelPath(\''.$extKey.'\').\''.$id.'/index.php\',
						),
					'));

					$cN = $this->returnName($extKey,'class',$id.'wiz');
					$this->writeStandardBE_xMod(
						$extKey,
						array('title'=>'Example Wizard title...'),
						$id.'/',
						$cN,
						0,
						$id.'wiz'
					);
					$this->addFileToFileArray($id.'/wizard_icon.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/notfound.gif'));

					$configL[]=trim($this->wrapBody('
						\'wizards\' => array(
							\'_PADDING\' => 2,
							',implode(chr(10),$wizards),'
						),
					'));
				}
			break;
			case 'textarea_rte':
				$DBfields[] = $fConf['fieldname'].' text,';
				$configL[]  = '\'type\' => \'text\',';
				$configL[]  = '\'cols\' => \'30\',';
				$configL[]  = '\'rows\' => \'5\',';
				if ($fConf['conf_rte_fullscreen'])	{
					$wizards =array();
					$wizards[] = trim($this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_fullscreen]').'
						\'RTE\' => array(
							\'notNewRecords\' => 1,
							\'RTEonly\'       => 1,
							\'type\'          => \'script\',
							\'title\'         => \'Full screen Rich Text Editing|Formatteret redigering i hele vinduet\',
							\'icon\'          => \'wizard_rte2.gif\',
							\'script\'        => \'wizard_rte.php\',
						),
					'));
					$configL[]=trim($this->wrapBody('
						\'wizards\' => array(
							\'_PADDING\' => 2,
							',implode(chr(10),$wizards),'
						),
					'));
				}

				$rteImageDir = '';
				if ($fConf['conf_rte_separateStorageForImages'] && t3lib_div::inList('moderate,basic,custom',$fConf['conf_rte']))	{
					$this->wizard->EM_CONF_presets['createDirs'][]=$this->ulFolder($extKey).'rte/';
					$rteImageDir = '|imgpath='.$this->ulFolder($extKey).'rte/';
				}

				$transformation='ts_images-ts_reglinks';
				if ($fConf['conf_mode_cssOrNot'] && t3lib_div::inList('moderate,custom',$fConf['conf_rte']))	{
					$transformation='ts_css';
				}


				switch($fConf['conf_rte'])	{
					case 'tt_content':
						$typeP = 'richtext[]:rte_transform[mode=ts]';
					break;
					case 'moderate':
						$typeP = 'richtext[]:rte_transform[mode='.$transformation.''.$rteImageDir.']';
					break;
					case 'basic':
						$typeP = 'richtext[]:rte_transform[mode=ts_css'.$rteImageDir.']';
						$this->wizard->ext_localconf[]=trim($this->wrapBody("
								t3lib_extMgm::addPageTSConfig('

									# ***************************************************************************************
									# CONFIGURATION of RTE in table \"".$table."\", field \"".$fConf["fieldname"]."\"
									# ***************************************************************************************

									",trim($this->slashValueForSingleDashes(str_replace(chr(9),"  ",$this->sPS("
										RTE.config.".$table.".".$fConf["fieldname"]." {
											hidePStyleItems = H1, H4, H5, H6
											proc.exitHTMLparser_db=1
											proc.exitHTMLparser_db {
												keepNonMatchedTags=1
												tags.font.allowedAttribs= color
												tags.font.rmTagIfNoAttrib = 1
												tags.font.nesting = global
											}
										}
									")))),"
								');
						",0));
					break;
					case 'none':
						$typeP = 'richtext[]';
					break;
					case 'custom':
						$enabledButtons=array();
						$traverseList = explode(',','cut,copy,paste,formatblock,class,fontstyle,fontsize,textcolor,bold,italic,underline,left,center,right,orderedlist,unorderedlist,outdent,indent,link,table,image,line,user,chMode');
						$HTMLparser=array();
						$fontAllowedAttrib=array();
						$allowedTags_WOP = array();
						$allowedTags=array();
						while(list(,$lI)=each($traverseList))	{
							$nothingDone=0;
							if ($fConf['conf_rte_b_'.$lI])	{
								$enabledButtons[]=$lI;
								switch($lI)	{
									case 'formatblock':
									case 'left':
									case 'center':
									case 'right':
										$allowedTags[]='div';
										$allowedTags[]='p';
									break;
									case 'class':
										$allowedTags[]='span';
									break;
									case 'fontstyle':
										$allowedTags[]='font';
										$fontAllowedAttrib[]='face';
									break;
									case 'fontsize':
										$allowedTags[]='font';
										$fontAllowedAttrib[]='size';
									break;
									case 'textcolor':
										$allowedTags[]='font';
										$fontAllowedAttrib[]='color';
									break;
									case 'bold':
										$allowedTags[]='b';
										$allowedTags[]='strong';
									break;
									case 'italic':
										$allowedTags[]='i';
										$allowedTags[]='em';
									break;
									case 'underline':
										$allowedTags[]='u';
									break;
									case 'orderedlist':
										$allowedTags[]='ol';
										$allowedTags[]='li';
									break;
									case 'unorderedlist':
										$allowedTags[]='ul';
										$allowedTags[]='li';
									break;
									case 'outdent':
									case 'indent':
										$allowedTags[]='blockquote';
									break;
									case 'link':
										$allowedTags[]='a';
									break;
									case 'table':
										$allowedTags[]='table';
										$allowedTags[]='tr';
										$allowedTags[]='td';
									break;
									case 'image':
										$allowedTags[]='img';
									break;
									case 'line':
										$allowedTags[]='hr';
									break;
									default:
										$nothingDone=1;
									break;
								}
								if (!$nothingDone)	$allowedTags_WOP[] = $WOP.'[conf_rte_b_'.$lI.']';
							}
						}
						if (count($fontAllowedAttrib))	{
							$HTMLparser[]='tags.font.allowedAttribs = '.implode(',',$fontAllowedAttrib);
							$HTMLparser[]='tags.font.rmTagIfNoAttrib = 1';
							$HTMLparser[]='tags.font.nesting = global';
						}
						if (count($enabledButtons))	{
							$typeP = 'richtext['.implode('|',$enabledButtons).']:rte_transform[mode='.$transformation.''.$rteImageDir.']';
						}

						$rte_colors=array();
						$setupUpColors=array();
						for ($a=1;$a<=3;$a++)	{
							if ($fConf['conf_rte_color'.$a])	{
								$rte_colors[$id.'_color'.$a]=trim($this->sPS('
									'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_color'.$a.']').'
									'.$id.'_color'.$a.' {
										name = Color '.$a.'
										value = '.$fConf['conf_rte_color'.$a].'
									}
								'));
								$setupUpColors[]=trim($fConf['conf_rte_color'.$a]);
							}
						}

						$rte_classes=array();
						for ($a=1;$a<=6;$a++)	{
							if ($fConf['conf_rte_class'.$a])	{
								$rte_classes[$id.'_class'.$a]=trim($this->sPS('
									'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class'.$a.']').'
									'.$id.'_class'.$a.' {
										name = '.$fConf['conf_rte_class'.$a].'
										value = '.$fConf['conf_rte_class'.$a.'_style'].'
									}
								'));
							}
						}

						$PageTSconfig= array();
						if ($fConf['conf_rte_removecolorpicker'])	{
							$PageTSconfig[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removecolorpicker]');
							$PageTSconfig[]='disableColorPicker = 1';
						}
						if (count($rte_classes))	{
							$PageTSconfig[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class*]');
							$PageTSconfig[]='classesParagraph = '.implode(', ',array_keys($rte_classes));
							$PageTSconfig[]='classesCharacter = '.implode(', ',array_keys($rte_classes));
							if (in_array('p',$allowedTags) || in_array('div',$allowedTags))	{
								$HTMLparser[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class*]');
								if (in_array('p',$allowedTags))	{$HTMLparser[]='p.fixAttrib.class.list = ,'.implode(',',array_keys($rte_classes));}
								if (in_array('div',$allowedTags))	{$HTMLparser[]='div.fixAttrib.class.list = ,'.implode(',',array_keys($rte_classes));}
							}
						}
						if (count($rte_colors))		{
							$PageTSconfig[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_color*]');
							$PageTSconfig[]='colors = '.implode(', ',array_keys($rte_colors));

							if (in_array('color',$fontAllowedAttrib) && $fConf['conf_rte_removecolorpicker'])	{
								$HTMLparser[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removecolorpicker]');
								$HTMLparser[]='tags.font.fixAttrib.color.list = ,'.implode(',',$setupUpColors);
								$HTMLparser[]='tags.font.fixAttrib.color.removeIfFalse = 1';
							}
						}
						if (!strcmp($fConf['conf_rte_removePdefaults'],1))	{
							$PageTSconfig[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removePdefaults]');
							$PageTSconfig[]='hidePStyleItems = H1, H2, H3, H4, H5, H6, PRE';
						} elseif ($fConf['conf_rte_removePdefaults']=='H2H3')	{
							$PageTSconfig[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removePdefaults]');
							$PageTSconfig[]='hidePStyleItems = H1, H4, H5, H6';
						} else {
							$allowedTags[]='h1';
							$allowedTags[]='h2';
							$allowedTags[]='h3';
							$allowedTags[]='h4';
							$allowedTags[]='h5';
							$allowedTags[]='h6';
							$allowedTags[]='pre';
						}


						$allowedTags = array_unique($allowedTags);
						if (count($allowedTags))	{
							$HTMLparser[]='	'.$this->WOPcomment('WOP:'.implode(' / ',$allowedTags_WOP));
							$HTMLparser[]='allowTags = '.implode(', ',$allowedTags);
						}
						if ($fConf['conf_rte_div_to_p'])	{
							$HTMLparser[]='	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_div_to_p]');
							$HTMLparser[]='tags.div.remap = P';
						}
						if (count($HTMLparser))	{
							$PageTSconfig[]=trim($this->wrapBody('
								proc.exitHTMLparser_db=1
								proc.exitHTMLparser_db {
									',implode(chr(10),$HTMLparser),'
								}
							'));
						}

						$finalPageTSconfig=array();
						if (count($rte_colors))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.colors {
								',implode(chr(10),$rte_colors),'
								}
							'));
						}
						if (count($rte_classes))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.classes {
								',implode(chr(10),$rte_classes),'
								}
							'));
						}
						if (count($PageTSconfig))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.config.'.$table.'.'.$fConf['fieldname'].' {
								',implode(chr(10),$PageTSconfig),'
								}
							'));
						}
						if (count($finalPageTSconfig))	{
							$this->wizard->ext_localconf[]=trim($this->wrapBody("
								t3lib_extMgm::addPageTSConfig('

									# ***************************************************************************************
									# CONFIGURATION of RTE in table \"".$table."\", field \"".$fConf["fieldname"]."\"
									# ***************************************************************************************

								",trim($this->slashValueForSingleDashes(str_replace(chr(9),"  ",implode(chr(10).chr(10),$finalPageTSconfig)))),"
								');
							",0));
						}
					break;
				}
				$this->wizard->_typeP[$fConf['fieldname']]	= $typeP;
			break;
			case 'check':
			case 'check_4':
			case 'check_10':
				$configL[]='\'type\' => \'check\',';
				if ($t=='check')	{
					$DBfields[] = $fConf['fieldname'].' tinyint(3) DEFAULT \'0\' NOT NULL,';
					if ($fConf['conf_check_default'])	$configL[]='\'default\' => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_check_default]');
				} else {
					$DBfields[] = $fConf['fieldname'].' int(11) DEFAULT \'0\' NOT NULL,';
				}
				if ($t=='check_4' || $t=='check_10')	{
					$configL[]='\'cols\' => 4,';
					$cItems=array();
					$aMax = intval($fConf["conf_numberBoxes"]);
					for($a=0;$a<$aMax;$a++)	{
						$cItems[]='array(\''.addslashes($this->getSplitLabels_reference($fConf,"conf_boxLabel_".$a,$table.".".$fConf["fieldname"].".I.".$a)).'\', \'\'),';
					}
					$configL[]=trim($this->wrapBody('
						\'items\' => array(
							', implode(chr(10), $cItems), '
						),
					'));
				}
			break;
			case 'radio':
			case 'select':
				$configL[]='\'type\' => \''.($t == 'select' ? 'select' : 'radio').'\',';
				$notIntVal=0;
				$len=array();
				$numberOfItems = ($version < 4006000) ? t3lib_div::intInRange($fConf['conf_select_items'], 1, 20) : t3lib_utility_Math::forceIntegerInRange($fConf['conf_select_items'], 1, 20);
				for ($a = 0; $a < $numberOfItems; $a++) {
					$val = $fConf["conf_select_itemvalue_".$a];
					if ($version < 4006000) {
						$notIntVal += t3lib_div::testInt($val) ? 0 : 1;
					} else {
						$notIntVal += t3lib_utility_Math::canBeInterpretedAsInteger($val) ? 0 : 1;
					}
					$len[]=strlen($val);
					if ($fConf["conf_select_icons"] && $t=="select")	{
						$icon = ', t3lib_extMgm::extRelPath(\''.$extKey.'\').\''.'selicon_'.$id.'_'.$a.'.gif'.'\'';
										// Add wizard icon
						$this->addFileToFileArray("selicon_".$id."_".$a.".gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/wiz.gif"));
					} else $icon="";
//					$cItems[]='Array("'.str_replace("\\'","'",addslashes($this->getSplitLabels($fConf,"conf_select_item_".$a))).'", "'.addslashes($val).'"'.$icon.'),';
					$cItems[]='array(\''.addslashes($this->getSplitLabels_reference($fConf,"conf_select_item_".$a,$table.".".$fConf["fieldname"].".I.".$a)).'\', \''.addslashes($val).'\''.$icon.'),';
				}
				$configL[]=trim($this->wrapBody('
					'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_items]').'
					\'items\' => array(
						', implode(chr(10), $cItems), '
					),
				'));
				if ($fConf['conf_select_pro'] && $t=='select')	{
					$cN = $this->returnName($extKey,'class',$id);
					$configL[]='\'itemsProcFunc\' => \''.$cN.'->main\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_pro]');

					$classContent = $this->sPS(
						'class '.$cN.' {

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$$params: ...
	 * @param	[type]		$pObj: ...
	 * @return	[type]		...
	 */
							function main(&$params,&$pObj)	{
/*
								debug(\'Hello World!\',1);
								debug(\'$params:\',1);
								debug($params);
								debug(\'$pObj:\',1);
								debug($pObj);
*/
									// Adding an item!
								$params[\'items\'][] = array($pObj->sL(\'Added label by PHP function|Tilfjet Dansk tekst med PHP funktion\'), 999);

								// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
							}
						}
					',
					0);

					$this->addFileToFileArray(
						'class.'.$cN.'.php',
						$this->PHPclassFile(
							$extKey,
							'class.'.$cN.'.php',
							$classContent,
							'Class/Function which manipulates the item-array for table/field '.$id.'.'
						)
					);

					$this->wizard->ext_tables[]=$this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_pro]:').'
						if (TYPO3_MODE === \'BE\')	{
							include_once(t3lib_extMgm::extPath(\''.$extKey.'\').\''.'class.'.$cN.'.php\');
						}
					');
				}

				$numberOfRelations = ($version < 4006000) ? t3lib_div::intInRange($fConf['conf_relations'], 1, 100) : t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 100);
				if ($t == 'select')	{
					if ($version < 4006000) {
						$configL[] = '\'size\' => ' . t3lib_div::intInRange($fConf['conf_relations_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations_selsize]');
					} else {
						$configL[] = '\'size\' => ' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations_selsize]');
					}
					$configL[]='\'maxitems\' => '.$numberOfRelations.',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations]');
				}

				if ($numberOfRelations>1 && $t=="select")	{
					if ($numberOfRelations*4 < 256)	{
						$DBfields[] = $fConf["fieldname"]." varchar(".($numberOfRelations*4).") DEFAULT '' NOT NULL,";
					} else {
						$DBfields[] = $fConf["fieldname"]." text,";
					}
				} elseif ($notIntVal)	{
					$varCharLn = ($version < 4006000) ? t3lib_div::intInRange(max($len), 1) : t3lib_utility_Math::forceIntegerInRange(max($len), 1);
					$DBfields[] = $fConf["fieldname"]." ".($varCharLn>$this->wizard->charMaxLng?'var':'')."char(".$varCharLn.") DEFAULT '' NOT NULL,";
				} else {
					$DBfields[] = $fConf["fieldname"].' int(11) DEFAULT \'0\' NOT NULL,';
				}
			break;
			case 'rel':
				if ($fConf["conf_rel_type"]=="group")	{
					$configL[]='\'type\' => \'group\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
					$configL[]='\'internal_type\' => \'db\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				} else {
					$configL[]='\'type\' => \'select\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				}

				if ($fConf["conf_rel_type"]!="group" && $fConf["conf_relations"]==1 && $fConf["conf_rel_dummyitem"])	{
					$configL[]=trim($this->wrapBody('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_dummyitem]').'
						\'items\' => array(
							', 'array(\'\', 0),', '
						),
					'));
				}

				if (t3lib_div::inList("tt_content,fe_users,fe_groups",$fConf["conf_rel_table"]))		$this->wizard->EM_CONF_presets["dependencies"][]="cms";

				if ($fConf["conf_rel_table"]=="_CUSTOM")	{
					$fConf["conf_rel_table"]=$fConf["conf_custom_table_name"]?$fConf["conf_custom_table_name"]:"NO_TABLE_NAME_AVAILABLE";
				}

				if ($fConf["conf_rel_type"]=="group")	{
					$configL[]='\'allowed\' => \''.($fConf["conf_rel_table"]!="_ALL"?$fConf["conf_rel_table"]:"*").'\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]');
					if ($fConf["conf_rel_table"]=="_ALL")	$configL[]='\'prepend_tname\' => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]=_ALL');
				} else {
					switch($fConf["conf_rel_type"])	{
						case "select_cur":
							$where="AND ".$fConf["conf_rel_table"].".pid=###CURRENT_PID### ";
						break;
						case "select_root":
							$where="AND ".$fConf["conf_rel_table"].".pid=###SITEROOT### ";
						break;
						case "select_storage":
							$where="AND ".$fConf["conf_rel_table"].".pid=###STORAGE_PID### ";
						break;
						default:
							$where="";
						break;
					}
					$configL[]='\'foreign_table\' => \''.$fConf["conf_rel_table"].'\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]');
					$configL[]='\'foreign_table_where\' => \''.$where.'ORDER BY '.$fConf["conf_rel_table"].'.uid\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				}
				if ($version < 4006000) {
					$configL[] = '\'size\' => ' . t3lib_div::intInRange($fConf['conf_relations_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations_selsize]');
					$configL[] = '\'minitems\' => 0,';
					$configL[] = '\'maxitems\' => ' . t3lib_div::intInRange($fConf['conf_relations'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations]');
					$confRelations = t3lib_div::intInRange($fConf['conf_relations'], 1, 100);
				} else {
					$configL[] = '\'size\' => ' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations_selsize]');
					$configL[] = '\'minitems\' => 0,';
					$configL[] = '\'maxitems\' => ' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_relations]');
					$confRelations = t3lib_utility_Math::forceIntegerInRange($fConf['conf_relations'], 1, 100);
				}

				if ($fConf["conf_relations_mm"])	{
					$mmTableName=$id."_mm";
					$configL[]='"MM" => "'.$mmTableName.'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations_mm]');
					$DBfields[] = $fConf["fieldname"].' int(11) DEFAULT \'0\' NOT NULL,';

					$createTable = $this->sPS("
						#
						# Table structure for table '".$mmTableName."'
						# ".$this->WOPcomment('WOP:'.$WOP.'[conf_relations_mm]')."
						#
						CREATE TABLE ".$mmTableName." (
						  uid_local int(11) DEFAULT '0' NOT NULL,
						  uid_foreign int(11) DEFAULT '0' NOT NULL,
						  tablenames varchar(30) DEFAULT '' NOT NULL,
						  sorting int(11) DEFAULT '0' NOT NULL,
						  KEY uid_local (uid_local),
						  KEY uid_foreign (uid_foreign)
						);
					");
					$this->wizard->ext_tables_sql[]=chr(10).$createTable.chr(10);
				} elseif ($confRelations > 1 || $fConf["conf_rel_type"]=="group") {
					$DBfields[] = $fConf["fieldname"]." text,";
				} else {
					$DBfields[] = $fConf["fieldname"].' int(11) DEFAULT \'0\' NOT NULL,';
				}

				if ($fConf["conf_rel_type"]!="group")	{
					$wTable=$fConf["conf_rel_table"];
					$wizards =array();
					if ($fConf["conf_wiz_addrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_addrec]').'
							\'add\' => array(
								\'type\'   => \'script\',
								\'title\'  => \'Create new record\',
								\'icon\'   => \'add.gif\',
								\'params\' => array(
									\'table\'    => \''.$wTable.'\',
									\'pid\'      => \'###CURRENT_PID###\',
									\'setValue\' => \'prepend\'
								),
								\'script\' => \'wizard_add.php\',
							),
						'));
					}
					if ($fConf["conf_wiz_listrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_listrec]').'
							\'list\' => array(
								\'type\'   => \'script\',
								\'title\'  => \'List\',
								\'icon\'   => \'list.gif\',
								\'params\' => array(
									\'table\' => \''.$wTable.'\',
									\'pid\'   => \'###CURRENT_PID###\',
								),
								\'script\' => \'wizard_list.php\',
							),
						'));
					}
					if ($fConf["conf_wiz_editrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_editrec]').'
							\'edit\' => array(
								\'type\'                     => \'popup\',
								\'title\'                    => \'Edit\',
								\'script\'                   => \'wizard_edit.php\',
								\'popup_onlyOpenIfSelected\' => 1,
								\'icon\'                     => \'edit2.gif\',
								\'JSopenParams\'             => \'height=350,width=580,status=0,menubar=0,scrollbars=1\',
							),
						'));
					}
					if (count($wizards))	{
						$configL[] = trim($this->wrapBody('
							\'wizards\' => array(
								\'_PADDING\'  => 2,
								\'_VERTICAL\' => 1,
								',implode(chr(10),$wizards),'
							),
						'));
					}
				}
			break;
			case "files":
				$configL[] = '\'type\' => \'group\',';
				$configL[] = '\'internal_type\' => \'file\',';
				switch($fConf["conf_files_type"])	{
					case "images":
						$configL[]='\'allowed\' => $GLOBALS[\'TYPO3_CONF_VARS\'][\'GFX\'][\'imagefile_ext\'],	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
					break;
					case "webimages":
						$configL[]='\'allowed\' => \'gif,png,jpeg,jpg\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]'); // TODO use web images definition from install tool
					break;
					case "all":
						$configL[] = '\'allowed\' => \'\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
						$configL[] = '\'disallowed\' => \'php,php3\',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
					break;
				}
				$configL[] = '\'max_size\' => $GLOBALS[\'TYPO3_CONF_VARS\'][\'BE\'][\'maxFileSize\'],	'.$this->WOPcomment('WOP:'.$WOP.'[conf_max_filesize]');

				$this->wizard->EM_CONF_presets["uploadfolder"]=1;

				$ulFolder  = 'uploads/tx_'.str_replace("_","",$extKey);
				$configL[] = '\'uploadfolder\' => \''.$ulFolder.'\',';
				if($fConf['conf_files_thumbs']) {
					$configL[] = '\'show_thumbs\' => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_thumbs]');
				}

				if ($version < 4006000) {
					$configL[] = '\'size\' => ' . t3lib_div::intInRange($fConf['conf_files_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_files_selsize]');
					$configL[] = '\'minitems\' => 0,';
					$configL[] = '\'maxitems\' => ' . t3lib_div::intInRange($fConf['conf_files'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_files]');
				} else {
					$configL[] = '\'size\' => ' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_files_selsize'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_files_selsize]');
					$configL[] = '\'minitems\' => 0,';
					$configL[] = '\'maxitems\' => ' . t3lib_utility_Math::forceIntegerInRange($fConf['conf_files'], 1, 100) . ',	' . $this->WOPcomment('WOP:' . $WOP . '[conf_files]');
				}

				$DBfields[] = $fConf["fieldname"]." text,";
			break;
			case 'flex':
				$DBfields[] = $fConf['fieldname'] . ' mediumtext,';
				$configL[]  = trim($this->sPS('
					\'type\' => \'flex\',
		\'ds\' => array(
			\'default\' => \'FILE:EXT:' . $extKey . '/flexform_' . $table . '_' . $fConf['fieldname'] . '.xml\',
		),
				'));
				$this->addFileToFileArray(
        			'flexform_'.$table.'_'.$fConf['fieldname'].'.xml',
        			$this->createFlexForm()
        		);
			break;
			case "none":
				$DBfields[] = $fConf["fieldname"]." tinytext,";
				$configL[]=trim($this->sPS('
					\'type\' => \'none\',
				'));
			break;
			case "passthrough":
				$DBfields[] = $fConf["fieldname"]." tinytext,";
				$configL[]=trim($this->sPS('
					\'type\' => \'passthrough\',
				'));
			break;
			case 'inline':
	#$DBfields=$this->getInlineDBfields($fConf);
				if ($DBfields) {
					$DBfields=array_merge($DBfields, $this->getInlineDBfields($table, $fConf));
				}
				$configL = $this->getInlineTCAconfig($table, $fConf);
			break;
			default:
				debug("Unknown type: ".(string)$fConf["type"]);
			break;
		}

		if ($t=="passthrough")	{
			$columns[$fConf["fieldname"]] = trim($this->wrapBody('
				\''.$fConf["fieldname"].'\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[fieldname]').'
					\'config\' => array(
						', implode(chr(10), $configL), '
					)
				),
			', 2));
		} else {
			$columns[$fConf["fieldname"]] = trim($this->wrapBody('
				\'' . $fConf["fieldname"] . '\' => array(		' . $this->WOPcomment('WOP:' . $WOP . '[fieldname]').'
					\'exclude\' => '.($fConf["excludeField"]?1:0).',		'.$this->WOPcomment('WOP:' . $WOP . '[excludeField]').'
					\'label\' => \'' . addslashes($this->getSplitLabels_reference($fConf, "title", $table . "." . $fConf["fieldname"])) . '\',		' . $this->WOPcomment('WOP:' . $WOP . '[title]').'
					\'config\' => array(
						', implode(chr(10), $configL), '
					)
				),
			',2));
		}
	}

	/**
	 * Return the uploadfolder for a extensionkey
	 *
	 * @param	string		$eKey: extension key
	 * @return	string		path to uploadfolder
	 */
	function ulFolder($eKey)	{
		return 'uploads/tx_'.str_replace('_','',$eKey).'/';
	}

	/**
	 * Renders input fields of type 'inline'
	 * Refers to the new IRRE (Inline Relational Record Editing) concept coming up on TYPO3 4.1
	 *
	 * @param	string		$prefix: The prefix for the fieldname
 	 * @param	array		$fConf: field config
	 * @return	array
	 */
	function renderInlineField($prefix,$fConf) {
	     $optValues = array(
		    'blank' => '',
		);

		$optValues = $this->addOtherExtensionTables($optValues);
		$fDetails .= '<br /><strong>Create relation to table:</strong><br />'
			.$this->renderSelectBox(
			  	$prefix.'[foreign_table]',
			  	$fConf['foreign_table'],
			  	$optValues
			)
			.'<br />';

		if($fConf['foreign_table'] == 'blank') {
			$fDetails .= '<strong><span style="color:red">Required!</span></strong> There is no type "inline" without a foreign table.<br />';
        }

		if($fConf['foreign_table'] == '_CUSTOM') {
			$fDetails .= 'Custom table name: '
				.$this->renderStringBox(
					$prefix.'[conf_custom_table_name]',
					$fConf['conf_custom_table_name'],
					200
				)
				.'<br />';
		}

		if($fConf['foreign_table'] !='blank' && $fConf['foreign_table'] !='_CUSTOM' || $fConf['conf_custom_table_name'] !='') {

			$fDetails .= '<br /><strong>Relation Type:</strong><br />'
				.$this->renderSelectBox(
				  	$prefix.'[relation_type]',
				  	$fConf['relation_type'],
				  	array(
						'1-n' => '1:N',
						'm-n' => 'M:N'
					)
				)
				.'<br />';

 			$fDetails .= '<br /><strong>Foreign field:</strong><br />'
			.$this->renderSelectBox(
				$prefix.'[foreign_field]',
				$fConf['foreign_field'],
				$this->getFieldsFromTable($fConf['foreign_table'])
				);
			$fDetails .= '<br />Field to store the uid of the parent record.<br />';




            $fDetails .= '<br /><strong>Foreign table field:</strong><br />'
            	.$this->renderStringBox(
            		$prefix.'[foreign_table_field]',
            		$fConf['foreign_table_field'],
            		200
            	);
            $fDetails .= '<br />If set together with <i>"foreign_field"</i> the child record knows about his parent.<br />';







			$fDetails .= '<br /><strong>Foreign sortby:</strong><br />'
				.$this->renderStringBox(
					$prefix.'[foreign_sortby]',
					$fConf['foreign_sortby'],
					200
				);
			$fDetails .= '<br />Field which stores the sorting information.<br />';

			$fDetails .= '<br /><strong>Foreign field label:</strong><br />'
				.$this->renderStringBox(
					$prefix.'[foreign_label]',
					$fConf['foreign_label'],
					200
				);
			$fDetails .= '<br />Overrides the label in TCA for the inline view.<br />';

        	$fDetails .= '<br /><strong>Foreign selector:</strong><br />'
        		.$this->renderStringBox(
        			$prefix.'[foreign_selector]',
        			$fConf['foreign_selector'],
        			200
        		);
			$fDetails .= '<br />Points to a field of the foreign_table that is responsible for providing a selectbox. Type is usually set to <i>"select"</i> and there is also a <i>"foreign_table"</i> defined.<br />';

			$fDetails .= '<br /><strong>Foreign unique:</strong><br />'
				.$this->renderStringBox(
					$prefix.'[foreign_unique]',
					$fConf['foreign_unique'],
					200
				);
			$fDetails .= '<br />Field which must be unique for all children of a parent record.<br />';

			$fDetails .= '<br /><strong>Intermediate table:</strong><br />'
				.$this->renderStringBox(
					$prefix.'[MM]',
					$fConf['MM'],
					200
				);
			$fDetails .= '<br />Intermediate table, like it\'s also done for MM-Relations.<br />';
			$fDetails .= 'Table name will look like <strong>\'parent_child_mm\'</strong>.<br />Three fields \'uid_local,uid_foreign,sorting\' are created to hold the values.<br />';

                // option for using symetric intermediate table
                // provide a checkbox
			$fDetails .= '<br />'
				.$this->renderCheckBox(
					$prefix.'[conf_symmetric]',
					$fConf['conf_symmetric']
				)
				.'<strong>Symmetric relations</strong><br />';

			if ($fConf['conf_symmetric']) {
				$fDetails .= '<br /><strong>Symmetric field:</strong><br />'
					.$this->renderStringBox(
						$prefix.'[conf_symmetric_filed]',
						$fConf['conf_symmetric_field'],
						200
					);
				$fDetails .= '<br />This works like <i>"foreign_field"</i>, but in case of using symmetric relations.<br />';

				$fDetails .= '<br /><strong>Symmetric label:</strong><br />'
					.$this->renderStringBox(
						$prefix.'[conf_symmetric_label]',
						$fConf['conf_symmetric_label'],
						200
					);
				$fDetails .= '<br />Overrrides the label set in TCA for the inline view and only if looking to a symmetric relation from the <i>"other"</i>side.<br />';

				$fDetails .= '<br /><strong>Symmetric sortby:</strong><br />'
					.$this->renderStringBox(
						$prefix.'[conf_symmetric_sortby]',
						$fConf['conf_symmetric_sortby'],
						200
					);
				$fDetails .= '<br />This works like <i>"foreign_sortby"</i>, but in case of using symmetric relations.<br />';
        	}
		}

		return $fDetails;
	}

	/**
	 * Create SQL definitions according to the appropriate input values
	 *
	 * @param	string		$table: current table name
	 * @param	array		$fConf: array holding field configuration values
	 * @return	array		DB fields definition
	 */
	function getInlineDBfields ($table, $fConf) {
			// field type set to 'text' to fit csv values
		if($fConf['conf_ff'] == 0) {
			$DBfields[] = $fConf['fieldname'].' text NOT NULL,';
		} elseif ($fConf['MM']) {
			$DBfields[] = $fConf['fieldname'].' int(10) DEFAULT \'0\' NOT NULL,';
		} else {
			$DBfields[] = $fConf['fieldname'].' tinytext NOT NULL,';
		}

			// FIXME: put the following case statments into separate function
		if($fConf['foreign_field']) {
			foreach($this->wizard->wizArray['tables'] as $k => $v) {
				$count = '';
				if(preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['foreign_table'])) {
					foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
						if(preg_match('/'.$fConf['foreign_field'].'/', $field['fieldname'], $match)) {
							print_r('Error: Field name already exists!');
						}
						$count++;
					}

					if(!count($match)) {
						$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['foreign_field'],'type'=>'passthrough');
					}
				}
			}
		}

		if($fConf['foreign_table_field']) {
			foreach ($this->wizard->wizArray['tables'] as $k => $v) {
				$count='';

				if(preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['foreign_table'])) {
					foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
						if(preg_match('/'.$fConf['foreign_table_field'].'/', $field['fieldname'], $match)) {
							print_r('Error: Field name already exists!');
						}
						$count++;
					}

					if(!count($match)) {
						$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['foreign_table_field'],'type'=>'passthrough');
					}
				}
			}
		}

		if($fConf['foreign_sortby']) {
			foreach($this->wizard->wizArray['tables'] as $k => $v) {
				$count='';

				if(preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['foreign_table'])) {
					foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
						if(preg_match('/'.$fConf['foreign_sortby'].'/', $field['fieldname'], $match)) {
							print_r('Error: Field name already exists!');
						}
						$count++;
					}
					if(!count($match)) {
						$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['foreign_sortby'],'type'=>'passthrough');
					}
				}
			}
		}

		if($fConf['foreign_selector']) {
			foreach($this->wizard->wizArray['tables'] as $k => $v) {
				$count = '';

				if(preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['foreign_selector'])) {
					foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
						if (preg_match('/'.$fConf['foreign_selector'].'/', $field['fieldname'], $match)) {
							print_r('Error: Field name already exists!');
						}
						$count++;
					}
					if(!count($match)) {
						$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['foreign_selector'],'type'=>'select');
					}
				}
			}
		}

		if($fConf['foreign_unique']) {
			foreach ($this->wizard->wizArray['tables'] as $k => $v) {
				$count='';

				if (preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['foreign_unique'])) {
					foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
						if (preg_match('/'.$fConf['foreign_unique'].'/', $field['fieldname'], $match)) {
							print_r('Error: Field name already exists!');
						}
						$count++;
					}
					if(!count($match)) {
						$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['foreign_unique'],'type'=>'select');
					}
				}
			}
		}

		if($fConf['MM']) {
			$intermediateTable = $this->sPS("
				#
				# Table structure for intermediate table '".$table."_".$fConf['MM']."_mm'
				#
				CREATE TABLE ".$table."_".$fConf['MM']."_mm (
					uid_local int(11) NOT NULL,
					uid_foreign int(11) NOT NULL,
					sorting int(10) NOT NULL,
				);
			");
			$this->wizard->ext_tables_sql[] = chr(10).$intermediateTable.chr(10);
		}

		if($fConf['conf_symmetric']) {
 			if($fConf['conf_symmetric_field']) {
				foreach ($this->wizard->wizArray['tables'] as $k => $v) {
					$count='';

					if(preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['conf_symmetric_field'])) {
						foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
							if (preg_match('/'.$fConf['conf_symmetric_field'].'/', $field['fieldname'], $match)) {
								print_r('Error: Field name already exists!');
							}
							$count++;
						}
						if(!count($match)) {
							$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['conf_symmetric_field'],'type'=>'select');
						}
					}
				}
			}

			if($fConf['conf_symmetric_sortby']) {
				foreach ($this->wizard->wizArray['tables'] as $k => $v) {
					$count = '';

					if (preg_match('/'.$this->wizard->wizArray['tables'][$k]['tablename'].'/', $fConf['conf_symmetric_sortby'])) {
						foreach($this->wizard->wizArray['tables'][$k]['fields'] as $n => $field) {
							if (preg_match('/'.$fConf['conf_symmetric_sortby'].'/', $field['fieldname'], $match)) {
								print_r('Error: Field name already exists!');
							}
							$count++;
						}
						if(!count($match)) {
							$this->wizard->wizArray['tables'][$k]['fields'][$count+1]= array('fieldname'=>$fConf['conf_symmetric_sortby'],'type'=>'passthrough');
						}
					}
				}
			}
		}

		return $DBfields;
	}

	/**
	 * Field configuration for setting up the correct TCA description.
	 *
	 * @param	string  	current table name
	 * @param	array		$fConf: array holding field configuration values
	 * @return	array		keys/values that become the table TCA definition
	 */
	function getInlineTCAconfig($table,$fConf) {
		$configL[] = '\'type\' => \'inline\',';
			foreach($fConf as $k => $v) {
				if($v && $v !=1 && ereg('^foreign',$k)) {
					$configL[] = '\''.$k.'\' => \''.$fConf[''.$k.''].'\',';
				}

				if($k == 'conf_symmetric_label' && $fConf[''.$k.'']) {
					$configL[] = '\'symmetric_label\' => \''.$fConf[''.$k.''].'\',';
				}
			}

				// maybe configurable later on. Depends on the options supplied by IRRE
			$configL[] = '\'minitems\'   => \'0\',';
			$configL[] = '\'maxitems\'   => \'10\',';

			$configL[] = '\'appearance\' => array(
				\'collapseAll\'           => \'1\',
				\'expandSingle\'          => \'1\',
				\'useSortable\'           => \'1\',
				\'newRecordLinkAddTitle\' => \'1\',
				\'newRecordLinkPosition\' => \'top\',
				\'useCombination\'        => \'0\',
			)';

		return $configL;
	}

}

	// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_fields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_fields.php']);
}

?>
