<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Kasper Skårhøj <kasperYYYY@typo3.com>
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
 * l10nmgr module cm1
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Daniel Zielinski <d.zielinski@l10ntech.de>
 * @author	Daniel Pötzinger <poetzinger@aoemedia.de>
 * @author	Fabian Seltmann <fs@marketing-factory.de>
 * @author	Andreas Otto <andreas.otto@dkd.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   68: class tx_l10nmgr_cm1 extends t3lib_SCbase
 *   75:     function menuConfig()
 *   89:     function main()
 *  101:     function jumpToUrl(URL)
 *  142:     function printContent()
 *  154:     function moduleContent($l10ncfg)
 *  203:     function render_HTMLOverview($accum)
 *  265:     function diffCMP($old, $new)
 *  278:     function submitContent($accum,$inputArray)
 *  376:     function getAccumulated($tree, $l10ncfg, $sysLang)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:l10nmgr/cm1/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');

require_once(t3lib_extMgm::extPath('l10nmgr').'views/class.tx_l10nmgr_l10ncfgDetailView.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'views/class.tx_l10nmgr_l10nHTMLListView.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'views/excelXML/class.tx_l10nmgr_excelXMLView.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'views/CATXML/class.tx_l10nmgr_CATXMLView.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'views/class.tx_l10nmgr_abstractExportView.php');

require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_l10nConfiguration.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_l10nBaseService.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_translationData.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_translationDataFactory.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_l10nBaseService.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_CATXMLImportManager.php');
require_once(t3lib_extMgm::extPath('l10nmgr').'models/class.tx_l10nmgr_mkPreviewLinkService.php');

require_once(PATH_t3lib.'class.t3lib_parsehtml_proc.php');

