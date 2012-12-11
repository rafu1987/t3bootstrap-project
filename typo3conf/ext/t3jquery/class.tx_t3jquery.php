<?php
/***************************************************************
 * Copyright notice
 *
 * Based on t3mootools from Peter Klein <peter@umloud.dk>
 * (c) 2007-2010 Juergen Furrer (juergen.furrer@gmail.com)
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

$confArr = tx_t3jquery::getConf();
if ($_POST['data']['jQueryVersion']) {
	$t3jqueryversion = $_POST['data']['jQueryVersion'];
	if ($_POST['data']['jQueryUiVersion']) {
		$t3jqueryversion .= '-' . $_POST['data']['jQueryUiVersion'];
	}
	if ($_POST['data']['jQueryTOOLSVersion']) {
		$t3jqueryversion .= '-' . $_POST['data']['jQueryTOOLSVersion'];
	}
	if ($_POST['data']['jQueryBootstrapVersion']) {
		$t3jqueryversion .= '-' . $_POST['data']['jQueryBootstrapVersion'];
	}
} else {
	$t3jqueryversion = $confArr['jQueryVersion'];
	if ($confArr['jQueryUiVersion']) {
		$t3jqueryversion .= '-' . $confArr['jQueryUiVersion'];
	}
	if ($confArr['jQueryTOOLSVersion']) {
		$t3jqueryversion .= '-' . $confArr['jQueryTOOLSVersion'];
	}
	if ($confArr['jQueryBootstrapVersion']) {
		$t3jqueryversion .= '-' . $confArr['jQueryBootstrapVersion'];
	}
}
define('T3JQUERYVERSION', $t3jqueryversion);

if (file_exists(PATH_site . tx_t3jquery::getJqPath() . tx_t3jquery::getJqName()) || ($confArr['integrateFromCDN'] && isset($confArr['locationCDN']))) {
	// check if dontIntegrateOnUID fit to the actual page
	if (tx_t3jquery::isIntegrated()) {
		define('T3JQUERY', TRUE);
	}
}

/**
 * jQuery Javascript Loader functions
 *
 * You are encouraged to use this library in your own scripts!
 *
 * USE:
 * The class is intended to be used without creating an instance of it.
 * So: Don't instantiate - call functions with "tx_t3jquery::" prefixed the function name.
 * So use tx_t3jquery::[method-name] to refer to the functions, eg. 'tx_t3jquery::addJqJS()'
 *
 * Example:
 *
 * if (t3lib_extMgm::isLoaded('t3jquery')) {
 *   require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
 * }
 *
 *
 * if (T3JQUERY === TRUE) {
 *   tx_t3jquery::addJqJS();
 * } else {
 *   // Here you add your own version of jQuery library, which is used if the
 *   // "t3jquery" extension is not installed.
 *   $GLOBALS['TSFE']->additionalHeaderData[] = ..
 * }
 *
 * @author Juergen Furrer (juergen.furrer@gmail.com)
 * @package TYPO3
 * @subpackage t3jquery
 */
class tx_t3jquery
{
	/**
	 * Adds the jquery script tag to the page headers first place
	 * For frontend usage only.
	 * @return	void
	 */
	function addJqJS()
	{
		if (tx_t3jquery::getIntFromVersion(TYPO3_version) >= 4003000) {
			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['t3jquery'] = 'EXT:t3jquery/class.tx_t3jquery.php:&tx_t3jquery->addJqJsByHook';
		} else {
			$confArr = tx_t3jquery::getConf();
			// Override the headerdata, THX to S. Delcroix (RVVN -  sdelcroix@rvvn.org)
			$block = NULL;
			if ($confArr['integrateFromCDN'] && isset($confArr['locationCDN'])) {
				$params = array();
				tx_t3jquery::getCdnScript($params);
				if (isset($params['jsLibs'])) {
					foreach ($params['jsLibs'] as $key => $param) {
						$block .= '<script type="text/javascript" src="'.$param['file'].'"></script>';
					}
				}
				if (isset($params['jsFiles'])) {
					foreach ($params['jsFiles'] as $key => $param) {
						$block .= '<script type="text/javascript" src="'.$param['file'].'"></script>';
					}
				}
			} else {
				$block .= tx_t3jquery::getJqJS();
			}
			if ($confArr['integrateToFooter']) {
				$GLOBALS['TSFE']->additionalFooterData['t3jquery.lib'] = $block;
			} else {
				$GLOBALS['TSFE']->additionalHeaderData['t3jquery.lib'] = $block;
			}

		}
	}

