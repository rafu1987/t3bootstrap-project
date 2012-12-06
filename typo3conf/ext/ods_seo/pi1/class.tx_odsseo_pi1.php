<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Robert Heel <rheel@1drop.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Redirect' for the 'ods_seo' extension.
 *
 * @author	Robert Heel <rheel@1drop.de>
 * @package	TYPO3
 * @subpackage	tx_odsseo
 */
class tx_odsseo_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_odsseo_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_odsseo_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ods_seo';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf){
		/* --------------------------------------------------
			Detect Startpage
		-------------------------------------------------- */
		$startpage=false;
		// is_siteroot
		if($conf['startpage']){
			if($GLOBALS['TSFE']->page['is_siteroot']){
				$startpage=true;
			}else{
				// shortcut from is_siteroot
				foreach($GLOBALS['TSFE']->rootLine as $page){
					if($page['is_siteroot'] && $page['doktype']==4 && $page['shortcut']==$GLOBALS['TSFE']->id){
						$startpage=true;
						break;
					}
				}
			}
			// Multilingual startpage
			if($startpage){
				$startlinks=explode(';',$conf['startpage']);
				if(sizeof($startlinks)>1){
					$startlink=$startlinks[intval($_GET['L'])];
				}else{
					$startlink=$startlinks[0];
				}
			}
		}
		$additionalParams=array_diff_key($_GET,array('id'=>0,'L'=>0,'type'=>0));
		$conf['typolink.']['additionalParams'].='&'.http_build_query($additionalParams);
		$url=t3lib_div::locationHeaderUrl($startpage ? $startlink : $this->cObj->typolink('',$conf['typolink.']));

		/* --------------------------------------------------
			Redirect
		-------------------------------------------------- */
		if($conf['redirect'] && !$GLOBALS['BE_USER']){
			$request_url=parse_url($_SERVER['REQUEST_URI']);
			if($request_url['path']!=parse_url($url,PHP_URL_PATH)){
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: '.$url.($request_url['query'] ? '?'.$request_url['query'] : ''));
				header('X-Note: Redirect by ods_seo');
				exit();
			}
		}

		/* --------------------------------------------------
			Canonical
		-------------------------------------------------- */
		if($conf['canonical']){
			$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId]='<link rel="canonical" href="'.htmlentities($url).'" />';
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_seo/pi1/class.tx_odsseo_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_seo/pi1/class.tx_odsseo_pi1.php']);
}

?>