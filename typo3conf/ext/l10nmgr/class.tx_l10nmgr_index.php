<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Cleaner module: Building index for translation
 * User function called from tx_lowlevel_cleaner_core configured in ext_localconf.php
 * See system extension, lowlevel!
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_l10nmgr_index extends tx_lowlevel_cleaner_core
 *   78:     function tx_l10nmgr_index()
 *   98:     function main()
 *  137:     function main_parseTreeCallBack($tableName,$uid,$echoLevel,$versionSwapmode,$rootIsVersion)
 *  172:     function main_autoFix($resultArray)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */





	// Include API
require_once (t3lib_extMgm::extPath('l10nmgr').'models/tools/class.tx_l10nmgr_tools.php');


/**
 * Finding unused content elements
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_lowlevel
 */
class tx_l10nmgr_index extends tx_lowlevel_cleaner_core {
	/**
	 * @var array Extension's configuration as from the EM
	 */
	protected $extensionConfiguration = array();

	/**
	 * @var array List of not allowed doktypes
	 */
	var $disallowDoktypes = array('--div--','255');

	/**
	 * @var bool Check reference index
	 */
	var $checkRefIndex = FALSE;

	/**
	 * @var bool
	 */
	var $genTree_traverseDeleted = FALSE;

	/**
	 * @var bool
	 */
	var $genTree_traverseVersions = FALSE;


	/**
	 * Constructor
	 *
	 * @return	void
	 */
	function tx_l10nmgr_index()	{
			// Load the extension's configuration
		$this->extensionConfiguration = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['l10nmgr'] );
		$this->disallowDoktypes = t3lib_div::trimExplode(',', $this->extensionConfiguration['disallowDoktypes']);

		parent::tx_lowlevel_cleaner_core();

			// Setting up help:
		$this->cli_options[] = array('--echotree level', 'When "level" is set to 1 or higher you will see the page of the page tree outputted as it is traversed. A value of 2 for "level" will show even more information.');
		$this->cli_options[] = array('--pid id_list', 'Setting start page in page tree. Default is the page tree root, 0 (zero). You can specify a list of ids, eg "22,7,3" if you like. If you specify a negative id (eg. -1) nothing is index, but the index table is just flushed.');
		$this->cli_options[] = array('--workspace id', 'Setting workspace uid for the session. Default is "0" for live workspace. The translation index depends on the workspace.');
		$this->cli_options[] = array('--depth int', 'Setting traversal depth. 0 (zero) will only analyse start page (see --pid), 1 will traverse one level of subpages etc.');
		$this->cli_options[] = array('--noFlush', 'If set, the index for the workspace will not be flushed. Normally you want to flush the index as a part of the process to make sure the rebuild of the index is empty before building it. But in cases you build individual parts of the tree you may like to use this option.');
                $this->cli_options[] = array('--bypassFilter', 'If set, the external filter will not be called. The external filter allows other extensions to block certain records from getting processed. For instance TemplaVoila provides such a filter than will make sure records which are not used on a page are not indexed.');

		$this->cli_help['name'] = 'tx_l10nmgr_index -- Building translation index';
		$this->cli_help['description'] = trim('
Traversing page tree and building an index of translation needs
');

		$this->cli_help['examples'] = '';
	}

	/**
	 * @return	array
	 */
	function main() {
		global $TYPO3_DB;

			// Initialize result array:
		$resultArray = array(
			'message' => $this->cli_help['name'].chr(10).chr(10).$this->cli_help['description'],
			'headers' => array(
				'index' => array('Full index of translation','NEEDS MUCH MORE WORK....',1),
			),
			'index' => array(),
		);

		$startingPoints = t3lib_div::intExplode(',',$this->cli_argValue('--pid'));
		$workspaceID = $this->cli_isArg('--workspace') ? t3lib_div::intInRange($this->cli_argValue('--workspace'),-1) : 0;
		$depth = $this->cli_isArg('--depth') ? t3lib_div::intInRange($this->cli_argValue('--depth'),0) : 1000;

		if ($workspaceID!=0)	{
			$GLOBALS['BE_USER']->setWorkspace($workspaceID);
			if ($GLOBALS['BE_USER']->workspace != $workspaceID)	{
				die('Workspace '.$workspaceID.' did not exist!'.chr(10));
			}
		}

		$this->resultArray = &$resultArray;
                foreach($startingPoints as $pidPoint)   {
                        if ($pidPoint>=0)       $this->genTree($pidPoint,$depth,(int)$this->cli_argValue('--echotree'),'main_parseTreeCallBack');
                }

		return $resultArray;
	}

	/**
	 * Call back function for page tree traversal!
	 *
	 * @param	string		Table name
	 * @param	integer		UID of record in processing
	 * @param	integer		Echo level  (see calling function
	 * @param	string		Version swap mode on that level (see calling function
	 * @param	integer		Is root version (see calling function
	 * @return	void
	 */
	function main_parseTreeCallBack($tableName,$uid,$echoLevel,$versionSwapmode,$rootIsVersion)	{
		global $TCA;

		if ($tableName=='pages' && $uid>0)	{
			$pageId = $uid;
			$flexFormDiff = array();
			$sysLang = 1;
			$excludeIndex = array();

			if (!$versionSwapmode)	{
					// Init:
				$t8Tools = t3lib_div::makeInstance('tx_l10nmgr_tools');
				$t8Tools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.
				$t8Tools->bypassFilter = $this->cli_isArg('--bypassFilter') ? TRUE : FALSE;

				$pageRecord = t3lib_BEfunc::getRecord('pages',$uid);
				if (!in_array($pageRecord['doktype'], $this->disallowDoktypes) && !isset($excludeIndex['pages:'.$pageId]))	{

					$accum['header']['title']	= $pageRecord['title'];
					$accum['items'] = $t8Tools->indexDetailsPage($pageId);

					$this->resultArray['index'][$uid] = $accum;
				}
			} else {
				if ($echoLevel>2) echo chr(10).'[tx_templavoila_unusedce:] Did not check page - was on offline page.';
			}
		}
	}

	/**
	 * Mandatory autofix function
	 * Will run auto-fix on the result array. Echos status during processing.
	 *
	 * @param	array		Result array from main() function
	 * @return	void
	 */
	function main_autoFix($resultArray)	{
			// Init:
		$t8Tools = t3lib_div::makeInstance('tx_l10nmgr_tools');
		$t8Tools->verbose = FALSE;	// Otherwise it will show records which has fields but none editable.

                if (!$this->cli_isArg('--noFlush'))     {
                        echo 'Flushing translation index for workspace '.$GLOBALS['BE_USER']->workspace.chr(10);
                        $t8Tools->flushIndexOfWorkspace($GLOBALS['BE_USER']->workspace);
                } else {
                        echo 'Did NOT flush translation index for workspace '.$GLOBALS['BE_USER']->workspace.' since it was disabled by --noFlush'.chr(10);
                }

		foreach($this->resultArray['index'] as $pageId => $accum)	{
			echo 'Adding entries for page '.$pageId.' "'.$accum['header']['title'].'":'.chr(10);
			if (is_array($accum['items']))	{
				foreach($accum['items'] as $tt => $rr)	{
					foreach($rr as $rUid => $rDetails)	{
						$t8Tools->updateIndexTableFromDetailsArray($rDetails,TRUE);
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.tx_l10nmgr_index.php'])        {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/l10nmgr/class.tx_l10nmgr_index.php']);
}
?>
