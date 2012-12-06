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

require_once(t3lib_extMgm::extPath('kickstarter').'sections/class.tx_kickstarter_section_fields.php');

class tx_kickstarter_section_tables extends tx_kickstarter_section_fields {
  var $sectionID = 'tables';

	/**
	 * Renders the form in the kickstarter; this was add_cat_tables()
	 *
	 * @return	string		wizard
	 */
	function render_wizard() {
		$lines = array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0] == 'edit')	{
			$this->regNewEntry($this->sectionID,$action[1]);
			$lines    = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],'&nbsp;',$action[1]);
			$piConf   = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix ='['.$this->sectionID.']['.$action[1].']';

				// Unique table name:
			$table_suffixes = array();
			if (is_array($this->wizard->wizArray[$this->sectionID]))	{
				foreach($this->wizard->wizArray[$this->sectionID] as $kk => $vv)	{
					if (!strcmp($action[1],$kk))	{
						if (count($table_suffixes) && t3lib_div::inList(implode(',',$table_suffixes),trim($vv['tablename']).'Z'))	{
							$piConf['tablename'] .= $kk;
						}
						break;
					}
					$table_suffixes[] = trim($vv['tablename']).'Z';
				}
			}


				// Enter title of the table
			$subContent = '<strong>Tablename:</strong><BR>'.
				$this->returnName($this->wizard->extKey,'tables').'_'.$this->renderStringBox($ffPrefix.'[tablename]',trim($piConf['tablename'])).
				'<BR><strong>Notice:</strong> Use characters a-z0-9 only. Only lowercase, no spaces.<BR>
				This becomes the table name in the database. ';
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


				// Enter title of the table
			$subContent = '<strong>Title of the table:</strong><BR>'.
				$this->renderStringBox_lang('title',$ffPrefix,$piConf);
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';



				// Fields - overview
			$c = array(0);
			$this->usedNames = array();
			if (is_array($piConf['fields']))	{
				$piConf['fields'] = $this->cleanFieldsAndDoCommands($piConf['fields'],$this->sectionID,$action[1],$piConf['which_table']?$piConf['which_table']:'');

				// Do it for real...
				$lines[] = '<tr'.$this->bgCol(1).'><td><strong> Fields Overview </strong></td></tr>';
				$lines[] = '<tr><td></td></tr>';

				$subContent ='<tr '.$this->bgCol(2).'>
					<td><strong>Name</strong></td>
					<td><strong>Title</strong></td>
					<td><strong>Type</strong></td>
					<td><strong>Exclude?</strong></td>
					<td><strong>Details</strong></td>
				</tr>';
				foreach($piConf['fields'] as $k=>$v)	{
					$c[] = $k;
					$subContent .= $this->renderFieldOverview($ffPrefix.'[fields]['.$k.']',$v);
				}
				$lines[] = '<tr'.$this->bgCol(3).'><td><table>'.$this->fw($subContent).'</table></td></tr>';
			}

			$lines[] = '<tr'.$this->bgCol(1).'><td><strong> Edit Fields </strong></td></tr>';
			$lines[] = '<tr><td></td></tr>';

			$subContent  = '';
			$subContent .= $this->renderCheckBox($ffPrefix.'[add_deleted]',$piConf['add_deleted'],1).'Add "Deleted" field '.$this->whatIsThis('Whole system: If a table has a deleted column, records are never really deleted, just "marked deleted" . Thus deleted records can actually be restored by clearing a deleted-flag later. Notice that all attached files are also not deleted from the server, so if you expect the table to hold some heavy size uploads, maybe you should not set this...') . '<BR>';
			$subContent .= $this->renderCheckBox($ffPrefix . '[add_hidden]', $piConf['add_hidden'],1) . 'Add "Hidden" flag ' . $this->whatIsThis('Frontend: The "Hidden" flag will prevent the record from being displayed on the frontend.') . '<BR>' . $this->resImg('t_flag_hidden.png','hspace=20','','<BR><BR>');
			$subContent .= $this->renderCheckBox($ffPrefix . '[add_starttime]', $piConf['add_starttime']) . 'Add "Starttime" ' . $this->whatIsThis('Frontend: If a "Starttime" is set, the record will not be visible on the website, before that date arrives.') . '<BR>' . $this->resImg('t_flag_starttime.png','hspace=20','','<BR><BR>');
			$subContent .= $this->renderCheckBox($ffPrefix . '[add_endtime]', $piConf['add_endtime']) . 'Add "Endtime" ' . $this->whatIsThis('Frontend: If a "Endtime" is set, the record will be hidden from that date and into the future.') . '<BR>' . $this->resImg('t_flag_endtime.png', 'hspace=20','','<BR><BR>');
			$subContent .= $this->renderCheckBox($ffPrefix . '[add_access]', $piConf['add_access']) . 'Add "Access group" ' . $this->whatIsThis('Frontend: If a frontend user group is set for a record, only frontend users that are members of that group will be able to see the record.') . '<BR>' . $this->resImg('t_flag_access.png', 'hspace=20','','<BR><BR>');
			$lines[]     = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Sorting
			$optValues = array(
				'crdate'    => '[crdate]',
				'cruser_id' => '[cruser_id]',
				'tstamp'    => '[tstamp]',
			);
			$subContent  = '';
			$subContent .= $this->renderCheckBox($ffPrefix.'[localization]',$piConf['localization']).'Enabled localization features'.$this->whatIsThis('If set, the records will have a selector box for language and a reference field which can point back to the original default translation for the record. These features are part of the internal framework for localization.').'<BR>';
			$subContent .= $this->renderCheckBox($ffPrefix.'[versioning]',$piConf['versioning']).'Enable versioning '.$this->whatIsThis('If set, you will be able to versionize records from this table. Highly recommended if the records are passed around in a workflow.').'<BR>';
			$subContent .= $this->renderCheckBox($ffPrefix.'[sorting]',$piConf['sorting']).'Manual ordering of records '.$this->whatIsThis('If set, the records can be moved up and down relative to each other in the backend. Just like Content Elements. Otherwise they are sorted automatically by any field you specify').'<BR>';
			$subContent .= $this->textSetup('','If "Manual ordering" is not set, order the table by this field:<BR>'.
				$this->renderSelectBox($ffPrefix.'[sorting_field]',$piConf['sorting_field'],$this->currentFields($optValues,$piConf['fields'])).'<BR>'.
				$this->renderCheckBox($ffPrefix.'[sorting_desc]',$piConf['sorting_desc']).' Descending');
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Type field
			$optValues = array(
				'0' => '[none]',
			);
			$subContent = '<strong>"Type-field", if any:<BR></strong>'.
					$this->renderSelectBox($ffPrefix.'[type_field]',$piConf['type_field'],$this->currentFields($optValues,$piConf['fields'])).
					$this->whatIsThis('A "type-field" is the field in the table which determines how the form is rendered in the backend, eg. which fields are shown under which circumstances. For instance the Content Element table "tt_content" has a type-field, CType. The value of this field determines if the editing form shows the bodytext field as is the case when the type is "Text" or if also the image-field should be shown as when the type is "Text w/Image"');
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Header field
			$optValues = array(
				'0' => '[none]',
			);
			$subContent = '<strong>Label-field:<BR></strong>'.
					$this->renderSelectBox($ffPrefix.'[header_field]',$piConf['header_field'],$this->currentFields($optValues,$piConf['fields'])).
					$this->whatIsThis('A "label-field" is the field used as record title in the backend.');
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Icon
			$optValues = array(
				'default.gif'        => 'Default (white)',
				'default_black.gif'  => 'Black',
				'default_gray4.gif'  => 'Gray',
				'default_blue.gif'   => 'Blue',
				'default_green.gif'  => 'Green',
				'default_red.gif'    => 'Red',
				'default_yellow.gif' => 'Yellow',
				'default_purple.gif' => 'Purple',
			);

			$subContent = $this->renderSelectBox($ffPrefix.'[defIcon]',$piConf['defIcon'],$optValues).' Default icon '.$this->whatIsThis('All tables have at least one associated icon. Select which default icon you wish. You can always substitute the file with another.');
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Allowed on pages
			$subContent = '<strong>Allowed on pages:<BR></strong>'.
					$this->renderCheckBox($ffPrefix.'[allow_on_pages]',$piConf['allow_on_pages']).' Allow records from this table to be created on regular pages.';
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Allowed in "Insert Records"
			$subContent = '<strong>Allowed in "Insert Records" field in content elements:<BR></strong>'.
					$this->renderCheckBox($ffPrefix.'[allow_ce_insert_records]',$piConf['allow_ce_insert_records']).' Allow records from this table to be linked to by content elements.';
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Add new button
			$subContent = '<strong>Add "Save and new" button in forms:<BR></strong>'.
					$this->renderCheckBox($ffPrefix.'[save_and_new]',$piConf['save_and_new']).' Will add an additional save-button to forms by which you can save the item and instantly create the next.';
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


			$subContent = '<strong>Notice on fieldnames:<BR></strong>'.
				'Don\'t use fieldnames from this list of reserved names/words: <BR>
				<blockquote><em>' . implode(', ', $this->wizard->reservedWords).'</em></blockquote>';
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// PRESETS:
			$selPresetBox = $this->presetBox($piConf["fields"]);

				// Fields
			$c = array(0);
			$this->usedNames = array();
			if (is_array($piConf['fields']))	{

				// Do it for real...
				foreach($piConf['fields'] as $k => $v)	{
					$c[] = $k;
					$subContent=$this->renderField($ffPrefix.'[fields]['.$k.']',$v);
					$lines[] = '<tr'.$this->bgCol(2).'><td>'.$this->fw('<strong>FIELD:</strong> <em>'.$v['fieldname'].'</em>').'</td></tr>';
					$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
				}
			}

				// New field:
			$k = max($c)+1;
			$v = array();
			$lines[] = '<tr'.$this->bgCol(2).'><td>'.$this->fw('<strong>NEW FIELD:</strong>').'</td></tr>';
			$subContent = $this->renderField($ffPrefix.'[fields]['.$k.']',$v,1);
			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


			$lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw('<BR><BR>Load preset fields: <BR>'.$selPresetBox).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_tables'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_tables'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode('',$lines).'</table>';

		return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$addFields: ...
	 * @param	[type]		$fArr: ...
	 * @return	[type]		...
	 */
	function currentFields($addFields, $fArr)	{
		if (is_array($fArr))	{
			foreach($fArr as $k => $v)	{
				if ($v['type'] && trim($v['fieldname']))	{
					$addFields[trim($v['fieldname'])] = $v['fieldname'].': '.$v['title'];
				}
			}
		}

		return $addFields;
	}

	/**
	 * renders field overview
	 *
	 * @author	Luite van Zelst <luite@aegee.org>
	 * @param	string		prefix (unused?)
	 * @param	array		field configuration
	 * @param	boolean		dontRemove (unused?)
	 * @return	string		table row with field data
	 */
	function renderFieldOverview($prefix, $fConf, $dontRemove=0)	{
			// Sorting
		$optTypes = array(
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
			'files'           => 'Files',
			'flex'            => 'Flex',
			'inline'          => 'Inine relation',
		);
		$optEval = array(
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
		$optRte = array(
			'tt_content' => 'Transform like "Bodytext"',
			'basic'      => 'Typical (based on CSS)',
			'moderate'   => 'Transform images / links',
			'none'       => 'No transform',
			'custom'     => 'Custom transform'
		);

		switch($fConf['type']) {
			case 'rel':
				if ($fConf['conf_rel_table'] == '_CUSTOM') {
					$details .= $fConf['conf_custom_table_name'];
				} else {
					$details .= $fConf['conf_rel_table'];
				}
			break;
			case 'input+':
				if($fConf['conf_varchar']) $details[] = 'varchar';
				if($fConf['conf_unique']) $details[] = ($fConf['conf_unique'] == 'L') ?  'unique (page)': 'unique (site)';
				if($fConf['conf_eval']) $details[] = $optEval[$fConf['conf_eval']];
				$details = implode(', ', (array) $details);
			break;
			case 'check_10':
			case 'check_4':
				$details = ($fConf['conf_numberBoxes'] ? $fConf['conf_numberBoxes'] : '4') . ' checkboxes';
			break;
			case 'radio':
				if($fConf['conf_select_items']) $details = $fConf['conf_select_items'] . ' options';
			break;
			case 'select':
				if($fConf['conf_select_items']) $details[] = $fConf['conf_select_items'] . ' options';
				if($fConf['conf_select_pro']) $details[] = 'preprocessing';
				$details = implode(', ', (array) $details);
			break;
			case 'textarea_rte':
				if($fConf['conf_rte']) $details = $optRte[$fConf['conf_rte']];
			break;
			case 'files':
				$details[] = $fConf['conf_files_type'];
				$details[] = $fConf['conf_files'] . ' files';
				$details[] = $fConf['conf_max_filesize'] . ' kB';
				$details = implode(', ', (array) $details);
			break;
		}
		return sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>',
			$fConf['fieldname'],
			$fConf['title'],
			$optTypes[$fConf['type']],
			$fConf['exludeField'] ? 'Yes' : '',
			$details
			);
	}

	/**
	 * Renders the files to be created
	 *
	 * @param	integer		key
	 * @param	array		table configuration
	 * @param	string		extension key
	 * @return	void
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP       = '[tables]['.$k.']';
		$tableName = trim($config['tablename']);
		$tableName = $this->returnName($extKey,'tables',$tableName);

		$DBfields = array();
		$columns  = array();
		$ctrl     = array();
		$enFields = array();

		$ctrl[] = trim($this->sPS('
			\'title\'     => \'' . $this->getSplitLabels_reference($config,'title',$tableName) . '\',		' . $this->WOPcomment('WOP:'.$WOP.'[title]').'
			\'label\'     => \'' . ($config['header_field']?$config['header_field']:'uid') . '\',	'.$this->WOPcomment('WOP:'.$WOP.'[header_field]').'
			\'tstamp\'    => \'tstamp\',
			\'crdate\'    => \'crdate\',
			\'cruser_id\' => \'cruser_id\',
		',0));

		$DBfields[] = trim($this->sPS('
			uid int(11) NOT NULL auto_increment,
			pid int(11) DEFAULT \'0\' NOT NULL,
			tstamp int(11) DEFAULT \'0\' NOT NULL,
			crdate int(11) DEFAULT \'0\' NOT NULL,
			cruser_id int(11) DEFAULT \'0\' NOT NULL,
		',0));

		if ($config['type_field'])	{
			$ctrl[] = '\'type\' => \''.$config['type_field'].'\',	'.$this->WOPcomment('WOP:'.$WOP.'[type_field]');
		}
		if ($config['versioning'])	{
			$ctrl[]     = '\'versioningWS\' => TRUE, ' . $this->WOPcomment('WOP:'.$WOP.'[versioning]');
			$ctrl[]     = '\'origUid\' => \'t3_origuid\',';
			$DBfields[] = 't3ver_oid int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_id int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_wsid int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_label varchar(30) DEFAULT \'\' NOT NULL,';
			$DBfields[] = 't3ver_state tinyint(4) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_stage int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_count int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_tstamp int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3ver_move_id int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 't3_origuid int(11) DEFAULT \'0\' NOT NULL,';

			$columns['t3ver_label'] = trim($this->sPS('
				\'t3ver_label\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[versioning]').'
					\'label\'  => \'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel\',
					\'config\' => array(
						\'type\' => \'input\',
						\'size\' => \'30\',
						\'max\'  => \'30\',
					)
				),
			'));
		}
		if ($config['localization'])	{
			$ctrl[] = '\'languageField\'            => \'sys_language_uid\',	'.$this->WOPcomment('WOP:'.$WOP.'[localization]');
			$ctrl[] = '\'transOrigPointerField\'    => \'l10n_parent\',	'.$this->WOPcomment('WOP:'.$WOP.'[localization]');
			$ctrl[] = '\'transOrigDiffSourceField\' => \'l10n_diffsource\',	'.$this->WOPcomment('WOP:'.$WOP.'[localization]');

			$DBfields[] = 'sys_language_uid int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 'l10n_parent int(11) DEFAULT \'0\' NOT NULL,';
			$DBfields[] = 'l10n_diffsource mediumblob,';

			$columns['sys_language_uid'] = trim($this->sPS('
				\'sys_language_uid\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[localization]').'
					\'exclude\' => 1,
					\'label\'  => \'LLL:EXT:lang/locallang_general.xml:LGL.language\',
					\'config\' => array(
						\'type\'                => \'select\',
						\'foreign_table\'       => \'sys_language\',
						\'foreign_table_where\' => \'ORDER BY sys_language.title\',
						\'items\' => array(
							array(\'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages\', -1),
							array(\'LLL:EXT:lang/locallang_general.xml:LGL.default_value\', 0)
						)
					)
				),
			'));

			$columns['l10n_parent'] = trim($this->sPS("
				'l10n_parent' => array(		".$this->WOPcomment('WOP:'.$WOP.'[localization]')."
					'displayCond' => 'FIELD:sys_language_uid:>:0',
					'exclude'     => 1,
					'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
					'config'      => array(
						'type'  => 'select',
						'items' => array(
							array('', 0),
						),
						'foreign_table'       => '".$tableName."',
						'foreign_table_where' => 'AND ".$tableName.".pid=###CURRENT_PID### AND ".$tableName.".sys_language_uid IN (-1,0)',
					)
				),
			"));

			$columns['l10n_diffsource'] = trim($this->sPS("
				'l10n_diffsource' => array(		".$this->WOPcomment('WOP:'.$WOP.'[localization]')."
					'config' => array(
						'type' => 'passthrough'
					)
				),
			"));
		}
		if ($config['sorting'])	{
			$ctrl[]     = '\'sortby\' => \'sorting\',	'.$this->WOPcomment('WOP:'.$WOP.'[sorting]');
			$DBfields[] = "sorting int(10) DEFAULT '0' NOT NULL,";
		} else {
			$ctrl[] = '\'default_sortby\' => \'ORDER BY '.trim($config['sorting_field'].' '.($config['sorting_desc'] ? 'DESC' : '')) . '\',	' . $this->WOPcomment('WOP:'.$WOP.'[sorting] / '.$WOP.'[sorting_field] / '.$WOP.'[sorting_desc]');
		}
		if ($config['add_deleted'])	{
			$ctrl[]     = '\'delete\' => \'deleted\',	'.$this->WOPcomment('WOP:'.$WOP.'[add_deleted]');
			$DBfields[] = "deleted tinyint(4) DEFAULT '0' NOT NULL,";
		}
		if ($config["add_hidden"])	{
			$enFields[] = '\'disabled\' => \'hidden\',	'.$this->WOPcomment('WOP:'.$WOP.'[add_hidden]');
			$DBfields[] = "hidden tinyint(4) DEFAULT '0' NOT NULL,";
			$columns["hidden"] = trim($this->sPS('
				\'hidden\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[add_hidden]').'
					\'exclude\' => 1,
					\'label\'   => \'LLL:EXT:lang/locallang_general.xml:LGL.hidden\',
					\'config\'  => array(
						\'type\'    => \'check\',
						\'default\' => \'0\'
					)
				),
			'));
		}
		if ($config["add_starttime"])	{
			$enFields[] = '\'starttime\' => \'starttime\',	'.$this->WOPcomment('WOP:'.$WOP.'[add_starttime]');
			$DBfields[] = "starttime int(11) DEFAULT '0' NOT NULL,";
			$columns["starttime"] = trim($this->sPS('
				\'starttime\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[add_starttime]').'
					\'exclude\' => 1,
					\'label\'   => \'LLL:EXT:lang/locallang_general.xml:LGL.starttime\',
					\'config\'  => array(
						\'type\'     => \'input\',
						\'size\'     => \'8\',
						\'max\'      => \'20\',
						\'eval\'     => \'date\',
						\'default\'  => \'0\',
						\'checkbox\' => \'0\'
					)
				),
			'));
		}
		if ($config["add_endtime"])	{
			$enFields[] = '\'endtime\' => \'endtime\',	'.$this->WOPcomment('WOP:'.$WOP.'[add_endtime]');
			$DBfields[] = "endtime int(11) DEFAULT '0' NOT NULL,";
			$columns["endtime"] = trim($this->sPS('
				\'endtime\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[add_endtime]').'
					\'exclude\' => 1,
					\'label\'   => \'LLL:EXT:lang/locallang_general.xml:LGL.endtime\',
					\'config\'  => array(
						\'type\'     => \'input\',
						\'size\'     => \'8\',
						\'max\'      => \'20\',
						\'eval\'     => \'date\',
						\'checkbox\' => \'0\',
						\'default\'  => \'0\',
						\'range\'    => array(
							\'upper\' => mktime(3, 14, 7, 1, 19, 2038),
							\'lower\' => mktime(0, 0, 0, date(\'m\')-1, date(\'d\'), date(\'Y\'))
						)
					)
				),
			'));
		}
		if ($config["add_access"])	{
			$enFields[] = '\'fe_group\' => \'fe_group\',	'.$this->WOPcomment('WOP:'.$WOP.'[add_access]');
			$DBfields[] = "fe_group int(11) DEFAULT '0' NOT NULL,";
			$columns["fe_group"] = trim($this->sPS('
				\'fe_group\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[add_access]').'
					\'exclude\' => 1,
					\'label\'   => \'LLL:EXT:lang/locallang_general.xml:LGL.fe_group\',
					\'config\'  => array(
						\'type\'  => \'select\',
						\'items\' => array(
							array(\'\', 0),
							array(\'LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login\', -1),
							array(\'LLL:EXT:lang/locallang_general.xml:LGL.any_login\', -2),
							array(\'LLL:EXT:lang/locallang_general.xml:LGL.usergroups\', \'--div--\')
						),
						\'foreign_table\' => \'fe_groups\'
					)
				),
			'));
		}
			// Add enable fields in header:
		if (is_array($enFields) && count($enFields))	{
			$ctrl[]=trim($this->wrapBody('
				\'enablecolumns\' => array(		'.$this->WOPcomment('WOP:'.$WOP.'[add_hidden] / '.$WOP.'[add_starttime] / '.$WOP.'[add_endtime] / '.$WOP.'[add_access]').'
				',implode(chr(10),$enFields),'
				),
			'));
		}
			// Add dynamic config file.
		$ctrl[]= '\'dynamicConfigFile\' => t3lib_extMgm::extPath($_EXTKEY) . \'tca.php\',';
		$ctrl[]= '\'iconfile\'          => t3lib_extMgm::extRelPath($_EXTKEY) . \'icon_' . $tableName . '.gif\',';

		if ($config['allow_on_pages'])	{
			$this->wizard->ext_tables[]=$this->sPS('
				'.$this->WOPcomment('WOP:'.$WOP.'[allow_on_pages]').'
				t3lib_extMgm::allowTableOnStandardPages(\''.$tableName.'\');
			');
		}
		if ($config['allow_ce_insert_records'])	{
			$this->wizard->ext_tables[]=$this->sPS('
				'.$this->WOPcomment('WOP:'.$WOP.'[allow_ce_insert_records]').'
				t3lib_extMgm::addToInsertRecords(\''.$tableName.'\');
			');
		}
		if ($config['save_and_new'])	{
			$this->wizard->ext_localconf[]=trim($this->wrapBody("
				t3lib_extMgm::addUserTSConfig('
					","options.saveDocNew.".$tableName."=1","
				');
			"));
		}

		if (is_array($config['fields']))	{
			reset($config['fields']);
			while(list($i,$fConf)=each($config["fields"]))	{
				$this->makeFieldTCA($DBfields,$columns,$fConf,$WOP."[fields][".$i."]",$tableName,$extKey);
			}
		}

			// Finalize tables.sql:
		$keys = '
				PRIMARY KEY (uid),
				KEY parent (pid)';

		if ($config['versioning'])	{
			$keys .= ',
				KEY t3ver_oid (t3ver_oid,t3ver_wsid)
			';
		}
		$DBfields[] = $this->sPS( $keys.'
		');
		$createTable = $this->wrapBody('
			#
			# Table structure for table \''.$tableName.'\'
			#
			CREATE TABLE '.$tableName.' (
		', implode(chr(10),$DBfields), '
			) ENGINE=InnoDB;
		');
		$this->wizard->ext_tables_sql[]=chr(10).$createTable.chr(10);

			// Finalize tca.php:
		$tca_file = '';
		list($typeList,$palList) = $this->implodeColumns($columns);
		$showRecordFieldList = $columns;
		unset($showRecordFieldList['t3ver_label']);
		$tca_file.=$this->wrapBody('
			$TCA[\'' . $tableName . '\'] = array(
				\'ctrl\' => $TCA[\''.$tableName.'\'][\'ctrl\'],
				\'interface\' => array(
					\'showRecordFieldList\' => \''.implode(',',array_keys($showRecordFieldList)).'\'
				),
				\'feInterface\' => $TCA[\''.$tableName.'\'][\'feInterface\'],
				\'columns\' => array(
			', trim(implode(chr(10),$columns)),'
				),
				\'types\' => array(
					\'0\' => array(\'showitem\' => \''.$typeList.'\')
				),
				\'palettes\' => array(
					\'1\' => array(\'showitem\' => \''.$palList.'\')
				)
			);
		',2);
		$this->wizard->ext_tca[]=chr(10).$tca_file.chr(10);

			// Finalize ext_tables.php:
		$feInterface = $columns;
		unset($feInterface['t3ver_label']);
		$this->wizard->ext_tables[] = $this->wrapBody('
			$TCA[\'' . $tableName . '\'] = array(
				\'ctrl\' => array(
			', implode(chr(10), $ctrl), '
				),
			);
		',2);

				// Add wizard icon
			$this->addFileToFileArray('icon_'.$tableName.'.gif', t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/'.$config['defIcon']));

	}

}



// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_tables.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_tables.php']);
}

?>