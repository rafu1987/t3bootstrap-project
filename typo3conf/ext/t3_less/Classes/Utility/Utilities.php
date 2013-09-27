<?php
/*
 * Author & Copyright: David Greiner
 * Contact: hallo@davidgreiner.de
 * Created on: 19.05.2013, 00:55:11
 */

class Tx_T3Less_Utility_Utilities
{
	/*
	 * Returns correct path to Less/Css-Folders
	 * @todo: is there no t3lib_xx-function to resolve folder pathes starting with 'EXT:' ?
	 *
	 * @param $path string Given path
	 */

	public static function getPath( $path, $file = false )
	{

		// resolving 'EXT:' from path, if path begins with 'EXT:'
		if( !strcmp( substr( $path, 0, 4 ), 'EXT:' ) )
		{
			list($extKey, $endOfPath) = explode( '/', substr( $path, 4 ), 2 );
			if( $extKey && t3lib_extMgm::isLoaded( $extKey ) )
			{
				$extPath = t3lib_extMgm::extPath( $extKey );
				$path = substr( $extPath, strlen( PATH_site ) ) . $endOfPath;
			}
		}

		// check for trailing slash and add it if it is not given
		if( substr( $path, -1, 1 ) !== '/' && $file === false )
		{
			$path = $path . '/';
		}

		return $path;
	}

	/*
	 * flatArray
	 * little helper function to flatten multi arrays to flat arrays
	 * @return array $elements
	 */

	public static function flatArray( $needle = null, $haystack = array( ) )
	{
		$iterator = new RecursiveIteratorIterator( new RecursiveArrayIterator( $haystack ) );
		$elements = array( );

		foreach( $iterator as $element )
		{

			if( is_null( $needle ) || $iterator->key() == $needle )
			{
				$elements[] = $element;
			}
		}
		return $elements;
	}

	/**
	 * @param string $dirs
	 * @return array
	 */
	public static function splitAndResolveDirNames( $dirs )
	{
		$dirs = explode( ',', $dirs );
		foreach( $dirs as &$dir )
		{
			$dir = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName( trim( $dir ) );
		}
		return $dirs;
	}

	/**
	 * wraps error messages in a div
	 * @param string $message
	 * @return string $errMsg
	 */
	public static function wrapErrorMessage( $message )
	{
		$errMsg = ' <div class="t3-less-error-message"
                    style="background:red;color:white;width:100%;font-size:14px;padding:5px;">
                    <b>T3_LESS error message: </b>'
			. $message .
			'</div> ';
		return $errMsg;
	}

}

?>
