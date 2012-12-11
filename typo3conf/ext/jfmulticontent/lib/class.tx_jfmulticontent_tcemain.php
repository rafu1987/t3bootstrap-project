<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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
 * This class implements a hook to TCEmain to ensure that data is correctly
 * inserted to pages when using TemplaVoila. It disables automatic TemplaVoila
 * element referencing for content elements that are part of jfmulticontent post.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jfmulticontent
 */
class tx_jfmulticontent_tcemain
{
	/**
	 * Checks if the colPos will be manipulate and if TemplaVoila references should be disabled for this record
	 *
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param integer $id
	 * @param t3lib_TCEmain $pObj
	 * @see tx_templavoila_tcemain::processDatamap_afterDatabaseOperations()
	 */
	public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, $table, $id, t3lib_TCEmain &$pObj) {
		if ($incomingFieldArray['list_type'] != 'jfmulticontent_pi1') {
			if (is_array($pObj->datamap['tt_content'])) {
				foreach ($pObj->datamap['tt_content'] as $key => $val) {
					if ($val['list_type'] == 'jfmulticontent_pi1' && $val['tx_jfmulticontent_view'] == 'irre') {
						// Change the colPos of the IRRE tt_content values
						$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['jfmulticontent']);
						$incomingFieldArray['colPos'] = $confArr['colPosOfIrreContent'];
						// Workaround for templavoila
						if (t3lib_extMgm::isLoaded('templavoila')) {
							$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_templavoila_tcemain']['doNotInsertElementRefsToPage'] = TRUE;
						}
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tcemain.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jfmulticontent/lib/class.tx_jfmulticontent_tcemain.php']);
}

?>