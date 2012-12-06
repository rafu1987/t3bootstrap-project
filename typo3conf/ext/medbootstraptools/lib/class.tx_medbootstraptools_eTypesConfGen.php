<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch (zschorsch@medialis.net)
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
 * ************************************************************* */

class tx_medbootstraptools_eTypesConfGen {

    public function bootstrapFields(&$params, &$tvObj) {
        $elArray = &$params['elArray'];
        $key = &$params['key'];

        switch ($elArray[$key]['tx_templavoila']['eType']) {
            case 't3bootstrap_bootstrapicons':
                $refTable = $elArray[$key]['tx_templavoila']['eType_EXTRA']['refTable'];

                $elArray[$key]['TCEforms']['config'] = array(
                    "form_type" => "user",
                    "userFunc" => "tx_medbootstraptools_extraFields->renderBootstrapIcons"
                );
                $elArray[$key]['tx_templavoila']['TypoScript_constants'] = '';
                $elArray[$key]['tx_templavoila']['TypoScript'] = '';
                break;
            case 't3bootstrap_slider':
                $refTable = $elArray[$key]['tx_templavoila']['eType_EXTRA']['refTable'];

                $elArray[$key]['TCEforms']['config'] = array(
                    "form_type" => "user",
                    "userFunc" => "tx_medbootstraptools_extraFields->getExtSlider",
                    "width" => 300,
                    "range" => array(
                        "lower" => 1,
                        "upper" => 12
                    ),
                    "decimalPrecision" => 0
                );
                $elArray[$key]['tx_templavoila']['TypoScript_constants'] = '';
                $elArray[$key]['tx_templavoila']['TypoScript'] = '';
                break;
            case 't3bootstrap_offsetSlider':
                $refTable = $elArray[$key]['tx_templavoila']['eType_EXTRA']['refTable'];

                $elArray[$key]['TCEforms']['config'] = array(
                    "form_type" => "user",
                    "userFunc" => "tx_medbootstraptools_extraFields->getExtSlider",
                    "width" => 300,
                    "range" => array(
                        "lower" => 0,
                        "upper" => 12
                    ),
                    "decimalPrecision" => 0
                );
                $elArray[$key]['tx_templavoila']['TypoScript_constants'] = '';
                $elArray[$key]['tx_templavoila']['TypoScript'] = '';
                break;            
        }
    }

}

?>