<?php

/**
 * A compiler using the original less.js via Node.js (if it is installed)
 *
 * @package TYPO3
 * @subpackage t3_less
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author  Thomas Heuer <technik@thomas-heuer.de>
 */
class Tx_T3Less_Controller_LessJsNodeController extends Tx_T3Less_Controller_BaseController
{

	public function isLesscInstalled()
	{
		exec( 'lessc', $output, $returnValue );
		if( $returnValue == 127 )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * lessc
	 *
	 * @return void
	 */
	public function lessc( $files )
	{

		// create outputfolder if it does not exist
		if( !is_dir( $this->outputfolder ) )
		{
			t3lib_div::mkdir_deep( '', $this->outputfolder );
		}

		// compile each less-file
		foreach( $files as $file )
		{
			//get only the name of less file
			$filename = array_pop( explode( '/', $file ) );

			$md5 = md5( $filename . md5_file( $file ) );

			$outputfile = $this->outputfolder . substr( $filename, 0, -5 ) . '_' . $md5 . '.css';

			if( $this->configuration['other']['forceMode'] && file_exists( $outputfile ) )
			{
				unlink( $outputfile );
			}

			if( !file_exists( $outputfile ) )
			{
				$compressed = $this->configuration['other']['compressed'] ? '--compress' : '';

				/* Define directories for @import scripts */
				$importDirs = '';
				if( isset( $this->configuration['other']['importDirs'] ) )
				{
					$importDirs = implode( ':', Tx_T3Less_Utility_Utilities::splitAndResolveDirNames( $this->configuration['other']['importDirs'] ) );
				}

				$lesscCommand = sprintf( 'lessc %s --line-numbers=\'comments\' --include-path=%s %s > %s 2>&1', $compressed, $importDirs, $file, $outputfile );
				$lesscOutput = array( );
				$lesscStatus = 0;
				exec( $lesscCommand, $lesscOutput, $lesscStatus );

				t3lib_div::fixPermissions( $outputfile, FALSE );
			}
		}

		// unlink compiled files which have no equal source less-file
		if( $this->configuration['other']['unlinkCssFilesWithNoSourceFile'] == 1 )
		{
			$this->unlinkGeneratedFilesWithNoSourceFile( $files );
		}

		$files = t3lib_div::getFilesInDir( $this->outputfolder, "css" );
		//respect given sort order defined in TS
		usort( $files, array( $this, 'getSortOrderPhp' ) );

		foreach( $files as $cssFile )
		{
			$excludeFromPageRender = isset( $this->configuration['phpcompiler']['filesettings'][substr( $cssFile, 0, -37 )]['excludeFromPageRenderer'] ) ? $this->configuration['phpcompiler']['filesettings'][substr( $cssFile, 0, -37 )]['excludeFromPageRenderer'] : 0;
			if( !$excludeFromPageRender || $excludeFromPageRender == 0 )
			{
				// array with filesettings from TS
				$tsOptions = $this->configuration['phpcompiler']['filesettings'][substr( $cssFile, 0, -37 )];

				$GLOBALS['TSFE']->getPageRenderer()->addCssFile(
					$this->outputfolder . $cssFile, $rel = 'stylesheet', $media = isset( $tsOptions['media'] ) ? $tsOptions['media'] : 'all', $title = isset( $tsOptions['title'] ) ? $tsOptions['title'] : '', $compress = isset( $tsOptions['compress'] ) && $tsOptions['compress'] >= '0' ? (boolean) $tsOptions['compress'] : TRUE, $forceOnTop = isset( $tsOptions['forceOnTop'] ) && $tsOptions['forceOnTop'] >= '0' ? (boolean) $tsOptions['forceOnTop'] : FALSE, $allWrap = isset( $tsOptions['allWrap'] ) ? $tsOptions['allWrap'] : '', $excludeFromConcatenation = isset( $tsOptions['excludeFromConcatenation'] ) && $tsOptions['excludeFromConcatenation'] >= '0' ? (boolean) $tsOptions['excludeFromConcatenation'] : FALSE
				);
			}
		}
	}

	/**
	 * unlink compiled files which have no equal source less-file
	 * Only for mode "PHP-Compiler"
	 */
	protected function unlinkGeneratedFilesWithNoSourceFile( $sourceFiles )
	{

		// all available sourcefiles
		//$sourceFiles = t3lib_div::getFilesInDir($this->lessfolder, "less");
		// build array with md5 values from sourcefiles
		$srcArr = array( );
		foreach( $sourceFiles as $file )
		{

			$filename = array_pop( explode( '/', $file ) );

			$md5 = md5( $filename . md5_file( $file ) );

			$srcArr[] .= $md5;
		}

		// unlink every css file, which have no equal less-file
		// checked by comparing md5-string from filename with md5_file(sourcefile)
		foreach( t3lib_div::getFilesInDir( $this->outputfolder, "css" ) as $cssFile )
		{
			$md5str = substr( substr( $cssFile, 0, -4 ), -32 );
			if( !in_array( $md5str, $srcArr ) )
			{
				unlink( $this->outputfolder . $cssFile );
			}
		}
	}

	/**
	 * getSortOrderPhp
	 * little helper function to respect given sort order defined in TS by using phpcompiler
	 * @param type $file1
	 * @param type $file2
	 * @return int
	 */
	protected function getSortOrderPhp( $file1, $file2 )
	{
		$fileSettings = $this->configuration['phpcompiler']['filesettings'];
		$tsOptions1 = $fileSettings[substr( $file1, 0, -37 )];
		$tsOptions2 = $fileSettings[substr( $file2, 0, -37 )];
		$sortOrder1 = isset( $tsOptions1['sortOrder'] ) ? $tsOptions1['sortOrder'] : 0;
		$sortOrder2 = isset( $tsOptions2['sortOrder'] ) ? $tsOptions2['sortOrder'] : 0;
		$forceOnTop1 = isset( $tsOptions1['forceOnTop'] ) ? $tsOptions1['forceOnTop'] : FALSE;
		$forceOnTop2 = isset( $tsOptions2['forceOnTop'] ) ? $tsOptions2['forceOnTop'] : FALSE;
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
