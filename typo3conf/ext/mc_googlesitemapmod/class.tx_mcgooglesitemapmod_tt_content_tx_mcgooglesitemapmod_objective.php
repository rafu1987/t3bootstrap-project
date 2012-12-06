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
 * Class/Function which manipulates the item-array for table/field tt_content_tx_mcgooglesitemapmod_objective.
 *
 * @author	Máximo Cuadros <mcuadros@gmail.com>
 */



						class tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective {
							function main(&$params,&$pObj)	{
								$show=array("tt","tx");
								$res=mysql_query("SHOW TABLES;");
								while ($row=mysql_fetch_array($res,MYSQL_NUM)) {
									$tmp=explode("_",$row[0]);
										if ( in_array($tmp[0],$show) && $tmp[count($tmp)-1] != "mm" ) {
											$params["items"][]=Array($row[0], $row[0]);
									}
								}
							}
						}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/class.tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/class.tx_mcgooglesitemapmod_tt_content_tx_mcgooglesitemapmod_objective.php"]);
}

?>
