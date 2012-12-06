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

require_once(t3lib_extMgm::extPath('l10nmgr').'views/class.tx_l10nmgr_abstractExportView.php');

/**
 * excelXML: Renders the excel XML
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_l10nmgr
 */
class tx_l10nmgr_excelXMLView extends tx_l10nmgr_abstractExportView{
	//internal flags:
	var $modeOnlyChanged=FALSE;

	var $exportType = '0';

    /**
    * @var integer         $forcedSourceLanguage           Overwrite the default language uid with the desired language to export
    */
    var $forcedSourceLanguage = false;

	function tx_l10nmgr_excelXMLView($l10ncfgObj, $sysLang) {
		parent::__construct($l10ncfgObj, $sysLang);
	}

	/**
	 * Render the excel XML export
	 *
	 * @param	array		Translation data for configuration
	 * @return	string		HTML content
	 */
	function render()	{
		global $LANG,$BE_USER;
		$sysLang=$this->sysLang;
		$accumObj=$this->l10ncfgObj->getL10nAccumulatedInformationsObjectForLanguage($sysLang);
		if ($this->forcedSourceLanguage) {
            $accumObj->setForcedPreviewLanguage($this->forcedSourceLanguage);
        }
		$accum=$accumObj->getInfoArray();

		$output = array();

			// Traverse the structure and generate HTML output:
		foreach($accum as $pId => $page)	{

			$output[]= '
			<!-- Page header -->
		   <Row>
		    <Cell ss:Index="2" ss:StyleID="s35"><Data ss:Type="String">'.htmlspecialchars($page['header']['title'].' ['.$pId.']').'</Data></Cell>
		    <Cell ss:StyleID="s35"></Cell>
		    <Cell ss:StyleID="s35"></Cell>
		    <Cell ss:StyleID="s35"></Cell>
		    '.($page['header']['prevLang'] ? '<Cell ss:StyleID="s35"></Cell>' : '').'
		   </Row>';

			$output[]= '
			<!-- Field list header -->
		   <Row>
		    <Cell ss:Index="2" ss:StyleID="s38"><Data ss:Type="String">Fieldname:</Data></Cell>
		    <Cell ss:StyleID="s38"><Data ss:Type="String">Source language:</Data></Cell>
			<Cell ss:StyleID="s38"><Data ss:Type="String">Alternative source language:</Data></Cell>
		    <Cell ss:StyleID="s38"><Data ss:Type="String">Translation:</Data></Cell>
		    <Cell ss:StyleID="s38"><Data ss:Type="String">Difference since last tr.:</Data></Cell>
		   </Row>';

			foreach($accum[$pId]['items'] as $table => $elements)	{
				foreach($elements as $elementUid => $data)	{
					if (is_array($data['fields']))	{

						$fieldsForRecord = array();
						foreach($data['fields'] as $key => $tData)	{
							if (is_array($tData))	{
								list(,$uidString,$fieldName) = explode(':',$key);
								list($uidValue) = explode('/',$uidString);

								//DZ
                                if ( ($this->forcedSourceLanguage && isset($tData['previewLanguageValues'][$this->forcedSourceLanguage])) || $this->forcedSourceLanguage === false) {
                                //DZ
                                if ($this->forcedSourceLanguage) {
                                    $dataForTranslation = $tData['previewLanguageValues'][$this->forcedSourceLanguage];
                                    $sourceColState = 'ss:Hidden="1" ss:AutoFitWidth="0"';
                                    $altSourceColState = 'ss:AutoFitWidth="0" ss:Width="233.0"';
                                }
                                else {
                                    $dataForTranslation = $tData['defaultValue'];
                                    $sourceColState = 'ss:AutoFitWidth="0" ss:Width="233.0"';
                                    $altSourceColState = 'ss:Hidden="1" ss:AutoFitWidth="0"';
                                }

								$diff = '';
								$noChangeFlag = !strcmp(trim($tData['diffDefaultValue']),trim($tData['defaultValue']));
								if ($uidValue==='NEW')	{
									$diff = htmlspecialchars('[New value]');
								} elseif (!$tData['diffDefaultValue']) {
									$diff = htmlspecialchars('[No diff available]');
								} elseif ($noChangeFlag)	{
									$diff = htmlspecialchars('[No change]');
								} else {
									$diff = $this->diffCMP($tData['diffDefaultValue'],$tData['defaultValue']);
									$diff = str_replace('<span class="diff-r">','<Font html:Color="#FF0000" xmlns="http://www.w3.org/TR/REC-html40">',$diff);
									$diff = str_replace('<span class="diff-g">','<Font html:Color="#00FF00" xmlns="http://www.w3.org/TR/REC-html40">',$diff);
									$diff = str_replace('</span>','</Font>',$diff);
								}
								$diff.= ($tData['msg']?'[NOTE: '.htmlspecialchars($tData['msg']).']':'');

								if (!$this->modeOnlyChanged || !$noChangeFlag)	{
									reset($tData['previewLanguageValues']);
									$fieldsForRecord[]= '
								<!-- Translation row: -->
								   <Row ss:StyleID="s25">
								    <Cell><Data ss:Type="String">'.htmlspecialchars('translation['.$table.']['.$elementUid.']['.$key.']').'</Data></Cell>
								    <Cell ss:StyleID="s26"><Data ss:Type="String">'.htmlspecialchars($fieldName).'</Data></Cell>
								    <Cell ss:StyleID="s27"><Data ss:Type="String">'.str_replace(chr(10),'&#10;',htmlspecialchars($tData['defaultValue'])).'</Data></Cell>
									<Cell ss:StyleID="s27"><Data ss:Type="String">'.str_replace(chr(10),'&#10;',htmlspecialchars(current($tData['previewLanguageValues']))).'</Data></Cell>
								    <Cell ss:StyleID="s39"><Data ss:Type="String">'.str_replace(chr(10),'&#10;',htmlspecialchars($tData['translationValue'])).'</Data></Cell>
								    <Cell ss:StyleID="s27"><Data ss:Type="String">'.$diff.'</Data></Cell>
								   </Row>
									';
								}
							} else {
                                $fieldsForRecord[]= '
                                <!-- Translation row: -->
                                <Row ss:StyleID="s25">
                                <Cell><Data ss:Type="String">'.htmlspecialchars('translation['.$table.']['.$elementUid.']['.$key.']').'</Data></Cell>
                                <Cell ss:StyleID="s26"><Data ss:Type="String">'.htmlspecialchars($fieldName).'</Data></Cell>
                                <Cell ss:StyleID="s40"><Data ss:Type="String">'.$LANG->getLL('export.process.error.empty.message').'!</Data></Cell>
                                <Cell ss:StyleID="s39"><Data ss:Type="String"></Data></Cell>
                                <Cell ss:StyleID="s27"><Data ss:Type="String"></Data></Cell>
                                '.($page['header']['prevLang'] ? '<Cell ss:StyleID="s27"><Data ss:Type="String">'.str_replace(chr(10),'&#10;',htmlspecialchars(current($tData['previewLanguageValues']))).'</Data></Cell>' : '').'
                                </Row>
                                ';
                            }
							}

						}

						if (count($fieldsForRecord))	{
							$output[]= '
							<!-- Element header -->
						   <Row>
						    <Cell ss:Index="2" ss:StyleID="s37"><Data ss:Type="String">Element: '.htmlspecialchars($table.':'.$elementUid).'</Data></Cell>
						    <Cell ss:StyleID="s37"></Cell>
						    <Cell ss:StyleID="s37"></Cell>
						    <Cell ss:StyleID="s37"></Cell>
						    '.($page['header']['prevLang'] ? '<Cell ss:StyleID="s37"></Cell>' : '').'
						   </Row>
							';

							$output = array_merge($output, $fieldsForRecord);
						}
					}
				}
			}

				$output[]= '
				<!-- Spacer row -->
			   <Row>
			    <Cell ss:Index="2"><Data ss:Type="String"></Data></Cell>
			   </Row>
				';
		}
			// Provide a hook for specific manipulations before building the actual XML
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['exportExcelXmlPreProcess'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['l10nmgr']['exportExcelXmlPreProcess'] as $classReference) {
				$processingObject = t3lib_div::getUserObj($classReference);
				$output = $processingObject->processBeforeExportingExcelXml($output, $this);
			}
		}