	/**
	 * Get the used Section from Configuration
	 * @return boolean
	 */
	function getSection()
	{
		$confArr = tx_t3jquery::getConf();
		if ($confArr['integrateToFooter']) {
			return t3lib_PageRenderer::PART_FOOTER;
		} else {
			return t3lib_PageRenderer::PART_HEADER;
		}
	}

	/**
	 * Return all scripts to include for CDN
	 * @param $params
	 * @return array
	 */
	function getCdnScript(&$params=array())
	{
		$confArr = tx_t3jquery::getConf();
		// The dev version does not exist...
		if (substr($confArr['jQueryTOOLSVersion'], -3) == 'dev') {
			t3lib_div::devLog('jQuery TOOLS Version \''.$confArr['jQueryTOOLSVersion'].'\' not in CDN', 't3jquery', 1);
			$confArr['jQueryTOOLSVersion'] = '1.2.5';
		}
		$temp_config = array();
		// CDN version for jQuery (t3jquery 2.0.0)
		if (preg_match("/x$/", $confArr['jQueryVersion'])) {
			$temp_config = $this->jQueryTOOLSConfig = tx_t3jquery::getJqueryConfiguration();
			$confArr['jQueryVersion'] = $temp_config['version']['cdn'];
		}
		// CDN version for jQueryUI (t3jquery 2.0.0)
		if (preg_match("/x$/", $confArr['jQueryUiVersion'])) {
			$temp_config = $this->jQueryTOOLSConfig = tx_t3jquery::getJqueryUiConfiguration();
			$confArr['jQueryUiVersion'] = $temp_config['version']['cdn'];
		}
		// CDN version for TOOLS (t3jquery 2.0.0)
		if (preg_match("/x$/", $confArr['jQueryTOOLSVersion'])) {
			$temp_config = $this->jQueryTOOLSConfig = tx_t3jquery::getJqueryToolsConfiguration();
			$confArr['jQueryTOOLSVersion'] = $temp_config['version']['cdn'];
		}
		// CDN version for Bootstrap (t3jquery 2.0.0)
		if (preg_match("/x$/", $confArr['jQueryBootstrapVersion'])) {
			$temp_config = $this->jQueryTOOLSConfig = tx_t3jquery::getJqueryBootstrapConfiguration();
			$confArr['jQueryBootstrapVersion'] = $temp_config['version']['cdn'];
		}
		switch ($confArr['locationCDN']) {
			case 'jquery' : {
				// in jQuery TOOLS jQuery is included
				if ($confArr['jQueryTOOLSVersion'] != '') {
					$params['jsLibs']['jQueryTOOLS'] = array(
						'file'                     => 'http://cdn.jquerytools.org/'.$confArr['jQueryTOOLSVersion'].'/jquery.tools.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				} else {
					$params['jsLibs']['jQuery'] = array(
						'file'                     => 'http://code.jquery.com/jquery-'.$confArr['jQueryVersion'].'.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryUiVersion'] != '') {
					$jsFile = 'http://code.jquery.com/ui/'.$confArr['jQueryUiVersion'].'/jquery-ui.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryBootstrapVersion'] != '') {
					if ($confArr['jQueryBootstrapVersion'] == '2.1.1') {
						t3lib_div::devLog('jQuery Bootstrap \''.$confArr['jQueryBootstrapVersion'].'\' not available', 't3jquery', 1);
						$confArr['jQueryBootstrapVersion'] = '2.1.0';
					}
					$jsFile = 'http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$confArr['jQueryBootstrapVersion'].'/bootstrap.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				break;
			}
			case 'google' : {
				// in jQuery TOOLS jQuery is included
				if ($confArr['jQueryTOOLSVersion'] != '') {
					$params['jsLibs']['jQueryTOOLS'] = array(
						'file'                     => 'http://cdn.jquerytools.org/'.$confArr['jQueryTOOLSVersion'].'/jquery.tools.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				} else {
					$params['jsLibs']['jQuery'] = array(
						'file'                     => 'https://ajax.googleapis.com/ajax/libs/jquery/'.$confArr['jQueryVersion'].'/jquery.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryUiVersion'] != '') {
					$jsFile = 'https://ajax.googleapis.com/ajax/libs/jqueryui/'.$confArr['jQueryUiVersion'].'/jquery-ui.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryBootstrapVersion'] != '') {
					if ($confArr['jQueryBootstrapVersion'] == '2.1.1') {
						t3lib_div::devLog('jQuery Bootstrap \''.$confArr['jQueryBootstrapVersion'].'\' not available', 't3jquery', 1);
						$confArr['jQueryBootstrapVersion'] = '2.1.0';
					}
					$jsFile = 'http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$confArr['jQueryBootstrapVersion'].'/bootstrap.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				break;
			}
			case 'msn' : {
				// in jQuery TOOLS jQuery is included
				if ($confArr['jQueryTOOLSVersion'] != '') {
					$params['jsLibs']['jQueryTOOLS'] = array(
						'file'                     => 'http://cdn.jquerytools.org/'.$confArr['jQueryTOOLSVersion'].'/jquery.tools.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				} else {
					if (tx_t3jquery::getIntFromVersion($confArr['jQueryVersion']) < 1003002) {
						t3lib_div::devLog('jQuery \''.$confArr['jQueryVersion'].'\' not in MSN-CDN', 't3jquery', 1);
						$confArr['jQueryVersion'] = '1.3.2';
					}
					// The MSN CDN does not support 1.x.0 version it's only available under 1.x
					if (preg_match("/\.0$/", $confArr['jQueryVersion'])) {
						$confArr['jQueryVersion'] = substr($confArr['jQueryVersion'], 0, -2);
					}
					$params['jsLibs']['jQuery'] = array(
						'file'                     => 'http://ajax.aspnetcdn.com/ajax/jquery/jquery-'.$confArr['jQueryVersion'].'.min.js',
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryUiVersion'] != '') {
					if (tx_t3jquery::getIntFromVersion($confArr['jQueryUiVersion']) < 1008005) {
						t3lib_div::devLog('jQuery UI \''.$confArr['jQueryUiVersion'].'\' not in MSN-CDN', 't3jquery', 1);
						$confArr['jQueryUiVersion'] = '1.8.5';
					}
					if (tx_t3jquery::getIntFromVersion($confArr['jQueryUiVersion']) == 1008024) {
						t3lib_div::devLog('jQuery UI \''.$confArr['jQueryUiVersion'].'\' not in MSN-CDN', 't3jquery', 1);
						$confArr['jQueryUiVersion'] = '1.8.23';
					}
					$jsFile = 'http://ajax.aspnetcdn.com/ajax/jquery.ui/'.$confArr['jQueryUiVersion'].'/jquery-ui.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				if ($confArr['jQueryBootstrapVersion'] != '') {
					if ($confArr['jQueryBootstrapVersion'] == '2.1.1') {
						t3lib_div::devLog('jQuery Bootstrap \''.$confArr['jQueryBootstrapVersion'].'\' not available', 't3jquery', 1);
						$confArr['jQueryBootstrapVersion'] = '2.1.0';
					}
					$jsFile = 'http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$confArr['jQueryBootstrapVersion'].'/bootstrap.min.js';
					$params['jsFiles'][$jsFile] = array(
						'file'                     => $jsFile,
						'type'                     => 'text/javascript',
						'section'                  => self::getSection(),
						'forceOnTop'               => TRUE,
						'disableCompression'       => FALSE,
						'excludeFromConcatenation' => TRUE
					);
				}
				break;
			}
			default : {
				t3lib_div::devLog('Unknown CDN-Provider: \''.$confArr['locationCDN'].'\'', 't3jquery', 3);
				break;
			}
		}
	}

	/**
	* Returns the configuration of jQuery UI
	* @return array
	*/
	function getJqueryConfiguration($version=NULL)
	{
		if ($version === NULL) {
			$confArr = tx_t3jquery::getConf();
			$version = $confArr['jQueryVersion'];
		}
		$configuration = t3lib_div::xml2array(t3lib_div::getUrl(t3lib_div::getFileAbsFileName('EXT:t3jquery/res/jquery/core/'.$version.'/jquery.xml')));
		return $configuration;
	}

	/**
	 * Returns the configuration of jQuery UI
	 * @return array
	 */
	function getJqueryUiConfiguration($version=NULL)
	{
		if ($version === NULL) {
			$confArr = tx_t3jquery::getConf();
			$version = $confArr['jQueryUiVersion'];
		}
		$configuration = t3lib_div::xml2array(t3lib_div::getUrl(t3lib_div::getFileAbsFileName('EXT:t3jquery/res/jquery/ui/'.$version.'/jquery.xml')));
		return $configuration;
	}

	/**
	 * Returns the configuration of jQuery TOOLS
	 * @return array
	 */
	function getJqueryToolsConfiguration($version=NULL)
	{
		if ($version === NULL) {
			$confArr = tx_t3jquery::getConf();
			$version = $confArr['jQueryTOOLSVersion'];
		}
		$configuration = t3lib_div::xml2array(t3lib_div::getUrl(t3lib_div::getFileAbsFileName('EXT:t3jquery/res/jquery/tools/'.$version.'/jquery.xml')));
		return $configuration;
	}

	/**
	 * Returns the configuration of jQuery Bootstrap
	 * @return array
	 */
	function getJqueryBootstrapConfiguration($version=NULL)
	{
		if ($version === NULL) {
			$confArr = tx_t3jquery::getConf();
			$version = $confArr['jQueryBootstrapVersion'];
		}
		$configuration = t3lib_div::xml2array(t3lib_div::getUrl(t3lib_div::getFileAbsFileName('EXT:t3jquery/res/jquery/bootstrap/'.$version.'/jquery.xml')));
		return $configuration;
	}

	/**
	 * Hook function for adding script
	 *
	 * @param	array	Params for hook
	 * @return	void
	 */
	function addJqJsByHook($params)
	{
		$confArr = tx_t3jquery::getConf();
		if (tx_t3jquery::isIntegrated()) {
			if ($confArr['integrateFromCDN'] && isset($confArr['locationCDN'])) {
				tx_t3jquery::getCdnScript($params);
			} else {
				$params['jsLibs']['jQuery'] = array(
					'file'       => tx_t3jquery::getJqJS(TRUE),
					'type'       => 'text/javascript',
					'section'    => self::getSection(),
					'compress'   => FALSE,
					'forceOnTop' => TRUE,
					'allWrap'    => ''
				);
			}
			define('T3JQUERY', TRUE);
		} else {
			t3lib_div::devLog('PID \'' . $GLOBALS['TSFE']->id . '\' in dontIntegrateOnUID', 't3jquery', 1);
			define('T3JQUERY', FALSE);
		}
	}

	/**
	 * Returns TRUE if the lib should be integrated
	 * 
	 * @return boolean
	 */
	function isIntegrated()
	{
		$confArr = tx_t3jquery::getConf();
		if (is_object($GLOBALS['TSFE']) and count($GLOBALS['TSFE']->rootLine) > 0) {
			foreach ($GLOBALS['TSFE']->rootLine as $page) {
				if (in_array($page['uid'], array_values(t3lib_div::trimExplode(',', $confArr['dontIntegrateInRootline'], TRUE)))) {
					return FALSE;
				}
			}
		}
		return (! $confArr['dontIntegrateOnUID'] || ! is_object($GLOBALS['TSFE']) || ! in_array($GLOBALS['TSFE']->id, array_values(t3lib_div::trimExplode(',', $confArr['dontIntegrateOnUID'], TRUE))));
	}

	/**
	 * Returns the path configuration and JS
	 * @return string
	 */
	function getJqPath()
	{
		$confArr = tx_t3jquery::getConf();
		if (preg_match("/\/$/", $confArr['configDir'])) {
			return $confArr['configDir'];
		} else {
			return $confArr['configDir'] . '/';
		}
	}

	/**
	 * Get the jQuery UI script tag.
	 * For frontend usage only.
	 * @param	boolean		If TRUE, only the URL is returned, not a full script tag
	 * @return	string		HTML Script tag to load the jQuery JavaScript library
	 */
	function getJqJS($urlOnly=FALSE)
	{
		$url = tx_t3jquery::getJqPath() . tx_t3jquery::getJqName();
		if (file_exists(PATH_site . $url)) {
			// Adding absRefPrefix here, makes sure that jquery gets included correctly
			$url = $GLOBALS['TSFE']->absRefPrefix . $url;
			if ($urlOnly) {
				return $url;
			} else {
				return '<script type="text/javascript" src="' . $url . '"></script>';
			}
		} else {
			t3lib_div::devLog('\'' . tx_t3jquery::getJqName() . '\' does not exists!', 't3jquery', 3);
		}
		return FALSE;
	}

	/**
	 * Get the jquery script tag.
	 * For backend usage only.
	 * @param	boolean		If TRUE, only the URL is returned, not a full script tag
	 * @return	string		HTML Script tag to load the jQuery JavaScript library
	 */
	function getJqJSBE($urlOnly=FALSE)
	{
		$file = tx_t3jquery::getJqPath() . tx_t3jquery::getJqName();
		if (file_exists(PATH_site . $file)) {
			$url = t3lib_div::resolveBackPath($GLOBALS['BACK_PATH'] . '../' . $file);
			if ($urlOnly) {
				return $url;
			} else {
				return '<script type="text/javascript" src="' . $url . '"></script>';
			}
		} else {
			t3lib_div::devLog('\''.tx_t3jquery::getJqName().'\' does not exists!', 't3jquery', 3);
		}
		return FALSE;
	}

	/**
	 * Returns the name of the jQuery Lib file
	 */
	function getJqName()
	{
		$confArr = tx_t3jquery::getConf();
		if ($_POST['data']['jqLibFilename']) {
			$confArr['jqLibFilename'] = $_POST['data']['jqLibFilename'];
		}
		if (! isset($confArr['jqLibFilename'])) {
			$confArr['jqLibFilename'] = 'jquery-###VERSION###.js';
		}
		$nameArr = array(
			'###VERSION###' => T3JQUERYVERSION,
		);
		$filename = str_replace(array_keys($nameArr), array_values($nameArr), $confArr['jqLibFilename']);
		return $filename;
	}

	/**
	 * Return the integer value of a version string
	 * 
	 * @param string $versionString
	 * @return integer
	 */
	function getIntFromVersion($versionString=NULL)
	{
		if (class_exists(t3lib_utility_VersionNumber)) {
			return t3lib_utility_VersionNumber::convertVersionNumberToInteger($versionString);
		} else {
			return t3lib_div::int_from_ver($versionString);
		}
	}

	/**
	 * Get the configuration of t3jquery
	 * @return array
	 */
	function getConf()
	{
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);
	}

	/**
	 * Function to be used from TypoScript to add Javascript after the jquery.js
	 *
	 * This is a small wrapper for adding javascripts script after the jQuery Library.
	 * This is needed in some situations because headerdata added with "page.headerData"
	 * is placed BEFORE the headerdata which is added using PHP.
	 *
	 * Usage:
	 *
	 * includeLibs.t3jquery = EXT:t3jquery/class.tx_t3jquery.php
	 * page.10 = USER
	 * page.10.userFunc = tx_t3jquery->addJS
	 * page.10.jsfile = fileadmin/testscript.js
	 * page.10.jsurl = http://www.example.com/script.js
	 * page.10.jsdata = alert('Hello World!');
	 * page.10.forceOnTop = 0
	 * page.10.compress = 0
	 * page.10.type = text/javascript
	 * page.10.allWrap = 
	 * page.10.jsinline = 0
	 * page.10.tofooter = 1
	 * 
	 * @param	string		$content: Content input, ignore (just put blank string)
	 * @param	array		$conf: TypoScript configuration of the plugin!
	 * @return	void
	 */
	function addJS($content, $conf)
	{
		// set the cObj from TSFE
		$cObj = $GLOBALS['TSFE']->cObj;
		// Set the tofooter to TRUE if integrateToFooter is set
		$confArr = tx_t3jquery::getConf();
		if ($confArr['integrateToFooter']) {
			$conf['tofooter'] = 'footer';
		}
		// If the jQuery lib is not added to page yet, add it!
		tx_t3jquery::addJqJS();
		// where should be the data stored (footer or header) / Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		$conf['tofooter'] = ($conf['tofooter'] || $GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter'] ? 'footer' : 'header');
		$conf['compress'] = ($conf['compress'] || $conf['jsminify']);
		$conf['type']     = $conf['type'] ? $conf['type'] : 'text/javascript';
		// Append JS file
		if ($conf['jsfile'] || $conf['jsfile.']) {
			$jsfile = preg_replace('|^'.PATH_site.'|i','', t3lib_div::getFileAbsFileName($cObj->stdWrap($conf['jsfile'], $conf['jsfile.'])));
			// Add the Javascript if file exists
			if ($jsfile != '' && file_exists(PATH_site . $jsfile)) {
				tx_t3jquery::addJsFile($jsfile, $conf);
			} else {
				t3lib_div::devLog('\''.$jsfile.'\' does not exists!', 't3jquery', 2);
			}
		}
		// add JS URL
		if ($conf['jsurl'] || $conf['jsurl.']) {
			tx_t3jquery::addJsFile($cObj->stdWrap($conf['jsurl'], $conf['jsurl.']), $conf);
		}
		// add JS data
		if ($conf['jsdata'] || $conf['jsdata.']) {
			$jsdata = trim($cObj->stdWrap($conf['jsdata'], $conf['jsdata.']));
			if ($jsdata != '') {
				tx_t3jquery::addJsInlineCode(md5($jsdata), $jsdata, $conf);
			}
		}
		// add JS ready code
		if ($conf['jsready'] || $conf['jsready.']) {
			$jsready = trim($cObj->stdWrap($conf['jsready'], $conf['jsready.']));
			if ($jsready != '') {
				$temp_js = 'jQuery(document).ready(function() {'.$jsready.'});';
				tx_t3jquery::addJsInlineCode(md5($jsready), $temp_js, $conf);
			}
		}
	}

	/**
	 * Add JS-File to the HTML
	 * 
	 * @param string $file
	 * @param array $conf
	 * @return void
	 */
	function addJsFile($file, $conf=array())
	{
		if (tx_t3jquery::getIntFromVersion(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
			if ($conf['tofooter'] == 'footer') {
				$pagerender->addJsFooterFile($file, $conf['type'], $conf['compress'], $conf['forceOnTop'], $conf['allWrap']);
			} else {
				$pagerender->addJsFile($file, $conf['type'], $conf['compress'], $conf['forceOnTop'], $conf['allWrap']);
			}
		} else {
			$temp_file = '<script type="text/javascript" src="' . $file . '"></script>';
			if ($conf['tofooter'] == 'footer') {
				$GLOBALS['TSFE']->additionalFooterData['t3jquery.jsfile.'.$file] = $temp_file;
			} else {
				$GLOBALS['TSFE']->additionalHeaderData['t3jquery.jsfile.'.$file] = $temp_file;
			}
		}
	}

	/**
	 * Add inline code to the HTML
	 * 
	 * @param string $name
	 * @param string $block
	 * @param array $conf
	 * @return void
	 */
	function addJsInlineCode($name, $block, $conf=array())
	{
		if ($conf['jsinline']) {
			$GLOBALS['TSFE']->inlineJS['t3jquery.jsdata.' . $name] = $block;
		} elseif (tx_t3jquery::getIntFromVersion(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
			if ($conf['tofooter'] == 'footer') {
				$pagerender->addJsFooterInlineCode($name, $block, $conf['compress'], $conf['forceOnTop']);
			} else {
				$pagerender->addJsInlineCode($name, $block, $conf['compress'], $conf['forceOnTop']);
			}
		} else {
			if ($conf['compress']) {
				$block = t3lib_div::minifyJavaScript($block);
			}
			if ($conf['tofooter'] == 'footer') {
				$GLOBALS['TSFE']->additionalFooterData['t3jquery.jsdata.'.$name] = t3lib_div::wrapJS($block, TRUE);
			} else {
				$GLOBALS['TSFE']->additionalHeaderData['t3jquery.jsdata.'.$name] = t3lib_div::wrapJS($block, TRUE);
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/class.tx_t3jquery.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/class.tx_t3jquery.php']);
}
?>
