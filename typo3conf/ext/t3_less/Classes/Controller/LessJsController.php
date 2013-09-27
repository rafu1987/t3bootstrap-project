<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013 David Greiner <hallo@davidgreiner.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * ************************************************************* */

/**
 *
 *
 * @package TYPO3
 * @subpackage t3_less
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author  David Greiner <hallo@davidgreiner.de>
 */
class Tx_T3Less_Controller_LessJsController extends Tx_T3Less_Controller_BaseController
{

	/**
	 * lessJs
	 * includes all less-files from defined lessfolder to head and less.js to the footer
	 */
	public function lessJs( $files )
	{
		//respect given sort order defined in TS
		usort( $files, array( $this, 'getSortOrderJs' ) );
		//files in defined lessfolder?
		if( $files )
		{
			foreach( $files as $lessFile )
			{
				$filename = array_pop( explode( '/', $lessFile ) );

				$excludeFromPageRenderer = $this->configuration['jscompiler']['filesettings'][substr( $filename, 0, -5 )]['excludeFromPageRenderer'];

				if( !$excludeFromPageRenderer || $excludeFromPageRenderer == 0 )
				{
					// array with filesettings from TS
					$tsOptions = $this->configuration['jscompiler']['filesettings'][substr( $filename, 0, -5 )];

					$GLOBALS['TSFE']->getPageRenderer()->addCssFile(
						$lessFile, $rel = 'stylesheet/less', $media = $tsOptions['media'] ? $tsOptions['media'] : 'all', $title = $tsOptions['title'] ? $tsOptions['title'] : '', $compress = FALSE, $forceOnTop = FALSE, $allWrap = $tsOptions['allWrap'] ? $tsOptions['allWrap'] : '', $excludeFromConcatenation = TRUE
					);
				}
			}
			//include less.js to footer
			$GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile( $file = Tx_T3Less_Utility_Utilities::getPath( 'EXT:t3_less/Resources/Public/Js/' . $this->configuration['other']['lessJsScriptPath'], true ), $type = 'text/javascript', $compress = TRUE, $forceOnTop = FALSE );
		}
	}

	/**
	 * getSortOrderJs
	 * little helper function to respect given sort order defined in TS by using jscompiler
	 * @param type $file1
	 * @param type $file2
	 * @return int
	 */
	protected function getSortOrderJs( $file1, $file2 )
	{
		$fileSettings = $this->configuration['jscompiler']['filesettings'];
		$tsOptions1 = $fileSettings[substr( array_pop( explode( '/', $file1 ) ), 0, -5 )];
		$tsOptions2 = $fileSettings[substr( array_pop( explode( '/', $file2 ) ), 0, -5 )];
		$sortOrder1 = $tsOptions1['sortOrder'] ? $tsOptions1['sortOrder'] : 0;
		$sortOrder2 = $tsOptions2['sortOrder'] ? $tsOptions2['sortOrder'] : 0;
		$forceOnTop1 = $tsOptions1['forceOnTop'] ? $tsOptions1['forceOnTop'] : FALSE;
		$forceOnTop2 = $tsOptions2['forceOnTop'] ? $tsOptions2['forceOnTop'] : FALSE;
		$sortDirection = 1;
		if( $forceOnTop1 || $forceOnTop2 )
		{
			$sortDirection = -1;
		}

		if( $sortOrder1 == $sortOrder2 )
		{
			return 0;
		}
		return $sortDirection * (($sortOrder1 < $sortOrder2) ? -1 : 1);
	}

}

?>
