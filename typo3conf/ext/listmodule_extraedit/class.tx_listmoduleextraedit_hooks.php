<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Sven Juergens <post@blue-side.de>
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
 * Hook methods for the list module
 *
 * @author	Sven Juergens <post@blue-side.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */


require_once(PATH_typo3 . 'interfaces/interface.localrecordlist_actionsHook.php');

class tx_listmoduleextraedit_hooks implements localRecordList_actionsHook
{

	/**
	 * modifies clip-icons
	 *
	 * @param	string		the current database table
	 * @param	array		the current record row
	 * @param	array		the default clip-icons to get modified
	 * @param	object		Instance of calling object
	 * @return	array		the modified clip-icons
	 */
	public function makeClip($table, $row, $cells, &$parentObject)
	{

		return $cells;
	}


	/**
	 * modifies control-icons
	 *
	 * @param	string		the current database table
	 * @param	array		the current record row
	 * @param	array		the default control-icons to get modified
	 * @param	object		Instance of calling object
	 * @return	array		the modified control-icons
	 */
	public function makeControl($table, $row, $cells, &$parentObject)
	{

		return $cells;
	}


	/**
	 * modifies Web>List header row columns/cells
	 *
	 * @param	string		the current database table
	 * @param	array		Array of the currently displayed uids of the table
	 * @param	array		An array of rendered cells/columns
	 * @param	object		Instance of calling (parent) object
	 * @return	array		Array of modified cells/columns
	 */
	public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject)
	{
		return $headerColumns;
	}

	/**
	 * modifies Web>List header row clipboard icons
	 *
	 * @param	string		the current database table
	 * @param	array		Array of the currently displayed uids of the table
	 * @param	array		An array of the current clipboard icons
	 * @param	object		Instance of calling (parent) object
	 * @return	array		Array of modified clipboard icons
	 */
	public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject)
	{

		if ($parentObject->table) {
			if (isset ($parentObject->setFields[$table]) && !empty ($parentObject->setFields[$table][0])) {
				$fields = implode(',', $parentObject->setFields[$table]);
				$cells['extraEdit'] = str_replace('=edit', '=edit&columnsOnly=' . $fields, $cells['edit']);

				$titleText = $GLOBALS['LANG']->getLL('clip_editMarked', TRUE);
				$newTitleText = $GLOBALS['LANG']->sL('LLL:EXT:listmodule_extraedit/locallang.xml:editSetFieldsInMarkedRecords', TRUE);

				$cells['extraEdit'] = str_replace($titleText, $newTitleText, $cells['extraEdit']);

			}
		}
		return $cells;
	}

}

?>
