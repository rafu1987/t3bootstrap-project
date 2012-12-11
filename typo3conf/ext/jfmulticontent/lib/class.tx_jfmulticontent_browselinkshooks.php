<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Juergen Furrer <juergen.furrer@gmail.com>
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
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_jfmulticontent
 */
class tx_jfmulticontent_browselinkshooks implements t3lib_browseLinksHook {

	protected $invokingObject;
	protected $mode;
	protected $act;
	protected $bparams;
	
	protected $browseLinks;

	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	parent browse_links object
	 * @param	array		additional parameters
	 * @return	void
	 */
	public function init($parentObject, $additionalParameters) {
		$invokingObjectClass = get_class($parentObject);
		$this->invokingObject =& $parentObject;
		$this->mode =& $this->invokingObject->mode;
		$this->act =& $this->invokingObject->act;
		$this->bparams =& $this->invokingObject->bparams;
		$this->invokingObject->anchorTypes[] = 'jfmulticontent';
		$GLOBALS['LANG']->includeLLFile('EXT:jfmulticontent/locallang.xml');
	}

	/**
	 * Adds new items to the currently allowed ones and returns them
	 * Replaces the 'file' item with the 'jfmulticontent' item
	 * Adds DAM upload tab
	 *
	 * @param	array	currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	public function addAllowedItems($currentlyAllowedItems) {
		$currentlyAllowedItems['jfmulticontent'] = 'jfmulticontent';
		return $currentlyAllowedItems;
	}

	/**
	 * Modifies the menu definition and returns it
	 * Adds definition of the 'jfmulticontent' menu item
	 *
	 * @param	array	menu definition
	 * @return	array	modified menu definition
	 */
	public function modifyMenuDefinition($menuDefinition) {
		$menuDef =& $menuDefinition;
		$menuDef['jfmulticontent']['isActive'] = $this->invokingObject->act == 'jfmulticontent';
		$menuDef['jfmulticontent']['label'] =  $GLOBALS['LANG']->sL('LLL:EXT:jfmulticontent/locallang_db.xml:browserlink_tab',1);
		$menuDef['jfmulticontent']['url'] = '#';
		$menuDef['jfmulticontent']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars('?act=jfmulticontent&editorNo='.$this->invokingObject->editorNo.'&contentTypo3Language='.$this->invokingObject->contentTypo3Language.'&contentTypo3Charset='.$this->invokingObject->contentTypo3Charset).'\');return false;"';
		return $menuDef;
	}

	/**
	 * Returns a new tab for the browse links wizard
	 * Returns the 'media' tab to the RTE link browser
	 *
	 * @param	string		current link selector action
	 * @return	string		a tab for the selected link action
	 */
	public function getTab($linkSelectorAction) {
		// Only return content if the media tab was called.
		if ($linkSelectorAction !== 'jfmulticontent') {
			return FALSE;
		}

		$this->browseLinks = t3lib_div::makeInstance('tx_rtehtmlarea_browse_links');
		$this->browseLinks->init();

		$content .= $this->browseLinks->addAttributesForm();

		$pagetree = t3lib_div::makeInstance('tx_rtehtmlarea_pageTree');
		$pagetree->ext_showNavTitle = $GLOBALS['BE_USER']->getTSConfigVal('options.pageTree.showNavTitle');
		$pagetree->ext_showPageId = $GLOBALS['BE_USER']->getTSConfigVal('options.pageTree.showPageIdWithTitle');
		$pagetree->addField('nav_title');
		$tree = $pagetree->getBrowsableTree();
		$cElements = $this->expandPage();


		// Outputting Temporary DB mount notice:
		if (intval($GLOBALS['BE_USER']->getSessionData('pageTree_temporaryMountPoint')))	{
			$link = '<a href="' . htmlspecialchars(t3lib_div::linkThisScript(array('setTempDBmount' => 0))) . '">' .
								$GLOBALS['LANG']->sl('LLL:EXT:lang/locallang_core.xml:labels.temporaryDBmount', 1) .
							'</a>';
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$link,
				'',
				t3lib_FlashMessage::INFO
			);
			$dbmount = $flashMessage->render();
		}

		$content .= '
		<!--
			Wrapper table for page tree / record list:
		-->
		<table border="0" cellpadding="0" cellspacing="0" id="typo3-linkPages">
			<tr>
				<td class="c-wCell" valign="top">' . $this->browseLinks->barheader($GLOBALS['LANG']->getLL('pageTree') . ':') . $dbmount . $tree . '</td>
				<td class="c-wCell" valign="top">' . $cElements . '</td>
			</tr>
		</table>
		';

