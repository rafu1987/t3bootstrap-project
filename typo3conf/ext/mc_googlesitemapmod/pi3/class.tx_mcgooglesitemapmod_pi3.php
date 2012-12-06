<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Máximo Cuadros (mcuadros@gmail.com)
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
 * Plugin 'Google Sitemap for Pages' for the 'mc_googlesitemapmod' extension.
 *
 * @author	Máximo Cuadros <mcuadros@gmail.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once($GLOBALS['TYPO3_LOADED_EXT']['mc_googlesitemapmod']['siteRelPath']."class.tx_mcgooglesitemapmod_base.php");

class tx_mcgooglesitemapmod_pi3 extends tslib_pibase {
	var $prefixId = "tx_mcgooglesitemapmod_pi3";		// Same as class name
	var $scriptRelPath = "pi3/class.tx_mcgooglesitemapmod_pi3.php";	// Path to this script relative to the extension dir.
	var $extKey = "mc_googlesitemapmod";	// The extension key.
	
	function main($content,$conf)	{
                $news= new tx_mcgooglesitemapmod_base($this->cObj,1);
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/pi3/class.tx_mcgooglesitemapmod_pi3.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/pi3/class.tx_mcgooglesitemapmod_pi3.php"]);
}

?>
