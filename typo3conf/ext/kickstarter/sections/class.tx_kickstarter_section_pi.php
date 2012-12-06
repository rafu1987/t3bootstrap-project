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

class tx_kickstarter_section_pi extends tx_kickstarter_sectionbase {
  var $sectionID = 'pi';

	/**
	 * Renders the form in the kickstarter; this was add_cat_pi()
	 *
	 * @return	HTML
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(':',$this->wizard->modData['wizAction']);
		if ($action[0]=='edit')	{
			$this->regNewEntry($this->sectionID, $action[1]);
			$lines = $this->catHeaderLines(
				$lines,
				$this->sectionID,
				$this->wizard->options[$this->sectionID],
				'<strong>Edit Plugin #'.$action[1].'</strong>',
				$action[1]
			);
			$piConf   = $this->wizard->wizArray[$this->sectionID][$action[1]];
			$ffPrefix = '['.$this->sectionID.']['.$action[1].']';


				// Enter title of the plugin
			$subContent='<strong>Enter a title for the plugin:</strong><br />'.
				$this->renderStringBox_lang('title',$ffPrefix,$piConf);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

			$subContent = $this->renderCheckBox($ffPrefix.'[plus_user_obj]',$piConf['plus_user_obj']).'By default plugins are generated as cachable USER cObjects. Check this checkbox to generate an uncached USER_INT cObject.<br />';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

			$subContent = $this->renderCheckBox($ffPrefix.'[plus_not_staticTemplate]',$piConf['plus_not_staticTemplate']).'Enable this option if you want the TypoScript code to be set by default. Otherwise the code will go into a static template file which must be included in the template record (it\'s <em>NOT</em> recommended to set this option).<br />';
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


				// Position
			if (is_array($this->wizard->wizArray['fields']))	{
				$optValues = array(
					'0' => '',
				);
				foreach($this->wizard->wizArray['fields'] as $kk => $fC)	{
					if ($fC['which_table']=='tt_content')	{
						$optValues[$kk]=($fC['title']?$fC['title']:'Item '.$kk).' ('.count($fC['fields']).' fields)';
					}
				}
				if (count($optValues)>1)	{
					$subContent='<strong>Apply a set of extended fields</strong><br />
						If you have configured a set of extra fields (Extend existing Tables) for the tt_content table, you can have them assigned to this plugin.
						<br />'.
						$this->renderSelectBox($ffPrefix.'[apply_extended]',$piConf['apply_extended'],$optValues);
					$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
				}
			}

				// Insert Plugin
			if (is_array($this->wizard->wizArray['tables']))	{
				$optValues = array(
					'0' => '',
				);
				foreach($this->wizard->wizArray['tables'] as $kk => $fC)	{
					$optValues[$kk]=($fC['tablename']||$fC['title']?$fC['title'].' ('.$this->returnName($this->wizard->extKey,'tables').($fC['tablename']?'_'.$fC['tablename']:'').')':'Item '.$kk).' ('.count($fC['fields']).' fields)';
				}
				$incListing='<br /><br />If you have configured custom tables you can select one of the tables to list by default as an example:
						<br />'.
						$this->renderSelectBox($ffPrefix.'[list_default]',$piConf['list_default'],$optValues);
				$incListing.='<br />'.$this->renderCheckBox($ffPrefix.'[list_default_listmode]',$piConf['list_default_listmode']).
					'Listing: Sections instead of table-rows';
				$incListing.='<br />'.$this->renderCheckBox($ffPrefix.'[list_default_singlemode]',$piConf['list_default_singlemode']).
					'Singleview: Sections instead of table-rows';
			} else $incListing='';


			if (!$piConf['addType'])	$piConf['addType']='list_type';
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'list_type').
				$this->textSetup(
				'Add to \'Insert Plugin\' list in Content Elements\'',
				'Most frontend plugins should be added to the Plugin list of Content Element type \'Insert Plugin\'. This is what happens with most other plugins you know of.'.
				$this->resImg('pi_pi.png').
				'<br />'.$this->renderCheckBox($ffPrefix.'[plus_wiz]',$piConf['plus_wiz']).
				'Add icon to \'New Content Element\' wizard:'.
				$this->resImg('pi_cewiz.png').
				'Write a description for the entry (if any):<br />'.
				$this->renderStringBox_lang('plus_wiz_description',$ffPrefix,$piConf).$incListing
				);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Text box
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'textbox').
				$this->textSetup('Add as a \'Textbox\' type',
				'The Textbox Content Element is not very common but has a confortable set of fields: Bodytext and image upload.'.
				$this->resImg('pi_textbox.png'));
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Menu/Sitemap
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'menu_sitemap').
				$this->textSetup('Add as a \'Menu/Sitemap\' item',
					'Adds the plugin to the Menu/Sitemap list. Use this if your plugin is a list of links to pages or elements on the website. An alternative sitemap? Or some special kind of menu in a special design?'.
					$this->resImg('pi_menu_sitemap.png'));
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// New content element
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'ce').
				$this->textSetup('Add as a totally new Content Element type',
					'You can also take the plunge into a whole new content element type! Scarry eh?'.
					$this->resImg('pi_ce.png')
				);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// New header type
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'header').
				$this->textSetup('Add as a new header type',
					'Finally you might insert a new header type here:'.
					$this->resImg('pi_header.png'));
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Processing of tags in content.
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'typotags').
				$this->textSetup('Processing of userdefined tag',
						htmlspecialchars('If you wish the plugin to process content from a userdefined tag in Content Element text-fields, enter the tagname here. Eg. if you wish the tags <mytag>This is the content</mytag> to be your userdefined tags, just enter \'mytag\' in this field (lowercase a-z, 0-9 and underscore):').'<br />'.
							$this->renderStringBox($ffPrefix.'[tag_name]',$piConf['tag_name'])
					);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

				// Just include library
			$subContent=$this->renderRadioBox($ffPrefix.'[addType]',$piConf['addType'],'includeLib').
				$this->textSetup('Just include library',
					'In this case your library is just included when pages are rendered.<br /><br />'.
					$this->renderCheckBox($ffPrefix.'[plus_user_ex]',$piConf['plus_user_ex']).'Provide TypoScript example for USER cObject in \'page.1000\'<br />'
					);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_pi'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_pi'] as $_funcRef) {
			$lines = t3lib_div::callUserFunction($_funcRef, $lines, $this->wizard);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode('',$lines).'</table>';
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
		$WOP='[pi]['.$k.']';
		$cN = $this->returnName($extKey,'class','pi'.$k);
		$pathSuffix = 'pi'.$k.'/';

		$setType='';
		switch($config['addType'])	{
			case 'list_type':
				$setType='list_type';

				$this->wizard->ext_tables[] = $this->sPS('
					'.$this->WOPcomment('WOP:' . $WOP . '[addType]') . "
					t3lib_div::loadTCA('tt_content');
					\$TCA['tt_content']['types']['list']['subtypes_excludelist'][\$_EXTKEY.'_pi".$k."'] = 'layout,select_key,pages';
					" . ($config['apply_extended'] ? "\$TCA['tt_content']['types']['list']['subtypes_addlist'][\$_EXTKEY . '_pi" . $k . "'] = '" . $this->wizard->_apply_extended_types[$config['apply_extended']] . "';" : "") . "
				");

//				$this->wizard->ext_localconf[]=$this->sPS('
//					'.$this->WOPcomment('WOP:'.$WOP.'[addType] / '.$WOP.'[tag_name]')."
//					  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
//					t3lib_extMgm::addTypoScript(\$_EXTKEY,'editorcfg','
//						tt_content.CSS_editor.ch.".$cN." = < plugin.".$cN.".CSS_editor
//					',43);
//				");
			break;
			case 'textbox':
				$setType='splash_layout';

				if ($config['apply_extended'])	{
					$this->wizard->ext_tables[]=$this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
						t3lib_div::loadTCA('tt_content');
						\$TCA['tt_content']['types']['splash']['subtype_value_field']='splash_layout';
						\$TCA['tt_content']['types']['splash']['subtypes_addlist'][\$_EXTKEY.'_pi".$k."']='".$this->wizard->_apply_extended_types[$config['apply_extended']]."';
					");
				}
			break;
			case 'menu_sitemap':
				$setType='menu_type';

				if ($config['apply_extended'])	{
					$this->wizard->ext_tables[]=$this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
						t3lib_div::loadTCA('tt_content');
						\$TCA['tt_content']['types']['menu']['subtype_value_field']='menu_type';
						\$TCA['tt_content']['types']['menu']['subtypes_addlist'][\$_EXTKEY.'_pi".$k."']='".$this->wizard->_apply_extended_types[$config['apply_extended']]."';
					");
				}
			break;
			case 'ce':
				$setType='CType';

				$tFields=array();
				$tFields[] = 'CType;;4;button;1-1-1, header;;3;;2-2-2';
				if ($config['apply_extended'])	{
					$tFields[] = $this->wizard->_apply_extended_types[$config['apply_extended']];
				}
				$this->wizard->ext_tables[]=$this->sPS('
					'.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
					t3lib_div::loadTCA('tt_content');
					\$TCA['tt_content']['types'][\$_EXTKEY . '_pi" . $k . "']['showitem'] = '" . implode(', ', $tFields) . "';
				");
			break;
			case 'header':
				$setType='header_layout';
			break;
			case 'includeLib':
				if ($config['plus_user_ex'])	$setType='includeLib';
			break;
			case 'typotags':
				$tagName = preg_replace('/[^a-z0-9_]/', '', strtolower($config['tag_name']));
				if ($tagName)	{
					$this->wizard->ext_localconf[]=$this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[addType] / '.$WOP.'[tag_name]')."
						  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
						t3lib_extMgm::addTypoScript(\$_EXTKEY,'setup','
							tt_content.text.20.parseFunc.tags.".$tagName." = < plugin.'.t3lib_extMgm::getCN(\$_EXTKEY).'_pi".$k."
						',43);
					");
				}
			break;
			default:
			break;
		}

		$cache= $config['plus_user_obj'] ? 0 : 1;

		$this->wizard->ext_localconf[]=$this->sPS('
			'.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
			t3lib_extMgm::addPItoST43(\$_EXTKEY, 'pi" . $k . "/class." . $cN .
			".php', '_pi" . $k . "', '" . $setType . "', " . $cache . ");
		");

		if ($setType && !t3lib_div::inList('typotags,includeLib', $setType)) {
			$this->wizard->ext_tables[]=$this->sPS('
				'.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
				t3lib_extMgm::addPlugin(array(
					'" . addslashes($this->getSplitLabels_reference($config, 'title', 'tt_content.' . $setType . '_pi' . $k)) . "',
					\$_EXTKEY . '_pi" . $k . "',
					t3lib_extMgm::extRelPath(\$_EXTKEY) . 'ext_icon.gif'
				),'" . $setType . "');
			");
		}

			// Make Plugin class:
		switch ($config['addType']) {
			case 'list_type':
				if ($config['list_default']) {
					if (is_array($this->wizard->wizArray['tables'][$config['list_default']])) {
						$tempTableConf = $this->wizard->wizArray['tables'][$config['list_default']];
						$tableName = $this->returnName($extKey,'tables',$tempTableConf['tablename']);

						$ll = array();

						$theLines = array();
						$theLines['getListRow']         = array();
						$theLines['getListHeader']      = array();
						$theLines['getFieldContent']    = array();
						$theLines['getFieldHeader']     = array();
						$theLines['singleRows']         = array();
						$theLines['listItemRows']       = array();
						$theLines['singleRows_section'] = array();
						$P_classes = array();

						$theLines['searchFieldList']    = array();
						$theLines['orderByList']        = array();

						$tcol = 'uid';
						$theLines['getListRow'][$tcol] = '<td><p>\' . $this->getFieldContent(\'' . $tcol . '\') . \'</p></td>';
						$theLines['getListHeader'][$tcol] = '<td><p>\' . $this->getFieldHeader_sortLink(\'' . $tcol . '\') . \'</p></td>';
						$theLines['orderByList'][$tcol] = $tcol;

						if (is_array($tempTableConf['fields']))	{
							reset($tempTableConf['fields']);
							while (list(,$fC) = each($tempTableConf['fields'])) {
								$tcol = $fC['fieldname'];
								if ($tcol) {
									$theLines['singleRows'][$tcol] = trim($this->sPS('
										<tr>
											<td nowrap="nowrap" valign="top"\' . $this->pi_classParam(\'singleView-HCell\') . \'><p>\' . $this->getFieldHeader(\'' . $tcol . '\') . \'</p></td>
											<td valign="top"><p>\' . $this->getFieldContent(\'' . $tcol . '\') . \'</p></td>
										</tr>
									'));

									if ($this->fieldIsRTE($fC))	{
										$theLines['singleRows_section'][$tcol] = trim($this->sPS('
											\'.$this->getFieldContent(\'' . $tcol . '\') . \'
										'));
									} else {
										$tempN = 'singleViewField-' . str_replace('_', '-', $tcol);
										$theLines['singleRows_section'][$tcol] = trim($this->sPS('
											<p\' . $this->pi_classParam("' . $tempN . '") . \'><strong>\' . $this->getFieldHeader(\'' . $tcol . '\') . \':</strong> \' . $this->getFieldContent(\'' . $tcol . '\') . \'</p>
										'));
										$P_classes['SV'][] = $tempN;
									}

									if (!strstr($fC['type'], 'textarea')) {
										$theLines['getListRow'][$tcol] = '<td valign="top"><p>\' . $this->getFieldContent(\'' . $tcol . '\') . \'</p></td>';
										$theLines['getListHeader'][$tcol] = '<td nowrap="nowrap"><p>\' . $this->getFieldHeader(\'' . $tcol . '\') . \'</p></td>';

										$tempN = 'listrowField-' . str_replace('_', '-', $tcol);
										$theLines['listItemRows'][$tcol] = trim($this->sPS('
											<p\' . $this->pi_classParam(\'' . $tempN . '\') . \'>\' . $this->getFieldContent(\'' . $tcol . '\') . \'</p>
										'));
										$P_classes['LV'][] = $tempN;
									}


									$this->addLocalConf($ll, array('listFieldHeader_' . $tcol => $fC['title']), 'listFieldHeader_' . $tcol, 'pi', $k, 1, 1);

									if ($tcol === 'title')	{
										$theLines['getFieldContent'][$tcol] = trim($this->sPS('
												case "' . $tcol . '":
														// This will wrap the title in a link.
													return $this->pi_list_linkSingle($this->internal[\'currentRow\'][\'' . $tcol . '\'], $this->internal[\'currentRow\'][\'uid\'], 1);
													break;
										'));
										$theLines['getFieldHeader'][$tcol] = trim($this->sPS('
												case "' . $tcol . '":
													return $this->pi_getLL(\'listFieldHeader_' . $tcol . '\', \'<em>' . $tcol . '</em>\');
													break;
										'));
									} elseif ($this->fieldIsRTE($fC)) {
											$theLines['getFieldContent'][$tcol] = trim($this->sPS('
													case "' . $tcol . '":
														return $this->pi_RTEcssText($this->internal[\'currentRow\'][\'' . $tcol . '\']);
														break;
											'));
									} elseif ($fC['type'] === 'datetime') {
										$theLines['getFieldContent'][$tcol] = trim($this->sPS('
												case "' . $tcol . '":
													return strftime(\'%d-%m-%y %H:%M:%S\', $this->internal[\'currentRow\'][\'' . $tcol . '\']);
													break;
										'));
									} elseif ($fC['type'] === 'date') {
										$theLines['getFieldContent'][$tcol] = trim($this->sPS('
												case "' . $tcol . '":
														// For a numbers-only date, use something like: %d-%m-%y
													return strftime(\'%A %e. %B %Y\', $this->internal[\'currentRow\'][\'' . $tcol . '\']);
													break;
										'));
									}
									if (strstr($fC['type'],'input')) {
										$theLines['getListHeader'][$tcol] = '<td><p>\' . $this->getFieldHeader_sortLink(\'' . $tcol . '\') . \'</p></td>';
										$theLines['orderByList'][$tcol] = $tcol;
									}
									if (strstr($fC['type'], 'input') || strstr($fC['type'], 'textarea')) {
										$theLines['searchFieldList'][$tcol] = $tcol;
									}
								}
							}
						}

						$theLines['singleRows']['tstamp'] = trim($this->sPS('
							<tr>
								<td nowrap="nowrap"\' . $this->pi_classParam(\'singleView-HCell\') . \'><p>Last updated:</p></td>
								<td valign="top"><p>\' . date(\'d-m-Y H:i\', $this->internal[\'currentRow\'][\'tstamp\']) . \'</p></td>
							</tr>
						'));
						$theLines['singleRows']['crdate'] = trim($this->sPS('
							<tr>
								<td nowrap="nowrap"\' . $this->pi_classParam(\'singleView-HCell\') . \'><p>Created:</p></td>
								<td valign="top"><p>\' . date(\'d-m-Y H:i\', $this->internal[\'currentRow\'][\'crdate\']) . \'</p></td>
							</tr>
						'));

							// Add title to local lang file
						$ll = $this->addStdLocalLangConf($ll,$k);

						$this->addLocalLangFile($ll, $pathSuffix . 'locallang.xml', 'Language labels for plugin "' . $cN . '"');


						$innerMainContent = $this->sPS('
							/**
							 * Main method of your Plugin.
							 *
							 * @param string $content The content of the Plugin
							 * @param array $conf The Plugin Configuration
							 * @return string The content that should be displayed on the website
							 */
							public function main($content, array $conf) {
								switch ((string)$conf[\'CMD\']) {
									case \'singleView\':
										list($t) = explode(\':\', $this->cObj->currentRecord);
										$this->internal[\'currentTable\'] = $t;
										$this->internal[\'currentRow\'] = $this->cObj->data;
										return $this->pi_wrapInBaseClass($this->singleView($content, $conf));
										break;
									default:
										if (strstr($this->cObj->currentRecord, \'tt_content\')) {
											$conf[\'pidList\'] = $this->cObj->data[\'pages\'];
											$conf[\'recursive\'] = $this->cObj->data[\'recursive\'];
										}
										return $this->pi_wrapInBaseClass($this->listView($content, $conf));
										break;
								}
							}
						');

						$innerMainContent .= $this->sPS('
							/**
							 * Shows a list of database entries.
							 *
							 * @param string $content content of the Plugin
							 * @param array $conf Plugin Configuration
							 * @return string HTML list of table entries
							 */
							protected function listView($content, array $conf) {
								$this->conf = $conf;		// Setting the TypoScript passed to this function in $this->conf
								$this->pi_setPiVarDefaults();
								$this->pi_loadLL();		// Loading the LOCAL_LANG values
								' . (!$cache ? '$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it\'s a USER_INT object!' : '').'
								$lConf = $this->conf[\'listView.\'];	// Local settings for the listView function

								if (is_numeric($this->piVars[\'showUid\'])) {	// If a single element should be displayed:
									$this->internal[\'currentTable\'] = \'' . $tableName . '\';
									$this->internal[\'currentRow\'] = $this->pi_getRecord(\'' . $tableName . '\', $this->piVars[\'showUid\']);

									$content = $this->singleView($content, $conf);
									return $content;
								} else {
									$items = array(
										\'1\' => $this->pi_getLL(\'list_mode_1\', \'Mode 1\'),
										\'2\' => $this->pi_getLL(\'list_mode_2\', \'Mode 2\'),
										\'3\' => $this->pi_getLL(\'list_mode_3\', \'Mode 3\'),
									);
									if (!isset($this->piVars[\'pointer\'])) $this->piVars[\'pointer\'] = 0;
									if (!isset($this->piVars[\'mode\'])) $this->piVars[\'mode\'] = 1;

										// Initializing the query parameters:
									list($this->internal[\'orderBy\'],$this->internal[\'descFlag\']) = explode(\':\', $this->piVars[\'sort\']);
									$version = class_exists(\'t3lib_utility_VersionNumber\')
											? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
											: t3lib_div::int_from_ver(TYPO3_version);
									if ($version < 4006000) {
										$this->internal[\'results_at_a_time\'] = t3lib_div::intInRange($lConf[\'results_at_a_time\'], 0, 1000, 3);		// Number of results to show in a listing.
										$this->internal[\'maxPages\'] = t3lib_div::intInRange($lConf[\'maxPages\'], 0, 1000, 2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
									} else {
										$this->internal[\'results_at_a_time\'] = t3lib_utility_Math::forceIntegerInRange($lConf[\'results_at_a_time\'], 0, 1000, 3);		// Number of results to show in a listing.
										$this->internal[\'maxPages\'] = t3lib_utility_Math::forceIntegerInRange($lConf[\'maxPages\'], 0, 1000, 2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
									}
									$this->internal[\'searchFieldList\'] = \'' . implode(',', $theLines['searchFieldList']) . '\';
									$this->internal[\'orderByList\'] = \'' . implode(',', $theLines['orderByList']) . '\';

										// Get number of records:
									$res = $this->pi_exec_query(\'' . $tableName . '\', 1);
									list($this->internal[\'res_count\']) = $GLOBALS[\'TYPO3_DB\']->sql_fetch_row($res);

										// Make listing query, pass query to SQL database:
									$res = $this->pi_exec_query(\'' . $tableName . '\');
									$this->internal[\'currentTable\'] = \'' . $tableName . '\';

										// Put the whole list together:
									$fullTable = \'\';	// Clear var;
									// $fullTable .= t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!

										// Adds the mode selector.
									$fullTable .= $this->pi_list_modeSelector($items);

										// Adds the whole list table
									$fullTable .= ' . ($config['list_default_listmode'] ? '$this->makeList($res);' : '$this->pi_list_makelist($res);') . '

										// Adds the search box:
									$fullTable .= $this->pi_list_searchBox();

										// Adds the result browser:
									$fullTable .= $this->pi_list_browseresults();

										// Returns the content from the plugin.
									return $fullTable;
								}
							}
						');


						if ($config['list_default_listmode']) {
							$innerMainContent .= $this->wrapBody('
								/**
								 * Creates a list from a database query.
								 *
								 * @param resource $res A database result resource
								 * @return string A HTML list if result items
								 */
								protected function makeList($res) {
									$items = array();
										// Make list table rows
									while (($this->internal[\'currentRow\'] = $GLOBALS[\'TYPO3_DB\']->sql_fetch_assoc($res)) !== FALSE) {
										$items[] = $this->makeListItem();
									}

									$out = \'<div\' . $this->pi_classParam(\'listrow\') . \'>
										\' . implode(chr(10), $items) . \'
										</div>\';
									return $out;
								}

								/**
								 * Implodes a single row from a database to a single line.
								 *
								 * @return string Imploded column values
								 */
								protected function makeListItem() {
									$out = \'
										', implode(chr(10), $theLines['listItemRows']), '
										\';
									return $out;
								}
							', 3);
						}

						// Single display:
						if ($config['list_default_singlemode'])	{
							$innerMainContent.= $this->wrapBody('
								/**
								 * Display a single item from the database
								 *
								 * @param string $content The Plugin content
								 * @param array $conf The Plugin configuration
								 * @return string HTML of a single database entry
								 */
								protected function singleView($content, array $conf) {
									$this->conf = $conf;
									$this->pi_setPiVarDefaults();
									$this->pi_loadLL();
									'.(!$cache ? '$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it\'s a USER_INT object!' : '').'

										// This sets the title of the page for use in indexed search results:
									if ($this->internal[\'currentRow\'][\'title\'])	{
										$GLOBALS[\'TSFE\']->indexedDocTitle = $this->internal[\'currentRow\'][\'title\'];
									}

									$content = \'<div\' . $this->pi_classParam(\'singleView\') . \'>
										<h2>Record "\' . $this->internal[\'currentRow\'][\'uid\'] . \'" from table "\' . $this->internal[\'currentTable\'] . \'":</h2>
										', implode(chr(10), $theLines['singleRows_section']), '
									<p>\' . $this->pi_list_linkSingle($this->pi_getLL(\'back\', \'Back\'), 0) . \'</p></div>\' .
									$this->pi_getEditPanel();

									return $content;
								}
							', 3);
						} else {
							$innerMainContent .= $this->wrapBody('
								/**
								 * Displays a single item from the database.
								 *
								 * @param string $content The Plugin content
								 * @param array $conf The Plugin configuration
								 * @return string HTML of a single database entry
								 */
								protected function singleView($content, array $conf) {
									$this->conf = $conf;
									$this->pi_setPiVarDefaults();
									$this->pi_loadLL();
									' . (!$cache ? '$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it\'s a USER_INT object!' : '').'

										// This sets the title of the page for use in indexed search results:
									if ($this->internal[\'currentRow\'][\'title\'])	$GLOBALS[\'TSFE\']->indexedDocTitle=$this->internal[\'currentRow\'][\'title\'];

									$content=\'<div\'.$this->pi_classParam(\'singleView\').\'>
										<h2>Record "\' . $this->internal[\'currentRow\'][\'uid\'] . \'" from table "\' . $this->internal[\'currentTable\'] . \'":</h2>
										<table>
											', implode(chr(10), $theLines['singleRows']), '
										</table>
									<p>\' . $this->pi_list_linkSingle($this->pi_getLL(\'back\', \'Back\'), 0) . \'</p></div>\' .
									$this->pi_getEditPanel();

									return $content;
								}
							', 3);
						}

						$this->wizard->ext_localconf[] = $this->sPS('
							' . $this->WOPcomment('WOP:' . $WOP . '[...]') . '
							t3lib_extMgm::addTypoScript($_EXTKEY, \'setup\', \'
								tt_content.shortcut.20.0.conf.' . $tableName . ' = < plugin.\' . t3lib_extMgm::getCN($_EXTKEY) . \'_pi' . $k . '
								tt_content.shortcut.20.0.conf.' . $tableName . '.CMD = singleView
							\', 43);
						');

						if (!$config['list_default_listmode'])	{
							$innerMainContent.= $this->wrapBody('
								/**
								 * Returns a single table row for list view.
								 *
								 * @param integer $c Counter for odd / even behavior
								 * @return string A HTML table row
								 */
								protected function pi_list_row($c) {
									$editPanel = $this->pi_getEditPanel();
									if ($editPanel)	$editPanel = \'<td>\' . $editPanel . \'</td>\';

									return \'<tr\' . ($c % 2 ? $this->pi_classParam(\'listrow-odd\') : \'\') . \'>
											', implode(chr(10), $theLines['getListRow']), '
											' . $editPanel . '
										</tr>\';
								}
							', 3);
							$innerMainContent .= $this->wrapBody('
								/**
								 * Returns a table row with column names of the table
								 *
								 * @return string A HTML table row
								 */
								protected function pi_list_header() {
									return \'<tr\' . $this->pi_classParam(\'listrow-header\') . \'>
											', implode(chr(10), $theLines['getListHeader']), '
										</tr>\';
								}
							',3);
						}
						$innerMainContent .= $this->wrapBody('
							/**
							 * Returns the content of a given field
							 *
							 * @param string $fN Name of table field
							 * @return string Value of the field
							 */
							protected function getFieldContent($fN) {
								switch($fN) {
									case \'uid\':
										return $this->pi_list_linkSingle($this->internal[\'currentRow\'][$fN], $this->internal[\'currentRow\'][\'uid\'], 1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
										break;
									', implode(chr(10), $theLines['getFieldContent']), '
									default:
										return $this->internal[\'currentRow\'][$fN];
										break;
								}
							}
						', 2);
						$innerMainContent .= $this->wrapBody('
							/**
							 * Returns the label for a field name from local language array.
							 *
							 * @param string $fN Name of table field
							 * @return string
							 */
							protected function getFieldHeader($fN) {
								switch ($fN) {
									', implode(chr(10), $theLines['getFieldHeader']), '
									default:
										return $this->pi_getLL(\'listFieldHeader_\' . $fN, \'[\' . $fN . \']\');
										break;
								}
							}
						',2);
						$innerMainContent .= $this->sPS('
							/**
							 * Returns a sorting link for a column header.
							 *
							 * @param string $fN Name of table field
							 * @return string The field label wrapped in link that contains sorting vars
							 */
							protected function getFieldHeader_sortLink($fN) {
								return $this->pi_linkTP_keepPIvars(
									$this->getFieldHeader($fN),
									array(
										\'sort\' => $fN . \' : \' . ($this->internal[\'descFlag\'] ? 0 : 1),
									)
								);
							}
						');

						$pCSSSel = str_replace('_', '-', $cN);
/*						$CSS_editor_code = '';

						if ($config['list_default_listmode'])	{
							$temp_merge=array();
							if (is_array($P_classes['LV']))	{
								while(list($c,$LVc)=each($P_classes['LV']))	{
									$temp_merge[]=$this->sPS('
										P_'.$c.' = ['.$LVc.']
										P_'.$c.'.selector = +.'.$pCSSSel.'-'.$LVc.'
										P_'.$c.'.attribs = BODYTEXT
										P_'.$c.'.example = <p class="'.$pCSSSel.'-'.$LVc.'">['.$LVc.'] text <a href="#">with a link</a> in it.</p><p class="'.$pCSSSel.'-'.$LVc.'">In principio creavit Deus caelum et terram terra autem erat inanis et vacua et tenebrae super faciem abyssi et spiritus...</p>
										P_'.$c.'.exampleStop = 1
										P_'.$c.'.ch.links = < CSS_editor.ch.A
									',1);
								}
							}
							$CSS_editor_code.=$this->wrapBody('
								list = List display
								list.selector = .'.$pCSSSel.'-listrow
								list.example = <div class="'.$pCSSSel.'-listrow"><p>This is regular bodytext in the list display.</p><p>Viditque Deus cuncta quae fecit et erant valde bona et factum est vespere et mane dies sextus.</p></div>
								list.exampleWrap = <div class="'.$pCSSSel.'-listrow"> | </div>
								list.ch.P < .P
								list.ch.P.exampleStop = 0
								list.ch.P.ch {
								',implode(chr(10),$temp_merge),'
								}
							');
						} else {
							$CSS_editor_code.=$this->sPS('
								list = List display
								list.selector = .'.$pCSSSel.'-listrow
								list.example = <div class="'.$pCSSSel.'-listrow"><table><tr class="'.$pCSSSel.'-listrow-header"><td nowrap><p>Time / Date:</p></td><td><p><a HREF="#">Title:</a></p></td></tr><tr><td valign="top"><p>25-08-02</p></td><td valign="top"><p><a HREF="#">New company name...</a></p></td></tr><tr class="'.$pCSSSel.'-listrow-odd"><td valign="top"><p>16-08-02</p></td><td valign="top"><p><a HREF="#">Yet another headline here</a></p></td></tr><tr><td valign="top"><p>05-08-02</p></td><td valign="top"><p><a HREF="#">The third line - even row</a></p></td></tr></table></div>
								list.exampleStop = 1
								list.ch {
									TABLE = Table
									TABLE.selector = TABLE
									TABLE.attribs = TABLE
									TD = Table cells
									TD.selector = TD
									TD.attribs = TD
									TD_header = Header row cells
									TD_header.selector = TR.'.$pCSSSel.'-listrow-header TD
									TD_header.attribs = TD
									TD_odd = Odd rows cells
									TD_odd.selector = TR.'.$pCSSSel.'-listrow-odd TD
									TD_odd.attribs = TD
								}
								list.ch.TD.ch.P < .P
								list.ch.TD_header.ch.P < .P
								list.ch.TD_odd.ch.P < .P
							');
						}

						if ($config['list_default_singlemode'])	{
							$temp_merge=array();
							if (is_array($P_classes['SV']))	{
								while(list($c,$LVc)=each($P_classes['SV']))	{
									$temp_merge[]=$this->sPS('
										P_'.$c.' = ['.$LVc.']
										P_'.$c.'.selector = +.'.$pCSSSel.'-'.$LVc.'
										P_'.$c.'.attribs = BODYTEXT
										P_'.$c.'.example = <p class="'.$pCSSSel.'-'.$LVc.'">['.$LVc.'] text <a href="#">with a link</a> in it.</p><p class="'.$pCSSSel.'-'.$LVc.'">In principio creavit Deus caelum et terram terra autem erat inanis et vacua et tenebrae super faciem abyssi et spiritus...</p>
										P_'.$c.'.exampleStop = 1
										P_'.$c.'.ch.links = < CSS_editor.ch.A
									',1);
								}
							}
							$CSS_editor_code.=$this->wrapBody('
								single = Single display
								single.selector = .'.$pCSSSel.'-singleView
								single.example = <div class="'.$pCSSSel.'-singleView"><H2>Header, if any:</H2><p>This is regular bodytext in the list display.</p><p>Viditque Deus cuncta quae fecit et erant valde bona et factum est vespere et mane dies sextus.</p><p><a href="#">Back</a></p></div>
								single.exampleWrap = <div class="'.$pCSSSel.'-singleView"> | </div>
								single.ch.P < .P
								single.ch.P.exampleStop = 0
								single.ch.P.ch {
								',implode(chr(10),$temp_merge),'
								}
							');
						} else {
							$CSS_editor_code.=$this->sPS('
								single = Single display
								single.selector = .'.$pCSSSel.'-singleView
								single.example = <div class="'.$pCSSSel.'-singleView"><H2>Header, if any:</H2><table><tr><td nowrap valign="top" class="'.$pCSSSel.'-singleView-HCell"><p>Date:</p></td><td valign="top"><p>13-09-02</p></td></tr><tr><td nowrap valign="top" class="'.$pCSSSel.'-singleView-HCell"><p>Title:</p></td><td valign="top"><p><a HREF="#">New title line</a></p></td></tr><tr><td nowrap valign="top" class="'.$pCSSSel.'-singleView-HCell"><p>Teaser text:</p></td><td valign="top"><p>Vocavitque Deus firmamentum caelum et factum est vespere et mane dies secundus dixit vero Deus congregentur.</p><p>Aquae quae sub caelo sunt in locum unum et appareat arida factumque est ita et vocavit Deus aridam terram congregationesque aquarum appellavit maria et vidit Deus quod esset bonum et ait germinet terra herbam virentem et facientem semen et lignum pomiferum faciens fructum iuxta genus suum cuius semen in semet ipso sit super terram et factum est ita et protulit terra herbam virentem et adferentem semen iuxta genus suum lignumque faciens fructum et habens unumquodque sementem secundum speciem suam et vidit Deus quod esset bonum.</p></td></tr><tr><td nowrap class="'.$pCSSSel.'-singleView-HCell"><p>Last updated:</p></td><td valign="top"><p>25-08-2002 18:28</p></td></tr><tr><td nowrap class="'.$pCSSSel.'-singleView-HCell"><p>Created:</p></td><td valign="top"><p>25-08-2002 18:27</p></td></tr></table><p><a href="#">Back</a></p></div>
								single.exampleStop = 1
								single.ch {
									TABLE = Table
									TABLE.selector = TABLE
									TABLE.attribs = TABLE
									TD = Table cells
									TD.selector = TD
									TD.attribs = TD
									TD.ch {
		  								TD = Header cells
			  							TD.selector = +.'.$pCSSSel.'-singleView-HCell
										TD.attribs = TD
									}
								}
								single.ch.P < .P
								single.ch.H2 < .H2
								single.ch.TD.ch.P < .P
								single.ch.TD.ch.TD.ch.P < .P
							');
						}

						$this->addFileToFileArray($config['plus_not_staticTemplate']?'ext_typoscript_editorcfg.txt':$pathSuffix.'static/editorcfg.txt',$this->wrapBody('
							plugin.'.$cN.'.CSS_editor = Plugin: "'.$cN.'"
							plugin.'.$cN.'.CSS_editor.selector = .'.$pCSSSel.'
							plugin.'.$cN.'.CSS_editor.exampleWrap = <HR><strong>Plugin: "'.$cN.'"</strong><HR><div class="'.$pCSSSel.'"> | </div>
							plugin.'.$cN.'.CSS_editor.ch {
								P = Text
								P.selector = P
								P.attribs = BODYTEXT
								P.example = <p>General text wrapped in &lt;P&gt;:<br />This is text <a href="#">with a link</a> in it. In principio creavit Deus caelum et terram terra autem erat inanis et vacua et tenebrae super faciem abyssi et spiritus...</p>
								P.exampleStop = 1
								P.ch.links = < CSS_editor.ch.A

								H2 = Header 2
								H2.selector = H2
								H2.attribs = HEADER
								H2.example = <H2>Header 2 example <a href="#"> with link</a></H2><p>Bodytext, Et praeessent diei ac nocti et dividerent lucem ac tenebras et vidit Deus quod esset bonum et factum est...</p>
								H2.ch.links = < CSS_editor.ch.A
								H2.exampleStop = 1

								H3 = Header 3
								H3.selector = H3
								H3.attribs = HEADER
								H3.example = <h3>Header 3 example <a href="#"> with link</a></h3><p>Bodytext, Et praeessent diei ac nocti et dividerent lucem ac tenebras et vidit Deus quod esset bonum et factum est...</p>
								H3.ch.links = < CSS_editor.ch.A
								H3.exampleStop = 1


									## LISTING:
								modeSelector = Mode selector
								modeSelector.selector = .'.$pCSSSel.'-modeSelector
								modeSelector.example = <div class="'.$pCSSSel.'-modeSelector"><table><tr><td class="'.$pCSSSel.'-modeSelector-SCell"><p><a HREF="#">Mode 1 (S)</a></p></td><td><p><a HREF="#">Mode 2</a></p></td><td><p><a HREF="#">Mode 3</a></p></td></tr></table></div>
								modeSelector.exampleStop = 1
								modeSelector.ch.P < .P
								modeSelector.ch.TABLE = Table
								modeSelector.ch.TABLE.selector = TABLE
								modeSelector.ch.TABLE.attribs = TABLE
								modeSelector.ch.TD = Table cells
								modeSelector.ch.TD.selector = TD
								modeSelector.ch.TD.attribs = TD
								modeSelector.ch.TD.ch {
								  TD = Selected table cells
								  TD.selector = + .'.$pCSSSel.'-modeSelector-SCell
								  TD.attribs = TD
								}
								modeSelector.ch.TD.ch.TD.ch.P < .P


								browsebox = Browsing box
								browsebox.selector = .'.$pCSSSel.'-browsebox
								browsebox.example = <div class="'.$pCSSSel.'-browsebox"><p>Displaying results <span class="'.$pCSSSel.'-browsebox-strong">1 to 3</span> out of <span class="'.$pCSSSel.'-browsebox-strong">4</span></p><table><tr><td class="'.$pCSSSel.'-browsebox-SCell"><p><a HREF="#">Page 1 (S)</a></p></td><td><p><a HREF="#">Page 2</a></p></td><td><p><a HREF="#">Next ></a></p></td></tr></table></div>
								browsebox.exampleStop = 1
								browsebox.ch.P < .P
								browsebox.ch.P.ch.strong = Emphasized numbers
								browsebox.ch.P.ch.strong {
								  selector = SPAN.'.$pCSSSel.'-browsebox-strong
								  attribs = TEXT
								}
								browsebox.ch.TABLE = Table
								browsebox.ch.TABLE.selector = TABLE
								browsebox.ch.TABLE.attribs = TABLE
								browsebox.ch.TD = Table cells
								browsebox.ch.TD.selector = TD
								browsebox.ch.TD.attribs = TD
								browsebox.ch.TD.ch {
								  TD = Selected table cells
								  TD.selector = + .'.$pCSSSel.'-browsebox-SCell
								  TD.attribs = TD
								}
								browsebox.ch.TD.ch.P < .P
								browsebox.ch.TD.ch.TD.ch.P < .P


								searchbox = Search box
								searchbox.selector = .'.$pCSSSel.'-searchbox
								searchbox.example = <div class="'.$pCSSSel.'-searchbox"><table><form action="#" method="POST"><tr><td><input type="text" name="'.$cN.'[sword]" value="Search word" class="'.$pCSSSel.'-searchbox-sword"></td><td><input type="submit" value="Search" class="'.$pCSSSel.'-searchbox-button"></td></tr></form></table></div>
								searchbox.exampleStop = 1
								searchbox.ch {
									TABLE = Table
									TABLE.selector = TABLE
									TABLE.attribs = TABLE
									TD = Table cells
									TD.selector = TD
									TD.attribs = TD
									INPUT = Form fields
									INPUT.selector = INPUT
									INPUT.attribs = TEXT,background-color,width
									INPUT.ch {
										sword = Search word field
										sword.selector = +.'.$pCSSSel.'-searchbox-sword
										sword.attribs = TEXT,background-color,width

										button = Submit button
										button.selector = +.'.$pCSSSel.'-searchbox-button
										button.attribs = TEXT,background-color,width
									}
								}
								',$CSS_editor_code,'
							}
						'),1);
*/
						$this->addFileToFileArray($config['plus_not_staticTemplate'] ? 'ext_typoscript_setup.txt' : $pathSuffix . 'static/setup.txt', $this->sPS('
							plugin.' . $cN . ' {
								CMD =
								pidList =
								recursive =
							}
							plugin.' . $cN . '.listView {
								results_at_a_time =
								maxPages =
							}
							  # Example of default set CSS styles (these go into the document header):
							plugin.' . $cN . '._CSS_DEFAULT_STYLE (
							  .' . $pCSSSel . ' h2 { margin-top: 0px; margin-bottom: 0px; }
							)
							  # Example of how to overrule LOCAL_LANG values for the plugin:
							plugin.' . $cN . '._LOCAL_LANG.default {
							  pi_list_searchBox_search = Search!
							}
							  # Example of how to set default values from TS in the incoming array, $this->piVars of the plugin:
							plugin.' . $cN . '._DEFAULT_PI_VARS.test = test
						'),1);

						$this->wizard->EM_CONF_presets['clearCacheOnLoad'] = 1;

						if (!$config['plus_not_staticTemplate']) {
							$this->wizard->ext_tables[]=$this->sPS('
								t3lib_extMgm::addStaticFile($_EXTKEY, \'' . $pathSuffix . 'static/\', \'' . addslashes(trim($config['title'])) . '\');
							');
						}
					}
				} else {
						// Add title to local lang file
					$ll = $this->addStdLocalLangConf($ll, $k, 1);
					$this->addLocalConf($ll, array('submit_button_label' => 'Click here to submit value'), 'submit_button_label', 'pi', $k, 1, 1);

					$this->addLocalLangFile($ll, $pathSuffix . 'locallang.xml', 'Language labels for plugin "' . $cN . '"');


					$innerMainContent = $this->sPS('
						/**
						 * The main method of the Plugin.
						 *
						 * @param string $content The Plugin content
						 * @param array $conf The Plugin configuration
						 * @return string The content that is displayed on the website
						 */
						public function main($content, array $conf) {
							$this->conf = $conf;
							$this->pi_setPiVarDefaults();
							$this->pi_loadLL();
							' . (!$cache ? '$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it\'s a USER_INT object!' : '').'

							$content = \'
								<strong>This is a few paragraphs:</strong><br />
								<p>This is line 1</p>
								<p>This is line 2</p>

								<h3>This is a form:</h3>
								<form action="\' . $this->pi_getPageLink($GLOBALS[\'TSFE\']->id) . \'" method="POST">
									<input type="text" name="\' . $this->prefixId . \'[input_field]" value="\' . htmlspecialchars($this->piVars[\'input_field\']) . \'" />
									<input type="submit" name="\' . $this->prefixId . \'[submit_button]" value="\' . htmlspecialchars($this->pi_getLL(\'submit_button_label\')) . \'" />
								</form>
								<br />
								<p>You can click here to \' . $this->pi_linkToPage(\'get to this page again\', $GLOBALS[\'TSFE\']->id) . \'</p>
							\';

							return $this->pi_wrapInBaseClass($content);
						}
					');


/*					$CSS_editor_code='';
					$pCSSSel = str_replace('_','-',$cN);

					$this->addFileToFileArray($config['plus_not_staticTemplate']?'ext_typoscript_editorcfg.txt':$pathSuffix.'static/editorcfg.txt',$this->sPS('
						plugin.'.$cN.'.CSS_editor = Plugin: "'.$cN.'"
						plugin.'.$cN.'.CSS_editor.selector = .'.$pCSSSel.'
						plugin.'.$cN.'.CSS_editor.exampleWrap = <HR><strong>Plugin: "'.$cN.'"</strong><HR><div class="'.$pCSSSel.'"> | </div>
						plugin.'.$cN.'.CSS_editor.ch {
							P = Text
							P.selector = P
							P.attribs = BODYTEXT
							P.example = <p>General text wrapped in &lt;P&gt;:<br />This is text <a href="#">with a link</a> in it. In principio creavit Deus caelum et terram terra autem erat inanis et vacua et tenebrae super faciem abyssi et spiritus...</p>
							P.exampleStop = 1
							P.ch.links = < CSS_editor.ch.A

							H3 = Header 3
							H3.selector = H3
							H3.attribs = HEADER
							H3.example = <h3>Header 3 example <a href="#"> with link</a></h3><p>Bodytext, Et praeessent diei ac nocti et dividerent lucem ac tenebras et vidit Deus quod esset bonum et factum est...</p>
							H3.ch.links = < CSS_editor.ch.A
							H3.exampleStop = 1
						}
					'),1);

					if (!$config['plus_not_staticTemplate'])	{
						$this->wizard->ext_tables[]=$this->sPS('
							t3lib_extMgm::addStaticFile($_EXTKEY, \''.$pathSuffix.'static/\', \''.addslashes(trim($config['title'])).'\');
						');
					}
*/				}
			break;
			case 'textbox':
				$this->wizard->ext_localconf[] = $this->sPS('
					  ## Setting TypoScript for the image in the textbox:
					t3lib_extMgm::addTypoScript($_EXTKEY,\'setup\',\'
						plugin.' . $cN . '_pi' . $k . '.IMAGEcObject {
						  file.width = 100
						}
					\', 43);
				');

				$innerMainContent = $this->sPS('
					/**
					 * The main method of the Plugin.
					 *
					 * @param string $content The Plugin content
					 * @param array $conf The Plugin configuration
					 * @return string The content that is displayed on the website (Textbox)
					 */
					public function main($content, array $conf) {

							// Processes the image-field content:
							// $conf[\'IMAGEcObject.\'] is passed to the getImage() function as TypoScript
							// configuration for the image (except filename which is set automatically here)
						$imageFiles = explode(\', \', $this->cObj->data[\'image\']);	// This returns an array with image-filenames, if many
						$imageRows = array();	// Accumulates the images
						foreach ($imageFiles as $iFile) {
							$imageRows[] = \'<tr>
								<td>\' . $this->getImage($iFile, $conf[\'IMAGEcObject.\']) . \'</td>
							</tr>\';
						}
						$imageBlock = count($imageRows) ? \'<table border="0" cellpadding="5" cellspacing="0">\' . implode(\'\', $imageRows) . \'</table>\' : \'<img src="clear.gif" width="100" height="1" alt="" />\';

							// Sets bodytext
						$bodyText = nl2br($this->cObj->data[\'bodytext\']);

							// And compiles everything into a table:
						$finalContent = \'<table border="1">
							<tr>
								<td valign="top">\' . $imageBlock . \'</td>
								<td valign="top">\' . $bodyText . \'</td>
							</tr>
						</table>\';

							// And returns content
						return $finalContent;
					}

					/**
					 * This calls a function in the TypoScript API which will return an image tag with the image
					 * processed according to the parsed TypoScript content in the $TSconf array.
					 *
					 * @param string $filename The filename of the image
					 * @param array $TSconf The TS configuration for displaying the image
					 * @return string The image HTML code
					 */
					protected function getImage($filename, array $TSconf) {
						list($theImage) = explode(\', \', $filename);
						$TSconf[\'file\'] = \'uploads/pics/\'.$theImage;
						$img = $this->cObj->IMAGE($TSconf);
						return $img;
					}
				');
			break;
			case 'header':
				$innerMainContent = $this->sPS('
					/**
					 * The main method of the Plugin.
					 *
					 * @param string $content The Plugin content
					 * @param array $conf The Plugin configuration
					 * @return string The content that is displayed on the website (Header)
					 */
					public function main($content, array $conf) {
						return \'<h1>\' . $this->cObj->data[\'header\'] . \'</j1>\';
					}
				');
			break;
			case 'menu_sitemap':
				$innerMainContent = $this->sPS('

					/**
					 * The main method of the Plugin.
					 *
					 * @param string $content The Plugin content
					 * @param array $conf The Plugin configuration
					 * @return string The content that is displayed on the website (Menu)
					 */
					public function main($content, array $conf) {
							// Get the PID from which to make the menu.
							// If a page is set as reference in the \'Startingpoint\' field, use that
							// Otherwise use the page\'s id-number from TSFE
						$menuPid = intval($this->cObj->data[\'pages\'] ? $this->cObj->data[\'pages\'] : $GLOBALS[\'TSFE\']->id);

							// Now, get an array with all the subpages to this pid:
							// (Function getMenu() is found in class.t3lib_page.php)
						$menuItems_level1 = $GLOBALS[\'TSFE\']->sys_page->getMenu($menuPid);

							// Prepare vars:
						$tRows = array();

							// Traverse menuitems:
						reset($menuItems_level1);
						while (list($uid, $pages_row) = each($menuItems_level1)) {
							$tRows[] = \'<tr bgcolor="#cccccc"><td>\' . $this->pi_linkToPage(
								$pages_row[\'nav_title\'] ? $pages_row[\'nav_title\'] : $pages_row[\'title\'],
								$pages_row[\'uid\'],
								$pages_row[\'target\']
							) . \'</td></tr>\';
						}

						$totalMenu = \'<table border="0" cellpadding="0" cellspacing="2">
							<tr><td>This is a menu. Go to your favourite page:</td></tr>
							\' . implode(\'\', $tRows) .
							\'</table><br />(\' . $this->tellWhatToDo(\'Click here if you want to know where to change the menu design\') . \')\';

						return $totalMenu;
					}

					/**
					 * Here you can do what ever you want.
					 *
					 * @param string $str The string that is processed
					 * @return It\'s your decision
					 */
					protected function tellWhatToDo($str) {
						return \'<a href="#" onClick="alert(\\\'Open the PHP-file \'.t3lib_extMgm::siteRelPath(\''.$extKey.'\').\''.$pathSuffix.'class.'.$cN.'.php and edit the function main()\nto change how the menu is rendered! It is pure PHP coding!\\\')">\'.$str.\'</a>\';
					}
				');
			break;
			case 'typotags':
				$innerMainContent = $this->sPS('
					/**
					 * The main method of the Plugin.
					 *
					 * @param string $content The Plugin content
					 * @param array $conf The Plugin configuration
					 * @return string The content that is displayed on the website (TypoTag)
					 */
					public function main($content, array $conf) {
						$tag_content = $this->cObj->getCurrentVal();
						return \'<b>\' . $this->tellWhatToDo(strtoupper($tag_content)) . \'</b>\';
					}

					/**
					 * Here you can do what ever you want
					 *
					 * @param string $str The string that is processed
					 * @return It\'s your decision
					 */
					protected function tellWhatToDo($str) {
						return \'<a href="#" onClick="alert(\\\'Open the PHP-file \'.t3lib_extMgm::siteRelPath(\''.$extKey.'\').\''.$pathSuffix.'class.'.$cN.'.php and edit the function main()\nto change how the tag content is processed!\\\')">\'.$str.\'</a>\';
					}
				');
			break;
			default:
				$innerMainContent = $this->sPS('
					/**
					 * The main method of the Plugin.
					 *
					 * @param string $content The Plugin content
					 * @param array $conf The Plugin configuration
					 * @return string The content that is displayed on the website
					 */
					public function main($content, array $conf) {
						return \'Hello World!<hr />
							Here is the TypoScript passed to the method:\' .
									t3lib_div::view_array($conf);
					}
				');
			break;
		}

		$indexRequire = '// require_once(PATH_tslib . \'class.tslib_pibase.php\');';
		$indexContent = $this->wrapBody('
			class ' . $cN . ' extends tslib_pibase {
				public $prefixId      = \'' . $cN . '\';		// Same as class name
				public $scriptRelPath = \'' . ($pathSuffix . "class." . $cN . ".php") . '\';	// Path to this script relative to the extension dir.
				public $extKey        = \'' . $extKey . '\';	// The extension key.
				' . ($cache ? 'public $pi_checkCHash = TRUE;
				' : '') . '
				', $innerMainContent, '
			}
		');
		$this->addFileToFileArray(
			$pathSuffix . 'class.' . $cN . '.php',
			$this->PHPclassFile(
				$extKey,
				$pathSuffix . 'class.' . $cN . '.php',
				$indexContent,
				'Plugin \'' . $config['title'] . '\' for the \'' . $extKey . '\' extension.',
				'',
				'',
				$indexRequire
			)
		);

			// Add wizard?
		if ($config['plus_wiz'] && $config['addType'] === 'list_type') {
			$this->addLocalConf($this->wizard->ext_locallang, $config, 'title', 'pi', $k);
			$this->addLocalConf($this->wizard->ext_locallang, $config, 'plus_wiz_description', 'pi', $k);

			$indexContent = $this->sPS(
				'class ' . $cN . '_wizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param array $wizardItems The wizard items
	 * @return array Modified array with wizard items
	 */
	public function proc(array $wizardItems) {
		$LL = $this->includeLocalLang();

		$wizardItems[\'plugins_' . $cN . '\'] = array(
			\'icon\' => t3lib_extMgm::extRelPath(\'' . $extKey . '\') . \'' . $pathSuffix . 'ce_wiz.gif\',
			\'title\' => $GLOBALS[\'LANG\']->getLLL(\'pi' . $k . '_title\', $LL),
			\'description\' => $GLOBALS[\'LANG\']->getLLL(\'pi' . $k . '_plus_wiz_description\', $LL),
			\'params\' => \'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=' . $extKey . '_pi' . $k . '\'
		);

		return $wizardItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return array The array with language labels
	 */
	protected function includeLocalLang() {
		$llFile = t3lib_extMgm::extPath(\'' . $extKey . '\') . \'locallang.xml\';
		$version = class_exists(\'t3lib_utility_VersionNumber\')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
		if ($version < 4006000) {
			$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS[\'LANG\']->lang);
		} else {
			/** @var $llxmlParser t3lib_l10n_parser_Llxml */
			$llxmlParser = t3lib_div::makeInstance(\'t3lib_l10n_parser_Llxml\');
			$LOCAL_LANG = $llxmlParser->getParsedData($llFile, $GLOBALS[\'LANG\']->lang);
		}

		return $LOCAL_LANG;
	}
}
',
			0);

			$this->addFileToFileArray(
				$pathSuffix.'class.' . $cN . '_wizicon.php',
				$this->PHPclassFile(
					$extKey,
					$pathSuffix . 'class.' . $cN . '_wizicon.php',
					$indexContent,
					'Class that adds the wizard icon.'
				)
			);

				// Add wizard icon
			$this->addFileToFileArray($pathSuffix . 'ce_wiz.gif', t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter') . 'res/wiz.gif'));

				// Add clear.gif
			$this->addFileToFileArray($pathSuffix . 'clear.gif', t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter') . 'res/clear.gif'));

			$this->wizard->ext_tables[] = $this->sPS('
				' . $this->WOPcomment('WOP:' . $WOP . '[plus_wiz]:') . '
				if (TYPO3_MODE === \'BE\') {
					$TBE_MODULES_EXT[\'xMOD_db_new_content_el\'][\'addElClasses\'][\'' . $cN . '_wizicon\'] = t3lib_extMgm::extPath($_EXTKEY) . \'pi' . $k . '/class.' . $cN . '_wizicon.php\';
				}
			');
		}
	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_pi.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_pi.php']);
}

?>