		return $content;
	}

	/**
	 * Checks the current URL and determines what to do
	 * If the link was determined to be a file link, then set the action to 'media'
	 *
	 * @param	string		$href
	 * @param	string		$siteUrl
	 * @param	array		$info
	 * @return	array
	 */
	public function parseCurrentUrl($href, $siteUrl, $info) {
		$info['act'] = 'jfmulticontent';
		return $info;
	}

	/**
	 * For RTE: This displays all content elements on a page and lets you create a link to the element.
	 *
	 * @return	string		HTML output. Returns content only if the ->expandPage value is set (pointing to a page uid to show tt_content records from ...)
	 */
	function expandPage() {
		$out='';
		$expPageId = $this->browseLinks->expandPage;		// Set page id (if any) to expand

			// If there is an anchor value (content element reference) in the element reference, then force an ID to expand:
		if (!$this->browseLinks->expandPage && $this->browseLinks->curUrlInfo['cElement'])	{
			$expPageId = $this->browseLinks->curUrlInfo['pageid'];	// Set to the current link page id.
		}

			// Draw the record list IF there is a page id to expand:
		if ($expPageId && t3lib_utility_Math::canBeInterpretedAsInteger($expPageId) && $GLOBALS['BE_USER']->isInWebMount($expPageId)) {

				// Set header:
			$out .= $this->browseLinks->barheader($GLOBALS['LANG']->getLL('contentElements').':');

				// Create header for listing, showing the page title/icon:
			$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);
			$mainPageRec = t3lib_BEfunc::getRecordWSOL('pages',$expPageId);
			$picon = t3lib_iconWorks::getSpriteIconForRecord('pages', $mainPageRec);
			$picon .= t3lib_BEfunc::getRecordTitle('pages', $mainPageRec, TRUE);
			$out .= $picon.'<br />';

				// Look up tt_content elements from the expanded page:
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid,header,hidden,starttime,endtime,fe_group,CType,colPos,bodytext,tx_jfmulticontent_view,tx_jfmulticontent_pages,tx_jfmulticontent_contents',
							'tt_content',
							'pid='.intval($expPageId).
								t3lib_BEfunc::deleteClause('tt_content').
								t3lib_BEfunc::versioningPlaceholderClause('tt_content'),
							'',
							'colPos,sorting'
						);
			$cc = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

				// Traverse list of records:
			$c = 0;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$c++;
				$icon = t3lib_iconWorks::getSpriteIconForRecord('tt_content', $row);
				if ($this->browseLinks->curUrlInfo['act'] == 'page' && $this->browseLinks->curUrlInfo['cElement'] == $row['uid'])	{
					$arrCol='<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/blinkarrow_left.gif','width="5" height="9"').' class="c-blinkArrowL" alt="" />';
				} else {
					$arrCol='';
				}
					// Putting list element HTML together:
				$out.='<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/join'.($c==$cc?'bottom':'').'.gif','width="18" height="16"').' alt="" />'.
						$arrCol.
						'<a href="#" onclick="return link_typo3Page(\''.$expPageId.'\',\'#'.$row['uid'].'\');">'.
						$icon.
						t3lib_BEfunc::getRecordTitle('tt_content', $row, TRUE) .
						'</a><br />';

				$contents = array();
				// get all contents
				switch ($row['tx_jfmulticontent_view']) {
					case "page" : {
						$contents = t3lib_div::trimExplode(",", $row['tx_jfmulticontent_pages']);
						break;
					}
					case "content" : {
						$contents = t3lib_div::trimExplode(",", $row['tx_jfmulticontent_contents']);
						break;
					}
					case "irre" : {
						$resIrre = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', 'tx_jfmulticontent_irre_parentid='.intval($row['uid']).' AND deleted = 0 AND hidden = 0', '', '');
						while ($rowIrre = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resIrre)) {
							$contents[] = $rowIrre['uid'];
						}
						
						break;
					}
				}

				if (count($contents) > 0) {
					$out .= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/line.gif','width="18" height="16"').' alt="" />'.
							'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/blank.gif','width="18" height="16"').' alt="" />';
					foreach ($contents as $key => $content) {
						$out .= '<a href="#" onclick="return link_typo3Page(\''.$expPageId.'\',\'#jfmulticontent_c'.$row['uid'].'-'.($key+1).'\');">'.
								'&nbsp;' . ($key + 1) . '&nbsp;' .
								'</a>';
					}
					$out .= '<br/>';
				}
			}
		}
		return $out;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/class.tx_jfmulticontent_browselinkshooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/class.tx_jfmulticontent_browselinkshooks.php']);
}
?>