/**
 * Translation management tool
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_cm1 extends t3lib_SCbase {
	/**
	 * @var array Extension configuration
	 */
	protected $lConf = array();

	var $flexFormDiffArray = array();	// Internal

	/**
	 * @var	integer		Default language to export
	 */
	var $sysLanguage = '0';

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig() {
		global $LANG;

		$this->loadExtConf();
		$this->MOD_MENU = Array (
			'action' => array(
				''             => $LANG->getLL('general.action.blank.title'),
				'link'         => $LANG->getLL('general.action.edit.link.title'),
				'inlineEdit'   => $LANG->getLL('general.action.edit.inline.title'),
				'export_excel' => $LANG->getLL('general.action.export.excel.title'),
				'export_xml'   => $LANG->getLL('general.action.export.xml.title'),
			),
			'lang' => array(),
			'onlyChangedContent' => '',
			'noHidden' => ''
		);

			// Load system languages into menu:
			/** @var $t8Tools t3lib_transl8tools */
		$t8Tools = t3lib_div::makeInstance('t3lib_transl8tools');
		$sysL = $t8Tools->getSystemLanguages();

		foreach($sysL as $sL)	{
			if ($sL['uid']>0 && $GLOBALS['BE_USER']->checkLanguageAccess($sL['uid']))	{
				if ($this->lConf['enable_hidden_languages'] == 1) {
					$this->MOD_MENU['lang'][$sL['uid']] = $sL['title'];
				} elseif ($sL['hidden'] == 0) {
					$this->MOD_MENU['lang'][$sL['uid']] = $sL['title'];
				}
			}
		}

		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to
	 *
	 * @return	void
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TYPO3_CONF_VARS;

			// Get language to export/import
		$this->sysLanguage = $this->MOD_SETTINGS["lang"];

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate('EXT:l10nmgr/templates/cm1_template.html');
		$this->doc->form = '<form action="" method="post" enctype="' . $TYPO3_CONF_VARS['SYS']['form_enctype'] . '">';

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
			<script language="javascript" type="text/javascript" src="' . t3lib_div::resolveBackPath($BACK_PATH . t3lib_extMgm::extRelPath('l10nmgr') . 'res/contrib/tabs.js') . '"></script>
			<link rel="stylesheet" type="text/css" href="' . t3lib_div::resolveBackPath($BACK_PATH . t3lib_extMgm::extRelPath('l10nmgr') . 'res/contrib/tabs.css') . '" />';


			// Find l10n configuration record
			/** @var $l10ncfgObj tx_l10nmgr_l10nConfiguration */
		$l10ncfgObj=t3lib_div::makeInstance('tx_l10nmgr_l10nConfiguration');
		$l10ncfgObj->load($this->id);

		if ($l10ncfgObj->isLoaded()) {

				// Setting page id
			$this->id = $l10ncfgObj->getData('pid');
			$this->perms_clause = $GLOBALS['BE_USER']->getPagePermsClause(1);
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			if ($this->id && $access) {

					// Header:
//				$this->content.=$this->doc->startPage($LANG->getLL('general.title'));
//				$this->content.=$this->doc->header($LANG->getLL('general.title'));

					// Create and render view to show details for the current l10nmgrcfg
					/** @var $l10nmgrconfigurationView tx_l10nmgr_l10ncfgDetailView */
				$l10nmgrconfigurationView= t3lib_div::makeInstance('tx_l10nmgr_l10ncfgDetailView', $l10ncfgObj, $this->doc);
				$this->content.=$this->doc->section('', $l10nmgrconfigurationView->render());

				$this->content.=$this->doc->divider(15);
				$this->content.=$this->doc->section($LANG->getLL('general.export.choose.action.title'),
						t3lib_BEfunc::getFuncMenu($l10ncfgObj->getId(),"SET[lang]",$this->sysLanguage,$this->MOD_MENU["lang"],'','&srcPID='.rawurlencode(t3lib_div::_GET('srcPID'))).
						t3lib_BEfunc::getFuncMenu($l10ncfgObj->getId(),"SET[action]",$this->MOD_SETTINGS["action"],$this->MOD_MENU["action"],'','&srcPID='.rawurlencode(t3lib_div::_GET('srcPID'))).
						t3lib_BEfunc::getFuncCheck($l10ncfgObj->getId(),"SET[onlyChangedContent]",$this->MOD_SETTINGS["onlyChangedContent"],'','&srcPID='.rawurlencode(t3lib_div::_GET('srcPID'))) . ' ' . $LANG->getLL('export.xml.new.title') .
						t3lib_BEfunc::getFuncCheck($l10ncfgObj->getId(),"SET[noHidden]",$this->MOD_SETTINGS["noHidden"],'','&srcPID='.rawurlencode(t3lib_div::_GET('srcPID'))) . ' ' . $LANG->getLL('export.xml.noHidden.title'). '</br>'
					);

					// Render content:
				if (!count($this->MOD_MENU['lang'])) {
					$this->content .= $this->doc->section('ERROR', $LANG->getLL('general.access.error.title'));
				} else {
					$this->moduleContent($l10ncfgObj);
				}
			}
		}

		$this->content .= $this->doc->spacer(10);

		$markers['CONTENT'] = $this->content;

			// Build the <body> for the module
		$docHeaderButtons = $this->getButtons();
		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('general.title'));
		$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		$this->content .= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
	}

	/**
	 * Printing output content
	 *
	 * @return	void
	 */
	public function printContent() {

//		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	function inlineEditAction($l10ncfgObj) {
		global $LANG, $BACK_PATH;

			/** @var $service tx_l10nmgr_l10nBaseService */
		$service = t3lib_div::makeInstance('tx_l10nmgr_l10nBaseService');
		$info='';
		// Buttons:
		$info.= '<input type="submit" value="'.$LANG->getLL('general.action.save.button.title').'" name="saveInline" onclick="return confirm(\''.$LANG->getLL('inlineedit.save.alert.title').'\');" />';
		$info.= '<input type="submit" value="'.$LANG->getLL('general.action.cancel.button.title').'" name="_" onclick="return confirm(\''.$LANG->getLL('inlineedit.cancel.alert.title').'\');" />';

		//simple init of translation object:
			/** @var $translationData tx_l10nmgr_translationData */
		$translationData=t3lib_div::makeInstance('tx_l10nmgr_translationData');
		$translationData->setTranslationData(t3lib_div::_POST('translation'));
		$translationData->setLanguage($this->sysLanguage);

			// See, if incoming translation is available, if so, submit it
		if (t3lib_div::_POST('saveInline')) {
			$service->saveTranslation($l10ncfgObj,$translationData);
		}
		return $info;
	}


	function _getSelectField($elementName,$currentValue,$menuItems) {
		$options = array();

		foreach($menuItems as $value => $label)	{
			$options[] = '<option value="'.htmlspecialchars($value).'"'.(!strcmp($currentValue,$value)?' selected="selected"':'').'>'.
						t3lib_div::deHSCentities(htmlspecialchars($label)).
						'</option>';
		}

		if (count($options) > 0) {
			return '
				<select name="'.$elementName.'" >
					'.implode('
					',$options).'
				</select>
						';
		}
	}

	function catXMLExportImportAction($l10ncfgObj) {
		global $LANG, $BACK_PATH, $BE_USER;
		$allowedSettingFiles = array(
			'across'     => 'acrossL10nmgrConfig.dst',
			'dejaVu'     => 'dejaVuL10nmgrConfig.dvflt',
			'memoq'     => 'memoQ.mqres',
			'transit'     => 'StarTransit_XML_UTF_TYPO3.FFD',
			'sdltrados2007'  => 'SDLTradosTagEditor.ini',
			'sdltrados2009'  => 'TYPO3_l10nmgr.sdlfiletype',
			'sdlpassolo' => 'SDLPassolo.xfg',
		);

			/** @var $service tx_l10nmgr_l10nBaseService */
		$service = t3lib_div::makeInstance('tx_l10nmgr_l10nBaseService');

		$info = '<br/>';
		$info .= '<input type="submit" value="'.$LANG->getLL('general.action.refresh.button.title').'" name="_" /><br /><br/>';

		$info .= '<div id="ddtabs" class="basictab" style="border:0px solid gray;margin:0px;">
                                <ul style="border:0px solid #999999; ">
                                <li><a onClick="expandcontent(\'sc1\', this)" style="margin:0px;">'.$LANG->getLL('export.xml.headline.title').'</a></li>
                                <li><a onClick="expandcontent(\'sc2\', this)" style="margin:0px;">'.$LANG->getLL('import.xml.headline.title').'</a></li>
                                <li><a onClick="expandcontent(\'sc3\', this)" style="margin:0px;">'.$LANG->getLL('file.settings.downloads.title').'</a></li>
                                <li><a onClick="expandcontent(\'sc4\', this)" style="margin:0px;">'.$LANG->getLL('l10nmgr.documentation.title').'</a></li>
				</ul></div>';

		$info .= '<div id="tabcontentcontainer" style="height:150px;border:1px solid gray;padding-right:5px;width:100%;">';

		$info .= '<div id="sc1" class="tabcontent">';
		//$info .= '<div id="sc1" class="tabcontent">';
		$_selectOptions=array('0'=>'-default-');
		$_selectOptions=$_selectOptions+$this->MOD_MENU["lang"];
		$info .= '<input type="checkbox" value="1" name="check_exports" /> ' . $LANG->getLL('export.xml.check_exports.title') . '<br />';
		$info .= '<input type="checkbox" value="1" name="no_check_xml" /> ' . $LANG->getLL('export.xml.no_check_xml.title') . '<br />';
		$info .= '<input type="checkbox" value="1" name="check_utf8" /> ' . $LANG->getLL('export.xml.checkUtf8.title') . '<br />';
		$info .= $LANG->getLL('export.xml.source-language.title') . $this->_getSelectField("export_xml_forcepreviewlanguage",'0',$_selectOptions) . '<br />';
			// Add the option to send to FTP server, if FTP information is defined
		if (!empty($this->lConf['ftp_server']) && !empty($this->lConf['ftp_server_username']) && !empty($this->lConf['ftp_server_password'])) {
			$info .= '<input type="checkbox" value="1" name="ftp_upload" id="tx_l10nmgr_ftp_upload" /> <label for="tx_l10nmgr_ftp_upload">' . $GLOBALS['LANG']->getLL('export.xml.ftp.title') . '</label>';
		}
		$info .= '<br /><br/>';
		$info .= '<input type="submit" value="Export" name="export_xml" /><br /><br /><br/>';
		$info .= '</div>';
		$info .= '<div id="sc2" class="tabcontent">';
		$info .= '<input type="checkbox" value="1" name="make_preview_link" /> ' . $LANG->getLL('import.xml.make_preview_link.title') . '<br />';
		$info .= '<input type="checkbox" value="1" name="import_delL10N" /> ' . $LANG->getLL('import.xml.delL10N.title') . '<br />';
		$info .= '<input type="checkbox" value="1" name="import_oldformat" /> ' . $LANG->getLL('import.xml.old-format.title') . '<br /><br />';
		$info .= '<input type="file" size="60" name="uploaded_import_file" /><br /><br /><input type="submit" value="Import" name="import_xml" /><br /><br /> ';
		$info .= '</div>';
		$info .= '<div id="sc3" class="tabcontent">';
		$info .= $this->doc->icons(1) .
			   $LANG->getLL('file.settings.available.title');

		for( reset($allowedSettingFiles); list($settingId, $settingFileName) = each($allowedSettingFiles); ) {

			$currentFile = t3lib_div::resolveBackPath($BACK_PATH . t3lib_extMgm::extRelPath('l10nmgr') . 'settings/' . $settingFileName);

			if ( is_file($currentFile) && is_readable($currentFile) ) {

				$size = t3lib_div::formatSize((int)filesize($currentFile), ' Bytes| KB| MB| GB');
				$info .= '<br/><a href="' . t3lib_div::rawUrlEncodeFP($currentFile) . '" title="' . $LANG->getLL('file.settings.download.title') . '" target="_blank">' . $LANG->getLL('file.settings.' . $settingId . '.title') . ' (' . $size . ')' . '</a> ';
			}
		}
		$info .= '</div>';
		$info .= '<div id="sc4" class="tabcontent">';
		$info .= '<a href="'.t3lib_extMgm::extRelPath('l10nmgr').'doc/manual.sxw" target="_new">Download</a>';
		$info .= '</div>';
		$info .= '</div>';

		$actionInfo = '';
		// Read uploaded file:
		if (t3lib_div::_POST('import_xml') && $_FILES['uploaded_import_file']['tmp_name'] && is_uploaded_file($_FILES['uploaded_import_file']['tmp_name']))	{
			$uploadedTempFile = t3lib_div::upload_to_tempfile($_FILES['uploaded_import_file']['tmp_name']);
				/** @var $factory tx_l10nmgr_translationDataFactory */
			$factory = t3lib_div::makeInstance('tx_l10nmgr_translationDataFactory');

		//print "<pre>";
		//var_dump($GLOBALS['BE_USER']->user);
		//print "</pre>";
			if (t3lib_div::_POST('import_oldformat')=='1') {
				//Support for the old Format of XML Import (without pageGrp element)
				$actionInfo .= $LANG->getLL('import.xml.old-format.message');
				$translationData = $factory->getTranslationDataFromOldFormatCATXMLFile($uploadedTempFile);
				$translationData->setLanguage($this->sysLanguage);
				$service->saveTranslation($l10ncfgObj,$translationData);
				$actionInfo .= '<br/><br/>'.$this->doc->icons(1).'Import done<br/><br/>(Command count:'.$service->lastTCEMAINCommandsCount.')';
			}
			else {
					// Relevant processing of XML Import with the help of the Importmanager
					/** @var $importManager tx_l10nmgr_CATXMLImportManager */
				$importManager=t3lib_div::makeInstance('tx_l10nmgr_CATXMLImportManager', $uploadedTempFile, $this->sysLanguage, $xmlString="");
				if ($importManager->parseAndCheckXMLFile()===false) {
					$actionInfo .= '<br/><br/>'.$this->doc->header($LANG->getLL('import.error.title')).$importManager->getErrorMessages();
				}
				else {
					if (t3lib_div::_POST('import_delL10N')=='1') {
						$actionInfo .= $LANG->getLL('import.xml.delL10N.message').'<br/>';
						$delCount = $importManager->delL10N($importManager->getDelL10NDataFromCATXMLNodes($importManager->xmlNodes));
						$actionInfo .= sprintf($LANG->getLL('import.xml.delL10N.count.message'),$delCount).'<br/><br/>';
					}
					if (t3lib_div::_POST('make_preview_link')=='1') {
						$pageIds = $importManager->getPidsFromCATXMLNodes($importManager->xmlNodes);
						$actionInfo .= '<b>'.$LANG->getLL('import.xml.preview_links.title').'</b><br/>';
							/** @var $mkPreviewLinks tx_l10nmgr_mkPreviewLinkService */
						$mkPreviewLinks=t3lib_div::makeInstance('tx_l10nmgr_mkPreviewLinkService', $t3_workspaceId=$importManager->headerData['t3_workspaceId'], $t3_sysLang=$importManager->headerData['t3_sysLang'], $pageIds);
						$actionInfo .= $mkPreviewLinks->renderPreviewLinks($mkPreviewLinks->mkPreviewLinks());
					}
					$translationData = $factory->getTranslationDataFromCATXMLNodes($importManager->getXMLNodes());
					$translationData->setLanguage($this->sysLanguage);
					//$actionInfo.="<pre>".var_export($GLOBALS['BE_USER'],true)."</pre>";
					unset($importManager);
					$service->saveTranslation($l10ncfgObj,$translationData);
					$actionInfo .= '<br/>'.$this->doc->icons(-1).$LANG->getLL('import.xml.done.message').'<br/><br/>(Command count:'.$service->lastTCEMAINCommandsCount.')';
				}
			}
			t3lib_div::unlink_tempfile($uploadedTempFile);
		}
		// If export of XML is asked for, do that (this will exit and push a file for download, or upload to FTP is option is checked)
		if (t3lib_div::_POST('export_xml')) {
			// Save user prefs
			$BE_USER->pushModuleData('l10nmgr/cm1/checkUTF8',t3lib_div::_POST('check_utf8'));

				// Render the XML
				/** @var $viewClass tx_l10nmgr_CATXMLView */
			$viewClass = t3lib_div::makeInstance('tx_l10nmgr_CATXMLView', $l10ncfgObj, $this->sysLanguage);
			$export_xml_forcepreviewlanguage = intval(t3lib_div::_POST('export_xml_forcepreviewlanguage'));
			if ($export_xml_forcepreviewlanguage > 0) {
				$viewClass->setForcedSourceLanguage($export_xml_forcepreviewlanguage);
			}
			if ($this->MOD_SETTINGS['onlyChangedContent']) {
				$viewClass->setModeOnlyChanged();
			}
			if ($this->MOD_SETTINGS['noHidden']) {
				$viewClass->setModeNoHidden();
			}
				// Check the export
			if ((t3lib_div::_POST('check_exports')=='1') && ($viewClass->checkExports() == FALSE)) {
					/** @var $flashMessage t3lib_FlashMessage */
				$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $LANG->getLL('export.process.duplicate.message'), $LANG->getLL('export.process.duplicate.title'), t3lib_FlashMessage::INFO);
				$actionInfo .= $flashMessage->render();
				$actionInfo .= $viewClass->renderExports();
			} else {
					// Upload to FTP
				if (t3lib_div::_POST('ftp_upload') == '1') {
					try {
						$filename = $this->uploadToFtp($viewClass);
							// Send a mail notification
						$this->emailNotification($filename, $l10ncfgObj, $this->sysLanguage);
							// Prepare a success message for display
						$title = $GLOBALS['LANG']->getLL('export.ftp.success');
						$message = sprintf($GLOBALS['LANG']->getLL('export.ftp.success.detail'), $this->lConf['ftp_server_path'] . $filename);
						$status = t3lib_FlashMessage::OK;
					}
					catch (Exception $e) {
							// Prepare an error message for display
						$title = $GLOBALS['LANG']->getLL('export.ftp.error');
						$message = $e->getMessage() . ' (' . $e->getCode() . ')';
						$status = t3lib_FlashMessage::ERROR;
					}
						/** @var $flashMessage t3lib_FlashMessage */
					$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $message, $title, $status);
					$actionInfo .= $flashMessage->render();
					$actionInfo .= $viewClass->renderInternalMessagesAsFlashMessage($status);

					// Download the XML file
				} else {
					try {
						$filename = $this->downloadXML($viewClass);
							// Prepare a success message for display
						$link = sprintf('<a href="%s" target="_blank">%s</a>', t3lib_div::getIndpEnv('TYPO3_SITE_URL').$filename, $filename);
						$title = $GLOBALS['LANG']->getLL('export.download.success');
						$message = sprintf($GLOBALS['LANG']->getLL('export.download.success.detail'), $link);
						$status = t3lib_FlashMessage::OK;
					}
					catch (Exception $e) {
							// Prepare an error message for display
						$title = $GLOBALS['LANG']->getLL('export.download.error');
						$message = $e->getMessage() . ' (' . $e->getCode() . ')';
						$status = t3lib_FlashMessage::ERROR;
					}
						/** @var $flashMessage t3lib_FlashMessage */
					$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $message, $title, $status);
					$actionInfo .= $flashMessage->render();
					$actionInfo .= $viewClass->renderInternalMessagesAsFlashMessage($status);
				}
			}
		}
		if (!empty($actionInfo)) {
			$info .= $this->doc->header($LANG->getLL('misc.messages.title'));
			$info .= $actionInfo;
		}

		$info .= '</div>';

		return $info;
	}

	function excelExportImportAction($l10ncfgObj) {
		global $LANG, $BACK_PATH;

		$service=t3lib_div::makeInstance('tx_l10nmgr_l10nBaseService');
		// Buttons:
		$_selectOptions=array('0'=>'-default-');
		$_selectOptions=$_selectOptions+$this->MOD_MENU["lang"];
		$info = $LANG->getLL('export.xml.source-language.title') . $this->_getSelectField("export_xml_forcepreviewlanguage",'0',$_selectOptions).'<br/>';
		$info.= '<input type="submit" value="'.$LANG->getLL('general.action.refresh.button.title').'" name="_" />';
		$info.= '<input type="submit" value="'.$LANG->getLL('general.action.export.xml.button.title').'" name="export_excel" />';
		$info.= '<input type="submit" value="'.$LANG->getLL('general.action.import.xml.button.title').'" name="import_excel" /><input type="file" size="60" name="uploaded_import_file" />';
		$info .= '<br /><br /><input type="checkbox" value="1" name="check_exports" /> ' . $LANG->getLL('export.xml.check_exports.title') . '<br />';

			// Read uploaded file:
		if (t3lib_div::_POST('import_excel') && $_FILES['uploaded_import_file']['tmp_name'] && is_uploaded_file($_FILES['uploaded_import_file']['tmp_name'])) {
			$uploadedTempFile = t3lib_div::upload_to_tempfile($_FILES['uploaded_import_file']['tmp_name']);

			$factory=t3lib_div::makeInstance('tx_l10nmgr_translationDataFactory');
			//TODO: catch exeption
			$translationData=$factory->getTranslationDataFromExcelXMLFile($uploadedTempFile);
			$translationData->setLanguage($this->sysLanguage);

			t3lib_div::unlink_tempfile($uploadedTempFile);

			$service->saveTranslation($l10ncfgObj,$translationData);

			$info.='<br/><br/>'.$this->doc->icons(1).$LANG->getLL('import.success.message').'<br/><br/>';
		}

			// If export of XML is asked for, do that (this will exit and push a file for download)
		if (t3lib_div::_POST('export_excel')) {

			// Render the XML
			/** @var $viewClass tx_l10nmgr_excelXMLView */
			$viewClass=t3lib_div::makeInstance('tx_l10nmgr_excelXMLView',$l10ncfgObj,$this->sysLanguage);
			$export_xml_forcepreviewlanguage=intval(t3lib_div::_POST('export_xml_forcepreviewlanguage'));
			if ($export_xml_forcepreviewlanguage > 0) {
				$viewClass->setForcedSourceLanguage($export_xml_forcepreviewlanguage);
			}
			if ($this->MOD_SETTINGS['onlyChangedContent']) {
				$viewClass->setModeOnlyChanged();
			}
			if ($this->MOD_SETTINGS['noHidden']) {
				$viewClass->setModeNoHidden();
			}

			//Check the export
			if ((t3lib_div::_POST('check_exports')=='1') && ($viewClass->checkExports() == FALSE)) {
					/** @var $flashMessage t3lib_FlashMessage */
				$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $LANG->getLL('export.process.duplicate.message'), $LANG->getLL('export.process.duplicate.title'), t3lib_FlashMessage::INFO);
				$info .= $flashMessage->render();
				$info .= $viewClass->renderExports();
			} else {
				try {
					$filename = $this->downloadXML($viewClass);
						// Prepare a success message for display
					$link = sprintf('<a href="%s" target="_blank">%s</a>', t3lib_div::getIndpEnv('TYPO3_SITE_URL').$filename, $filename);
					$title = $GLOBALS['LANG']->getLL('export.download.success');
					$message = sprintf($GLOBALS['LANG']->getLL('export.download.success.detail'), $link);
					$status = t3lib_FlashMessage::OK;
				}
				catch (Exception $e) {
						// Prepare an error message for display
					$title = $GLOBALS['LANG']->getLL('export.download.error');
					$message = $e->getMessage() . ' (' . $e->getCode() . ')';
					$status = t3lib_FlashMessage::ERROR;
				}
					/** @var $flashMessage t3lib_FlashMessage */
				$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $message, $title, $status);
				$info .= $flashMessage->render();
				$info .= $viewClass->renderInternalMessagesAsFlashMessage($status);
			}
		}

		return $info;
	}

	/**
	 * Creating module content
	 *
	 * @param	array		Localization Configuration record
	 * @return	void
	 */
	function moduleContent($l10ncfgObj) {
		global $LANG,$BE_USER;

		switch ($this->MOD_SETTINGS["action"]) {
			case 'inlineEdit':
			case 'link':
					/** @var $htmlListView tx_l10nmgr_l10nHTMLListView */
				$htmlListView = t3lib_div::makeInstance('tx_l10nmgr_l10nHTMLListView', $l10ncfgObj, $this->sysLanguage);
				$subheader=$LANG->getLL('inlineEdit');
				$subcontent = '';

				if ($this->MOD_SETTINGS["action"]=='inlineEdit') {
					$subheader = $LANG->getLL('link');
					$subcontent = $this->inlineEditAction($l10ncfgObj);
					$htmlListView->setModeWithInlineEdit();
				}
				// Render the module content (for all modes):
				//*******************************************

				if ($this->MOD_SETTINGS["onlyChangedContent"]) {
					$htmlListView->setModeOnlyChanged();
				}
				if ($this->MOD_SETTINGS["noHidden"]) {
					$htmlListView->setModeNoHidden();
				}
				if ($this->MOD_SETTINGS["action"]=='link') {
					$htmlListView->setModeShowEditLinks();
				}
				$subcontent .= $htmlListView->renderOverview();
			break;

			case 'export_excel':
				$subheader  = $LANG->getLL('export_excel');
				$subcontent = $this->excelExportImportAction($l10ncfgObj);
			break;

			case 'export_xml':		// XML import/export
				$prefs['utf8']=t3lib_div::_POST('check_utf8');
				$prefs['noxmlcheck']=t3lib_div::_POST('no_check_xml');
				$BE_USER->pushModuleData('l10nmgr/cm1/prefs', $prefs);

				$subheader  = $LANG->getLL('export_xml');
				$subcontent = $this->catXMLExportImportAction($l10ncfgObj);
			break;

			DEFAULT:	// Default display:
				$subcontent = '<input type="submit" value="'.$LANG->getLL('general.action.refresh.button.title').'" name="_" />';
			break;
		} //switch block

		$this->content .= $this->doc->section($subheader,$subcontent);
	}

	/**
	 * Sends download header and calls render method of the view.
	 * Used for excelXML and CATXML.
	 *
	 * @param tx_l10nmgr_abstractExportView $xmlView Object for generating the XML export
	 * @return string $filename
	 */
	protected function downloadXML(tx_l10nmgr_abstractExportView $xmlView) {
			// Save content to the disk and get the file name
		$filename = $xmlView->render();

		return $filename;
	}

	/**
	 * @param string Mime type
	 * @param string Filename
	 * @return void
	 */
	protected function sendDownloadHeader($mimeType,$filename) {
			// Creating output header:
		Header('Charset: utf-8');
		Header('Content-Type: '.$mimeType);
		Header('Content-Disposition: attachment; filename='.$filename);
	}

	/**
	 * Uploads the XML export to the FTP server
	 *
	 * @param tx_l10nmgr_CATXMLView $xmlView Object for generating the XML export
	 * @return string The file name, if successful
	 * @throws Exception
	 */
	protected function uploadToFtp(tx_l10nmgr_CATXMLView $xmlView) {
			// Save content to the disk and get the file name
		$filename = $xmlView->render();
		$xmlFileName = basename($filename);

			// Try connecting to FTP server and uploading the file
			// If any step fails, an exception is thrown
		$connection = ftp_connect($this->lConf['ftp_server']);
		if ($connection) {
			if (@ftp_login($connection, $this->lConf['ftp_server_username'], $this->lConf['ftp_server_password'])) {
				if (ftp_put($connection, $this->lConf['ftp_server_path'] . $xmlFileName, $filename, FTP_BINARY)) {
					ftp_close($connection);
				} else {
					ftp_close($connection);
					throw new Exception(sprintf($GLOBALS['LANG']->getLL('export.ftp.upload_failed'), $filename, $this->lConf['ftp_server_path']), 1326906926);
				}
			} else {
				ftp_close($connection);
				throw new Exception(sprintf($GLOBALS['LANG']->getLL('export.ftp.login_failed'), $this->lConf['ftp_server_username']), 1326906772);
			}
		} else {
			throw new Exception($GLOBALS['LANG']->getLL('export.ftp.connection_failed'), 1326906675);
		}
			// If everything went well, return the file's base name
		return $xmlFileName;
	}

	/**
	 * The function emailNotification sends an email with a translation job to the recipient specified in the extension config.
	 *
	 * @param string $xmlFileName Name of the XML file
	 * @param tx_l10nmgr_l10nConfiguration $l10nmgrCfgObj L10N Manager configuration object
	 * @param integer $tlang ID of the language to translate to
	 * @return void
	 */
	protected function emailNotification($xmlFileName, $l10nmgrCfgObj, $tlang) {
			// If at least a recipient is indeed defined, proceed with sending the mail
		$recipients = t3lib_div::trimExplode(',', $this->lConf['email_recipient']);
		if (count($recipients) > 0) {
			$fullFilename = PATH_site . 'uploads/tx_l10nmgr/saved_files/'. $xmlFileName;

				// Get source & target language ISO codes
			$sourceStaticLangArr = t3lib_BEfunc::getRecord('static_languages', $l10nmgrCfgObj->l10ncfg['sourceLangStaticId'], 'lg_iso_2');
			$targetStaticLang = t3lib_BEfunc::getRecord('sys_language', $tlang, 'static_lang_isocode');
			$targetStaticLangArr = t3lib_BEfunc::getRecord('static_languages', $targetStaticLang['static_lang_isocode'], 'lg_iso_2');
			$sourceLang = $sourceStaticLangArr['lg_iso_2'];
			$targetLang = $targetStaticLangArr['lg_iso_2'];
				// Collect mail data
			$fromMail = $this->lConf['email_sender'];
			$fromName = $this->lConf['email_sender_name'];
			$organisation = $this->lConf['email_sender_organisation'];
			$subject = sprintf($GLOBALS['LANG']->getLL('email.suject.msg'), $sourceLang, $targetLang, $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
				// Assemble message body
			$message = array(
				'msg1' => $GLOBALS['LANG']->getLL('email.greeting.msg'),
				'msg2' => '',
				'msg3' => sprintf($GLOBALS['LANG']->getLL('email.new_translation_job.msg'), $sourceLang, $targetLang, $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']),
				'msg4' => $GLOBALS['LANG']->getLL('email.info.msg'),
				'msg5' => $GLOBALS['LANG']->getLL('email.info.import.msg'),
				'msg6' => '',
				'msg7' => $GLOBALS['LANG']->getLL('email.goodbye.msg'),
				'msg8' => $fromName,
				'msg9' => '--',
				'msg10' => $GLOBALS['LANG']->getLL('email.info.exportef_file.msg'),
				'msg11' => $xmlFileName,
			);
			if ($this->lConf['email_attachment']) {
				$message['msg3'] = sprintf($GLOBALS['LANG']->getLL('email.new_translation_job_attached.msg'), $sourceLang, $targetLang, $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
			}
			$msg = implode(chr(10), $message);

				// Instantiate the mail object, set all necessary properties and send the mail
			if (class_exists('t3lib_mail_Message')) {
					/** @var $mailObject t3lib_mail_Message */
				$mailObject = t3lib_div::makeInstance('t3lib_mail_Message');
				$mailObject->setFrom(array($fromMail => $fromName));
				$mailObject->setTo($recipients);
				$mailObject->setSubject($subject);
				$mailObject->setFormat('text/plain');
				$mailObject->setBody($msg);
				if ($this->lConf['email_attachment']) {
					$attachment = Swift_Attachment::fromPath($fullFilename, 'text/xml');
					$mailObject->attach($attachment);
				}
				$mailObject->send();

				// @TODO: Compatibility with TYPO3 4.4 or lower. Should be removed at some point.
			} else {
					/** @var $email t3lib_htmlmail */
				$email = t3lib_div::makeInstance('t3lib_htmlmail');
				$email->start();
				$email->useQuotedPrintable();
				$email->from_email = $fromMail;
				$email->from_name = $fromName;
				$email->replyto_email = $fromMail;
				$email->replyto_name = $fromName;
				$email->organisation = $organisation;
				$email->subject = $subject;
				$email->addPlain($msg);
				if ($this->lConf['email_attachment']) {
					$email->addAttachment($fullFilename);
				}
				$email->send(implode(',', $recipients));
			}
		}
	}

	/**
	 * Diff-compare markup
	 *
	 * @param	string		Old content
	 * @param	string		New content
	 * @return	string		Marked up string.
	 */
	function diffCMP($old, $new) {
			// Create diff-result:
			/** @var $t3lib_diff_Obj t3lib_diff */
		$t3lib_diff_Obj = t3lib_div::makeInstance('t3lib_diff');
		return $t3lib_diff_Obj->makeDiffDisplay($old,$new);
	}

	/**
	 * The function loadExtConf loads the extension configuration.
	 * @return      void
	 *
	*/
	function loadExtConf() {
		// Load the configuration
		$this->lConf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['l10nmgr'] );
	}

	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	protected function getButtons()	{
		$buttons = array();

		$buttons['reload'] = '<a href="' . $GLOBALS['MCONF']['_'] . '" title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.reload', TRUE) . '">' .
			t3lib_iconWorks::getSpriteIcon('actions-system-refresh') .
			'</a>';

			// Shortcut
		if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
			$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}

		return $buttons;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/cm1/index.php']);
}


	// Make instance:
	/** @var $SOBE tx_l10nmgr_cm1 */
$SOBE = t3lib_div::makeInstance('tx_l10nmgr_cm1');
$SOBE->init();

$SOBE->main();
$SOBE->printContent();
?>