		$excelXML = t3lib_div::getUrl(t3lib_extMgm::extPath('l10nmgr') . 'views/excelXML/excel_template.xml');
		$excelXML = str_replace('###INSERT_ROWS###',implode('', $output), $excelXML);
		$excelXML = str_replace('###INSERT_ROW_COUNT###',count($output), $excelXML);
        $excelXML = str_replace('###SOURCE_COL_STATE###',$sourceColState, $excelXML);
        $excelXML = str_replace('###ALT_SOURCE_COL_STATE###',$altSourceColState, $excelXML);
		$excelXML = str_replace('###INSERT_INFORMATION###', $this->renderInternalMessage(), $excelXML);

		return $this->saveExportFile($excelXML);
	}

	/**
	 * Renders the list of internal message as XML tags
	 *
	 * @return string The XML structure to output
	 */
	protected function renderInternalMessage() {
		$messages = '';
		foreach ($this->internalMessages as $messageInformation) {
			$messages .=  "\n\t\t\t" . '<Row>' . "\n\t\t\t\t" . '<Cell ss:Index="1" ss:StyleID="s37"><Data ss:Type="String">Skipped item	</Data></Cell>' . "\n\t\t\t" . '</Row>';
			$messages .=  "\n\t\t\t" . '<Row>' . "\n\t\t\t\t" . '<Cell ss:Index="2" ss:StyleID="s26"><Data ss:Type="String">Description</Data></Cell>' . "\n\t\t\t\t" . '<Cell ss:StyleID="s27"><Data ss:Type="String">' . $messageInformation['message'] . '</Data></Cell>' . "\n\t\t\t" . '</Row>';
			$messages .=  "\n\t\t\t" . '<Row>' . "\n\t\t\t\t" . '<Cell ss:Index="2" ss:StyleID="s26"><Data ss:Type="String">Key</Data></Cell>' . "\n\t\t\t\t" . '<Cell ss:StyleID="s27"><Data ss:Type="String">' . $messageInformation['key'] . '</Data></Cell>' . "\n\t\t\t" . '</Row>';
		}
		return $messages;
	}

	/**
      * Force a new source language to export the content to translate
      *
      * @param        integer         $id
      * @access       public
      * @return       void
    */
        function setForcedSourceLanguage($id) {
                $this->forcedSourceLanguage = $id;
        }


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/excelXML/class.tx_l10nmgr_excelXMLView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/views/excelXML/class.tx_l10nmgr_excelXMLView.php']);
}


?>
